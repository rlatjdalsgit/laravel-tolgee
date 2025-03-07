<?php

return [
    /*
     * Specify the path to your language files
     * Default is 'lang' it can be set to 'resources/lang'
     */
    'lang_path' => env('TOLGEE_LANG_PATH', 'lang'),

    /*
     * Specify a language files subfolder, in order to filter specific language files.
     * So if you have folder structure like `lang/en/messages/...`, you can set this env variable to `messages`
     * and package will use only files from messages folder.
     */
    'lang_subfolder' => env('TOLGEE_LANG_SUBFOLDER'),

    /*
     * Host to your Tolgee service instance
     */
    'host' => env('TOLGEE_HOST', 'https://app.tolgee.io'),

    /**
     * Project ID of your Tolgee service.
     */
    'project_id' => env('TOLGEE_PROJECT_ID'),

    /**
     * Valid API key from Tolgee service for the given project.
     * The API key needs to have all permissions to manage the project.
     */
    'api_key' => env('TOLGEE_API_KEY'),

    /**
     * Base locale of the project.
     */
    'locale' => env('TOLGEE_LOCALE', 'en'),

    /**
     * Override base locale translation files.
     */
    'override' => env('TOLGEE_OVERRIDE', false),

    /**
     * Accepted translation states.
     * Ex: REVIEWED,DISABLED,UNTRANSLATED,TRANSLATED
     */
    'accepted_states' => explode(",", env('TOLGEE_ACCEPTED_STATES', 'REVIEWED')),
];
