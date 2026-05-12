<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Fashion' => [
                'Men\'s Clothing', 'Women\'s Clothing', 'Footwear', 'Accessories', 'Bags & Wallets',
            ],
            'Electronics' => [
                'Smartphones', 'Laptops & Computers', 'Audio & Headphones',
                'Cameras', 'Smartwatches & Wearables',
            ],
            'Home & Living' => [
                'Furniture', 'Kitchen & Dining', 'Bedding & Bath',
                'Decor & Lighting', 'Storage & Organisation',
            ],
            'Beauty & Health' => [
                'Skincare', 'Haircare', 'Fragrances',
                'Vitamins & Supplements', 'Grooming',
            ],
            'Sports & Fitness' => [
                'Gym Equipment', 'Sports Gear', 'Outdoor & Adventure',
                'Yoga & Meditation', 'Cycling',
            ],
            'Books & Media' => [
                'Fiction', 'Non-Fiction', 'Educational',
                'Comics & Graphic Novels', 'Music & Movies',
            ],
        ];

        foreach ($tree as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name'       => $parentName,
                    'is_active'  => true,
                    'sort_order' => array_search($parentName, array_keys($tree)),
                ]
            );

            foreach ($children as $i => $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'name'       => $childName,
                        'parent_id'  => $parent->id,
                        'is_active'  => true,
                        'sort_order' => $i,
                    ]
                );
            }
        }
    }
}