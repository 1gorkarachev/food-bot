<table>
    <thead>
    <tr>
        <th><b>№</b></th>
        <th><b>Заказ</b></th>
        <th><b>Сумма</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>
                #{{ $order->number }}
            </td>
            <td style="width: 500px">
                <ul>
                    @foreach($order->orderItems as $item)
                        <li>{{ "$item->name x".$item->pivot->count }}</li>
                    @endforeach
                </ul>
            </td>
            <td>
                {{ "$order->amount руб." }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="width: 500px">Общая сумма:</td>
        <td>{{ "$amount руб." }}</td>
    </tr>
    </tbody>
</table>