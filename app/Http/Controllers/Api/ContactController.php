<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Contact\OwnerInformation;
use App\Notifications\Contact\UserConfirmation;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
  public function store(Request $request)
  {
    $validationResult = $this->validateRequest($request);

    if ($validationResult !== TRUE)
    {
      return $validationResult;
    }

    $slug = $request->input('firstname') . '-' . $request->input('name') . '-' . $request->input('email');

    // build data
    $data = [
      'date_submission' => date('d.m.Y', time()),
      'title' => $request->input('firstname') . ' ' . $request->input('name') . ', ' . $request->input('email'),
      'name' => $request->input('name'),
      'firstname' => $request->input('firstname'),
      'street' => $request->input('street'),
      'location' => $request->input('location'),
      'phone' => $request->input('phone') ?? null,
      'email' => $request->input('email'),
      'message' => $request->input('message'),
      'privacy' => TRUE,
    ];


    $entry = Entry::make()
      ->collection('contact_form')
      ->slug($slug)
      ->data($data)
      ->save();

    Notification::route('mail', env('MAIL_TO'))
      ->notify(new OwnerInformation($data)
    );

    Notification::route('mail', $data['email'])
      ->notify(new UserConfirmation($data)
    );

    return response()->json(['message' => 'Store successful']);
  }

  protected function validateRequest(Request $request)
  {
    $validationRules = $this->getValidationRules();

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
      'street' => 'required',
      'location' => 'required',
      'message' => 'required',
      'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
      'privacy' => 'accepted',
    ];

    // Set validation messages
    $validationMessages = [
      'name.required' => 'Name ist erforderlich',
      'firstname.required' => 'Vorname ist erforderlich',
      'street.required' => 'Strasse, Nr. ist erforderlich',
      'location.required' => 'PLZ/Ort ist erforderlich',
      'message.required' => 'Nachricht ist erforderlich',
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
}