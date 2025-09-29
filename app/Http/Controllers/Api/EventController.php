<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Entry;
use App\Notifications\Event\UserConfirmation;
use App\Notifications\Event\OwnerInformation;
use App\Actions\Newsletter\CreateSubscriber as CreateSubscriberAction;

class EventController extends Controller
{
  public function get($eventId)
  {
    $event = Entry::find($eventId, 'de');
   
    return response()->json([
      'title' => $event->title,
      'has_open_seats' => $this->hasOpenSeats($event),
      'available_seats' => $this->getOpenSeats($event),
      'has_salutation' => $event->has_salutation,
      'requires_salutation' => $event->requires_salutation,
      'has_name' => $event->has_name,
      'requires_name' => $event->requires_name,
      'has_firstname' => $event->has_firstname,
      'requires_firstname' => $event->requires_firstname,
      'has_email' => $event->has_email,
      'requires_email' => $event->requires_email,
      'has_phone' => $event->has_phone,
      'requires_phone' => $event->requires_phone,
      'has_street' => $event->has_street,
      'requires_street' => $event->requires_street,
      'has_zip' => $event->has_zip,
      'requires_zip' => $event->requires_zip,
      'has_location' => $event->has_location,
      'requires_location' => $event->requires_location,
      'has_remarks' => $event->has_remarks,
      'requires_remarks' => $event->requires_remarks,
      'has_number_people' => $event->has_number_people,
      'has_number_adults' => $event->has_number_adults,
      'has_number_teenagers' => $event->has_number_teenagers,
      'has_number_kids' => $event->has_number_kids,
    ]);
  }

  public function register(Request $request)
  {
    $event = Entry::find($request->input('event_id'));

    $validationResult = $this->validateRequest($request, $event);

    if ($validationResult !== TRUE)
    {
      return $validationResult;
    }

    $slug = $event->title . ' ' . $request->input('firstname') . ' ' . $request->input('name');

    // build data
    $data = [
      'title' => $event->title,
      'number_open_seats' => $event->number_open_seats,
      'event_id' => $event->id,
      'event_date' => $event->event_date->format('d.m.Y'),
      'salutation' => $request->input('salutation') ?? null,
      'name' => $request->input('name') ?? null,
      'firstname' => $request->input('firstname') ?? null,
      'email' => $request->input('email') ?? null,
      'phone' => $request->input('phone') ?? null,
      'street' => $request->input('street') ?? null,
      'zip' => $request->input('zip') ?? null,
      'location' => $request->input('location') ?? null,
      'number_people' => $request->input('number_people') ?? null,
      'number_adults' => $request->input('number_adults') ?? null,
      'number_teenagers' => $request->input('number_teenagers') ?? null,
      'number_kids' => $request->input('number_kids') ?? null,
      'cost_people' => $request->input('number_people') && $event->chargeable ? $event->cost_people * $request->input('number_people') : null,
      'cost_adults' => $request->input('number_adults') && $event->chargeable ? $event->cost_adults * $request->input('number_adults') : null,
      'cost_teenagers' => $request->input('number_teenagers') && $event->chargeable ? $event->cost_teenagers * $request->input('number_teenagers') : null,
      'cost_kids' => $request->input('number_kids') && $event->chargeable ? $event->cost_kids * $request->input('number_kids') : null,
      'remarks' => $request->input('remarks') ?? null,
      'state' => !$this->hasOpenSeats($event) ? 'waitinglist' : null,
    ];

    // Set total cost if chargeable
    if ($event->chargeable)
    {
      if ($event->has_number_people) {
        $data['cost_total'] = $data['cost_people'];
      }
      else {
        $data['cost_total'] = $data['cost_adults'] + $data['cost_teenagers'] + $data['cost_kids'];
      }
    }

    // Set number people if number_people is not required
    // sum up number_adults, number_teenagers and number_kids
    if ($event->has_number_people) {
      $data['total_registrations'] = $data['number_people'];
    }
    else {
      $data['total_registrations'] = $data['number_adults'] + $data['number_teenagers'] + $data['number_kids'];
    }

    // Add 'invoice' to $data
    $data['invoice'] = $event->invoice;

    $entry = Entry::make()
      ->collection('event_registrations')
      ->slug($slug)
      ->data($data)
      ->save();

    // Add 'text_email' to $data
    $data['text_user_confirmation_email'] = $event->text_email;

    Notification::route('mail', $request->input('email'))
      ->notify(new UserConfirmation($data)
    );

    Notification::route('mail', env('MAIL_TO'))
      ->notify(new OwnerInformation($data)
    );

    if ($request->input('newsletter') && !app()->environment('local'))
    {
      (new CreateSubscriberAction())->execute([
        'email' => $data['email'],
        'firstname' => $data['firstname'],
        'name' => $data['name'],
      ]);
    }   

    return response()->json(['message' => 'Store successful']);
  }

  protected function validateRequest(Request $request, $event)
  {
    $validationRules = $this->getValidationRules($event);

    $validator = Validator::make(
      $request->all(),
      $validationRules['rules'],
      $validationRules['messages']
    );

    if ($validator->fails())
    {
      $errors = $validator->errors();
      $formattedErrors = [];

      foreach ($errors->messages() as $field => $messages)
      {
        $formattedErrors[$field] = $messages[0];
      }

      return response()->json(['errors' => $formattedErrors], 422);
    }

    return TRUE;
  }

  protected function getValidationRules($event)
  {
    if ($event->has_salutation && $event->requires_salutation) {
      $validationRules['salutation'] = 'required';
    }

    if ($event->has_name && $event->requires_name) {
      $validationRules['name'] = 'required';
    }

    if ($event->has_firstname && $event->requires_firstname) {
      $validationRules['firstname'] = 'required';
    }

    if ($event->has_email && $event->requires_email) {
      $validationRules['email'] = 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    } 

    if ($event->has_phone && $event->requires_phone) {
      $validationRules['phone'] = 'required';
    }

    if ($event->has_street && $event->requires_street) {
      $validationRules['street'] = 'required';
    }

    if ($event->has_zip && $event->requires_zip) {
      $validationRules['zip'] = 'required';
    }

    if ($event->has_location && $event->requires_location) {
      $validationRules['location'] = 'required';
    }

    if ($event->has_number_people) {
      $validationRules['number_people'] = 'nullable|required_without_all:number_adults,number_teenagers,number_kids|integer|min:1';
    }

    if ($event->has_number_adults) {
      $validationRules['number_adults'] = 'nullable|required_without_all:number_people,number_teenagers,number_kids|integer|min:1';
    }

    if ($event->has_number_teenagers) {
      $validationRules['number_teenagers'] = 'nullable|required_without_all:number_people,number_adults,number_kids|integer|min:1';
    }

    if ($event->has_number_kids) {
      $validationRules['number_kids'] = 'nullable|required_without_all:number_people,number_adults,number_teenagers|integer|min:1';
    }

    if ($event->has_remarks && $event->requires_remarks) {
      $validationRules['remarks'] = 'required';
    }

    // always required
    // $validationRules['number_people'] = 'required|integer|min:1';
    $validationRules['privacy'] = 'accepted';

    // Set validation messages
    $validationMessages = [
      'salutation.required' => 'Anrede ist erforderlich',
      'name.required' => 'Name ist erforderlich',
      'firstname.required' => 'Vorname ist erforderlich',
      'email.required' => 'E-Mail ist erforderlich',
      'email.email' => 'E-Mail muss gültig sein',
      'email.regex' => 'E-Mail muss gültig sein',
      'phone.required' => 'Telefon ist erforderlich',
      'street.required' => 'Strasse ist erforderlich',
      'zip.required' => 'PLZ ist erforderlich',
      'location.required' => 'Ort ist erforderlich',
      'number_people.required_without_all' => 'Anzahl Personen ist erforderlich',
      'number_people.integer' => 'Anzahl Personen muss eine Zahl sein',
      'number_people.min' => 'Anzahl Personen muss mindestens 1 sein',
      'number_adults.required_without_all' => 'Anzahl Erwachsene, Jugendliche oder Kinder ist erforderlich',
      'number_adults.integer' => 'Anzahl Erwachsene muss eine Zahl sein',
      'number_adults.min' => 'Anzahl Erwachsene muss mindestens 1 sein',
      'number_teenagers.required_without_all' => 'Anzahl Erwachsene, Jugendliche oder Kinder ist erforderlich',
      'number_teenagers.integer' => 'Anzahl Jugendliche muss eine Zahl sein',
      'number_teenagers.min' => 'Anzahl Jugendliche muss mindestens 1 sein',
      'number_kids.required_without_all' => 'Anzahl Erwachsene, Jugendliche oder Kinder ist erforderlich',
      'number_kids.integer' => 'Anzahl Kinder muss eine Zahl sein',
      'number_kids.min' => 'Anzahl Kinder muss mindestens 1 sein',
      'remarks.required' => 'Bemerkungen sind erforderlich',
      'privacy.accepted' => 'Die Datenschutzbestimmungen müssen akzeptiert werden',
    ];
    
    return [
      'rules' => $validationRules,
      'messages' => $validationMessages,
    ];
  }

  protected function hasOpenSeats($event)
  {
    $registrations = Entry::query()
      ->where('collection', 'event_registrations')
      ->where('event_id', $event->id)
      ->whereNotIn('state', ['cancelled', 'partially-cancelled'])
      ->get();
    $openSeats = $event->number_open_seats - $registrations->sum('total_registrations');
    return $openSeats > 0 ? true : false;
  }

  protected function getOpenSeats($event)
  {
    $registrations = Entry::query()
      ->where('collection', 'event_registrations')
      ->where('event_id', $event->id)
      ->whereNotIn('state', ['cancelled', 'partially-cancelled'])
      ->get();
      
    return $event->number_open_seats - $registrations->sum('total_registrations');
  }
}