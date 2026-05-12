<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Map leaf category names → ids
        $cats = Category::whereNotNull('parent_id')
                         ->pluck('id', 'name')
                         ->toArray();

        foreach ($this->products() as $data) {
            $catId = $cats[$data['category']] ?? array_values($cats)[0];

            $slug = Str::slug($data['name']) . '-' . Str::random(4);

            Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'uuid'              => (string) \Illuminate\Support\Str::uuid(),
                    'category_id'       => $catId,
                    'name'              => $data['name'],
                    'brand'             => $data['brand'],
                    'sku'               => strtoupper(Str::random(3)) . '-' . rand(1000, 9999),
                    'short_description' => $data['short'],
                    'description'       => $data['desc'],
                    'price'             => $data['price'],
                    'compare_price'     => $data['compare'] ?? null,
                    'cost_price'        => round($data['price'] * 0.55, 2),
                    'stock'             => rand(5, 200),
                    'low_stock_threshold' => 5,
                    'track_inventory'   => true,
                    'is_active'         => true,
                    'is_featured'       => $data['featured'] ?? false,
                    'avg_rating'        => round(rand(35, 50) / 10, 1),
                    'review_count'      => rand(4, 380),
                    'tags' => isset($data['tags'])
                    ? json_encode($data['tags'])
                    : json_encode([]),
                    'sort_order'        => 0,
                ]
            );
        }
    }

    private function products(): array
    {
        return [
            // ── Fashion: Men's ─────────────────────────────────────
            ['name' => 'Classic Oxford Button-Down Shirt', 'brand' => 'Arrow',
             'category' => "Men's Clothing", 'price' => 1299, 'compare' => 1999,
             'short' => 'Crisp cotton Oxford weave. Perfect for office or casual wear.',
             'desc'  => 'Crafted from 100% combed cotton, this button-down features a classic fit with a button-down collar. Machine washable and wrinkle-resistant for everyday convenience.',
             'featured' => true, 'tags' => ['trending', 'bestseller']],

            ['name' => 'Slim-Fit Chino Trousers', 'brand' => 'Peter England',
             'category' => "Men's Clothing", 'price' => 1599, 'compare' => 2499,
             'short' => 'Versatile slim-fit chinos in stretch cotton blend.',
             'desc'  => 'These slim-fit chinos are cut from a stretch cotton blend for all-day comfort. Features a flat front, side pockets, and a zip-fly closure. Available in multiple colours.',
             'tags' => ['new', 'trending']],

            ['name' => 'Merino Wool Crewneck Sweater', 'brand' => 'Raymond',
             'category' => "Men's Clothing", 'price' => 2799, 'compare' => 3999,
             'short' => 'Luxuriously soft 100% Merino wool sweater.',
             'desc'  => 'Knitted from premium Merino wool, this crewneck offers exceptional warmth without bulk. Features ribbed cuffs, hem, and neckline. Dry clean recommended.',
             'featured' => true, 'tags' => ['premium', 'new']],

            ['name' => 'Relaxed Fit Linen Shirt', 'brand' => 'Fabindia',
             'category' => "Men's Clothing", 'price' => 999, 'compare' => 1499,
             'short' => 'Breathable linen shirt for hot-weather comfort.',
             'desc'  => 'Woven from 100% pure linen, this shirt keeps you cool and comfortable in warm weather. Features a mandarin collar and two chest pockets.',
             'tags' => ['summer', 'trending']],

            ['name' => 'Premium Denim Jacket', 'brand' => 'Levis',
             'category' => "Men's Clothing", 'price' => 3499, 'compare' => 4999,
             'short' => 'Classic denim jacket with modern slim cut.',
             'desc'  => 'Made from heavyweight 12oz denim, this jacket features classic trucker styling with chest flap pockets, side entry pockets, and adjustable button cuffs.',
             'featured' => true, 'tags' => ['bestseller', 'classic']],

            ['name' => 'Polo T-Shirt 3-Pack', 'brand' => 'U.S. Polo Assn.',
             'category' => "Men's Clothing", 'price' => 1799,
             'short' => 'Set of 3 premium cotton polo shirts.',
             'desc'  => 'Three classic polo shirts in complementary colours. Made from 100% piqué cotton with a 2-button placket and embroidered logo.',
             'tags' => ['value', 'trending']],

            // ── Fashion: Women's ────────────────────────────────────
            ['name' => 'Floral Wrap Maxi Dress', 'brand' => 'W',
             'category' => "Women's Clothing", 'price' => 2199, 'compare' => 3499,
             'short' => 'Flowy wrap maxi dress in vibrant floral print.',
             'desc'  => 'Cut from lightweight viscose, this wrap dress features a deep V-neck, tie-waist detail, and a floor-length skirt with a flirty slit. Perfect for summer occasions.',
             'featured' => true, 'tags' => ['trending', 'summer']],

            ['name' => 'Tailored Blazer', 'brand' => 'AND',
             'category' => "Women's Clothing", 'price' => 3299, 'compare' => 4999,
             'short' => 'Structured blazer for office or evening looks.',
             'desc'  => 'This single-breasted blazer features a notch lapel, padded shoulders, two-button front, and two flap pockets. Lined throughout for a polished drape.',
             'tags' => ['office', 'premium']],

            ['name' => 'High-Rise Skinny Jeans', 'brand' => 'Levis',
             'category' => "Women's Clothing", 'price' => 2499, 'compare' => 3499,
             'short' => 'High-rise skinny jeans in stretch denim.',
             'desc'  => 'Crafted from 2% elastane stretch denim, these high-rise skinny jeans offer a sculpting fit with all-day stretch comfort. Five-pocket styling.',
             'featured' => true, 'tags' => ['bestseller', 'trending']],

            ['name' => 'Embroidered Kurta Set', 'brand' => 'Biba',
             'category' => "Women's Clothing", 'price' => 1899, 'compare' => 2799,
             'short' => 'Beautifully embroidered kurta with palazzo.',
             'desc'  => 'This kurta set features intricate threadwork embroidery on pure cotton fabric. Includes matching palazzo pants. Suitable for festive and casual occasions.',
             'tags' => ['ethnic', 'festive']],

            ['name' => 'Ribbed Knit Turtleneck', 'brand' => 'Marks & Spencer',
             'category' => "Women's Clothing", 'price' => 1499, 'compare' => 2199,
             'short' => 'Cosy ribbed turtleneck in soft cotton-modal blend.',
             'desc'  => 'Knitted from a cotton-modal blend, this ribbed turtleneck offers warmth with a sleek silhouette. Features long sleeves and a relaxed fit.',
             'tags' => ['winter', 'trending']],

            // ── Footwear ────────────────────────────────────────────
            ['name' => 'Air Cushion Running Shoes', 'brand' => 'Nike',
             'category' => 'Footwear', 'price' => 5999, 'compare' => 7999,
             'short' => 'Lightweight running shoes with React foam cushioning.',
             'desc'  => 'Engineered mesh upper provides breathability while React foam midsole delivers responsive cushioning for long-distance runs. Rubber outsole for grip on all surfaces.',
             'featured' => true, 'tags' => ['sports', 'trending', 'bestseller']],

            ['name' => 'Leather Derby Shoes', 'brand' => 'Clarks',
             'category' => 'Footwear', 'price' => 4299, 'compare' => 5999,
             'short' => 'Classic full-grain leather derby for formal occasions.',
             'desc'  => 'Crafted from premium full-grain leather with a cushioned leather footbed. Features a Goodyear welt construction for durability and resoling capability.',
             'tags' => ['formal', 'premium']],

            ['name' => 'Canvas Slip-On Sneakers', 'brand' => 'Vans',
             'category' => 'Footwear', 'price' => 2499, 'compare' => 3299,
             'short' => 'Classic canvas slip-ons with elastic side accents.',
             'desc'  => 'The original slip-on features canvas upper, elastic side accents for easy on-off, and waffle rubber outsole for grip and flexibility.',
             'tags' => ['casual', 'trending']],

            ['name' => 'Block Heel Ankle Boots', 'brand' => 'Steve Madden',
             'category' => 'Footwear', 'price' => 3799, 'compare' => 5299,
             'short' => 'Chic ankle boots with stable block heel.',
             'desc'  => 'Faux leather upper with a side zip closure and a 6cm block heel. Cushioned insole for comfort. Pairs well with jeans or dresses.',
             'featured' => true, 'tags' => ['fashion', 'trending']],

            ['name' => 'Kolhapuri Leather Sandals', 'brand' => 'Fabindia',
             'category' => 'Footwear', 'price' => 1299,
             'short' => 'Handcrafted genuine leather Kolhapuri sandals.',
             'desc'  => 'Traditional Kolhapuri chappals handcrafted by artisans using vegetable-tanned leather. Features intricate cutwork and a T-bar strap.',
             'tags' => ['ethnic', 'artisan', 'trending']],

            // ── Electronics: Smartphones ────────────────────────────
            ['name' => 'ProX 15 Ultra Smartphone', 'brand' => 'Samsung',
             'category' => 'Smartphones', 'price' => 84999, 'compare' => 99999,
             'short' => '200MP camera, 5000mAh battery, 6.8" AMOLED display.',
             'desc'  => 'Features Snapdragon 8 Gen 3 processor, 12GB RAM, 256GB storage. 200MP main camera with OIS. 45W fast charging. IP68 water resistance. One UI 6.0.',
             'featured' => true, 'tags' => ['premium', 'bestseller', 'new']],

            ['name' => 'BudgetPro 5G Phone', 'brand' => 'Redmi',
             'category' => 'Smartphones', 'price' => 14999, 'compare' => 17999,
             'short' => '5G-ready smartphone with 50MP camera and 5000mAh battery.',
             'desc'  => 'MediaTek Dimensity 6020 chipset, 6GB RAM, 128GB storage. 50MP AI triple camera. 5000mAh battery with 33W fast charge. 6.6" FHD+ display.',
             'featured' => true, 'tags' => ['value', 'trending', 'bestseller']],

            ['name' => 'Pixel-Style Camera Phone', 'brand' => 'Google',
             'category' => 'Smartphones', 'price' => 59999,
             'short' => 'AI-powered camera in a compact form factor.',
             'desc'  => 'Tensor G3 chip, 8GB RAM, 128GB storage. 50MP main camera with Super Res Zoom. Real Tone and Magic Eraser AI features. Pure Android experience.',
             'tags' => ['camera', 'premium', 'new']],

            ['name' => 'Foldable Pro Max', 'brand' => 'Samsung',
             'category' => 'Smartphones', 'price' => 164999, 'compare' => 179999,
             'short' => 'Revolutionary foldable with 7.6" inner display.',
             'desc'  => 'Snapdragon 8 Gen 2 for Galaxy, 12GB RAM, 256GB storage. 7.6" foldable Dynamic AMOLED 2X inner display. IPX8 rating. S Pen support.',
             'tags' => ['premium', 'new', 'exclusive']],

            // ── Electronics: Audio ──────────────────────────────────
            ['name' => 'NoiseCancel Pro Headphones', 'brand' => 'Sony',
             'category' => 'Audio & Headphones', 'price' => 24999, 'compare' => 29999,
             'short' => 'Industry-leading ANC with 30-hour battery.',
             'desc'  => 'Sony WH-1000XM5-inspired design with custom 40mm drivers. Industry-leading Active Noise Cancellation. LDAC support. 30-hour battery life. Foldable design.',
             'featured' => true, 'tags' => ['premium', 'bestseller', 'trending']],

            ['name' => 'True Wireless Earbuds Pro', 'brand' => 'boAt',
             'category' => 'Audio & Headphones', 'price' => 2999, 'compare' => 4999,
             'short' => '42-hour total playtime with ANC and ENC.',
             'desc'  => 'Active noise cancellation + environmental noise cancellation. 10mm drivers. 42-hour total battery with case. IPX5 water resistance. Gaming mode 60ms low latency.',
             'featured' => true, 'tags' => ['value', 'bestseller', 'trending']],

            ['name' => 'Studio Monitor Headphones', 'brand' => 'Audio-Technica',
             'category' => 'Audio & Headphones', 'price' => 7999, 'compare' => 9999,
             'short' => 'Professional-grade open-back studio headphones.',
             'desc'  => '45mm large-aperture drivers. Open-back design for natural soundstage. Detachable cables. Self-adjusting headband. Ideal for mixing and mastering.',
             'tags' => ['audiophile', 'professional']],

            ['name' => 'Portable Bluetooth Speaker', 'brand' => 'JBL',
             'category' => 'Audio & Headphones', 'price' => 5499, 'compare' => 6999,
             'short' => 'Waterproof speaker with 20-hour playtime.',
             'desc'  => 'JBL Pro Sound with dual passive radiators. IPX7 waterproof. 20-hour battery. PartyBoost to pair with compatible JBL speakers. Built-in microphone.',
             'tags' => ['outdoor', 'trending', 'bestseller']],

            // ── Electronics: Laptops ────────────────────────────────
            ['name' => 'UltraBook Pro 14"', 'brand' => 'Dell',
             'category' => 'Laptops & Computers', 'price' => 84999, 'compare' => 99999,
             'short' => 'Intel Core i7, 16GB RAM, 512GB NVMe SSD.',
             'desc'  => 'Dell XPS-inspired ultrabook. 12th Gen Intel Core i7-1260P. 16GB LPDDR5 RAM. 512GB NVMe SSD. 14" 2.8K OLED display. 56Whr battery. Thunderbolt 4.',
             'featured' => true, 'tags' => ['premium', 'trending']],

            ['name' => 'Gaming Laptop RTX 4060', 'brand' => 'ASUS',
             'category' => 'Laptops & Computers', 'price' => 109999, 'compare' => 129999,
             'short' => 'RTX 4060, i9 processor, 144Hz display.',
             'desc'  => 'ASUS ROG-inspired gaming laptop. Intel Core i9-13900HX. 16GB DDR5. 1TB SSD. NVIDIA RTX 4060 8GB. 15.6" FHD 144Hz IPS. Per-key RGB keyboard.',
             'tags' => ['gaming', 'premium', 'new']],

            ['name' => 'Chromebook Flex 13"', 'brand' => 'Lenovo',
             'category' => 'Laptops & Computers', 'price' => 32999, 'compare' => 39999,
             'short' => 'Lightweight convertible with 12-hour battery.',
             'desc'  => '2-in-1 touchscreen convertible. Intel Core i3. 8GB RAM. 256GB eMMC. 13.3" FHD IPS touchscreen. 360° hinge. 12-hour battery. Chrome OS.',
             'tags' => ['value', 'portable']],

            // ── Smartwatches ────────────────────────────────────────
            ['name' => 'SmartWatch Ultra Series', 'brand' => 'Apple',
             'category' => 'Smartwatches & Wearables', 'price' => 69900, 'compare' => 79900,
             'short' => '49mm titanium case with advanced health sensors.',
             'desc'  => 'Apple Watch Ultra-inspired design. Titanium case with flat sapphire crystal. Dual-frequency GPS. Depth gauge. ECG and blood oxygen monitoring. 60-hour battery.',
             'featured' => true, 'tags' => ['premium', 'bestseller']],

            ['name' => 'Fitness Band Pro 6', 'brand' => 'Mi',
             'category' => 'Smartwatches & Wearables', 'price' => 2999, 'compare' => 3999,
             'short' => '14-day battery with SpO2 and stress monitoring.',
             'desc'  => 'AMOLED display, 14-day battery, 24/7 heart rate monitoring, SpO2, stress, sleep tracking. 5ATM water resistance. 110+ workout modes.',
             'featured' => true, 'tags' => ['value', 'bestseller', 'trending']],

            // ── Home & Living ───────────────────────────────────────
            ['name' => 'Bamboo Serving Board Set', 'brand' => 'Cravings by Chrissy',
             'category' => 'Kitchen & Dining', 'price' => 1299, 'compare' => 1999,
             'short' => 'Set of 3 premium bamboo serving and cutting boards.',
             'desc'  => 'Made from sustainably sourced Moso bamboo. Includes small, medium, and large boards. Juice groove for meats. Naturally antibacterial. Hand wash recommended.',
             'tags' => ['kitchen', 'eco', 'trending']],

            ['name' => 'Cast Iron Dutch Oven 5L', 'brand' => 'Wonderchef',
             'category' => 'Kitchen & Dining', 'price' => 3499, 'compare' => 4999,
             'short' => 'Enamelled cast iron pot for slow cooking.',
             'desc'  => '5-litre enamelled cast iron Dutch oven. Works on all hobs including induction. Even heat distribution. Self-basting lid. Oven safe to 250°C. Dishwasher safe.',
             'featured' => true, 'tags' => ['kitchen', 'premium', 'bestseller']],

            ['name' => 'Memory Foam Pillow Pair', 'brand' => 'Sleepyhead',
             'category' => 'Bedding & Bath', 'price' => 1599, 'compare' => 2299,
             'short' => 'Pair of contouring memory foam pillows.',
             'desc'  => 'Viscoelastic memory foam contours to head and neck for optimal support. Bamboo-charcoal infused for cooling and odour control. Hypoallergenic cover included.',
             'tags' => ['bedroom', 'trending', 'bestseller']],

            ['name' => 'Geometric Concrete Planter Set', 'brand' => 'Nestasia',
             'category' => 'Decor & Lighting', 'price' => 899, 'compare' => 1299,
             'short' => 'Set of 3 modern geometric concrete planters.',
             'desc'  => 'Hand-poured concrete planters in three sizes with drainage holes. Geometric faceted design adds a modern touch to any room or balcony. Indoor/outdoor use.',
             'tags' => ['decor', 'trending']],

            ['name' => 'Macramé Wall Hanging', 'brand' => 'The Wooden Stories',
             'category' => 'Decor & Lighting', 'price' => 1199, 'compare' => 1799,
             'short' => 'Handcrafted bohemian macramé wall art.',
             'desc'  => 'Handmade by artisans using 100% natural cotton rope. Features a driftwood rod and long fringe. Approximately 50cm × 90cm. Each piece is uniquely crafted.',
             'tags' => ['decor', 'artisan', 'trending']],

            ['name' => 'Scented Soy Candle Collection', 'brand' => 'Neom',
             'category' => 'Decor & Lighting', 'price' => 799, 'compare' => 999,
             'short' => 'Set of 4 hand-poured soy wax candles.',
             'desc'  => 'Made from 100% natural soy wax with cotton wicks. Four scents: Sandalwood & Vanilla, Jasmine & Rose, Cedarwood & Amber, Lavender & Chamomile. 30-hour burn time each.',
             'tags' => ['gifting', 'bestseller', 'trending']],

            // ── Beauty & Health ─────────────────────────────────────
            ['name' => 'Vitamin C Brightening Serum', 'brand' => 'Minimalist',
             'category' => 'Skincare', 'price' => 599, 'compare' => 799,
             'short' => '10% Vitamin C + E + Ferulic Acid serum.',
             'desc'  => 'Stabilised 10% ascorbic acid with vitamin E and ferulic acid. Brightens dark spots, evens skin tone, and provides antioxidant protection. Suitable for all skin types.',
             'featured' => true, 'tags' => ['skincare', 'bestseller', 'trending']],

            ['name' => 'Retinol 0.3% Night Cream', 'brand' => 'The Ordinary',
             'category' => 'Skincare', 'price' => 849, 'compare' => 1199,
             'short' => 'Anti-ageing retinol cream for overnight renewal.',
             'desc'  => '0.3% retinol in a squalane base for gentle, effective overnight skin renewal. Reduces fine lines and improves skin texture. Apply every third night initially.',
             'tags' => ['skincare', 'trending', 'new']],

            ['name' => 'Hyaluronic Acid Moisturiser', 'brand' => 'Cetaphil',
             'category' => 'Skincare', 'price' => 699, 'compare' => 899,
             'short' => 'Lightweight 72-hour hydration moisturiser.',
             'desc'  => 'Formulated with 3 types of hyaluronic acid, niacinamide, and vitamin E. Provides 72-hour hydration. Fragrance-free. Non-comedogenic. Dermatologist tested.',
             'featured' => true, 'tags' => ['skincare', 'bestseller', 'trending']],

            ['name' => 'Argan Oil Hair Mask', 'brand' => 'Mamaearth',
             'category' => 'Haircare', 'price' => 449, 'compare' => 599,
             'short' => 'Deep conditioning mask with Argan & Avocado.',
             'desc'  => 'Enriched with Moroccan argan oil and avocado butter. Repairs damaged hair, reduces frizz, and adds intense shine. Paraben-free and silicone-free. 200ml.',
             'tags' => ['haircare', 'trending', 'value']],

            ['name' => 'Eau de Parfum — Oud & Amber', 'brand' => 'Forest Essentials',
             'category' => 'Fragrances', 'price' => 3499, 'compare' => 4499,
             'short' => 'Luxurious oriental fragrance with oud and amber.',
             'desc'  => 'A warm oriental fragrance with top notes of bergamot and saffron, heart notes of oud and rose, and a base of amber, sandalwood, and musk. 100ml EDP.',
             'featured' => true, 'tags' => ['fragrance', 'premium', 'gifting']],

            // ── Sports & Fitness ────────────────────────────────────
            ['name' => 'Adjustable Dumbbell Set 5-25kg', 'brand' => 'Boldfit',
             'category' => 'Gym Equipment', 'price' => 8999, 'compare' => 12999,
             'short' => 'Space-saving adjustable dumbbells with rack.',
             'desc'  => 'Each dumbbell adjusts from 5kg to 25kg in 2.5kg increments. Includes stand. Replaces 10 pairs of dumbbells. Quick-select dial system. Durable resin shell.',
             'featured' => true, 'tags' => ['fitness', 'trending', 'bestseller']],

            ['name' => 'Yoga Mat Premium 6mm', 'brand' => 'Liforme',
             'category' => 'Yoga & Meditation', 'price' => 2499, 'compare' => 3299,
             'short' => 'Extra-wide 6mm eco-rubber yoga mat with alignment guides.',
             'desc'  => 'Made from eco-friendly natural rubber. 6mm cushioning. Unique alignment lines for positioning. Non-slip GripForTM surface. Carrying strap included. 185×68cm.',
             'tags' => ['yoga', 'trending', 'bestseller']],

            ['name' => 'Cycling Helmet MIPS', 'brand' => 'Bell',
             'category' => 'Cycling', 'price' => 4999, 'compare' => 6499,
             'short' => 'MIPS-equipped road cycling helmet.',
             'desc'  => 'MIPS rotational protection system. 20 wind tunnel vents. Integrated MIPS brain protection. In-mold construction. Fit System dialling. CE EN1078 certified.',
             'tags' => ['cycling', 'safety']],

            // ── Books ────────────────────────────────────────────────
            ['name' => 'Atomic Habits — Hardcover', 'brand' => 'Penguin',
             'category' => 'Non-Fiction', 'price' => 599, 'compare' => 799,
             'short' => 'James Clear\'s #1 bestseller on building good habits.',
             'desc'  => 'A proven framework for improving every day. James Clear, one of the world\'s leading experts on habit formation, reveals practical strategies for forming good habits, breaking bad ones.',
             'featured' => true, 'tags' => ['books', 'bestseller', 'trending']],

            ['name' => 'The Midnight Library — Paperback', 'brand' => 'Canongate',
             'category' => 'Fiction', 'price' => 399, 'compare' => 499,
             'short' => 'Matt Haig\'s award-winning novel about lives unlived.',
             'desc'  => 'Between life and death there is a library, and within that library, the shelves go on forever. Every book provides a chance to try another life you could have lived.',
             'tags' => ['books', 'trending', 'fiction']],

            // ── Accessories ──────────────────────────────────────────
            ['name' => 'Leather Bifold Wallet', 'brand' => 'Hidesign',
             'category' => 'Bags & Wallets', 'price' => 1499, 'compare' => 1999,
             'short' => 'Full-grain vegetable-tanned leather bifold.',
             'desc'  => 'Handcrafted from full-grain vegetable-tanned leather. Features 6 card slots, 2 cash compartments, and an ID window. RFID-blocking lining. Gets better with age.',
             'tags' => ['accessories', 'gifting', 'premium']],

            ['name' => 'Canvas Tote Bag — Large', 'brand' => 'DailyObjects',
             'category' => 'Bags & Wallets', 'price' => 799, 'compare' => 999,
             'short' => 'Heavy-duty 20oz canvas tote with zip pocket.',
             'desc'  => 'Made from 20oz natural cotton canvas with reinforced handles. Interior zip pocket and two open pockets. Fits a 15" laptop. Machine washable. 40L capacity.',
             'tags' => ['bags', 'eco', 'trending']],

            ['name' => 'Aviator Sunglasses Polarised', 'brand' => 'Ray-Ban',
             'category' => 'Accessories', 'price' => 4999, 'compare' => 6999,
             'short' => 'Classic aviator with polarised G-15 lenses.',
             'desc'  => 'Classic teardrop aviator silhouette with gold metal frame and polarised G-15 lenses. UV400 protection. Adjustable nose pads. Includes case and cloth.',
             'featured' => true, 'tags' => ['accessories', 'bestseller', 'trending']],

            ['name' => 'Silk Pocket Square Set', 'brand' => 'Van Heusen',
             'category' => 'Accessories', 'price' => 599, 'compare' => 899,
             'short' => 'Set of 5 silk pocket squares in assorted colours.',
             'desc'  => 'Five 100% silk pocket squares with hand-rolled edges. Dimensions: 30×30cm each. Includes a presentation gift box. Dry clean only.',
             'tags' => ['accessories', 'formal', 'gifting']],

            ['name' => 'Minimalist Mesh Watch', 'brand' => 'MVMT',
             'category' => 'Accessories', 'price' => 3999, 'compare' => 5499,
             'short' => 'Ultra-slim watch with stainless mesh bracelet.',
             'desc'  => '36mm stainless steel case, 6mm slim profile. Miyota quartz movement. Stainless steel mesh bracelet with fold-over clasp. Mineral crystal glass. 3ATM water resistance.',
             'featured' => true, 'tags' => ['accessories', 'trending', 'gifting']],
        ];
    }
}