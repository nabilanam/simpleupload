<?php

return [
    /** Default diskName */
    'default_disk'          => 'simpleupload',

    /** Root upload directory. Don't add trailing '/'. */
    'root_directory'        => 'uploads',

    /**
     * SimpleUpload will not delete the file if you call deleteIfExists
     * and the path starts with protected_directory
     */
    'protected_directory'   => 'defaults/',

    /** simpleupload disk config */
    'disk_config'           => [
        'url'           => env('APP_URL'),
        'root'          => public_path(),
        'driver'        => 'local',
        'visibility'    => 'public',
    ]
];
