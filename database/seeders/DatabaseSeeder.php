<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\CompanySetting;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\StockMovement;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexora.com'],
            [
                'name' => 'Administrador Nexora',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'phone' => '+51 987 654 321',
            ]
        );

        User::firstOrCreate(
            ['email' => 'vendedor@nexora.com'],
            [
                'name' => 'Asesor de Ventas',
                'password' => Hash::make('vendedor123'),
                'role' => 'seller',
                'phone' => '+51 987 654 322',
            ]
        );

        // 2. Company Settings
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'NEXORA',
                'tagline' => 'Tecnología sin límites',
                'legal_name' => 'NEXORA TECHNOLOGY S.A.C.',
                'ruc' => '20681254567',
                'email' => 'ventas@nexora.com',
                'phone' => '+51 987 654 321',
                'whatsapp_number' => '+51987654321',
                'whatsapp_default_message' => '¡Hola Nexora! Me gustaría solicitar información sobre sus productos y cotizaciones.',
                'address' => 'Av. Los Héroes 123, Moyobamba, San Martín, Perú',
                'schedule_weekdays' => 'Lun - Sáb: 8:00 a.m. - 7:00 p.m.',
                'schedule_weekends' => 'Dom: 9:00 a.m. - 2:00 p.m.',
                'yape_number' => '987 654 321',
                'yape_holder' => 'NEXORA TECHNOLOGY S.A.C.',
                'yape_qr' => '/images/qr-yape.png',
                'plin_number' => '987 654 321',
                'plin_holder' => 'NEXORA TECHNOLOGY S.A.C.',
                'plin_qr' => '/images/qr-plin.png',
                'bank_name' => 'Banco de Crédito del Perú (BCP)',
                'bank_account' => '191-12345678-0-72',
                'bank_cci' => '002-191-0012345678072-55',
                'bank_holder' => 'NEXORA TECHNOLOGY S.A.C.',
                'facebook_url' => 'https://facebook.com/nexoratech',
                'instagram_url' => 'https://instagram.com/nexora',
                'tiktok_url' => 'https://tiktok.com/@nexora',
                'youtube_url' => 'https://youtube.com/@nexora',
                'enable_whatsapp_button' => true,
                'enable_dark_mode' => true,
            ]
        );

        // 3. Banners
        Banner::truncate();
        Banner::create([
            'title' => 'Tu mundo tecnológico en un solo lugar',
            'subtitle' => 'Laptops, computadoras de alto rendimiento, componentes, accesorios y más con envío garantizado a todo el Perú.',
            'badge' => 'Tecnología que impulsa tus ideas',
            'image' => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?auto=format&fit=crop&w=1400&q=80',
            'link_url' => '/catalogo',
            'button_text' => 'Ver catálogo',
            'type' => 'hero',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Ofertas Especiales de Temporada',
            'subtitle' => 'Hasta 30% de descuento en periféricos gamer y accesorios seleccionados.',
            'badge' => 'OFERTAS TOP',
            'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80',
            'link_url' => '/catalogo?ofertas=1',
            'button_text' => 'Ver ofertas',
            'type' => 'offer_special',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Banner::create([
            'title' => 'Combos Tecnológicos Pro',
            'subtitle' => 'Equípate completo para oficina o streaming al mejor precio.',
            'badge' => 'COMBOS PRO',
            'image' => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=1200&q=80',
            'link_url' => '/catalogo?combos=1',
            'button_text' => 'Ver combos',
            'type' => 'combo_card',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // 4. Brands
        $brandsData = [
            ['name' => 'Lenovo', 'slug' => 'lenovo', 'is_featured' => true],
            ['name' => 'HP', 'slug' => 'hp', 'is_featured' => true],
            ['name' => 'ASUS', 'slug' => 'asus', 'is_featured' => true],
            ['name' => 'Logitech', 'slug' => 'logitech', 'is_featured' => true],
            ['name' => 'MSI', 'slug' => 'msi', 'is_featured' => true],
            ['name' => 'Hikvision', 'slug' => 'hikvision', 'is_featured' => true],
            ['name' => 'Kingston', 'slug' => 'kingston', 'is_featured' => true],
            ['name' => 'Redragon', 'slug' => 'redragon', 'is_featured' => true],
            ['name' => 'TP-Link', 'slug' => 'tp-link', 'is_featured' => true],
            ['name' => 'LG', 'slug' => 'lg', 'is_featured' => true],
        ];

        $brands = [];
        foreach ($brandsData as $b) {
            $brands[$b['slug']] = Brand::firstOrCreate(
                ['slug' => $b['slug']],
                ['name' => $b['name'], 'is_featured' => $b['is_featured'], 'is_active' => true]
            );
        }

        // 5. Categories & Subcategories
        $categoriesData = [
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'icon' => 'laptop',
                'is_featured' => true,
                'subcategories' => ['Laptops Intel Core i3', 'Laptops Intel Core i5', 'Laptops Intel Core i7', 'Laptops Gamer', 'MacBooks'],
            ],
            [
                'name' => 'PC de Escritorio',
                'slug' => 'pc-de-escritorio',
                'icon' => 'desktop',
                'is_featured' => true,
                'subcategories' => ['PC Oficina', 'PC Gamer', 'All in One', 'Workstations'],
            ],
            [
                'name' => 'Monitores',
                'slug' => 'monitores',
                'icon' => 'monitor',
                'is_featured' => true,
                'subcategories' => ['Monitores Gamer 144Hz+', 'Monitores IPS Oficina', 'Monitores Curvos', 'Monitores 4K'],
            ],
            [
                'name' => 'Componentes',
                'slug' => 'componentes',
                'icon' => 'cpu',
                'is_featured' => true,
                'subcategories' => ['Procesadores', 'Tarjetas de Video', 'Placas Madre', 'Fuentes de Poder', 'Memorias RAM'],
            ],
            [
                'name' => 'Almacenamiento',
                'slug' => 'almacenamiento',
                'icon' => 'hard-drive',
                'is_featured' => true,
                'subcategories' => ['SSD NVMe M.2', 'SSD Sata 2.5', 'Discos Duros Externos', 'Memorias USB'],
            ],
            [
                'name' => 'Accesorios',
                'slug' => 'accesorios',
                'icon' => 'headphones',
                'is_featured' => true,
                'subcategories' => ['Teclados Mecánicos', 'Mouse Gamer', 'Auriculares con Micrófono', 'Mousepads', 'Hubs USB'],
            ],
            [
                'name' => 'Cámaras',
                'slug' => 'camaras',
                'icon' => 'camera',
                'is_featured' => true,
                'subcategories' => ['Webcams Full HD', 'Cámaras para Streaming'],
            ],
            [
                'name' => 'Impresoras',
                'slug' => 'impresoras',
                'icon' => 'printer',
                'is_featured' => true,
                'subcategories' => ['Impresoras Multifuncionales', 'Tinta Continua', 'Impresoras Térmicas'],
            ],
            [
                'name' => 'Redes',
                'slug' => 'redes',
                'icon' => 'wifi',
                'is_featured' => true,
                'subcategories' => ['Routers Wi-Fi 6', 'Repetidores de Señal', 'Switches Gigabit', 'Cables de Red'],
            ],
            [
                'name' => 'Seguridad',
                'slug' => 'seguridad',
                'icon' => 'shield',
                'is_featured' => true,
                'subcategories' => ['Cámaras IP Wi-Fi', 'Kits DVR CCTV', 'Cámaras PTZ'],
            ],
        ];

        $categories = [];
        $subcategories = [];

        foreach ($categoriesData as $idx => $cat) {
            $createdCategory = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'is_featured' => $cat['is_featured'],
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                    'description' => "Equipos y soluciones en {$cat['name']} con garantía oficial.",
                ]
            );
            $categories[$cat['slug']] = $createdCategory;

            foreach ($cat['subcategories'] as $subName) {
                $subSlug = Str::slug($subName);
                $subcategories[$subSlug] = Subcategory::firstOrCreate(
                    ['category_id' => $createdCategory->id, 'slug' => $subSlug],
                    ['name' => $subName, 'is_active' => true]
                );
            }
        }

        // 6. Real Products with rich specs and images
        $productsData = [
            [
                'name' => 'Laptop Lenovo IdeaPad 3',
                'slug' => 'laptop-lenovo-ideapad-3',
                'sku' => 'LAP-LEN-001',
                'model' => 'IdeaPad 3 15IAU7',
                'brand_slug' => 'lenovo',
                'category_slug' => 'laptops',
                'subcategory_slug' => 'laptops-intel-core-i5',
                'price' => 2499.00,
                'original_price' => 2999.00,
                'offer_price' => 2499.00,
                'min_price' => 2350.00,
                'stock' => 12,
                'min_stock' => 3,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => true,
                'rating' => 4.90,
                'reviews_count' => 24,
                'main_image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Laptop ideal para productividad, estudio y trabajo exigente. Potente procesador Intel Core i5 con pantalla FHD antirreflejo.',
                'description' => "La Lenovo IdeaPad 3 es una laptop potente, ligera y elegante diseñada para el trabajo diario, conferencias virtuales y multitarea fluida.\nCuenta con pantalla de 15.6 pulgadas Full HD con bordes delgados, teclado en español con teclado numérico y sistema de audio Dolby Audio para llamadas y multimedia cristalinos.\n\nIncluye garantía de 12 meses de fábrica y soporte técnico autorizado.",
                'specs' => [
                    'Pantalla' => '15.6" Full HD (1920x1080) Antirreflejo IPS',
                    'Procesador' => 'Intel Core i5-1235U (hasta 4.40 GHz)',
                    'Memoria RAM' => '8 GB DDR4 3200MHz (expandible)',
                    'Almacenamiento' => '512 GB SSD M.2 NVMe PCIe',
                    'Gráficos' => 'Intel Iris Xe Graphics',
                    'Conectividad' => 'Wi-Fi 6, Bluetooth 5.1, USB-C 3.2, HDMI 1.4b',
                    'Batería' => 'Hasta 7.5 horas de autonomía',
                    'Sistema Operativo' => 'Windows 11 Home 64-bit original',
                ],
            ],
            [
                'name' => 'Mouse Logitech G502 Hero High Performance',
                'slug' => 'mouse-logitech-g502-hero',
                'sku' => 'MOU-LOG-002',
                'model' => 'G502 HERO',
                'brand_slug' => 'logitech',
                'category_slug' => 'accesorios',
                'subcategory_slug' => 'mouse-gamer',
                'price' => 129.00,
                'original_price' => 179.00,
                'offer_price' => 129.00,
                'min_price' => 110.00,
                'stock' => 25,
                'min_stock' => 5,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => false,
                'rating' => 5.00,
                'reviews_count' => 48,
                'main_image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Sensor HERO 25K con precisión milimétrica, 11 botones programables y sistema de pesas ajustables.',
                'description' => 'El mouse gamer más aclamado del mundo. Equipado con el sensor HERO 25K para una precisión sin suavizado ni aceleración en todo el rango de DPI.',
                'specs' => [
                    'Sensor' => 'HERO 25K (100 - 25,600 DPI)',
                    'Botones' => '11 botones totalmente programables',
                    'Iluminación' => 'LIGHTSYNC RGB configurable',
                    'Peso' => '121 g + 5 pesas de 3.6 g ajustables',
                    'Cable' => 'Trenzado resistente de 2.1 metros',
                ],
            ],
            [
                'name' => 'Teclado Mecánico RGB Redragon Kumara K552',
                'slug' => 'teclado-mecanico-redragon-kumara-k552',
                'sku' => 'TEC-RED-003',
                'model' => 'Kumara K552 RGB',
                'brand_slug' => 'redragon',
                'category_slug' => 'accesorios',
                'subcategory_slug' => 'teclados-mecanicos',
                'price' => 249.00,
                'original_price' => 299.00,
                'offer_price' => 249.00,
                'min_price' => 210.00,
                'stock' => 18,
                'min_stock' => 4,
                'is_featured' => true,
                'is_offer' => false,
                'is_new' => true,
                'rating' => 4.80,
                'reviews_count' => 31,
                'main_image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Teclado mecánico compacto TKL con switches Outemu Red y chasis de aluminio aeroespacial reforzado.',
                'description' => 'Diseño compacto Tenkeyless que ahorra espacio en tu escritorio para movimientos más amplios de mouse. Retroiluminación RGB de hasta 18 modos dinámicos.',
                'specs' => [
                    'Tipo de Switch' => 'Outemu Red Lineal Silencioso',
                    'Formato' => 'TKL (Tenkeyless 87 teclas)',
                    'Construcción' => 'Aluminio y ABS reforzado',
                    'Anti-Ghosting' => '100% teclas con N-Key Rollover',
                ],
            ],
            [
                'name' => 'Cámara de Seguridad Hikvision ColorVu 2MP',
                'slug' => 'camara-seguridad-hikvision-colorvu-2mp',
                'sku' => 'CAM-HIK-004',
                'model' => 'DS-2CE10DF0T-F',
                'brand_slug' => 'hikvision',
                'category_slug' => 'seguridad',
                'subcategory_slug' => 'camaras-ip-wi-fi',
                'price' => 199.00,
                'original_price' => 230.00,
                'offer_price' => 199.00,
                'min_price' => 170.00,
                'stock' => 14,
                'min_stock' => 3,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => false,
                'rating' => 4.85,
                'reviews_count' => 19,
                'main_image' => 'https://images.unsplash.com/photo-1557597774-9d273605dfa9?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1557597774-9d273605dfa9?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Imágenes a color las 24 horas del día incluso en oscuridad total gracias a su lente de apertura F1.0 y luz blanca cálida.',
                'description' => 'La tecnología ColorVu de Hikvision proporciona imágenes vívidas en color las 24 horas del día con sensores avanzados y aperturas superlativas.',
                'specs' => [
                    'Resolución' => '2 Megapíxeles Full HD (1920x1080)',
                    'Tecnología' => 'ColorVu a color 24/7',
                    'Protección' => 'IP67 resistente a intemperie y lluvia',
                    'Alcance de Luz' => 'Hasta 20 metros de luz blanca suave',
                ],
            ],
            [
                'name' => 'Monitor 24" LG UltraGear 144Hz IPS FHD',
                'slug' => 'monitor-24-lg-ultragear-144hz',
                'sku' => 'MON-LG-005',
                'model' => '24GN600-B',
                'brand_slug' => 'lg',
                'category_slug' => 'monitores',
                'subcategory_slug' => 'monitores-gamer-144hz',
                'price' => 599.00,
                'original_price' => 699.00,
                'offer_price' => 599.00,
                'min_price' => 550.00,
                'stock' => 9,
                'min_stock' => 2,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => true,
                'rating' => 4.95,
                'reviews_count' => 28,
                'main_image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Panel IPS de 1ms MBR, 144Hz de tasa de refresco y compatibilidad AMD FreeSync Premium para partidas hiper fluidas.',
                'description' => 'El monitor gamer LG UltraGear redefine la fluidez en tus videojuegos competitivos con colores vibrantes sRGB 99% y HDR10.',
                'specs' => [
                    'Pantalla' => '24 pulgadas IPS FHD (1920 x 1080)',
                    'Frecuencia' => '144 Hz',
                    'Tiempo de Respuesta' => '1ms MBR',
                    'Sincronización' => 'AMD FreeSync Premium',
                    'Puertos' => '2x HDMI, 1x DisplayPort 1.4, Salida Audio',
                ],
            ],
            [
                'name' => 'Memoria RAM Kingston Fury Beast 16GB DDR4 3200MHz',
                'slug' => 'ram-kingston-fury-beast-16gb-ddr4',
                'sku' => 'RAM-KIN-006',
                'model' => 'KF432C16BB/16',
                'brand_slug' => 'kingston',
                'category_slug' => 'componentes',
                'subcategory_slug' => 'memorias-ram',
                'price' => 189.00,
                'original_price' => 220.00,
                'offer_price' => 189.00,
                'min_price' => 170.00,
                'stock' => 30,
                'min_stock' => 5,
                'is_featured' => false,
                'is_offer' => true,
                'is_new' => false,
                'rating' => 4.90,
                'reviews_count' => 41,
                'main_image' => 'https://images.unsplash.com/photo-1562976540-1502c2145186?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1562976540-1502c2145186?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Disipador de calor de bajo perfil negro, compatibilidad Intel XMP y AMD Ryzen para máximo rendimiento.',
                'description' => 'Mejora radicalmente el rendimiento multitarea y la tasa de cuadros en juegos con la memoria Kingston Fury Beast.',
                'specs' => [
                    'Capacidad' => '16 GB (1 x 16GB)',
                    'Velocidad' => 'DDR4 3200 MT/s',
                    'Latencia' => 'CL16',
                    'Voltaje' => '1.35V',
                ],
            ],
            [
                'name' => 'SSD Kingston NV2 1TB PCIe 4.0 NVMe M.2',
                'slug' => 'ssd-kingston-nv2-1tb-nvme',
                'sku' => 'SSD-KIN-007',
                'model' => 'SNV2S/1000G',
                'brand_slug' => 'kingston',
                'category_slug' => 'almacenamiento',
                'subcategory_slug' => 'ssd-nvme-m2',
                'price' => 259.00,
                'original_price' => 310.00,
                'offer_price' => 259.00,
                'min_price' => 239.00,
                'stock' => 22,
                'min_stock' => 4,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => true,
                'rating' => 4.90,
                'reviews_count' => 35,
                'main_image' => 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Velocidades de lectura de hasta 3,500 MB/s para cargas de sistema y juegos instantáneas.',
                'description' => 'Solución de almacenamiento sustancial de próxima generación impulsada por un controlador NVMe Gen 4x4.',
                'specs' => [
                    'Capacidad' => '1 TB',
                    'Factor de Forma' => 'M.2 2280',
                    'Interfaz' => 'PCIe 4.0 x4 NVMe',
                    'Lectura Secuencial' => 'Hasta 3,500 MB/s',
                    'Escritura Secuencial' => 'Hasta 2,100 MB/s',
                ],
            ],
            [
                'name' => 'Tarjeta de Video ASUS Dual GeForce RTX 4060 EVO OC 8GB',
                'slug' => 'gpu-asus-dual-geforce-rtx-4060-8gb',
                'sku' => 'GPU-ASU-008',
                'model' => 'DUAL-RTX4060-O8G-EVO',
                'brand_slug' => 'asus',
                'category_slug' => 'componentes',
                'subcategory_slug' => 'tarjetas-de-video',
                'price' => 1499.00,
                'original_price' => 1650.00,
                'offer_price' => 1499.00,
                'min_price' => 1420.00,
                'stock' => 7,
                'min_stock' => 2,
                'is_featured' => true,
                'is_offer' => false,
                'is_new' => true,
                'rating' => 5.00,
                'reviews_count' => 15,
                'main_image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Arquitectura Ada Lovelace con DLSS 3, trazado de rayos de 3ra gen y doble ventilador Axial-tech.',
                'description' => 'La tarjeta ideal para jugar a 1080p y 1440p con los ajustes más altos y renderizado asistido por IA acelerado.',
                'specs' => [
                    'Memoria de Video' => '8GB GDDR6 (17 Gbps)',
                    'Interfaz de Memoria' => '128-bit',
                    'Núcleos CUDA' => '3072',
                    'Conectores' => '1x HDMI 2.1a, 3x DisplayPort 1.4a',
                    'Fuente Recomendada' => '550W',
                ],
            ],
            [
                'name' => 'Router TP-Link Archer AX53 Wi-Fi 6 AX3000 Gigabit',
                'slug' => 'router-tp-link-archer-ax53-wifi6',
                'sku' => 'NET-TPL-009',
                'model' => 'Archer AX53',
                'brand_slug' => 'tp-link',
                'category_slug' => 'redes',
                'subcategory_slug' => 'routers-wi-fi-6',
                'price' => 289.00,
                'original_price' => 340.00,
                'offer_price' => 289.00,
                'min_price' => 260.00,
                'stock' => 15,
                'min_stock' => 3,
                'is_featured' => false,
                'is_offer' => true,
                'is_new' => true,
                'rating' => 4.85,
                'reviews_count' => 17,
                'main_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Velocidades de hasta 3 Gbps (2402 Mbps en 5GHz y 574 Mbps en 2.4GHz), 4 antenas de alta ganancia y seguridad HomeShield.',
                'description' => 'Dile adiós al lag y a las zonas sin cobertura en tu casa u oficina con la tecnología Wi-Fi 6 OFDMA y MU-MIMO.',
                'specs' => [
                    'Estándar' => 'Wi-Fi 6 (IEEE 802.11ax/ac/n/a/b/g)',
                    'Velocidad Wi-Fi' => 'AX3000 (2402 Mbps + 574 Mbps)',
                    'Puertos Ethernet' => '1x WAN Gigabit + 4x LAN Gigabit',
                    'Seguridad' => 'WPA3, TP-Link HomeShield Antivirus',
                ],
            ],
            [
                'name' => 'PC Gamer Nexora Cyberpunk Intel i5 + RTX 4060 16GB RAM',
                'slug' => 'pc-gamer-nexora-cyberpunk-i5-rtx4060',
                'sku' => 'PC-NEX-010',
                'model' => 'Nexora Cyber Beast V2',
                'brand_slug' => 'asus',
                'category_slug' => 'pc-de-escritorio',
                'subcategory_slug' => 'pc-gamer',
                'price' => 3899.00,
                'original_price' => 4299.00,
                'offer_price' => 3899.00,
                'min_price' => 3700.00,
                'stock' => 5,
                'min_stock' => 1,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => true,
                'rating' => 5.00,
                'reviews_count' => 11,
                'main_image' => 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Computadora gamer ensamblada y configurada lista para jugar y transmitir en Twitch. Incluye Windows 11 Pro activado y optimizado.',
                'description' => 'Armada con componentes de primeras marcas, pasta térmica de alta conductividad y refrigeración líquida ARGB.',
                'specs' => [
                    'Procesador' => 'Intel Core i5-13400F 10 núcleos / 16 hilos',
                    'Tarjeta Gráfica' => 'GeForce RTX 4060 8GB GDDR6 Dual Fan',
                    'Placa Madre' => 'ASUS PRIME B760M-A Wi-Fi',
                    'Memoria RAM' => '16GB (2x8GB) DDR4 3200MHz RGB',
                    'Almacenamiento' => '1TB SSD NVMe M.2 PCIe 4.0',
                    'Fuente' => '650W 80 Plus Bronze Certificada',
                    'Gabinete' => 'Vidrio Templado con 4 Fans ARGB',
                ],
            ],
            [
                'name' => 'Impresora Multifuncional HP Smart Tank 580 Wi-Fi',
                'slug' => 'impresora-multifuncional-hp-smart-tank-580',
                'sku' => 'IMP-HP-011',
                'model' => 'Smart Tank 580',
                'brand_slug' => 'hp',
                'category_slug' => 'impresoras',
                'subcategory_slug' => 'tinta-continua',
                'price' => 649.00,
                'original_price' => 749.00,
                'offer_price' => 649.00,
                'min_price' => 610.00,
                'stock' => 11,
                'min_stock' => 2,
                'is_featured' => false,
                'is_offer' => false,
                'is_new' => true,
                'rating' => 4.75,
                'reviews_count' => 16,
                'main_image' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Imprime, escanea y copia sin cartuchos. Incluye tinta para hasta 12,000 páginas en negro o 6,000 a color.',
                'description' => 'Conexión Wi-Fi autorreparable y configuración súper simple desde tu celular con la aplicación HP Smart.',
                'specs' => [
                    'Funciones' => 'Impresión, Copia, Escáner',
                    'Velocidad de Impresión' => 'Hasta 12 ppm negro / 5 ppm color',
                    'Conectividad' => 'Wi-Fi 2.4/5GHz, Wi-Fi Direct, USB 2.0',
                    'Rendimiento de Tinta' => 'Hasta 12,000 págs negro / 6,000 págs color',
                ],
            ],
            [
                'name' => 'Auriculares Gamer Redragon Zeus X RGB 7.1',
                'slug' => 'auriculares-gamer-redragon-zeus-x-rgb',
                'sku' => 'AUD-RED-012',
                'model' => 'Zeus X H510-RGB',
                'brand_slug' => 'redragon',
                'category_slug' => 'accesorios',
                'subcategory_slug' => 'auriculares-con-microfono',
                'price' => 199.00,
                'original_price' => 249.00,
                'offer_price' => 199.00,
                'min_price' => 175.00,
                'stock' => 16,
                'min_stock' => 3,
                'is_featured' => true,
                'is_offer' => true,
                'is_new' => false,
                'rating' => 4.90,
                'reviews_count' => 22,
                'main_image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Sonido envolvente 7.1 virtual, almohadillas con memoria de forma ultra suaves y micrófono con cancelación pasiva de ruido.',
                'description' => 'Sonido inmersivo para detectar los pasos de tus enemigos antes de verlos con drivers de 53 mm de neodimio.',
                'specs' => [
                    'Altavoces' => '53 mm con imanes de neodimio',
                    'Respuesta de Frecuencia' => '20Hz - 20kHz',
                    'Micrófono' => 'Unidireccional con filtro antipop',
                    'Conexión' => 'USB Plug & Play con control en cable',
                ],
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $prod) {
            $cat = $categories[$prod['category_slug']] ?? Category::first();
            $sub = $subcategories[$prod['subcategory_slug']] ?? null;
            $brand = $brands[$prod['brand_slug']] ?? Brand::first();

            $p = Product::updateOrCreate(
                ['sku' => $prod['sku']],
                [
                    'category_id' => $cat->id,
                    'subcategory_id' => $sub ? $sub->id : null,
                    'brand_id' => $brand->id,
                    'name' => $prod['name'],
                    'slug' => $prod['slug'],
                    'model' => $prod['model'],
                    'short_description' => $prod['short_description'],
                    'description' => $prod['description'],
                    'technical_specs' => $prod['specs'],
                    'price' => $prod['price'],
                    'original_price' => $prod['original_price'],
                    'offer_price' => $prod['offer_price'],
                    'min_price' => $prod['min_price'],
                    'stock' => $prod['stock'],
                    'min_stock' => $prod['min_stock'],
                    'main_image' => $prod['main_image'],
                    'is_featured' => $prod['is_featured'],
                    'is_offer' => $prod['is_offer'],
                    'is_new' => $prod['is_new'],
                    'is_active' => true,
                    'rating' => $prod['rating'],
                    'reviews_count' => $prod['reviews_count'],
                    'views_count' => rand(150, 2400),
                ]
            );

            $createdProducts[$prod['sku']] = $p;

            // Gallery images
            ProductImage::where('product_id', $p->id)->delete();
            foreach ($prod['gallery'] as $gIdx => $gUrl) {
                ProductImage::create([
                    'product_id' => $p->id,
                    'image_path' => $gUrl,
                    'is_primary' => $gIdx === 0,
                    'sort_order' => $gIdx,
                ]);
            }
        }

        // 7. Combos
        $combo1 = Combo::firstOrCreate(
            ['slug' => 'combo-productividad-estudio'],
            [
                'name' => 'Combo Productividad & Estudio Pro',
                'description' => 'Laptop Lenovo IdeaPad 3 + Mouse Logitech G502 con precio promocional.',
                'price' => 2599.00,
                'original_price' => 2748.00,
                'image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        if (isset($createdProducts['LAP-LEN-001']) && isset($createdProducts['MOU-LOG-002'])) {
            ComboItem::firstOrCreate([
                'combo_id' => $combo1->id,
                'product_id' => $createdProducts['LAP-LEN-001']->id,
            ], ['quantity' => 1]);

            ComboItem::firstOrCreate([
                'combo_id' => $combo1->id,
                'product_id' => $createdProducts['MOU-LOG-002']->id,
            ], ['quantity' => 1]);
        }

        $combo2 = Combo::firstOrCreate(
            ['slug' => 'combo-gamer-perifericos'],
            [
                'name' => 'Combo Gamer Completo (Teclado + Mouse + Headset)',
                'description' => 'Teclado Redragon Kumara + Mouse Logitech G502 + Auriculares Zeus X RGB.',
                'price' => 499.00,
                'original_price' => 577.00,
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        if (isset($createdProducts['TEC-RED-003']) && isset($createdProducts['MOU-LOG-002']) && isset($createdProducts['AUD-RED-012'])) {
            ComboItem::firstOrCreate(['combo_id' => $combo2->id, 'product_id' => $createdProducts['TEC-RED-003']->id], ['quantity' => 1]);
            ComboItem::firstOrCreate(['combo_id' => $combo2->id, 'product_id' => $createdProducts['MOU-LOG-002']->id], ['quantity' => 1]);
            ComboItem::firstOrCreate(['combo_id' => $combo2->id, 'product_id' => $createdProducts['AUD-RED-012']->id], ['quantity' => 1]);
        }

        // 8. Coupons
        Coupon::firstOrCreate(
            ['code' => 'NEXORA10'],
            [
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_amount' => 500.00,
                'max_uses' => 100,
                'used_count' => 14,
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'BIENVENIDA20'],
            [
                'type' => 'fixed',
                'value' => 20.00,
                'min_order_amount' => 150.00,
                'max_uses' => 200,
                'used_count' => 38,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ]
        );

        // 9. Orders with different statuses (Pending, Pending Review with proof, Paid with stock deduction, Rejected)
        if (Order::count() === 0 && isset($createdProducts['LAP-LEN-001'])) {
            // Order 1: Pending Review (User uploaded screenshot via Yape)
            $order1 = Order::create([
                'order_number' => 'PED-2026-00125',
                'customer_name' => 'Juan Pérez Quispe',
                'customer_email' => 'juan.perez@gmail.com',
                'customer_phone' => '+51 976 543 210',
                'customer_document_type' => 'DNI',
                'customer_document_number' => '47891234',
                'department' => 'San Martín',
                'province' => 'Moyobamba',
                'district' => 'Moyobamba',
                'address' => 'Jr. San Martín 450 - Centro',
                'reference' => 'Frente a la Plaza de Armas',
                'shipping_type' => 'delivery',
                'notes' => 'Por favor llamar antes de entregar.',
                'subtotal' => 2499.00,
                'discount' => 0.00,
                'igv' => 449.82,
                'total' => 2499.00,
                'payment_method' => 'yape',
                'payment_status' => 'pending_review',
                'order_status' => 'pending',
                'payment_proof' => '/images/sample-yape-proof.jpg',
                'proof_uploaded_at' => now()->subHours(2),
                'admin_notes' => 'Comprobante Yape recibido. Verificando abono en cuenta comercial.',
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $createdProducts['LAP-LEN-001']->id,
                'product_name' => $createdProducts['LAP-LEN-001']->name,
                'product_sku' => $createdProducts['LAP-LEN-001']->sku,
                'unit_price' => 2499.00,
                'quantity' => 1,
                'subtotal' => 2499.00,
            ]);

            // Order 2: Confirmed Paid (Stock WAS deducted and registered in stock_movements)
            $order2 = Order::create([
                'order_number' => 'PED-2026-00124',
                'customer_name' => 'María Torres Ramos',
                'customer_email' => 'mtorres@gmail.com',
                'customer_phone' => '+51 981 234 567',
                'customer_document_type' => 'DNI',
                'customer_document_number' => '72345678',
                'department' => 'San Martín',
                'province' => 'Tarapoto',
                'district' => 'Morales',
                'address' => 'Av. Circunvalación 890',
                'reference' => 'A espaldas del colegio',
                'shipping_type' => 'delivery',
                'subtotal' => 378.00,
                'discount' => 0.00,
                'igv' => 68.04,
                'total' => 378.00,
                'payment_method' => 'plin',
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'payment_proof' => '/images/sample-plin-proof.jpg',
                'proof_uploaded_at' => now()->subDay(),
                'paid_at' => now()->subDay()->addMinutes(15),
                'verified_by_user_id' => $admin->id,
                'admin_notes' => 'Abono Plin validado con éxito en banco.',
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $createdProducts['MOU-LOG-002']->id,
                'product_name' => $createdProducts['MOU-LOG-002']->name,
                'product_sku' => $createdProducts['MOU-LOG-002']->sku,
                'unit_price' => 129.00,
                'quantity' => 1,
                'subtotal' => 129.00,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $createdProducts['TEC-RED-003']->id,
                'product_name' => $createdProducts['TEC-RED-003']->name,
                'product_sku' => $createdProducts['TEC-RED-003']->sku,
                'unit_price' => 249.00,
                'quantity' => 1,
                'subtotal' => 249.00,
            ]);

            StockMovement::create([
                'product_id' => $createdProducts['MOU-LOG-002']->id,
                'order_id' => $order2->id,
                'type' => 'sale',
                'quantity' => -1,
                'stock_before' => 26,
                'stock_after' => 25,
                'reason' => 'Venta confirmada Pedido PED-2026-00124',
                'user_id' => $admin->id,
            ]);

            StockMovement::create([
                'product_id' => $createdProducts['TEC-RED-003']->id,
                'order_id' => $order2->id,
                'type' => 'sale',
                'quantity' => -1,
                'stock_before' => 19,
                'stock_after' => 18,
                'reason' => 'Venta confirmada Pedido PED-2026-00124',
                'user_id' => $admin->id,
            ]);

            // Order 3: Pending payment (no proof yet)
            $order3 = Order::create([
                'order_number' => 'PED-2026-00123',
                'customer_name' => 'Carlos Mendoza Díaz',
                'customer_email' => 'cmendoza@empresa.com',
                'customer_phone' => '+51 952 789 123',
                'customer_document_type' => 'RUC',
                'customer_document_number' => '20556677881',
                'department' => 'Lima',
                'province' => 'Lima',
                'district' => 'Miraflores',
                'address' => 'Av. Benavides 1540 Of. 402',
                'shipping_type' => 'delivery',
                'subtotal' => 3899.00,
                'discount' => 0.00,
                'igv' => 701.82,
                'total' => 3899.00,
                'payment_method' => 'bcp',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order3->id,
                'product_id' => $createdProducts['PC-NEX-010']->id,
                'product_name' => $createdProducts['PC-NEX-010']->name,
                'product_sku' => $createdProducts['PC-NEX-010']->sku,
                'unit_price' => 3899.00,
                'quantity' => 1,
                'subtotal' => 3899.00,
            ]);
        }

        // 10. Quotes (COT-000001, COT-000002)
        if (Quote::count() === 0 && isset($createdProducts['LAP-LEN-001'])) {
            $quote1 = Quote::create([
                'quote_number' => 'COT-000001',
                'customer_name' => 'Ing. Roberto Silva',
                'customer_email' => 'rsilva@consultores.pe',
                'customer_phone' => '+51 942 112 233',
                'company_name' => 'SILVA ASOCIADOS S.A.C.',
                'company_ruc' => '20543219876',
                'message' => 'Requerimos cotización formal de 5 laptops Lenovo para nuestra área de desarrollo y diseño.',
                'status' => 'sent',
                'subtotal' => 12495.00,
                'total' => 12495.00,
                'valid_until' => now()->addDays(15),
                'admin_notes' => 'Cotización enviada con descuento corporativo del 5%.',
            ]);

            QuoteItem::create([
                'quote_id' => $quote1->id,
                'product_id' => $createdProducts['LAP-LEN-001']->id,
                'product_name' => $createdProducts['LAP-LEN-001']->name,
                'product_sku' => $createdProducts['LAP-LEN-001']->sku,
                'quantity' => 5,
                'unit_price' => 2499.00,
                'subtotal' => 12495.00,
            ]);

            $quote2 = Quote::create([
                'quote_number' => 'COT-000002',
                'customer_name' => 'Karina Flores',
                'customer_email' => 'kflores@outlook.com',
                'customer_phone' => '+51 988 554 433',
                'message' => 'Quisiera saber disponibilidad y costo de envío a Rioja de cámaras de seguridad Hikvision.',
                'status' => 'pending',
                'subtotal' => 796.00,
                'total' => 796.00,
                'valid_until' => now()->addDays(7),
            ]);

            QuoteItem::create([
                'quote_id' => $quote2->id,
                'product_id' => $createdProducts['CAM-HIK-004']->id,
                'product_name' => $createdProducts['CAM-HIK-004']->name,
                'product_sku' => $createdProducts['CAM-HIK-004']->sku,
                'quantity' => 4,
                'unit_price' => 199.00,
                'subtotal' => 796.00,
            ]);
        }
    }
}
