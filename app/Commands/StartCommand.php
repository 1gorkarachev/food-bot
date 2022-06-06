<?php

namespace App\Commands;

use App\Conversations\StartConversation;

class StartCommand
{
    public function start($bot)
    {
        $bot->startConversation(new StartConversation());
    }
}