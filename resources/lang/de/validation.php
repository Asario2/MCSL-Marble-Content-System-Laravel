<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Validation Messages
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Die :attribute muss akzeptiert werden.',
    'required' => 'Bitte Feld :attribute ausfüllen.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'privacy' => [
            'accepted' => 'Bitte Datenschutzerklärung aktivieren.',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'first_name' => 'Vorname',
        "terms"      => 'Datenschutzerklärung',
        'name'       => 'Nickname',
        'email'      => 'E-Mail',
        'password'   => 'Passwort',
        'privacy'    => 'Datenschutzerklärung',
    ],

];
