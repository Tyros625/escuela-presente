<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canonical time-slot labels by shift
    |--------------------------------------------------------------------------
    | Must stay in sync with resources/js/views/tenants/teachers/TeacherView.vue
    | (morning / evening grids used for schedule_availability).
    */
    'morning_slots' => [
        '7:30-8:20',
        '8:20-9:10',
        '9:10-10:00',
        '10:00-10:50',
        '11:10-12:00',
        '12:00-12:50',
        '12:50-13:40',
    ],

    'evening_slots' => [
        '14:00-14:50',
        '14:50-15:40',
        '15:40-16:30',
        '16:30-17:20',
        '17:20-18:10',
        '18:10-19:00',
        '19:00-19:50',
        '19:50-20:40',
    ],

    'days' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'],

    /*
    | Map legacy morning slot keys (before schedule correction) to canonical keys
    | so existing teacher availability JSON still validates.
    */
    'legacy_morning_slot_map' => [
        '10:50-11:40' => '11:10-12:00',
        '11:40-12:30' => '12:00-12:50',
        '12:30-13:20' => '12:50-13:40',
    ],
];
