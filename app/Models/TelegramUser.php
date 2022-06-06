<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'chat_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'telegram_user_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }

    public function getTelegramAttribute()
    {
        return "<p>Telegram: <a href='https://t.me/$this->username' target='_blank'>$this->username</a></p>";
    }
}
