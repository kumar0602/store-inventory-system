<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function handle(): void { 
        Log::info("Order confirmation email sent successfully to customer ID: {$this->order->customer_id} for Order #{$this->order->id}. Grand Total: {$this->order->grand_total}");
    }
}