<?php

declare(strict_types=1);

return [
    'default_user_model' => 'App\Models\User',

    'migrations_connection' => 'non_persistent',

    'user_stamp_columns' => [
        'created_by' => 'created_by',
        'updated_by' => 'updated_by',
        'deleted_by' => 'deleted_by',
    ],
];
