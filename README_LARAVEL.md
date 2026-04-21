# Sistema de Restaurante "Los Troncos" — Documentación técnica para migración a Laravel

---

## 1. Descripción general del sistema

Sistema web de gestión para un restaurante. Permite manejar **40 mesas**, tomar **pedidos por mesa**, visualizar el estado en tiempo real, imprimir tickets y generar reportes por día o por mes.

El sistema actual está hecho en **PHP puro + MySQL + JavaScript vanilla** (sin framework). Esta documentación describe toda la lógica para replicarlo en **Laravel**.

---

## 2. Stack tecnológico actual

| Capa | Tecnología actual | Equivalente en Laravel |
|---|---|---|
| Backend | PHP puro | Laravel (PHP) |
| Base de datos | MySQL | MySQL + Eloquent ORM |
| Conexión DB | PDO | Eloquent / Query Builder |
| Autenticación | `$_SESSION` manual | Laravel Auth / Sanctum |
| Frontend | HTML + CSS + JS vanilla | Blade templates + JS |
| API interna | `api.php` (switch/case) | Routes + Controllers |
| Reportes | PHP con SQL directo | Controllers + Blade |
| Tickets | PHP con estilos inline | Blade + CSS de impresión |
| Componente Java | `ModeloPedidos.java` + `ItemPedido.java` | No aplica (era desktop/servlet) |

---

## 3. Base de datos

### 3.1 Tablas

#### `productos`
Catálogo de todos los productos del restaurante.

```sql
CREATE TABLE productos (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(100) NOT NULL,
  tipo      VARCHAR(50)  NOT NULL,   -- 'Bebida', 'Comida', 'Postre'
  categoria VARCHAR(50)  NOT NULL,   -- 'Gaseosa', 'Pizza', 'Cerveza', etc.
  precio    DECIMAL(10,2) NOT NULL,
  stock     INT NOT NULL DEFAULT 0
);
```

**Categorías que maneja el sistema:**
- Tipo `Bebida`: Gaseosa, Agua, Cerveza, Jugo Natural, Café, Té, Caliente
- Tipo `Comida`: Entrada, Ensalada, Hamburguesa, Pizza, Pasta, Carne, Pollo
- Tipo `Postre`: Helado, Casero, Torta, Chocolate

#### `mesa pedido`
Pedidos activos por mesa (la tabla tiene espacio en el nombre — importante conservarlo o renombrarlo en Laravel).

```sql
CREATE TABLE `mesa pedido` (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  mesa        INT NOT NULL,                          -- número de mesa (1-40)
  producto_id INT NOT NULL,
  cantidad    INT NOT NULL,
  fecha_hora  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);
```

> **Nota para Laravel:** renombrar la tabla a `mesa_pedidos` (sin espacio) y actualizar el modelo con `protected $table = 'mesa_pedidos'`.

#### `usuario` (implícita, usada en Login)
```sql
CREATE TABLE usuario (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  contraseña VARCHAR(255) NOT NULL  -- bcrypt o texto plano
);
```

---

## 4. Lógica de negocio

### 4.1 Mesas

- El sistema maneja **40 mesas fijas** (numeradas del 1 al 40).
- El estado de cada mesa se determina consultando si tiene registros en `mesa_pedidos`:
  - **Libre** → sin registros para ese número de mesa.
  - **Ocupada** → tiene al menos un ítem de pedido en la tabla.
- El menú principal muestra todas las mesas como tarjetas en un grid 2D con colores:
  - Verde = libre
  - Rojo/naranja = ocupada
- El conteo de mesas libres/ocupadas se calcula en tiempo real con un `GROUP BY mesa`.
- La pantalla se **recarga automáticamente cada 30 segundos** para reflejar cambios.

**Query de estado de mesas:**
```sql
SELECT mesa, COUNT(*) as items
FROM `mesa pedido`
GROUP BY mesa;
```

**En Laravel (Eloquent):**
```php
// MesaPedido Model
$ocupadas = MesaPedido::select('mesa', DB::raw('COUNT(*) as items'))
    ->groupBy('mesa')
    ->pluck('items', 'mesa'); // [mesaNum => cantItems]
```

---

### 4.2 Pedidos

Flujo completo de un pedido:

```
1. Usuario hace click en una mesa → ventana_pedido.php?mesa=X
2. Se muestran dos paneles:
   - IZQUIERDO: lista de todos los productos disponibles (cargada via fetch/AJAX)
   - DERECHO: pedido actual de la mesa (cargado desde DB al abrir)
3. El mozo selecciona un producto y una cantidad, luego pulsa "Agregar"
4. El sistema hace POST a api.php action=agregar_pedido
   - Si el producto ya existe en la mesa → UPDATE cantidad += nueva cantidad
   - Si no existe → INSERT nuevo registro
5. El mozo puede:
   - Cambiar cantidades inline (onchange → api.php action=actualizar_cantidad)
   - Eliminar un ítem (DELETE → api.php action=eliminar_item)
   - Borrar todo el pedido (DELETE todos los ítems de esa mesa)
   - Imprimir ticket → redirige a imprimir_ticket.php?mesa=X
   - Cerrar mesa → descuenta stock + borra pedido
```

**Query para obtener pedido de una mesa:**
```sql
SELECT mp.id, mp.producto_id, p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) AS subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE mp.mesa = ?
ORDER BY mp.id;
```

**Agregar o actualizar producto en pedido:**
```php
// Verificar si ya existe
$existe = MesaPedido::where('mesa', $mesa)
    ->where('producto_id', $productoId)
    ->first();

if ($existe) {
    $existe->increment('cantidad', $cantidad);
} else {
    MesaPedido::create([
        'mesa'        => $mesa,
        'producto_id' => $productoId,
        'cantidad'    => $cantidad,
    ]);
}
```

---

### 4.3 Cierre de mesa

Al cerrar una mesa se ejecutan **dos operaciones en secuencia** (idealmente en una transacción DB):

1. **Descontar stock** de cada producto según la cantidad pedida.
2. **Eliminar** todos los registros de `mesa_pedidos` para esa mesa.

```php
// En Laravel con transacción
DB::transaction(function () use ($mesa) {
    $items = MesaPedido::where('mesa', $mesa)->with('producto')->get();

    foreach ($items as $item) {
        $item->producto->decrement('stock', $item->cantidad);
    }

    MesaPedido::where('mesa', $mesa)->delete();
});
```

---

### 4.4 Vista rápida del pedido (modal)

Con doble click en una mesa se abre un modal mostrando un resumen del pedido sin salir del menú principal. Esto usa AJAX (`fetch`) hacia la API.

```sql
SELECT p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) AS subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE mp.mesa = ?
ORDER BY mp.id;
```

**En Laravel:** crear una ruta `GET /api/pedido/{mesa}/rapido` que devuelva JSON.

---

### 4.5 Impresión de ticket

- Redirige a `imprimir_ticket.php?mesa=X`
- Esa página carga el pedido, **descuenta stock y borra el pedido** al mismo tiempo que genera el HTML del ticket.
- El ticket está formateado para impresoras de **80mm** con fuente monoespaciada (`Courier New`).
- Al cargar la página, JavaScript ejecuta `window.print()` automáticamente.

> **Advertencia:** La lógica actual de `imprimir_ticket.php` tiene un side-effect fuerte: borra el pedido al cargar la página. En Laravel, separar la lógica: el ticket debería solo mostrar datos, y el cierre de mesa ser una acción explícita.

---

## 5. API REST actual (`api.php`)

Todas las acciones pasan por un único archivo con un `switch ($action)`. En Laravel, cada case se convierte en un método de controller.

| `action` | Método HTTP | Descripción | Controller Laravel sugerido |
|---|---|---|---|
| `obtener_productos` | GET | Lista todos los productos | `ProductoController@index` |
| `agregar_producto` | POST | Crea un producto | `ProductoController@store` |
| `actualizar_producto` | PUT | Edita un producto | `ProductoController@update` |
| `eliminar_producto` | DELETE | Borra un producto | `ProductoController@destroy` |
| `agregar_pedido` | POST | Agrega ítem a mesa (upsert) | `PedidoController@store` |
| `actualizar_cantidad` | PUT | Cambia cantidad de un ítem | `PedidoController@update` |
| `eliminar_item` | DELETE | Borra un ítem del pedido | `PedidoController@destroyItem` |
| `borrar_pedido` | DELETE | Borra todos los ítems de una mesa | `PedidoController@borrar` |
| `cerrar_mesa` | POST | Descuenta stock + borra pedido | `MesaController@cerrar` |
| `ver_pedido_rapido` | GET | Devuelve HTML del resumen de mesa | `PedidoController@resumenRapido` |
| `obtener_resumen_dia` | GET | Reporte del día actual | `ReporteController@dia` |
| `obtener_resumen_mes` | GET | Reporte del mes actual | `ReporteController@mes` |
| `limpiar_dia` | DELETE | Borra pedidos del día | `ReporteController@limpiarDia` |
| `limpiar_mes` | DELETE | Borra pedidos del mes | `ReporteController@limpiarMes` |

---

## 6. Autenticación

### Sistema actual
- Login con `$_SESSION['usuario']`
- Acepta usuario/clave vacíos → entra como `admin` directamente (acceso rápido para desarrollo)
- Contraseña soporta texto plano o bcrypt (`password_verify`)
- Todas las páginas llaman a `requireAuth()` que redirige a `login.php` si no hay sesión

### En Laravel
Usar **Laravel Breeze** o **Laravel Jetstream** para el scaffolding de autenticación. Las contraseñas deben estar hasheadas con `bcrypt` (`Hash::make()`). Eliminar el acceso rápido con campos vacíos.

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/mesas', [MesaController::class, 'index']);
    Route::get('/mesa/{numero}/pedido', [PedidoController::class, 'show']);
    // ...
});
```

---

## 7. Estructura de archivos actual → estructura Laravel sugerida

```
PHP actual                  →   Laravel
─────────────────────────────────────────────────────────
config.php                  →   .env + config/database.php
Login.php                   →   Auth (Breeze/Jetstream)
logout.php                  →   /logout route (Auth)
menu_principal.php          →   resources/views/mesas/index.blade.php
ventana_pedido.php          →   resources/views/pedidos/show.blade.php
reportes.php                →   resources/views/reportes/index.blade.php
imprimir_ticket.php         →   resources/views/ticket/print.blade.php
api.php                     →   routes/api.php + Controllers
scripts.js                  →   public/js/app.js (o resources/js/)
styles.css                  →   public/css/app.css (o resources/css/)
check_mesa_pedido.php       →   eliminable (era debug)
check_table.php             →   eliminable (era debug)
create_admin.php            →   DatabaseSeeder / Artisan command
ModelPedidos.php (Java)     →   Lógica movida a PedidoController.php
ItemPedido.php (Java)       →   Modelo MesaPedido.php (Eloquent)
```

---

## 8. Modelos Eloquent sugeridos

### `Producto.php`
```php
class Producto extends Model {
    protected $table = 'productos';
    protected $fillable = ['nombre', 'tipo', 'categoria', 'precio', 'stock'];

    public function itemsPedido() {
        return $this->hasMany(MesaPedido::class, 'producto_id');
    }
}
```

### `MesaPedido.php`
```php
class MesaPedido extends Model {
    protected $table = 'mesa_pedidos'; // renombrada sin espacio
    protected $fillable = ['mesa', 'producto_id', 'cantidad'];

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Accessor para subtotal
    public function getSubtotalAttribute(): float {
        return $this->cantidad * $this->producto->precio;
    }
}
```

### `Usuario.php`
```php
// Usar el modelo User de Laravel con tabla `users`
// o apuntar a la tabla `usuario` existente
class User extends Authenticatable {
    protected $table = 'usuario';
    // ...
}
```

---

## 9. Rutas sugeridas en Laravel

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/',                     [MesaController::class,    'index']);
    Route::get('/mesas',                [MesaController::class,    'index']);
    Route::post('/mesas/{mesa}/cerrar', [MesaController::class,    'cerrar']);

    Route::get('/pedido/{mesa}',        [PedidoController::class,  'show']);
    Route::post('/pedido/{mesa}',       [PedidoController::class,  'store']);
    Route::put('/pedido/item/{id}',     [PedidoController::class,  'update']);
    Route::delete('/pedido/item/{id}',  [PedidoController::class,  'destroyItem']);
    Route::delete('/pedido/{mesa}',     [PedidoController::class,  'borrar']);

    Route::get('/ticket/{mesa}',        [TicketController::class,  'imprimir']);

    Route::get('/reportes/dia',         [ReporteController::class, 'dia']);
    Route::get('/reportes/mes',         [ReporteController::class, 'mes']);
});

// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pedido/{mesa}/rapido', [PedidoController::class, 'resumenRapido']);
    Route::apiResource('/productos',     ProductoController::class);
});
```

---

## 10. Lógica de reportes

### Reporte del día
```sql
SELECT mp.mesa, p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) AS subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE DATE(mp.fecha_hora) = CURDATE()
ORDER BY mp.mesa, mp.id;
```

### Reporte del mes
```sql
SELECT p.nombre,
       SUM(mp.cantidad) AS cantidad,
       p.precio,
       SUM(mp.cantidad * p.precio) AS subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE DATE_FORMAT(mp.fecha_hora, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
GROUP BY p.id, p.nombre, p.precio
ORDER BY subtotal DESC;
```

**En Laravel:**
```php
// Día
MesaPedido::with('producto')
    ->whereDate('fecha_hora', today())
    ->get();

// Mes
MesaPedido::with('producto')
    ->whereYear('fecha_hora', now()->year)
    ->whereMonth('fecha_hora', now()->month)
    ->selectRaw('producto_id, SUM(cantidad) as cantidad')
    ->groupBy('producto_id')
    ->get();
```

---

## 11. Frontend / JavaScript

El JS actual hace llamadas AJAX directamente a `api.php`. En Laravel, apuntar esas llamadas a las rutas de `routes/api.php`.

### Funciones JS principales en `ventana_pedido.php`:
- `cargarProductos()` → `fetch('/api/productos')`
- `filtrarProductos()` → filtro en memoria sobre el array local
- `seleccionarProducto(p)` → resalta fila y guarda selección
- `agregarProductoSeleccionado()` → `fetch POST /pedido/{mesa}` con `{producto_id, cantidad}`
- `actualizarCantidad(id, val)` → `fetch PUT /pedido/item/{id}` con `{cantidad}`
- `eliminarItemPedido(id)` → `fetch DELETE /pedido/item/{id}`
- `cerrarMesa()` → `fetch POST /mesas/{mesa}/cerrar`
- `borrarPedido()` → `fetch DELETE /pedido/{mesa}`
- `imprimirTicket()` → `window.open('/ticket/{mesa}', '_blank')`

### Funciones JS en `menu_principal.php`:
- `abrirMesa(n)` → `window.location.href = '/pedido/' + n`
- `verPedidoRapido(n)` → `fetch GET /api/pedido/{n}/rapido` → muestra modal
- Auto-reload cada 30 segundos con `setInterval(() => location.reload(), 30000)`

---

## 12. Consideraciones de seguridad para Laravel

- Usar `$request->validated()` con Form Requests para validar entradas.
- Las cantidades y IDs deben ser enteros positivos (`'cantidad' => 'required|integer|min:1'`).
- El número de mesa debe estar entre 1 y 40 (`'mesa' => 'required|integer|between:1,40'`).
- Usar `CSRF` en todos los formularios Blade (ya viene por defecto en Laravel).
- No permitir acceso sin autenticación a ninguna ruta con el middleware `auth`.
- Mover las credenciales de DB al archivo `.env` (nunca hardcodeadas).
- El "acceso rápido con campos vacíos" del login actual debe eliminarse en producción.

---

## 13. Componente Java (referencia histórica)

El proyecto incluye `ModeloPedidos.java` e `ItemPedido.java`. Estos eran parte de una aplicación **Java de escritorio o servlet** que se conectaba directamente a la misma base de datos MySQL.

- `ItemPedido` → POJO equivalente a un ítem de pedido (id, nombre, precio, cantidad, subtotal).
- `ModeloPedidos` → mantenía un `ConcurrentHashMap<Integer, List<ItemPedido>>` en memoria (mesa → lista de ítems), y además consultaba/escribía en MySQL.
- En Laravel este componente se reemplaza completamente con Eloquent y los Controllers.

---

## 14. Comando para instalar Laravel (referencia rápida)

```bash
composer create-project laravel/laravel restaurante-los-troncos
cd restaurante-los-troncos

# Configurar .env
DB_DATABASE=los_troncos
DB_USERNAME=root
DB_PASSWORD=

# Instalar auth
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build

# Correr migraciones
php artisan migrate

# Seed de productos iniciales
php artisan db:seed --class=ProductoSeeder
```

---

## 15. Resumen del flujo completo del sistema

```
[Login]
   ↓ autenticación con sesión
[Menu Principal - Mesas]
   → muestra grid de 40 mesas (verde=libre, rojo=ocupada)
   → click → [Ventana de Pedido]
   → doble click → modal con resumen rápido (AJAX)
   → botones: Resumen del Día | Resumen del Mes | Salir

[Ventana de Pedido - Mesa X]
   → panel izquierdo: productos disponibles (cargados via AJAX)
   → panel derecho: pedido actual de la mesa (cargado desde DB)
   → acciones: Agregar | Cambiar cantidad | Eliminar ítem | Borrar todo
   → botones: Imprimir Ticket | Cerrar Mesa | Borrar Pedido

[Imprimir Ticket]
   → genera HTML formato 80mm
   → descuenta stock + borra pedido automáticamente
   → window.print()

[Reportes]
   → Día: todos los pedidos de hoy agrupados por mesa
   → Mes: todos los pedidos del mes agrupados por producto (ranking de ventas)
```
