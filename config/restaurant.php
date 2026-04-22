<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Datos del restaurante para tickets e impresión
    |--------------------------------------------------------------------------
    */
    'name'    => env('RESTAURANT_NAME',    'LOS TRONCOS RESTO BAR'),
    'address' => env('RESTAURANT_ADDRESS', 'Av. Principal 123'),
    'contact' => env('RESTAURANT_CONTACT', 'Tel: (011) 1234-5678'),
    'message' => env('RESTAURANT_MESSAGE', 'Gracias por elegirnos'),

    /*
    | Ancho de línea para impresora térmica 58mm (máx ~32 chars)
    */
    'thermal_width' => 32,
];
