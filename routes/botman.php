<?php

$botman = resolve('botman');

$botman->hears('/start', 'App\Commands\StartCommand@start');

//$botman->hears('Сделать заказ 🍽', 'App\Commands\StartOrderCommand@start');
//
//$botman->hears('Мои заказы 🧾', 'App\Commands\MyOrdersCommand@start');
//
//$botman->hears('Помощь ℹ️', 'App\Commands\HelpCommand@start');