<?php

namespace App\Conversations;

use App\Models\Menu;
use App\Services\MenuService;
use App\Services\OrderBuilder;
use App\Services\TelegramUserService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Facades\Cache;

class StartOrderConversation extends Conversation
{
    public $menu, $order, $user, $cache_key;

    public function __construct()
    {
        $this->menu  = (new MenuService())->getMenu();
    }

    public function run()
    {
        $this->setTelegramUser();
        $this->setCacheKey();
        $this->order = $this->getOrder();
        $this->order->setUser($this->user);
        $this->getCategories();
    }

    public function getCategories()
    {
        $keyboard = $this->categoriesKeyboard()
            ->addRow(
                KeyboardButton::create('Моя корзина 🛒')->callbackData('cart'),
                KeyboardButton::create('Мои заказы 🧾')->callbackData('my_orders')
            )
            ->addRow(
                KeyboardButton::create('В меню 📕')->callbackData('menu'),
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $this->ask('Категории', function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'menu':
                        $this->getCategories();
                        break;
                    case 'back':
                        $this->bot->startConversation(new StartConversation());
                        break;
                    case 'cart':
                        $this->bot->startConversation(new CartConversation);
                        break;
                    case 'my_orders':
                        $this->bot->startConversation(new MyOrdersConversation());
                        break;
                    default:
                        $this->getMenu($callback);
                }
            }
        }, $keyboard);

    }

    public function getMenu($category)
    {
        $keyboard = $this->menuKeyboard($category)
            ->addRow(
                KeyboardButton::create('Моя корзина 🛒')->callbackData('cart'),
                KeyboardButton::create('Мои заказы 🧾')->callbackData('my_orders')
            )
            ->addRow(
                KeyboardButton::create('В меню 📕')->callbackData('menu'),
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();

        $this->ask($category, function (Answer $answer) use ($category) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'back':
                    case 'menu':
                        $this->getCategories();
                        break;
                    case 'cart':
                        $this->bot->startConversation(new CartConversation);
                        break;
                    case 'my_orders':
                        $this->bot->startConversation(new MyOrdersConversation());
                        break;
                    default:
                        $this->getProduct($category, $callback);
                }
            }
        }, $keyboard);
    }

    public function getProduct($category, $slug, $count = 1)
    {
        $menu_item = $this->menu[$category]->where('slug', $slug)->first();

        $keyboard = $this->productKeyboard();

        $message = "$menu_item->name x $count".PHP_EOL."Цена: $menu_item->price x $count = ".$menu_item->price*$count." руб.";

        $this->ask($message, function (Answer $answer) use ($menu_item, $count) {
            if ($answer->isInteractiveMessageReply()) {
                $callback = $answer->getValue();

                switch ($callback) {
                    case 'decrement_item':
                        $this->getProduct($menu_item->category, $menu_item->slug, $count == 1 ? 1 : --$count);
                        break;
                    case 'increment_item':
                        $this->getProduct($menu_item->category, $menu_item->slug, ++$count);
                        break;
                    case 'add_to_cart':
                        $this->addToCart($menu_item, $count);
                        $this->getCategories();
                        break;
                    case 'cart':
                        $this->bot->startConversation(new CartConversation);
                        break;
                    case 'back':
                        $this->getMenu($menu_item->category);
                        break;
                    case 'my_orders':
                        $this->bot->startConversation(new MyOrdersConversation());
                        break;
                    case 'menu':
                        $this->getCategories();
                        break;
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

    public function getOrder()
    {
        return Cache::remember($this->cache_key, 60, function () {
            return new OrderBuilder();
        });
    }

    public function addToCart(Menu $item, $count)
    {
        $this->order->setItem($item, $count);

        Cache::put($this->cache_key, $this->order,60);
    }

    public function setCacheKey()
    {
        $this->cache_key = 'cart_'.$this->user->chat_id.'_'.$this->user->username;
    }

    public function productKeyboard()
    {
        return Keyboard::create(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('-')->callbackData('decrement_item'),
                KeyboardButton::create('Добавить в корзину 🛒')->callbackData('add_to_cart'),
                KeyboardButton::create('+')->callbackData('increment_item')
            )
            ->addRow(
                KeyboardButton::create('Моя корзина 🛒')->callbackData('cart'),
                KeyboardButton::create('Мои заказы 🧾')->callbackData('my_orders')
            )
            ->addRow(
                KeyboardButton::create('В меню 📕')->callbackData('menu'),
                KeyboardButton::create('⬅️ Назад')->callbackData('back')
            )
            ->resizeKeyboard()
            ->toArray();
    }

    public function menuKeyboard($category)
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE);

        foreach ($this->menu[$category] as $menu_item) {

            $keyboard->addRow(
                KeyboardButton::create($menu_item->menu_item)->callbackData($menu_item->slug)
            );
        }

        return $keyboard;
    }

    public function categoriesKeyboard()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE);

        foreach ($this->menu as $category => $value) {
            $keyboard->addRow(
                KeyboardButton::create($category)->callbackData($category)
            );
        }

        return $keyboard;
    }
}