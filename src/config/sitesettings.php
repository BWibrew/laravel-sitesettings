<?php

return [

    /*
     * Enforce a way of formatting the setting name.
     */
    'force_naming_style' => false,

    /*
     * The list of available naming styles to use.
     *
     * Choose from: snake_case, camel_case, kebab_case, studly_case
     *
     */
    'naming_styles' => [
        'snake_case',
    ],

    /*
     * Enable the use of setting scopes.
     */
    'use_scopes' => true,

    /*
     * Set the default type of value to return when getting a setting value.
     *
     * Choose from: file_name, path, url
     */
    'media_value_type' => 'file_name',

];
