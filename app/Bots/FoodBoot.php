<?php

namespace App\Bots;

use App\Models\Order;
use BotMan\BotMan\BotMan;

class FoodBoot
{
    protected $bot;

    protected $chat;

    protected $driver;

    public function __construct(BotMan $bot, string $chat, string $driver)
    {
        $this->bot    = $bot;
        $this->chat   = $chat;
        $this->driver = $driver;
    }

    public function sendMessage($message)
    {
        $this->bot->say(Order::MESSAGES[$message], $this->chat, $this->driver);
    }
}