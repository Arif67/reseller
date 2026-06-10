<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $vendor = Vendor::first();
        if (!$vendor) {
            echo "No vendor found to assign data.\n";
            return;
        }

        // Get some statuses or create dummy ones if they don't exist
        $pendingStatus = OrderStatus::where('name', 'Pending')->first() ?? OrderStatus::create(['name' => 'Pending', 'slug' => 'pending', 'status' => 1]);

        // Create Dummy Products
        $products = [];
        $productNames = ['Premium Drop Shoulder T-Shirt', 'Premium Polo Shirt', 'Classic Denim Jacket', 'Summer Casual Shirt'];
        
        foreach ($productNames as $index => $name) {
            $products[] = Product::create([
                'name' => $name,
                'slug' => Str::slug($name . '-' . rand(100, 999)),
                'vendor_id' => $vendor->id,
                'category_id' => 24,
                'new_price' => rand(500, 1500),
                'old_price' => rand(1500, 2000),
                'stock' => rand(10, 50),
                'status' => 1,
                'type' => 1,
                'product_code' => 'DUMMY-' . rand(1000, 9999),
            ]);
        }

        // Create Dummy Orders
        $sizes = ['M', 'L', 'XL', 'XXL'];

        for ($i = 0; $i < 5; $i++) {
            $order = Order::create([
                'invoice_id' => rand(900000, 999999),
                'amount' => rand(1000, 5000),
                'discount' => 0,
                'shipping_charge' => 0,
                'order_status' => $pendingStatus->id,
                'created_at' => now()->subDays(rand(0, 5))->subHours(rand(0, 24)),
            ]);

            // Add 1 to 3 items per order
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products[array_rand($products)];
                $qty = rand(1, 3);
                $salePrice = $product->new_price;

                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_size' => $sizes[array_rand($sizes)],
                    'qty' => $qty,
                    'sale_price' => $salePrice,
                    'purchase_price' => $salePrice - 200,
                    'created_at' => $order->created_at,
                ]);
            }
        }

        echo "Dummy data seeded successfully.\n";
    }
}
