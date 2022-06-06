<?php

namespace App\Providers;

use App\Bots\FoodBoot;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Services\Converter\Contracts\Converter',
            'App\Services\Converter\PDFConverter'
        );

        $this->registerFoodBot();
    }

    protected function registerFoodBot()
    {
        $this->app->singleton('food_bot', static function ($app) {
            $config = [
                'telegram' => [
                    'token' => config('botman.telegram.token')
                ]
            ];
            $bot    = BotManFactory::create($config);
            $chat   = config('botman.telegram.chats.foog_chat_id');
            $driver = TelegramDriver::class;

            return new FoodBoot($bot, $chat, $driver);
        });
    }
}
