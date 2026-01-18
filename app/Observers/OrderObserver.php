<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function created(Order $order): void
    {
        if (DB::transactionLevel() > 0) {
            DB::afterCommit(fn () => broadcast(new OrderCreated($order)));

            return;
        }

        broadcast(new OrderCreated($order));
    }
}
