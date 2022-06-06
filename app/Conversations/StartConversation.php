<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class StartConversation extends Conversation
{
    public function run()
    {
        $this->start();
    }

    public function start()
    {
        $this->say('Привет, я NordClan Bot.'.PHP_EOL.'Чтобы сделать заказ, воспользуйтесь кнопками внизу чата.');

        $this->ask("Бот начал работу! 👋", function (Answer $answer) {

            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'create_order':
                        $this->bot->startConversation(new StartOrderConversation());
                        break;
                    case 'my_orders':
                        $this->bot->startConversation(new MyOrdersConversation());
                        break;
                }
            }

        }, $this->keyboard());
    }

    public function keyboard()
    {
        return Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('Сделать заказ 🍽')->callbackData('create_order')
            )
            ->addRow(
                KeyboardButton::create('Мои заказы 🧾')->callbackData('my_orders')
            )
            ->resizeKeyboard()
            ->toArray();
    }
}
