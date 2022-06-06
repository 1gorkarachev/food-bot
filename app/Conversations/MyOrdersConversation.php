<?php

namespace App\Conversations;

use App\Models\Order;
use App\Services\TelegramUserService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Carbon;

class MyOrdersConversation extends Conversation
{
    public $user, $orders;

    public function run()
    {
        $this->setTelegramUser();
        $this->loadOrders();
        $this->showOrders();
    }

    public function showOrders()
    {
        if ($this->orders->isEmpty())
        {
            $this->say('У вас нет активных заказов.');
        } else {
            $keyboard = $this->ordersKeyboard()
                ->addRow(
                    KeyboardButton::create('В меню 📕')->callbackData('menu'),
                    KeyboardButton::create('⬅️ Назад')->callbackData('back')
                )
                ->resizeKeyboard()
                ->toArray();

            $this->ask('Ваши заказы: ', function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $callback = $answer->getValue();

                    switch ($callback) {
                        case 'menu':
                            $this->bot->startConversation(new StartOrderConversation);
                            break;
                        case 'back':
                            $this->bot->startConversation(new StartConversation);
                            break;
                        default:
                            $this->showOrder($callback);
                            break;
                    }
                }
            }, $keyboard);
        }
    }

    public function showOrder($key)
    {
        $order = $this->orders->get($key-1);

        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('Отменить заказ ❎')->callbackData('cancel_order')
            )
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $message = 'Заказ: '.PHP_EOL."Номер заказа в очереди: $order->number".PHP_EOL;

        foreach ($order->orderItems as $item) {
            $message .= "*$item->name x ".$item->pivot->count." = ".$item->pivot->amount." руб.".PHP_EOL;
        }

        $message .= "Итого: $order->amount руб.";

        $this->ask($message, function (Answer $answer) use ($order) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'cancel_order':
                        $this->confirmCancelOrder($order);
                        break;
                    case 'back':
                        $this->showOrders();
                        break;
                }
            }
        }, $keyboard);
    }

    public function confirmCancelOrder(Order $order)
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('Подтвердить отмену ❎')->callbackData('cancel_order')
            )
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $this->ask('Вы действительно хотите отменить заказ?', function (Answer $answer) use ($order) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'cancel_order':
                        $this->deleteOrder($order);
                        break;
                    case 'back':
                        $this->showOrders();
                        break;
                }
            }
        }, $keyboard);
    }

    public function deleteOrder(Order $order)
    {
        $order->delete();

        $this->loadOrders();

        $this->say('Заказ был успешно отменен!');

        $this->showOrders();
    }

    public function ordersKeyboard()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE);

        foreach ($this->orders as $key => $order) {
            $keyboard->addRow(
                KeyboardButton::create("Заказ №".++$key." - Сумма: $order->amount руб.")->callbackData("$key")
            );
        }

        return $keyboard;
    }

    public function loadOrders()
    {
        $today = Carbon::today();

        $this->orders = $this->user->orders()
            ->whereMonth('created_at', $today->format('m'))
            ->whereDay('created_at', $today->format('d'))
            ->where('status', Order::CREATED)
            ->with('orderItems')
            ->get();
    }

    public function setTelegramUser()
    {
        $service    = (new TelegramUserService);
        $user       = $this->bot->getUser();
        $sender     = $this->bot->getMessage()->getSender();
        $this->user = $service->getUser($user, $sender);
    }
}