<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Redirect / and /login to marketing landing page
    |--------------------------------------------------------------------------
    |
    | When true, guests visiting "/" or "/login" are redirected to the active
    | published landing page (unless a "seen landing" cookie/session is set).
    |
    | Set to false for staff-only hosts (e.g. system.khbevents.com) so the root
    | URL goes to login/dashboard only. Landing pages remain available at /l/{slug}.
    |
    */

    'gate_root_and_login' => env('LANDING_PAGE_GATE', false),

];
