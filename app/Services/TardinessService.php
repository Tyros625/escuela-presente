<?php

namespace App\Services;

use App\Models\Tenants\Assist;
use App\Models\Tenants\GeneralConfiguration;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TardinessService
{
    public const SHIFTS = ['morning', 'afternoon', 'fulltime'];

    public static function defaultSchoolSchedule(): array
    {
        return [
            'morning' => ['enabled' => true, 'start' => '07:00', 'end' => '13:40'],
            'afternoon' => ['enabled' => false, 'start' => '', 'end' => ''],
            'fulltime' => ['enabled' => false, 'start' => '', 'end' => ''],
        ];
    }

    public static function defaultTardinessSchedule(): array
    {
        return [
            'morning' => ['enabled' => true, 'time' => '07:41'],
            'afternoon' => ['enabled' => false, 'time' => ''],
            'fulltime' => ['enabled' => false, 'time' => ''],
        ];
    }

    public static function mergeSchoolSchedule(?array $saved): array
    {
        return array_replace_recursive(self::defaultSchoolSchedule(), $saved ?? []);
    }

    public static function mergeTardinessSchedule(?array $saved): array
    {
        return array_replace_recursive(self::defaultTardinessSchedule(), $saved ?? []);
    }

    public function thresholdForShift(GeneralConfiguration $config, string $shift): ?string
    {
        if (! in_array($shift, self::SHIFTS, true)) {
            return null;
        }

        $schedule = self::mergeTardinessSchedule($config->tardiness_schedule);
        $entry = $schedule[$shift] ?? null;

        if (empty($entry['enabled']) || empty($entry['time'])) {
            return null;
        }

        return $this->normalizeTime($entry['time']);
    }

    public function lateAssistsForDate(string $date, string $shift): Collection
    {
        $config = GeneralConfiguration::first();
        $threshold = $config ? $this->thresholdForShift($config, $shift) : null;

        if ($threshold === null) {
            return collect();
        }

        return Assist::query()
            ->with(['student.academicGroup.grade', 'student.academicGroup.section'])
            ->whereDate('created_at', $date)
            ->whereHas('student.academicGroup', function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->get()
            ->filter(fn (Assist $assist) => $this->isLate($assist->created_at, $threshold))
            ->values();
    }

    public function isLate(?string $checkInAt, string $threshold): bool
    {
        if (empty($checkInAt)) {
            return false;
        }

        $checkIn = Carbon::parse($checkInAt);
        $limit = Carbon::createFromFormat('H:i', $this->normalizeTime($threshold));

        $checkInMinutes = ((int) $checkIn->format('H')) * 60 + (int) $checkIn->format('i');
        $limitMinutes = ((int) $limit->format('H')) * 60 + (int) $limit->format('i');

        return $checkInMinutes > $limitMinutes;
    }

    public function formatArrivalTime(?string $checkInAt): string
    {
        if (empty($checkInAt)) {
            return '';
        }

        return Carbon::parse($checkInAt)->format('G:i');
    }

    private function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i');
    }
}
