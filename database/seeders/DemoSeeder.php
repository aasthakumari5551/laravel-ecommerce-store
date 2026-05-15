<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────
        Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // ── Admin account ─────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@velura.in'],
            [
                'name'              => 'Velura Admin',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // ── Customer accounts ─────────────────────────────
        $customers = [
            ['name' => 'Priya Sharma',   'email' => 'priya@demo.in'],
            ['name' => 'Rahul Mehta',    'email' => 'rahul@demo.in'],
            ['name' => 'Ananya Patel',   'email' => 'ananya@demo.in'],
        ];

        $userModels = [];
        foreach ($customers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'email_verified_at' => now(),
                    'password'          => Hash::make('password'),
                    'phone'             => '+91 ' . rand(7000000000, 9999999999),
                ]
            );
            $user->assignRole('customer');

            // Address
            $user->addresses()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'label'      => 'Home',
                    'first_name' => explode(' ', $data['name'])[0],
                    'last_name'  => explode(' ', $data['name'])[1] ?? 'User',
                    'phone'      => '+91 9876543210',
                    'line1'      => rand(1, 99) . ', MG Road',
                    'city'       => collect(['Mumbai', 'Delhi', 'Bengaluru', 'Chennai'])->random(),
                    'state'      => collect(['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu'])->random(),
                    'pincode'    => (string) rand(400001, 600099),
                    'country'    => 'India',
                    'is_default' => true,
                ]
            );

            $userModels[] = $user;
        }

        // ── Demo orders ───────────────────────────────────
        $products = Product::active()->inStock()->limit(20)->get();
        if ($products->isEmpty()) {
            $this->command->warn('No products found — run ProductSeeder first.');
            return;
        }

        $statusSets = [
            [OrderStatus::Confirmed,  PaymentStatus::Paid,    'confirmed'],
            [OrderStatus::Processing, PaymentStatus::Paid,    'processing'],
            [OrderStatus::Shipped,    PaymentStatus::Paid,    'shipped'],
            [OrderStatus::Delivered,  PaymentStatus::Paid,    'delivered'],
            [OrderStatus::Pending,    PaymentStatus::Pending, 'pending'],
        ];

        foreach ($userModels as $i => $user) {
            foreach (array_slice($statusSets, 0, 3) as $j => $set) {
                [$orderStatus, $payStatus, $statusStr] = $set;

                $items    = $products->random(rand(1, 3));
                $subtotal = $items->sum('price');
                $shipping = $subtotal >= 999 ? 0 : 99;
                $tax      = round($subtotal * 0.18, 2);
                $total    = $subtotal + $shipping + $tax;

                $address = $user->addresses()->first();

                $order = Order::create([
                    'uuid'                => (string) Str::uuid(),
                    'number'              => 'ORD-' . now()->format('Ymd') . '-'
                                            . str_pad(($i * 10 + $j + 1), 4, '0', STR_PAD_LEFT),
                    'user_id'             => $user->id,
                    'shipping_first_name' => $address?->first_name ?? 'Demo',
                    'shipping_last_name'  => $address?->last_name  ?? 'User',
                    'shipping_phone'      => $address?->phone ?? '+91 9876543210',
                    'shipping_line1'      => $address?->line1 ?? 'Demo Address',
                    'shipping_city'       => $address?->city  ?? 'Mumbai',
                    'shipping_state'      => $address?->state ?? 'Maharashtra',
                    'shipping_pincode'    => $address?->pincode ?? '400001',
                    'shipping_country'    => 'India',
                    'subtotal'            => $subtotal,
                    'shipping_amount'     => $shipping,
                    'tax_amount'          => $tax,
                    'total'               => $total,
                    'status'              => $orderStatus,
                    'payment_status'      => $payStatus,
                    'payment_method'      => 'demo_gateway',
                    'razorpay_order_id'   => 'demo_order_' . Str::random(14),
                    'razorpay_payment_id' => $payStatus === PaymentStatus::Paid
                        ? 'demo_pay_' . Str::random(14) : null,
                    'paid_at'             => $payStatus === PaymentStatus::Paid ? now() : null,
                    'created_at'          => now()->subDays(rand(1, 30)),
                    'updated_at'          => now()->subDays(rand(0, 5)),
                ]);

                foreach ($items as $product) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'product_sku'  => $product->sku,
                        'quantity'     => rand(1, 2),
                        'unit_price'   => $product->price,
                        'subtotal'     => $product->price * rand(1, 2),
                    ]);
                }

                OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'status'     => $orderStatus,
                    'comment'    => 'Demo order created.',
                    'changed_by' => null,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }
        }

        // ── Demo reviews ──────────────────────────────────
        $reviewTexts = [
            [5, 'Absolutely love this product!', 'Exceeded expectations. The quality is premium and delivery was super fast. Would definitely buy again!'],
            [5, 'Worth every rupee', 'Amazing build quality. Looks exactly like the pictures. Very happy with my purchase.'],
            [4, 'Great product, minor issue', 'Overall very satisfied. The product is excellent but packaging could be better. Delivery was on time.'],
            [4, 'Good value for money', 'Solid product at a reasonable price. Does exactly what it promises. Recommended!'],
            [3, 'Decent but not great', 'Average product. It works fine but I expected a bit more for the price. Delivery was quick.'],
            [5, 'Brilliant quality!', 'This is the best purchase I\'ve made this year. The quality is outstanding and it looks fantastic.'],
            [4, 'Happy with my purchase', 'Nice product. The colour is exactly as shown. Fits perfectly. Will shop here again.'],
            [5, 'Fast delivery, great product', 'Ordered on Thursday, received Saturday. Product is exactly as described. Very impressed!'],
        ];

        $reviewedPairs = [];
        foreach ($userModels as $user) {
            $reviewProducts = $products->random(rand(3, 5));
            foreach ($reviewProducts as $product) {
                $pairKey = $user->id . '_' . $product->id;
                if (in_array($pairKey, $reviewedPairs)) continue;
                $reviewedPairs[] = $pairKey;

                [$rating, $title, $body] = collect($reviewTexts)->random();

                Review::firstOrCreate(
                    ['product_id' => $product->id, 'user_id' => $user->id],
                    [
                        'rating'               => $rating,
                        'title'                => $title,
                        'body'                 => $body,
                        'status'               => 'approved',
                        'is_verified_purchase' => true,
                        'created_at'           => now()->subDays(rand(1, 20)),
                    ]
                );

                // Update cached rating
                $stats = Review::where('product_id', $product->id)
                    ->where('status', 'approved')
                    ->selectRaw('COUNT(*) as total, AVG(rating) as average')
                    ->first();

                $product->update([
                    'avg_rating'   => round((float) ($stats->average ?? 0), 2),
                    'review_count' => (int) ($stats->total ?? 0),
                ]);
            }
        }

        $this->command->info('✅ Demo data seeded.');
        $this->command->info('   admin@velura.in / password');
        $this->command->info('   priya@demo.in   / password');
    }
}