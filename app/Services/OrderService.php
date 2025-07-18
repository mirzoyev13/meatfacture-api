<?php

 namespace App\Services;

 use App\Models\Order;
 use App\Models\OrderItem;
 use App\Models\Product;
 use App\Models\User;
 use Illuminate\Support\Facades\DB;

 class OrderService
 {
     public function createOrder(User $user, array $data): Order
     {
         return DB::transaction(function () use ($user, $data) {
             $order = Order::create([
                 'user_id' => $user->id,
                 'comment' => $data['comment'] ?? null,
                 'total'   => 0,
             ]);

             $total = 0;

             foreach ($data['items'] as $item) {
                 $product = Product::findOrFail($item['product_id']);

                 $subtotal = $product->price * $item['quantity'];
                 $total += $subtotal;

                 OrderItem::create([
                     'order_id'   => $order->id,
                     'product_id' => $product->id,
                     'quantity'   => $item['quantity'],
                     'price'      => $product->price,
                 ]);
             }

             $order->update(['total' => $total]);

             return $order;
         });
     }
 }
