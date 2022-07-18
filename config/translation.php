<?php

return [


    /*
    |--------------------------------------------------------------------------
    | Shorthand Enabled
    |--------------------------------------------------------------------------
    |
    | Enables use of the shorthand translation function.
    |
    | For example: _t($text = 'Translate', $replacements = array(), $toLocale = 'en');
    |
    */

    'shorthand_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache Time
    |--------------------------------------------------------------------------
    |
    | The amount of minutes to store the translations / locales in cache,
    | default is 30 minutes.
    |
    */

    'cache_time' => 30,

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default application locale you would like to translate strings from.
    |
    | For example, if you choose `en` as the default locale, then all strings
    | will be translated from english, to the new set locale.
    |
    */

    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The locales array is used for allowing only certain locales to
    | be located inside the route segment for `Translation::getRoutePrefix()`.
    |
    | The list is also used for converting locale codes to locale names.
    |
    | Feel free to add or remove locales you don't need.
    |
    */

    'locales' => [
        'ar' => 'Arabic',
        'en' => 'English',
    ],

];
