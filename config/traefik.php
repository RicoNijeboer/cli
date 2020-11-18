<?php

return [
    'path'  => env('TRAEFIK_DIRECTORY'),
    'files' => [
        'compose' => explode(',', env('TRAEFIK_COMPOSE_FILES')),
    ],
];