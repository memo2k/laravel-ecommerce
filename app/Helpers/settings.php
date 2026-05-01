<?php

use App\Models\Setting;

if (!function_exists('setting_value')) {
    /**
     * Get the value of a setting
     *
     * @param string $key
     * @return string
     */
    function setting_value(string $key): string
    {
        return Setting::where('key', $key)->first()->value;
    }
}