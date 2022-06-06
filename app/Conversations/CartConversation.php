<?php

namespace App\Conversations;

use App\Services\TelegramUserService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Facades\Cache;

class CartConversation extends Conversation
{
    public $user, $order, $cache_key;

    public function run()
    {
        $this->setTelegramUser();
        $this->setCacheKey();
        $this->getCart();
        $this->showCart();
    }

    public function showCart()
    {
        if ($this->order->getItems()->isEmpty()) {
            $this->showEmptyCart();
        } else {
            $keyboard = $this->cartKeyboard()
                ->addRow(
                    KeyboardButton::create('Подтвердить заказ ✅')->callbackData('confirm_order')
                )
                ->addRow(
                    KeyboardButton::create('⬅️ Назад')->callbackData('back'),
                    KeyboardButton::create('Очистить корзину ❎')->callbackData('clear_cart')
                )
                ->resizeKeyboard()
                ->toArray();

            $this->ask('Корзина: '.$this->order->getAmount().' руб.', function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $callback = $answer->getValue();

                    switch ($callback) {
                        case 'confirm_order':
                            $this->confirmOrder();
                            break;
                        case 'back':
                            $this->bot->startConversation(new StartConversation());
                            break;
                        case 'clear_cart':
                            $this->confirmClearCart();
                            break;
                        default:
                            $this->showCartItem($callback);
                            break;
                    }
                }
            }, $keyboard);
        }
    }

    public function showCartItem($key)
    {
        $cart_item = $this->order->getItem($key);

        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('-')->callbackData('decrement_item'),
                KeyboardButton::create('Удалить 🛒')->callbackData('delete_item'),
                KeyboardButton::create('+')->callbackData('increment_item')
            )
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $message = $cart_item['item']->name." x ".$cart_item['count']." = ".$cart_item['amount']." руб.";

        $this->ask($message, function (Answer $answer) use ($cart_item, $key) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'decrement_item':
                        $this->order->setItem($cart_item['item'], $cart_item['count'] == 1 ? 1 : $cart_item['count'] - 1);
                        Cache::put($this->cache_key, $this->order, 60 );
                        $this->getCart();
                        $this->showCartItem($key);
                        break;
                    case 'increment_item':
                        $this->order->setItem($cart_item['item'], $cart_item['count'] + 1);
                        Cache::put($this->cache_key, $this->order, 60 );
                        $this->getCart();
                        $this->showCartItem($key);
                        break;
                    case 'delete_item':
                        $this->order->unsetItem($key);
                        Cache::put($this->cache_key, $this->order, 60 );
                        $this->getCart();
                        $this->showCart();
                        break;
                    case 'back':
                        $this->showCart();
                        break;
                }
            }
        }, $keyboard);
    }

    public function confirmOrder()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('Подтвердить заказ ✅')->callbackData('confirm')
            )
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $message = 'Ваш заказ: '.PHP_EOL;
        foreach ($this->order->getItems() as $item) {
            $message .= '*'.$item['item']->name.' x'.$item['count'].' = '.$item['amount'].' руб.'.PHP_EOL;
        }

        $message .= 'Сумма к оплате: '.$this->order->getAmount(). 'руб.';

        $this->say($message);

        $this->ask('Подтвердите отправку заказа: ', function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'back':
                        $this->showCart();
                        break;
                    case 'confirm':
                        $this->saveOrder();
                        break;
                }
            }
        }, $keyboard);
    }

    public function saveOrder()
    {
        $success = $this->order->save();

        Cache::forget($this->cache_key);

        $this->say($success);
    }

    public function confirmClearCart()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('Очистить ✅')->callbackData('confirm')
            )
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $this->ask('Подтвердите действие: ', function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'back':
                        $this->showCart();
                        break;
                    case 'confirm':
                        $this->clearCart();
                        $this->bot->startConversation(new StartConversation());
                        break;
                }
            }
        }, $keyboard);
    }

    public function clearCart()
    {
        $this->order->unsetItems()->unsetAmount();

        Cache::put($this->cache_key, $this->order, 60 );
    }

    public function showEmptyCart()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $this->ask('Ваша корзина пуста!', function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                if ($callback) {
                    $this->bot->startConversation(new StartConversation());
                }
            }
        }, $keyboard);
    }

    public function setTelegramUser()
    {
        $service    = (new TelegramUserService);
        $user       = $this->bot->getUser();
        $sender     = $this->bot->getMessage()->getSender();
        $this->user = $service->getUser($user, $sender);
    }

    public function getCart()
    {
        $this->order = Cache::get($this->cache_key);
    }

    public function setCacheKey()
    {
        $this->cache_key = 'cart_'.$this->user->chat_id.'_'.$this->user->username;
    }

    public function cartKeyboard()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE);

        foreach ($this->order->getItems() as $key => $item) {

            $keyboard->addRow(
                KeyboardButton::create($item['item']->name.' x'.$item['count'].' '.$item['amount'].' руб.')
                    ->callbackData($key)
            );
        }

        return $keyboard;
    }
}