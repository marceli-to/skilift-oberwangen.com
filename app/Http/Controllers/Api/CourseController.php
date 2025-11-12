<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRegistrationRequest;
use App\Notifications\Course\OwnerInformation;
use App\Notifications\Course\UserConfirmation;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Entry;

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

    public function register(CourseRegistrationRequest $request)
    {
        $course = Entry::find($request->input('course_id'));

        $slug = $course->title.' '.$request->input('firstname').' '.$request->input('name');

        // build data
        $data = [
            'title' => $course->title.' – '.$course->course_date->format('d.m.Y'),
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
            'state' => ! $this->hasOpenSeats($course) ? 'waitinglist' : null,
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

        try {
            Notification::route('mail', $request->input('email'))->notify(new UserConfirmation($data));
        } catch (\Exception $e) {
            \Log::error('Failed to send user confirmation for course registration: '.$e->getMessage(), [
                'email' => $request->input('email'),
                'course_id' => $course->id,
                'data' => $data,
            ]);
        }

        try {
            Notification::route('mail', env('MAIL_TO'))->notify(new OwnerInformation($data));
        } catch (\Exception $e) {
            \Log::error('Failed to send owner notification for course registration: '.$e->getMessage(), [
                'email' => env('MAIL_TO'),
                'course_id' => $course->id,
                'data' => $data,
            ]);
        }

        return response()->json(['message' => 'Store successful']);
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
