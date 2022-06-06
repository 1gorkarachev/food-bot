<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\Order;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $orders = Order::whereMonth('created_at', $today->format('m'))
            ->whereDay('created_at', $today->format('d'))
            ->with(['telegramUser', 'orderItems'])
            ->paginate(50);

        $amount = $orders->sum('amount') ?? 0;

        return view('orders.index', compact('orders', 'amount'));
    }

    public function export()
    {
        $filename = Carbon::now().'orders.xlsx';

        return (new OrdersExport)->download($filename);
    }

    public function sendMessage($message)
    {
        $bot = app('food_bot');

        $bot->sendMessage($message);

        return back();
    }
}