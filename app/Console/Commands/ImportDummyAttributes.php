<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportDummyAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dummy-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dummy attributes and attribute options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing dummy attributes and attribute options');
        Log::info('Importing dummy attributes and attribute options');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('product_attribute_option')->truncate();
        DB::table('product_category_attribute')->truncate();
        DB::table('attribute_options')->truncate();
        DB::table('attributes')->truncate();
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        try {
            $attributes = $this->attributesData();

            foreach ($attributes as $attributeData) {
                $attribute = Attribute::create([
                    'name' => $attributeData['name'],
                    'description' => $attributeData['description'],
                ]);

                $optionsToImport = [];
                $now = now();

                foreach ($attributeData['options'] as $option) {
                    $optionsToImport[] = [
                        'attribute_id' => $attribute->id,
                        'name' => $option['name'],
                        'description' => $option['description'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                AttributeOption::insert($optionsToImport);
            }

            $this->attachAttributesToCategories();
            $this->attachOptionsToProducts();

            $this->info('Dummy attributes and attribute options imported successfully');
        } catch (\Throwable $e) {
            $this->error('Error importing dummy attributes: ' . $e->getMessage());
            Log::error('Error importing dummy attributes: ' . $e);
            return Command::FAILURE;
        }
    }

    /**
     * Populate the product_category_attribute pivot using a curated map of
     * which attributes are relevant to each category (by category slug).
     */
    protected function attachAttributesToCategories(): void
    {
        $categories = ProductCategory::query()->get(['id', 'slug']);

        if ($categories->isEmpty()) {
            $this->warn('No product categories found — skipping category ↔ attribute linking.');
            return;
        }

        $attributesByName = Attribute::query()->pluck('id', 'name')->all();
        $map = $this->categoryAttributeMap();
        $rows = [];

        foreach ($categories as $category) {
            $attributeNames = $map[$category->slug] ?? array_keys($attributesByName);

            foreach ($attributeNames as $name) {
                if (!isset($attributesByName[$name])) {
                    continue;
                }

                $rows[] = [
                    'product_category_id' => $category->id,
                    'attribute_id' => $attributesByName[$name],
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_category_attribute')->insert($chunk);
        }

        $this->info('Linked '.count($rows).' attributes across '.$categories->count().' categories.');
    }

    /**
     * For every product, pick a few random attributes from those allowed for
     * its category, then attach one random option from each into the
     * product_attribute_option pivot.
     */
    protected function attachOptionsToProducts(): void
    {
        $optionsByAttribute = AttributeOption::query()
            ->get(['id', 'attribute_id'])
            ->groupBy('attribute_id')
            ->map(fn ($options) => $options->pluck('id')->all())
            ->all();

        if (empty($optionsByAttribute)) {
            return;
        }

        $attributeIdsByCategory = DB::table('product_category_attribute')
            ->get()
            ->groupBy('product_category_id')
            ->map(fn ($rows) => $rows->pluck('attribute_id')->all())
            ->all();

        $products = Product::query()->get(['id', 'product_category_id']);

        if ($products->isEmpty()) {
            $this->warn('No products found — skipping product ↔ attribute option linking.');
            return;
        }

        $now = now();
        $rows = [];

        foreach ($products as $product) {
            $allowedAttributeIds = $attributeIdsByCategory[$product->product_category_id]
                ?? array_keys($optionsByAttribute);

            $allowedAttributeIds = array_values(array_intersect(
                $allowedAttributeIds,
                array_keys($optionsByAttribute)
            ));

            if (empty($allowedAttributeIds)) {
                continue;
            }

            $pickCount = min(count($allowedAttributeIds), random_int(4, 7));
            $pickedAttributeIds = (array) array_rand(array_flip($allowedAttributeIds), $pickCount);

            foreach ($pickedAttributeIds as $attributeId) {
                $optionIds = $optionsByAttribute[$attributeId];
                $optionId = $optionIds[array_rand($optionIds)];

                $rows[] = [
                    'product_id' => $product->id,
                    'attribute_option_id' => $optionId,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_attribute_option')->insert($chunk);
        }

        $this->info('Attached '.count($rows).' attribute options across '.$products->count().' products.');
    }

    /**
     * Curated map of category slug => attribute names that apply to it.
     * Any slug missing here falls back to "all attributes".
     *
     * @return array<string, array<int, string>>
     */
    protected function categoryAttributeMap(): array
    {
        $common = [
            'Color', 'Brand', 'Material', 'Warranty',
            'Condition', 'Country of Origin', 'Certification', 'Shipping',
        ];

        return [
            'laptops' => array_merge($common, [
                'Storage', 'RAM', 'Screen Size', 'Battery Life', 'Connectivity',
                'Operating System', 'Processor', 'Graphics Card',
                'Display Type', 'Resolution', 'Refresh Rate',
                'Charging Speed', 'Charging Type', 'Ports', 'Biometrics',
                'Keyboard', 'Audio', 'Weight Category',
            ]),
            'smartphones' => array_merge($common, [
                'Storage', 'RAM', 'Screen Size', 'Battery Life', 'Connectivity',
                'Water Resistance', 'Operating System', 'Processor',
                'Display Type', 'Resolution', 'Refresh Rate',
                'Rear Camera', 'Front Camera',
                'Charging Speed', 'Charging Type', 'Biometrics', 'SIM Type',
                'Stylus Support', 'Audio',
            ]),
            'tablets' => array_merge($common, [
                'Storage', 'RAM', 'Screen Size', 'Battery Life', 'Connectivity',
                'Water Resistance', 'Operating System', 'Processor',
                'Display Type', 'Resolution', 'Refresh Rate',
                'Rear Camera', 'Front Camera',
                'Charging Speed', 'Charging Type', 'Ports', 'Biometrics',
                'SIM Type', 'Stylus Support', 'Audio', 'Weight Category',
            ]),
            'mobile-accessories' => array_merge($common, [
                'Battery Life', 'Connectivity', 'Water Resistance',
                'Charging Speed', 'Charging Type', 'Ports', 'Audio',
            ]),
            'mens-watches' => array_merge($common, [
                'Battery Life', 'Connectivity', 'Water Resistance',
                'Operating System', 'Display Type',
                'Case Size', 'Case Shape', 'Band Material',
                'Movement Type', 'Dial Color', 'Gender',
            ]),
            'womens-watches' => array_merge($common, [
                'Battery Life', 'Connectivity', 'Water Resistance',
                'Operating System', 'Display Type',
                'Case Size', 'Case Shape', 'Band Material',
                'Movement Type', 'Dial Color', 'Gender',
            ]),
        ];
    }

    /**
     * Curated dummy attributes with options tailored to the existing product
     * categories (laptops, smartphones, mobile-accessories, tablets, watches).
     *
     * @return array<int, array{name: string, description: string, options: array<int, array{name: string, description: string}>}>
     */
    protected function attributesData(): array
    {
        return [
            [
                'name' => 'Color',
                'description' => 'Product color',
                'options' => $this->toOptions([
                    'Black', 'White', 'Silver', 'Space Gray', 'Gold',
                    'Rose Gold', 'Blue', 'Red', 'Green', 'Purple',
                ]),
            ],
            [
                'name' => 'Brand',
                'description' => 'Product manufacturer',
                'options' => $this->toOptions([
                    'Apple', 'Samsung', 'Google', 'Sony', 'Dell',
                    'HP', 'Lenovo', 'Asus', 'Microsoft', 'Huawei', 'Xiaomi',
                ]),
            ],
            [
                'name' => 'Storage',
                'description' => 'Internal storage capacity',
                'options' => $this->toOptions([
                    '64GB', '128GB', '256GB', '512GB', '1TB', '2TB',
                ]),
            ],
            [
                'name' => 'RAM',
                'description' => 'System memory',
                'options' => $this->toOptions([
                    '4GB', '8GB', '16GB', '32GB', '64GB',
                ]),
            ],
            [
                'name' => 'Screen Size',
                'description' => 'Display size in inches',
                'options' => $this->toOptions([
                    '5.5"', '6.1"', '6.7"', '10.1"', '11"',
                    '13"', '14"', '15.6"', '16"', '17"',
                ]),
            ],
            [
                'name' => 'Material',
                'description' => 'Primary material',
                'options' => $this->toOptions([
                    'Aluminum', 'Stainless Steel', 'Titanium',
                    'Plastic', 'Leather', 'Silicone', 'Glass', 'Carbon Fiber',
                ]),
            ],
            [
                'name' => 'Battery Life',
                'description' => 'Estimated battery life',
                'options' => $this->toOptions([
                    'Up to 6 hours', 'Up to 12 hours', 'Up to 18 hours',
                    'Up to 24 hours', 'Up to 48 hours', 'Up to 72 hours',
                ]),
            ],
            [
                'name' => 'Connectivity',
                'description' => 'Supported connectivity standards',
                'options' => $this->toOptions([
                    'Wi-Fi', 'Wi-Fi 6', 'Bluetooth 5.0', 'Bluetooth 5.3',
                    '4G LTE', '5G', 'NFC', 'USB-C', 'Thunderbolt',
                ]),
            ],
            [
                'name' => 'Warranty',
                'description' => 'Manufacturer warranty duration',
                'options' => $this->toOptions([
                    '6 Months', '1 Year', '2 Years', '3 Years', 'Lifetime',
                ]),
            ],
            [
                'name' => 'Water Resistance',
                'description' => 'Water resistance rating',
                'options' => $this->toOptions([
                    'None', 'Splash Proof', 'IP67', 'IP68', '5 ATM', '10 ATM',
                ]),
            ],
            [
                'name' => 'Operating System',
                'description' => 'Pre-installed operating system',
                'options' => $this->toOptions([
                    'iOS', 'iPadOS', 'Android', 'Windows 11', 'Windows 10',
                    'macOS', 'ChromeOS', 'Linux', 'watchOS', 'Wear OS',
                ]),
            ],
            [
                'name' => 'Processor',
                'description' => 'Main processor / SoC',
                'options' => $this->toOptions([
                    'Apple M1', 'Apple M2', 'Apple M3', 'Apple A17 Pro',
                    'Intel Core i5', 'Intel Core i7', 'Intel Core i9',
                    'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9',
                    'Snapdragon 8 Gen 2', 'Snapdragon 8 Gen 3',
                    'MediaTek Dimensity 9200', 'Exynos 2400',
                ]),
            ],
            [
                'name' => 'Graphics Card',
                'description' => 'Dedicated or integrated GPU',
                'options' => $this->toOptions([
                    'Integrated', 'Apple GPU',
                    'NVIDIA RTX 3050', 'NVIDIA RTX 3060', 'NVIDIA RTX 4060',
                    'NVIDIA RTX 4070', 'NVIDIA RTX 4080', 'NVIDIA RTX 4090',
                    'AMD Radeon RX 6600', 'AMD Radeon RX 7700',
                    'Intel Iris Xe', 'Intel Arc A370M',
                ]),
            ],
            [
                'name' => 'Display Type',
                'description' => 'Display panel technology',
                'options' => $this->toOptions([
                    'LCD', 'IPS', 'LED', 'OLED', 'AMOLED',
                    'Super AMOLED', 'Dynamic AMOLED', 'Retina',
                    'Liquid Retina', 'Mini-LED', 'Micro-LED',
                ]),
            ],
            [
                'name' => 'Resolution',
                'description' => 'Screen resolution',
                'options' => $this->toOptions([
                    'HD (1280x720)', 'Full HD (1920x1080)',
                    '2K (2560x1440)', '4K (3840x2160)',
                    'Retina', 'Liquid Retina XDR', 'Super Retina XDR',
                ]),
            ],
            [
                'name' => 'Refresh Rate',
                'description' => 'Display refresh rate',
                'options' => $this->toOptions([
                    '60Hz', '90Hz', '120Hz', '144Hz', '165Hz', '240Hz',
                ]),
            ],
            [
                'name' => 'Rear Camera',
                'description' => 'Main rear camera resolution',
                'options' => $this->toOptions([
                    '8MP', '12MP', '48MP', '50MP', '64MP', '108MP', '200MP',
                ]),
            ],
            [
                'name' => 'Front Camera',
                'description' => 'Front-facing camera resolution',
                'options' => $this->toOptions([
                    '5MP', '8MP', '12MP', '16MP', '32MP',
                ]),
            ],
            [
                'name' => 'Charging Speed',
                'description' => 'Maximum charging speed',
                'options' => $this->toOptions([
                    '5W', '10W', '15W', '20W', '25W', '45W',
                    '65W', '100W', '140W', '240W',
                ]),
            ],
            [
                'name' => 'Charging Type',
                'description' => 'Supported charging methods',
                'options' => $this->toOptions([
                    'Wired', 'Wireless (Qi)', 'Fast Charging',
                    'Super Fast Charging', 'MagSafe', 'Reverse Wireless',
                ]),
            ],
            [
                'name' => 'Ports',
                'description' => 'Available I/O ports',
                'options' => $this->toOptions([
                    'USB-A', 'USB-C', 'Thunderbolt 4', 'HDMI',
                    'MicroSD', 'SD Card', '3.5mm Headphone Jack',
                    'Ethernet', 'DisplayPort',
                ]),
            ],
            [
                'name' => 'Biometrics',
                'description' => 'Biometric unlock methods',
                'options' => $this->toOptions([
                    'None', 'Fingerprint', 'Face ID', 'Face Unlock',
                    'Iris Scanner', 'In-display Fingerprint',
                ]),
            ],
            [
                'name' => 'SIM Type',
                'description' => 'SIM card support',
                'options' => $this->toOptions([
                    'Nano-SIM', 'eSIM', 'Dual SIM', 'Hybrid SIM', 'No SIM',
                ]),
            ],
            [
                'name' => 'Keyboard',
                'description' => 'Laptop keyboard type',
                'options' => $this->toOptions([
                    'Chiclet', 'Membrane', 'Mechanical',
                    'Backlit', 'RGB Backlit', 'Magic Keyboard',
                ]),
            ],
            [
                'name' => 'Stylus Support',
                'description' => 'Stylus / pen support',
                'options' => $this->toOptions([
                    'Not Supported', 'Apple Pencil (1st gen)',
                    'Apple Pencil (2nd gen)', 'Apple Pencil USB-C',
                    'Samsung S Pen', 'Surface Pen', 'Generic Stylus',
                ]),
            ],
            [
                'name' => 'Audio',
                'description' => 'Audio features',
                'options' => $this->toOptions([
                    'Mono Speaker', 'Stereo Speakers', 'Quad Speakers',
                    'Dolby Atmos', 'Hi-Res Audio', 'Spatial Audio',
                    'Noise Cancellation',
                ]),
            ],
            [
                'name' => 'Weight Category',
                'description' => 'Relative weight class',
                'options' => $this->toOptions([
                    'Ultralight (< 1 kg)', 'Light (1 - 1.5 kg)',
                    'Standard (1.5 - 2 kg)', 'Heavy (> 2 kg)',
                ]),
            ],
            [
                'name' => 'Case Size',
                'description' => 'Watch case size',
                'options' => $this->toOptions([
                    '36mm', '38mm', '40mm', '41mm', '42mm',
                    '44mm', '45mm', '46mm', '49mm',
                ]),
            ],
            [
                'name' => 'Case Shape',
                'description' => 'Watch case shape',
                'options' => $this->toOptions([
                    'Round', 'Square', 'Rectangle', 'Oval', 'Tonneau',
                ]),
            ],
            [
                'name' => 'Band Material',
                'description' => 'Watch band material',
                'options' => $this->toOptions([
                    'Leather', 'Stainless Steel', 'Silicone', 'Rubber',
                    'Nylon', 'Milanese Loop', 'Ceramic', 'Fabric', 'Titanium',
                ]),
            ],
            [
                'name' => 'Movement Type',
                'description' => 'Watch movement',
                'options' => $this->toOptions([
                    'Quartz', 'Automatic', 'Mechanical', 'Solar',
                    'Kinetic', 'Smart',
                ]),
            ],
            [
                'name' => 'Dial Color',
                'description' => 'Watch dial color',
                'options' => $this->toOptions([
                    'Black', 'White', 'Silver', 'Blue', 'Green',
                    'Gold', 'Rose Gold', 'Champagne', 'Mother of Pearl',
                ]),
            ],
            [
                'name' => 'Gender',
                'description' => 'Target gender',
                'options' => $this->toOptions([
                    'Men', 'Women', 'Unisex', 'Kids',
                ]),
            ],
            [
                'name' => 'Condition',
                'description' => 'Product condition',
                'options' => $this->toOptions([
                    'New', 'Open Box', 'Refurbished', 'Used - Like New',
                    'Used - Good', 'Used - Acceptable',
                ]),
            ],
            [
                'name' => 'Country of Origin',
                'description' => 'Country where the product is manufactured',
                'options' => $this->toOptions([
                    'USA', 'China', 'South Korea', 'Japan', 'Taiwan',
                    'Vietnam', 'Germany', 'Switzerland', 'India',
                ]),
            ],
            [
                'name' => 'Certification',
                'description' => 'Product certifications',
                'options' => $this->toOptions([
                    'CE', 'FCC', 'RoHS', 'Energy Star',
                    'MIL-STD-810', 'ISO 9001',
                ]),
            ],
            [
                'name' => 'Shipping',
                'description' => 'Shipping options',
                'options' => $this->toOptions([
                    'Free Shipping', 'Standard Shipping',
                    'Express Shipping', 'Same-day Delivery',
                    'International Shipping',
                ]),
            ],
        ];
    }

    /**
     * Turn a flat list of option names into the option payload shape.
     *
     * @param array<int, string> $names
     * @return array<int, array{name: string, description: string}>
     */
    protected function toOptions(array $names): array
    {
        return array_map(fn (string $name) => [
            'name' => $name,
            'description' => $name,
        ], $names);
    }
}
