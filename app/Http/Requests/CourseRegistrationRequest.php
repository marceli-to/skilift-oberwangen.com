<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class CourseRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'firstname' => 'required',
            'dob' => 'required|date',
            'name_parents' => 'required',
            'street' => 'required',
            'location' => 'required',
            'phone' => 'required',
            'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'privacy' => 'accepted',
            'recaptcha_token' => 'required',
            'website' => 'nullable|max:0',
            '_start' => 'required|integer',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
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
            'recaptcha_token.required' => 'reCAPTCHA Verifizierung fehlgeschlagen',
            'website.max' => 'Website ist falsch',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if form was submitted too quickly (bot detection)
            $startTime = $this->input('_start');
            if ($startTime) {
                $elapsedTime = now()->timestamp * 1000 - $startTime;
                if ($elapsedTime < config('spam-protection.min_submit_time')) {
                    $validator->errors()->add('error', 'Something went wrong. Please try again.');
                    return;
                }
            }

            if (! $this->verifyRecaptcha()) {
                $validator->errors()->add('recaptcha_token', 'reCAPTCHA Verifizierung fehlgeschlagen. Bitte versuchen Sie es erneut.');
            }
        });
    }

    /**
     * Verify reCAPTCHA token with Google.
     */
    protected function verifyRecaptcha(): bool
    {
        $token = $this->input('recaptcha_token');

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post(config('recaptcha.verify_url'), [
                'secret' => config('recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $result = $response->json();

            if (! $result['success']) {
                return false;
            }

            // Check score for reCAPTCHA v3
            if (isset($result['score']) && $result['score'] < config('recaptcha.min_score')) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            // Log the error but don't expose it to the user
            \Log::error('reCAPTCHA verification failed: '.$e->getMessage());

            return false;
        }
    }
}
