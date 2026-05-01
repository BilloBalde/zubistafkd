<?php

namespace App\Services;

use Carbon\Carbon;

class SeasonalPromoService
{
    public function currentEvent(): ?array
    {
        $now = Carbon::now();

        foreach (config('seasonal_promos.events', []) as $key => $event) {
            $end = $this->resolveEnd($event, $now);
            $start = $this->resolveStart($event, $now, $end);

            if ($now->between($start, $end)) {
                return array_merge($event, [
                    'key'      => $key,
                    'end_date' => $end->toDateTimeString(),
                ]);
            }
        }

        return null;
    }

    public function upcomingEvent(): ?array
    {
        $now = Carbon::now();
        $closest = null;
        $closestDiff = PHP_INT_MAX;

        foreach (config('seasonal_promos.events', []) as $key => $event) {
            $end   = $this->resolveEnd($event, $now);
            $start = $this->resolveStart($event, $now, $end);

            if ($start->gt($now)) {
                $diff = $start->diffInSeconds($now);
                if ($diff < $closestDiff) {
                    $closestDiff = $diff;
                    $closest = array_merge($event, [
                        'key'        => $key,
                        'start_date' => $start->toDateTimeString(),
                        'end_date'   => $end->toDateTimeString(),
                    ]);
                }
            }
        }

        return $closest;
    }

    private function resolveEnd(array $event, Carbon $now): Carbon
    {
        if (isset($event['end_date'])) {
            return Carbon::parse($event['end_date'])->endOfDay();
        }

        $year = $now->year;
        $end  = Carbon::create($year, $event['end']['month'], $event['end']['day'])->endOfDay();

        // Wrap into next year if end month is before start month (ex: Dec→Jan)
        if ($event['end']['month'] < $event['start']['month'] && $now->month >= $event['start']['month']) {
            $end->addYear();
        }

        return $end;
    }

    private function resolveStart(array $event, Carbon $now, Carbon $end): Carbon
    {
        if (isset($event['start_date'])) {
            return Carbon::parse($event['start_date'])->startOfDay();
        }

        $year  = $end->year;
        $start = Carbon::create($year, $event['start']['month'], $event['start']['day'])->startOfDay();

        // If end wraps to next year, start is in the previous year
        if ($event['end']['month'] < $event['start']['month']) {
            $start->subYear();
        }

        return $start;
    }
}
