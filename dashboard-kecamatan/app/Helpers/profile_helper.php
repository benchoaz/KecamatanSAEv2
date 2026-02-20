<?php

if (!function_exists('appProfile')) {
    function appProfile()
    {
        return app(\App\Services\ApplicationProfileService::class)->getProfile();
    }
}
