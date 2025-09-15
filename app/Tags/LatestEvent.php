<?php

namespace App\Tags;

use Carbon\Carbon;
use Statamic\Tags\Tags;
use Statamic\Facades\Entry;

class LatestEvent extends Tags
{
    /**
     * Get the latest event based on time-based visibility rules.
     */
    public function index()
    {
        $now = Carbon::now();
        
        // Get all agenda entries
        $latestEvent = Entry::query()
            ->where('collection', 'agenda')
            ->where('published', true)
            ->get()
            ->filter(function ($entry) use ($now) {
                return $this->shouldShowEvent($entry, $now);
            })
            ->sortBy('agenda_date')
            ->first();

        // Return the entry data for use in tag pair
        return $latestEvent ? $latestEvent->toArray() : [];
    }

    /**
     * Determine if an event should be shown based on the visibility rules.
     */
    protected function shouldShowEvent($entry, Carbon $now): bool
    {
        $eventDate = $entry->get('agenda_date');
        $eventTime = $entry->get('agenda_time');
        $eventTimeEnd = $entry->get('agenda_time_end');

        if (!$eventDate) {
            return false;
        }

        // Parse the event date
        $eventDateTime = Carbon::parse($eventDate);

        // Rule 1: No times set - show until event date at 23:59
        if (!$eventTime && !$eventTimeEnd) {
            return $now->lte($eventDateTime->endOfDay());
        }

        // Rule 3: End time is set - show until end time
        if ($eventTimeEnd) {
            $endDateTime = $eventDateTime->copy()->setTimeFromTimeString($eventTimeEnd);
            return $now->lte($endDateTime);
        }

        // Rule 2: Only start time is set - show until start time
        if ($eventTime) {
            $startDateTime = $eventDateTime->copy()->setTimeFromTimeString($eventTime);
            return $now->lte($startDateTime);
        }

        return false;
    }
}