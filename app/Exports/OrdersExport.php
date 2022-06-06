<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersExport implements FromView
{
    use Exportable;

    public function view(): View
    {
        $today = Carbon::today();

        $orders =  Order::whereMonth('created_at', $today->format('m'))
            ->whereDay('created_at', $today->format('d'))
            ->with(['telegramUser', 'orderItems'])
            ->get();

        $amount = $orders->sum('amount');

        return view('exports.orders-export', compact('orders', 'amount'));
    }
}
