<?php

namespace App\Services;

use App\Models\TelegramUser;
use BotMan\BotMan\Interfaces\UserInterface;

class TelegramUserService
{
    public function getUser(UserInterface $user, $sender)
    {
        $telegram_user = TelegramUser::firstOrCreate([
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $user->getUsername(),
            'chat_id' => $sender
        ]);

        return $telegram_user;
    }
}