<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $products = [

            // ── ENTRADAS ─────────────────────────────────────────────────────────────
            ['name' => 'Papas Fritas simples',                              'type' => 'Comida', 'category' => 'Entrada',      'price' => 11000, 'stock' =>  50],
            ['name' => 'Papas Fritas gratinadas (queso tybo)',              'type' => 'Comida', 'category' => 'Entrada',      'price' => 14000, 'stock' =>  50],
            ['name' => 'Papas Fritas Los Troncos (cheddar, panceta, verdeo)','type'=> 'Comida', 'category' => 'Entrada',      'price' => 16000, 'stock' =>  50],
            ['name' => 'Mandioca Frita',                                    'type' => 'Comida', 'category' => 'Entrada',      'price' => 10000, 'stock' =>  50],
            ['name' => 'Bastones de Muzzarella',                            'type' => 'Comida', 'category' => 'Entrada',      'price' => 15500, 'stock' =>  40],
            ['name' => 'Rabas',                                             'type' => 'Comida', 'category' => 'Entrada',      'price' => 18000, 'stock' =>  30],
            ['name' => 'Ensalada Cesar',                                    'type' => 'Comida', 'category' => 'Entrada',      'price' => 13000, 'stock' =>  40],

            // ── PICADAS ───────────────────────────────────────────────────────────────
            ['name' => 'Picada para 2 personas',                            'type' => 'Comida', 'category' => 'Picada',       'price' => 27000, 'stock' =>  20],
            ['name' => 'Picada para 4 personas',                            'type' => 'Comida', 'category' => 'Picada',       'price' => 39000, 'stock' =>  20],
            ['name' => 'Picada para 6 personas',                            'type' => 'Comida', 'category' => 'Picada',       'price' => 49000, 'stock' =>  20],

            // ── EMPANADAS ─────────────────────────────────────────────────────────────
            ['name' => 'Empanada Jamon y Queso',                            'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Carne Molida',                             'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Cebolla y Queso',                          'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Pollo',                                    'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Verdura',                                  'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Humita',                                   'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Capresse',                                 'type' => 'Comida', 'category' => 'Empanada',     'price' =>  1800, 'stock' => 200],
            ['name' => 'Empanada Roquefort y Nueces',                       'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2000, 'stock' => 200],
            ['name' => 'Empanada Cuatro Quesos',                            'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2000, 'stock' => 200],
            ['name' => 'Empanada Jamon Crudo y Mozza',                      'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2000, 'stock' => 200],
            ['name' => 'Empanada Arabe',                                    'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2000, 'stock' => 200],
            ['name' => 'Empanada Panceta',                                  'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2000, 'stock' => 200],
            ['name' => 'Empanada Carne a Cuchillo',                         'type' => 'Comida', 'category' => 'Empanada',     'price' =>  2200, 'stock' => 200],

            // ── SANDWICHES ────────────────────────────────────────────────────────────
            ['name' => 'Loro Negro',                                        'type' => 'Comida', 'category' => 'Sandwich',     'price' => 15000, 'stock' =>  30],
            ['name' => 'Guatembu Blanco',                                   'type' => 'Comida', 'category' => 'Sandwich',     'price' => 13000, 'stock' =>  30],
            ['name' => 'Angelim Rojo',                                      'type' => 'Comida', 'category' => 'Sandwich',     'price' => 16000, 'stock' =>  30],
            ['name' => 'Los Troncos Sandwich',                              'type' => 'Comida', 'category' => 'Sandwich',     'price' => 16000, 'stock' =>  30],
            ['name' => 'Ibira',                                             'type' => 'Comida', 'category' => 'Sandwich',     'price' => 15000, 'stock' =>  30],
            ['name' => 'Guayubira',                                         'type' => 'Comida', 'category' => 'Sandwich',     'price' => 13000, 'stock' =>  30],
            ['name' => 'Mediterraneo',                                      'type' => 'Comida', 'category' => 'Sandwich',     'price' => 13000, 'stock' =>  30],
            ['name' => 'Tostado',                                           'type' => 'Comida', 'category' => 'Sandwich',     'price' =>  8000, 'stock' =>  50],
            ['name' => 'Tostado gratinado',                                 'type' => 'Comida', 'category' => 'Sandwich',     'price' => 10000, 'stock' =>  50],
            ['name' => 'Veggie Los Troncos',                                'type' => 'Comida', 'category' => 'Sandwich',     'price' =>  8500, 'stock' =>  40],

            // ── HAMBURGUESAS ──────────────────────────────────────────────────────────
            ['name' => 'Lapacho Negro',                                     'type' => 'Comida', 'category' => 'Hamburguesa',  'price' =>  9500, 'stock' =>  50],
            ['name' => 'Cedro Misionero',                                   'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 10500, 'stock' =>  50],
            ['name' => 'Araucaria',                                         'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 11500, 'stock' =>  50],
            ['name' => 'Cancharana',                                        'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 11000, 'stock' =>  50],
            ['name' => 'Maria Preta',                                       'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 12000, 'stock' =>  50],
            ['name' => 'Laurel Negro',                                      'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 12000, 'stock' =>  50],
            ['name' => 'Tacuarembo',                                        'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 13000, 'stock' =>  50],
            ['name' => 'Chicken Burger',                                    'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 10500, 'stock' =>  50],
            ['name' => 'Kid Burger',                                        'type' => 'Comida', 'category' => 'Hamburguesa',  'price' =>  8000, 'stock' =>  50],
            ['name' => 'Cajita Feliz Los Troncos ONE',                      'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 25000, 'stock' =>  20],
            ['name' => 'Cajita Feliz Los Troncos TWO',                      'type' => 'Comida', 'category' => 'Hamburguesa',  'price' => 34000, 'stock' =>  20],

            // ── PIZZAS (enteras) ──────────────────────────────────────────────────────
            ['name' => 'Pizza Timbo',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' => 14000, 'stock' =>  40],
            ['name' => 'Pizza Incienso',                                    'type' => 'Comida', 'category' => 'Pizza',        'price' => 14000, 'stock' =>  40],
            ['name' => 'Pizza Mora Blanca',                                 'type' => 'Comida', 'category' => 'Pizza',        'price' => 15000, 'stock' =>  40],
            ['name' => 'Pizza Carandai',                                    'type' => 'Comida', 'category' => 'Pizza',        'price' => 15000, 'stock' =>  40],
            ['name' => 'Pizza Palo Rosa',                                   'type' => 'Comida', 'category' => 'Pizza',        'price' => 16000, 'stock' =>  40],
            ['name' => 'Pizza Palmito',                                     'type' => 'Comida', 'category' => 'Pizza',        'price' => 16000, 'stock' =>  40],
            ['name' => 'Pizza Petiribo',                                    'type' => 'Comida', 'category' => 'Pizza',        'price' => 16000, 'stock' =>  40],
            ['name' => 'Pizza Pindo',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' => 16000, 'stock' =>  40],
            ['name' => 'Pizza Anchico',                                     'type' => 'Comida', 'category' => 'Pizza',        'price' => 18000, 'stock' =>  40],
            ['name' => 'Pizza 4 Quesos',                                    'type' => 'Comida', 'category' => 'Pizza',        'price' => 18000, 'stock' =>  40],
            ['name' => 'Pizza Anana',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' => 18000, 'stock' =>  40],
            ['name' => 'Pizza Urunday',                                     'type' => 'Comida', 'category' => 'Pizza',        'price' => 18000, 'stock' =>  40],
            ['name' => 'Pizza Cana Fistola',                                'type' => 'Comida', 'category' => 'Pizza',        'price' => 19000, 'stock' =>  30],
            ['name' => 'Pizza Champinones',                                 'type' => 'Comida', 'category' => 'Pizza',        'price' => 19000, 'stock' =>  30],
            ['name' => 'Pizza Los Troncos',                                 'type' => 'Comida', 'category' => 'Pizza',        'price' => 19000, 'stock' =>  30],
            ['name' => 'Pizza Kurupi',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' => 20000, 'stock' =>  30],
            ['name' => 'Pizza Araticu',                                     'type' => 'Comida', 'category' => 'Pizza',        'price' => 22000, 'stock' =>  30],
            ['name' => 'Hambur Pizza',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' => 27000, 'stock' =>  20],

            // ── PIZZAS MITADES (1|2) ──────────────────────────────────────────────────
            ['name' => '1|2 Timbo',                                         'type' => 'Comida', 'category' => 'Pizza',        'price' =>  7000, 'stock' =>  40],
            ['name' => '1|2 Incienso',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' =>  7000, 'stock' =>  40],
            ['name' => '1|2 Mora Blanca',                                   'type' => 'Comida', 'category' => 'Pizza',        'price' =>  7500, 'stock' =>  40],
            ['name' => '1|2 Carandai',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' =>  7500, 'stock' =>  40],
            ['name' => '1|2 Palo Rosa',                                     'type' => 'Comida', 'category' => 'Pizza',        'price' =>  8000, 'stock' =>  40],
            ['name' => '1|2 Palmito',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' =>  8000, 'stock' =>  40],
            ['name' => '1|2 Petiribo',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' =>  8000, 'stock' =>  40],
            ['name' => '1|2 Pindo',                                         'type' => 'Comida', 'category' => 'Pizza',        'price' =>  8000, 'stock' =>  40],
            ['name' => '1|2 Anchico',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9000, 'stock' =>  40],
            ['name' => '1|2 4 Quesos',                                      'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9000, 'stock' =>  40],
            ['name' => '1|2 Anana',                                         'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9000, 'stock' =>  40],
            ['name' => '1|2 Urunday',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9000, 'stock' =>  40],
            ['name' => '1|2 Cana Fistola',                                  'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9500, 'stock' =>  30],
            ['name' => '1|2 Champinones',                                   'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9500, 'stock' =>  30],
            ['name' => '1|2 Los Troncos',                                   'type' => 'Comida', 'category' => 'Pizza',        'price' =>  9500, 'stock' =>  30],
            ['name' => '1|2 Kurupi',                                        'type' => 'Comida', 'category' => 'Pizza',        'price' => 10000, 'stock' =>  30],
            ['name' => '1|2 Araticu',                                       'type' => 'Comida', 'category' => 'Pizza',        'price' => 11000, 'stock' =>  30],
            ['name' => '1|2 Hambur Pizza',                                  'type' => 'Comida', 'category' => 'Pizza',        'price' => 13500, 'stock' =>  20],

            // ── MILANESAS (sin guarnicion) ────────────────────────────────────────────
            ['name' => 'Milanesa de Ternera',                               'type' => 'Comida', 'category' => 'Milanesa',     'price' => 14000, 'stock' =>  40],
            ['name' => 'Milanesa a Caballo',                                'type' => 'Comida', 'category' => 'Milanesa',     'price' => 15000, 'stock' =>  40],
            ['name' => 'Milanesa Marinera',                                 'type' => 'Comida', 'category' => 'Milanesa',     'price' => 16000, 'stock' =>  40],
            ['name' => 'Milanesa Napolitana',                               'type' => 'Comida', 'category' => 'Milanesa',     'price' => 16500, 'stock' =>  40],
            ['name' => 'Milanesa a la Suiza',                               'type' => 'Comida', 'category' => 'Milanesa',     'price' => 17000, 'stock' =>  40],
            ['name' => 'Milanesa Agridulce',                                'type' => 'Comida', 'category' => 'Milanesa',     'price' => 17000, 'stock' =>  40],
            ['name' => 'Milanesa Los Troncos',                              'type' => 'Comida', 'category' => 'Milanesa',     'price' => 17000, 'stock' =>  40],
            ['name' => 'Milanesa Desborde',                                 'type' => 'Comida', 'category' => 'Milanesa',     'price' => 21000, 'stock' =>  30],

            // ── CARNES (sin guarnicion) ───────────────────────────────────────────────
            ['name' => 'Lomo al Champinon',                                 'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],
            ['name' => 'Lomo a la Mostaza',                                 'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],
            ['name' => 'Lomo a la Pimienta',                                'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],
            ['name' => 'Lomo a la Plancha',                                 'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],
            ['name' => 'Lomo a la Suiza',                                   'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],
            ['name' => 'Bife de Chorizo',                                   'type' => 'Comida', 'category' => 'Carne',        'price' => 17000, 'stock' =>  30],

            // ── MILANESAS DE PESCADO (sin guarnicion) ─────────────────────────────────
            ['name' => 'Milanesa de Merluza',                               'type' => 'Comida', 'category' => 'Pescado',      'price' => 11000, 'stock' =>  30],
            ['name' => 'Milanesa de Merluza Napolitana',                    'type' => 'Comida', 'category' => 'Pescado',      'price' => 13000, 'stock' =>  30],
            ['name' => 'Milanesa de Merluza al Roquefort',                  'type' => 'Comida', 'category' => 'Pescado',      'price' => 14000, 'stock' =>  30],

            // ── GUARNICIONES ──────────────────────────────────────────────────────────
            ['name' => 'Guarn. Papas Fritas',                               'type' => 'Comida', 'category' => 'Guarnicion',   'price' =>  5000, 'stock' => 100],
            ['name' => 'Guarn. Mandioca Frita',                             'type' => 'Comida', 'category' => 'Guarnicion',   'price' =>  5000, 'stock' => 100],
            ['name' => 'Guarn. Pure de Papa',                               'type' => 'Comida', 'category' => 'Guarnicion',   'price' =>  5000, 'stock' => 100],
            ['name' => 'Guarn. Pure Mixto',                                 'type' => 'Comida', 'category' => 'Guarnicion',   'price' =>  5000, 'stock' => 100],
            ['name' => 'Guarn. Ensalada Mixta',                             'type' => 'Comida', 'category' => 'Guarnicion',   'price' =>  5000, 'stock' => 100],

            // ── PASTAS ────────────────────────────────────────────────────────────────
            ['name' => 'Tallarines',                                        'type' => 'Comida', 'category' => 'Pasta',        'price' => 11000, 'stock' =>  50],
            ['name' => 'Ravioles',                                          'type' => 'Comida', 'category' => 'Pasta',        'price' => 14000, 'stock' =>  50],
            ['name' => 'Noquis de Papa',                                    'type' => 'Comida', 'category' => 'Pasta',        'price' => 14000, 'stock' =>  50],
            ['name' => 'Sorrentinos',                                       'type' => 'Comida', 'category' => 'Pasta',        'price' => 15000, 'stock' =>  50],
            ['name' => 'Pastel de Papa',                                    'type' => 'Comida', 'category' => 'Pasta',        'price' => 15000, 'stock' =>  50],
            ['name' => 'Canelones',                                         'type' => 'Comida', 'category' => 'Pasta',        'price' => 15000, 'stock' =>  50],

            // ── POSTRES ───────────────────────────────────────────────────────────────
            ['name' => 'Flan Casero',                                       'type' => 'Comida', 'category' => 'Postre',       'price' =>  5500, 'stock' =>  50],
            ['name' => 'Mamon en Almibar',                                  'type' => 'Comida', 'category' => 'Postre',       'price' =>  5500, 'stock' =>  50],
            ['name' => 'Bombon Helado',                                     'type' => 'Comida', 'category' => 'Postre',       'price' =>  5500, 'stock' =>  50],
            ['name' => 'Panqueque de Dulce de Leche',                       'type' => 'Comida', 'category' => 'Postre',       'price' =>  5500, 'stock' =>  50],
            ['name' => 'Helado Arcor',                                      'type' => 'Comida', 'category' => 'Postre',       'price' =>     0, 'stock' =>  50],

            // ── GASEOSAS 500ml ────────────────────────────────────────────────────────
            ['name' => 'Coca-Cola 500ml',                                   'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],
            ['name' => 'Sprite 500ml',                                      'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],
            ['name' => 'Fanta 500ml',                                       'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],
            ['name' => 'Pepsi 500ml',                                       'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],
            ['name' => 'H2O 500ml',                                         'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],
            ['name' => 'Paso de los Toros 500ml',                           'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  3000, 'stock' => 100],

            // ── GASEOSAS 1L ───────────────────────────────────────────────────────────
            ['name' => 'Coca-Cola 1L',                                      'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],
            ['name' => 'Sprite 1L',                                         'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],
            ['name' => 'Fanta 1L',                                          'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],
            ['name' => 'Pepsi 1L',                                          'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],
            ['name' => 'H2O 1L',                                            'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],
            ['name' => 'Paso de los Toros 1L',                              'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  4500, 'stock' => 100],

            // ── GASEOSAS 1.5L ─────────────────────────────────────────────────────────
            ['name' => 'Coca-Cola 1.5L',                                    'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],
            ['name' => 'Sprite 1.5L',                                       'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],
            ['name' => 'Fanta 1.5L',                                        'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],
            ['name' => 'Pepsi 1.5L',                                        'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],
            ['name' => 'H2O 1.5L',                                          'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],
            ['name' => 'Paso de los Toros 1.5L',                            'type' => 'Bebida', 'category' => 'Gaseosa',      'price' =>  5000, 'stock' => 100],

            // ── AGUAS / LEVITE ────────────────────────────────────────────────────────
            ['name' => 'Levite 500ml',                                      'type' => 'Bebida', 'category' => 'Agua',         'price' =>  2000, 'stock' => 100],
            ['name' => 'Agua Mineral 500ml',                                'type' => 'Bebida', 'category' => 'Agua',         'price' =>  2000, 'stock' => 100],
            ['name' => 'Levite 1.5L',                                       'type' => 'Bebida', 'category' => 'Agua',         'price' =>  3500, 'stock' => 100],
            ['name' => 'Agua Mineral 1.5L',                                 'type' => 'Bebida', 'category' => 'Agua',         'price' =>  3500, 'stock' => 100],

            // ── LIMONADAS ─────────────────────────────────────────────────────────────
            ['name' => 'Limonada',                                          'type' => 'Bebida', 'category' => 'Jugo Natural', 'price' =>  7000, 'stock' =>  50],
            ['name' => 'Limonada con menta y jengibre',                     'type' => 'Bebida', 'category' => 'Jugo Natural', 'price' =>  7000, 'stock' =>  50],

            // ── CERVEZAS ──────────────────────────────────────────────────────────────
            ['name' => 'Stella Artois',                                     'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  7500, 'stock' => 100],
            ['name' => 'Patagonia',                                         'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  7000, 'stock' => 100],
            ['name' => 'Corona 710ml',                                      'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  7000, 'stock' => 100],
            ['name' => 'Brahama',                                           'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  6000, 'stock' => 100],
            ['name' => 'Quilmes Original',                                  'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  6000, 'stock' => 100],
            ['name' => 'Quilmes Stout',                                     'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  6000, 'stock' => 100],
            ['name' => 'Budweiser',                                         'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  6000, 'stock' => 100],
            ['name' => 'Corona 330ml',                                      'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  4500, 'stock' => 100],
            ['name' => 'Patagonia Lata',                                    'type' => 'Bebida', 'category' => 'Cerveza',      'price' =>  4500, 'stock' => 100],

            // ── TRAGOS ────────────────────────────────────────────────────────────────
            ['name' => 'Aperol',                                            'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6500, 'stock' =>  50],
            ['name' => 'Cuba Libre',                                        'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6500, 'stock' =>  50],
            ['name' => 'Gin Tonic',                                         'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6500, 'stock' =>  50],
            ['name' => 'Campari',                                           'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6500, 'stock' =>  50],
            ['name' => 'Fernet',                                            'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6000, 'stock' =>  50],
            ['name' => 'Gancia',                                            'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6000, 'stock' =>  50],
            ['name' => 'Vermut',                                            'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6000, 'stock' =>  50],
            ['name' => 'Caipirina',                                         'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6000, 'stock' =>  50],
            ['name' => 'Daikiri',                                           'type' => 'Bebida', 'category' => 'Trago',        'price' =>  6000, 'stock' =>  50],
            ['name' => 'Gin Tonic Frutos Rojos',                            'type' => 'Bebida', 'category' => 'Trago',        'price' =>  7000, 'stock' =>  50],
            ['name' => 'Fernet XL',                                         'type' => 'Bebida', 'category' => 'Trago',        'price' => 10000, 'stock' =>  50],

            // ── VINOS ─────────────────────────────────────────────────────────────────
            ['name' => 'Latitud 33 Malbec',                                 'type' => 'Bebida', 'category' => 'Vino',         'price' => 10000, 'stock' =>  30],
            ['name' => 'Santa Julia Malbec',                                'type' => 'Bebida', 'category' => 'Vino',         'price' => 10000, 'stock' =>  30],
            ['name' => 'Santa Julia Dulce Tinto Natural',                   'type' => 'Bebida', 'category' => 'Vino',         'price' => 10000, 'stock' =>  30],
            ['name' => 'Santa Julia Blanco Chenin Dulce Natural',           'type' => 'Bebida', 'category' => 'Vino',         'price' => 10000, 'stock' =>  30],
            ['name' => 'Trumpeter Malbec',                                  'type' => 'Bebida', 'category' => 'Vino',         'price' => 13000, 'stock' =>  20],
            ['name' => 'DV Catena Cabernet-Malbec',                         'type' => 'Bebida', 'category' => 'Vino',         'price' => 18000, 'stock' =>  20],
            ['name' => 'Mosquita Muerta Blend',                             'type' => 'Bebida', 'category' => 'Vino',         'price' => 19000, 'stock' =>  20],
            ['name' => 'DV Catena Malbec-Malbec',                           'type' => 'Bebida', 'category' => 'Vino',         'price' => 24000, 'stock' =>  20],
            ['name' => 'Angelica Zapata Malbec Alta',                       'type' => 'Bebida', 'category' => 'Vino',         'price' => 30000, 'stock' =>  10],
            ['name' => 'El Enemigo Malbec',                                 'type' => 'Bebida', 'category' => 'Vino',         'price' => 30000, 'stock' =>  10],
            ['name' => 'Bramare Malbec',                                    'type' => 'Bebida', 'category' => 'Vino',         'price' => 40000, 'stock' =>  10],
            ['name' => 'Gran Enemigo Gualtallary',                          'type' => 'Bebida', 'category' => 'Vino',         'price' => 68000, 'stock' =>   5],

        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, ['active' => true]));
        }
    }
}
