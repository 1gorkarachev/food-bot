<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\TelegramUser;
use Carbon\Carbon;

class OrderBuilder
{
    protected $user;

    protected $items;

    protected $amount;

    public function __construct()
    {
        $this->items = collect();
    }

    public function setUser(TelegramUser $user)
    {
        $this->user = $user;

        return $this;
    }

    public function setItem(Menu $item, int $count)
    {
        $new_item = array('item' => $item, 'count' => $count, 'amount' => $item->price * $count);
        $this->items->put($item->slug, $new_item);

        $this->calculateAmount();

        return $this;
    }

    public function unsetItem($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    public function save()
    {
        $today = Carbon::today();

        $order_count = Order::whereMonth('created_at', $today->format('m'))
            ->whereDay('created_at', $today->format('d'))
            ->count();

        $order = new Order([
            'amount' => $this->amount,
            'status' => Order::CREATED,
            'number' => $order_count + 1,
        ]);

        $this->user->orders()->save($order);

        $this->items->each(function ($order_item) use ($order) {
            $order->orderItems()
                ->attach($order_item['item'], [
                    'count'  => $order_item['count'],
                    'amount' => $order_item['amount'],
                ]);
        });

        $message = 'Заказ успешно сформирован!'.PHP_EOL.'Номер вашего заказа: '.++$order_count.PHP_EOL.'Ожидайте прибытие Проспекта 🏃';

        return $message;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getItem($key)
    {
        return $this->items[$key];
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function unsetItems()
    {
        $this->items = collect();

        return $this;
    }

    public function unsetAmount()
    {
        $this->amount = null;

        return $this;
    }

    protected function calculateAmount()
    {
        $this->amount = $this->items->sum('amount');
    }
}
