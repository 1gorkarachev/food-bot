<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const CREATED = 0;
    public const DELIVERED = 1;

    public const STATUSES = [
        self::CREATED => 'Создан',
        self::DELIVERED => 'Доставлен',
    ];

    public const MENU_MESSAGE = 0;
    public const PREPARE_AVENUE_SENT_MESSAGE = 1;
    public const AVENUE_SENT_MESSAGE = 2;
    public const AVENUE_ARRIVED_MESSAGE = 3;

    public const MESSAGES = [
        self::MENU_MESSAGE => '🔄 Меню Проспект - Обновлено!',
        self::PREPARE_AVENUE_SENT_MESSAGE => 'Проспект будет отправлен через 20 минут!',
        self::AVENUE_SENT_MESSAGE => 'Проспект отправлен!',
        self::AVENUE_ARRIVED_MESSAGE => 'Проспект прибыл!'
    ];

    protected $fillable = [
        'amount',
        'status',
        'number',
    ];

    public function telegramUser()
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }

    public function orderItems()
    {
        return $this->belongsToMany(Menu::class, 'menu_order', 'order_id', 'menu_id')
            ->withPivot('count', 'amount');
    }
}
