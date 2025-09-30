<?php

return [

    'title' => 'Redefina sua senha',

    'heading' => 'Redefina sua senha',

    'passwords' => [
        'sent' => 'Enviado com Sucesso'
    ],

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'sent' => 'Enviado com Sucesso',
            'label' => 'Senha',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar senha',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Redefinir senha',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de redefinição. Por favor tente novamente em :seconds segundos.',
        ],

    ],

];
