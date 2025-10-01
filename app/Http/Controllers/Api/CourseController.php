<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Entry;
use App\Notifications\Course\UserConfirmation;
use App\Notifications\Course\OwnerInformation;

class CourseController extends Controller
{
  public function get($courseId)
  {
    $course = Entry::find($courseId, 'de');

    return response()->json([
      'title' => $course->title,
      'has_open_seats' => $this->hasOpenSeats($course),
    ]);
  }

  public function register(Request $request)
  {
    $course = Entry::find($request->input('course_id'));

    $validationResult = $this->validateRequest($request, $course);

    if ($validationResult !== TRUE)
    {
      return $validationResult;
    }

    $slug = $course->title . ' ' . $request->input('firstname') . ' ' . $request->input('name');

    // build data
    $data = [
      'title' => $course->title . ' – ' . $course->course_date->format('d.m.Y'),
      'course_id' => $course->id,
      'course_date' => $course->course_date ? $course->course_date->format('d.m.Y') : null,
      'name' => $request->input('name'),
      'firstname' => $request->input('firstname'),
      'dob' => $request->input('dob'),
      'name_parents' => $request->input('name_parents'),
      'street' => $request->input('street'),
      'location' => $request->input('location'),
      'phone' => $request->input('phone'),
      'email' => $request->input('email'),
      'remarks' => $request->input('remarks') ?? null,
      'state' => !$this->hasOpenSeats($course) ? 'waitinglist' : null,
    ];

    $entry = Entry::make()
      ->collection('course_registrations')
      ->slug($slug)
      ->data($data)
      ->save();


    // Add 'invoice' to $data
    $data['invoice'] = $course->invoice ?? null;

    // Add 'text_email' to $data for notification
    $data['text_user_confirmation_email'] = $course->text_email ?? null;

    Notification::route('mail', $request->input('email'))
      ->notify(new UserConfirmation($data));

    Notification::route('mail', env('MAIL_TO'))
      ->notify(new OwnerInformation($data));

    return response()->json(['message' => 'Store successful']);
  }

  protected function validateRequest(Request $request, $course)
  {
    $validationRules = $this->getValidationRules($course);

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

  protected function getValidationRules()
  {
    $validationRules = [
      'name' => 'required',
      'firstname' => 'required',
      'dob' => 'required|date',
      'name_parents' => 'required',
      'street' => 'required',
      'location' => 'required',
      'phone' => 'required',
      'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
      'privacy' => 'accepted',
    ];

    // Set validation messages
    $validationMessages = [
      'name.required' => 'Name ist erforderlich',
      'firstname.required' => 'Vorname ist erforderlich',
      'dob.required' => 'Geburtsdatum ist erforderlich',
      'dob.date' => 'Geburtsdatum muss ein gültiges Datum sein',
      'name_parents.required' => 'Vorname, Name (Erziehungsberechtigte) ist erforderlich',
      'street.required' => 'Adresse ist erforderlich',
      'location.required' => 'PLZ/Ort ist erforderlich',
      'phone.required' => 'Telefon ist erforderlich',
      'email.required' => 'E-Mail ist erforderlich',
      'email.email' => 'E-Mail muss gültig sein',
      'email.regex' => 'E-Mail muss gültig sein',
      'privacy.accepted' => 'Die Datenschutzbestimmungen müssen akzeptiert werden',
    ];

    return [
      'rules' => $validationRules,
      'messages' => $validationMessages,
    ];
  }

  protected function hasOpenSeats($course)
  {
    $registrations = Entry::query()
      ->where('collection', 'course_registrations')
      ->where('course_id', $course->id)
      ->whereNotIn('state', ['cancelled'])
      ->get();
    $totalRegistrations = $registrations->count();
    $openSeats = $course->number_open_seats - $totalRegistrations;
    return $openSeats > 0 ? true : false;
  }

  protected function getOpenSeats($course)
  {
    $registrations = Entry::query()
      ->where('collection', 'course_registrations')
      ->where('course_id', $course->id)
      ->whereNotIn('state', ['cancelled'])
      ->get();

    return $course->number_open_seats - $registrations->count();
  }
}