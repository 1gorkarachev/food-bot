<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram bot token you received after creating
    | the chatbot through Telegram.
    |
    */
    'token' => env('TELEGRAM_TOKEN'),

    'chats' => [
        'foog_chat_id' => env('FOOD_TELEGRAM_CHAT_ID', '890228147')
    ],
];
