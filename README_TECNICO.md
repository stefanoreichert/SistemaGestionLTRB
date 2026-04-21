# README Técnico — Sistema de Restaurante "Los Troncos"

> **Propósito de este documento:** Documentar en detalle toda la lógica, arquitectura, estructura de datos y flujos del sistema actual en PHP plano, para servir de base en la reconstrucción completa en Laravel.

---

## Índice

1. [Descripción General](#1-descripción-general)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Estructura de Archivos](#3-estructura-de-archivos)
4. [Módulos Principales](#4-módulos-principales)
5. [Estructura de Datos](#5-estructura-de-datos)
6. [Flujo Completo de Negocio](#6-flujo-completo-de-negocio)
7. [Reglas de Negocio](#7-reglas-de-negocio)
8. [Relaciones Entre Entidades](#8-relaciones-entre-entidades)
9. [Procesos Importantes — Lógica Detallada](#9-procesos-importantes--lógica-detallada)
10. [API Interna](#10-api-interna)
11. [Frontend y JavaScript](#11-frontend-y-javascript)
12. [Componente Java Paralelo](#12-componente-java-paralelo)
13. [Problemas de Arquitectura y Bugs Críticos](#13-problemas-de-arquitectura-y-bugs-críticos)
14. [Guía de Migración a Laravel](#14-guía-de-migración-a-laravel)

---

## 1. Descripción General

**Los Troncos** es un sistema de gestión de pedidos para un restaurante. Su función central es permitir a los mozos o cajeros:

- Ver el estado de las **40 mesas** del restaurante (libres u ocupadas).
- Abrir una **ventana de pedido** por mesa para agregar, modificar o eliminar ítems.
- **Imprimir un ticket** de consumo al cierre de mesa.
- Consultar **reportes de ventas** del día o del mes.
- **Limpiar** el registro de ventas por período.

El sistema está construido en **PHP plano** con una base de datos **MySQL**, sin ningún framework. Existe adicionalmente un componente paralelo en **Java** (archivos `ModelPedidos.php` e `ItemPedido.php`, que a pesar de la extensión `.php` contienen código Java) que implementa la misma lógica de gestión de pedidos para una aplicación de escritorio que comparte la misma base de datos MySQL.

---

## 2. Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Lenguaje backend | PHP (sin framework) |
| Base de datos | MySQL / MariaDB |
| Conexión DB | PDO con `ERRMODE_EXCEPTION` |
| Charset | `utf8mb4` |
| Sesiones | `session_start()` nativo de PHP |
| Frontend | HTML5 + CSS3 (custom, archivo `styles.css`) |
| JavaScript | Vanilla JS (sin librerías) + Fetch API |
| Impresión | Ventana emergente `window.open()` + `window.print()` |
| Servidor | Apache/Nginx local (XAMPP/WAMP), puerto estándar |
| Componente alternativo | Java con JDBC (acceso directo a la misma BD) |

---

## 3. Estructura de Archivos

```
FINAL_LGI_LOS_TRONCOS/
│
├── index.php              # Punto de entrada. Redirige a login.php o menu_principal.php
├── config.php             # Configuración de BD, conexión PDO, funciones de sesión/auth
├── Login.php              # Formulario y lógica de autenticación
├── logout.php             # Destruye sesión y redirige al login
│
├── menu_principal.php     # Dashboard principal: grilla de 40 mesas con estado
├── ventana_pedido.php     # Gestión del pedido de una mesa específica
├── imprimir_ticket.php    # Genera, imprime y CIERRA el pedido (descuenta stock + elimina)
├── reportes.php           # Reportes de ventas por día o por mes
│
├── api.php                # API REST interna (JSON). Maneja todas las operaciones CRUD
│
├── scripts.js             # Utilidades JavaScript del frontend
├── styles.css             # Estilos CSS globales del sistema
│
├── database.sql           # Script SQL para crear la BD y poblar productos iniciales
│
│ --- Archivos de diagnóstico/utilidad (no son parte del flujo principal) ---
├── check_table.php        # Diagnóstico: imprime estructura de la tabla `usuario`
├── check_mesa_pedido.php  # Diagnóstico: imprime estructura de la tabla `mesa pedido`
├── create_admin.php       # Utilidad: crea el usuario 'admin' con bcrypt en la tabla `usuario`
│
│ --- Archivos Java (extensión .php incorrecta) ---
├── ModelPedidos.php       # [JAVA] Clase que gestiona pedidos en memoria + consultas JDBC
└── ItemPedido.php         # [JAVA] Clase entidad que representa un ítem de pedido
```

> **Nota:** `ModelPedidos.php` e `ItemPedido.php` contienen código **Java**, no PHP. Son parte de un proyecto de escritorio paralelo. No son ejecutados por el servidor web.

---

## 4. Módulos Principales

### 4.1 Módulo de Autenticación
- **Archivos:** `config.php`, `Login.php`, `logout.php`, `create_admin.php`
- Gestiona el inicio y cierre de sesión.
- Referencia una tabla `usuario` (no definida en `database.sql`, debe crearse manualmente o con `create_admin.php`).

### 4.2 Módulo de Mesas
- **Archivo:** `menu_principal.php`
- Muestra la grilla de 40 mesas.
- Determina qué mesa está ocupada consultando si tiene filas en `mesa pedido`.
- Auto-refresca cada 30 segundos para reflejar cambios en tiempo real.

### 4.3 Módulo de Pedidos
- **Archivos:** `ventana_pedido.php`, `api.php`
- Permite agregar, modificar cantidades y eliminar ítems del pedido de una mesa.
- Muestra productos disponibles con búsqueda en tiempo real (lado cliente).
- Los productos se cargan desde la BD mediante `api.php?action=obtener_productos`.

### 4.4 Módulo de Ticket / Cierre de Mesa
- **Archivo:** `imprimir_ticket.php`
- Genera un ticket imprimible en formato térmico (80mm).
- Al ser ejecutado, descuenta el stock de cada producto consumido y elimina el pedido de la BD.
- Se abre en una ventana nueva y ejecuta `window.print()` automáticamente.

### 4.5 Módulo de Reportes
- **Archivo:** `reportes.php`
- Genera resúmenes de ventas por día o por mes.
- Permite imprimir el reporte y "limpiar" (eliminar permanentemente) los registros del período.

### 4.6 Módulo de API
- **Archivo:** `api.php`
- Punto central de comunicación entre el frontend (JS) y la base de datos.
- Retorna siempre JSON.
- Acepta parámetros por `GET`, `POST` o cuerpo JSON.

---

## 5. Estructura de Datos

### 5.1 Tabla `productos`

```sql
CREATE TABLE `productos` (
  `id`        INT(11)        NOT NULL AUTO_INCREMENT,
  `nombre`    VARCHAR(100)   NOT NULL,
  `tipo`      VARCHAR(50)    NOT NULL,   -- 'Bebida', 'Comida', 'Postre'
  `categoria` VARCHAR(50)    NOT NULL,   -- 'Gaseosa', 'Hamburguesa', 'Pizza', etc.
  `precio`    DECIMAL(10,2)  NOT NULL,
  `stock`     INT(11)        NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
| Campo | Tipo | Descripción |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Clave primaria |
| `nombre` | VARCHAR(100) | Nombre del producto (ej: "Hamburguesa Clásica") |
| `tipo` | VARCHAR(50) | Clasificación de primer nivel: `Bebida`, `Comida`, `Postre` |
| `categoria` | VARCHAR(50) | Sub-clasificación: `Gaseosa`, `Cerveza`, `Pizza`, `Pasta`, `Carne`, etc. |
| `precio` | DECIMAL(10,2) | Precio unitario |
| `stock` | INT | Unidades disponibles. Se descuenta al cerrar mesa o imprimir ticket |

**Datos precargados (40 productos):** 10 bebidas (gaseosas, agua, cerveza, jugos naturales), 16 comidas (entradas, ensaladas, hamburguesas, pizzas, pastas, carnes, pollo), 5 postres, 9 bebidas calientes (café, té, chocolate).

---

### 5.2 Tabla `mesa pedido`

> **Advertencia:** El nombre de la tabla contiene un **espacio**, lo que obliga a escaparla con backticks (`` `mesa pedido` ``) en cada query SQL.

```sql
CREATE TABLE `mesa pedido` (
  `id`          INT(11)   NOT NULL AUTO_INCREMENT,
  `mesa`        INT(11)   NOT NULL,                         -- número de mesa (1–40)
  `producto_id` INT(11)   NOT NULL,                         -- FK a productos.id
  `cantidad`    INT(11)   NOT NULL,
  `fecha_hora`  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mesa` (`mesa`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_fecha` (`fecha_hora`),
  CONSTRAINT `fk_producto` FOREIGN KEY (`producto_id`)
    REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
| Campo | Tipo | Descripción |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Clave primaria del ítem de pedido |
| `mesa` | INT | Número de mesa (1 al 40). No es FK a ninguna tabla de mesas |
| `producto_id` | INT | FK a `productos.id` |
| `cantidad` | INT | Cantidad pedida de ese producto |
| `fecha_hora` | DATETIME | Timestamp de inserción (usado para filtros de reportes) |

**Nota importante:** No existe una tabla `mesas` separada. Las mesas son simplemente valores enteros del 1 al 40. El estado "ocupada" se infiere dinámicamente al verificar si existe al menos una fila con ese número de mesa.

---

### 5.3 Tabla `usuario` (implícita, no en `database.sql`)

```sql
-- Inferida por el código de Login.php y create_admin.php
CREATE TABLE `usuario` (
  `id`         INT(11)       NOT NULL AUTO_INCREMENT,
  `nombre`     VARCHAR(100)  NOT NULL,
  `contraseña` VARCHAR(255)  NOT NULL,   -- puede ser bcrypt o texto plano
  `nivel`      INT(11)       DEFAULT 1,  -- nivel de acceso (1 = admin)
  PRIMARY KEY (`id`)
);
```

**Nota:** Esta tabla no está en `database.sql`. Se crea implícitamente o mediante `create_admin.php`. Su nombre de campo `contraseña` usa tilde (ñ), lo que puede causar problemas de charset en algunas configuraciones.

---

## 6. Flujo Completo de Negocio

```
[1] Usuario accede a index.php
        │
        ├─── ¿Sesión activa? ──SÍ──► menu_principal.php
        │
        └─── NO ──► Login.php

[2] Login.php
        │
        ├─── Campos vacíos ──► Acceso directo como 'admin' (bypass de seguridad)
        │
        ├─── Credenciales con DB ──► Busca en tabla `usuario`
        │         ├─ Hash bcrypt ──► password_verify()
        │         └─ Texto plano ──► comparación directa
        │
        └─── Éxito ──► $_SESSION['usuario'] = nombre ──► menu_principal.php

[3] menu_principal.php
        │
        ├─── Consulta `mesa pedido` agrupado por mesa
        ├─── Muestra grilla 40 mesas (verde=libre, rojo=ocupada)
        ├─── Click simple ──► ventana_pedido.php?mesa=N
        ├─── Doble click ──► Modal con resumen via api.php?action=ver_pedido_rapido
        ├─── Botón "Resumen del Día" ──► reportes.php?tipo=dia
        ├─── Botón "Resumen del Mes" ──► reportes.php?tipo=mes
        └─── Auto-refresh cada 30 segundos

[4] ventana_pedido.php?mesa=N
        │
        ├─── PHP carga pedido actual de la mesa desde BD (JOIN con productos)
        ├─── Calcula total inicial: SUM(cantidad * precio)
        │
        ├─── Panel izquierdo: Productos disponibles
        │         ├─ Carga via api.php?action=obtener_productos (JS/Fetch)
        │         ├─ Filtro en tiempo real por nombre (client-side)
        │         ├─ Click ──► selecciona producto
        │         └─ Doble click ──► agrega directamente
        │
        ├─── Panel derecho: Ítems del pedido actual
        │         ├─ Renderizado por PHP en carga inicial
        │         ├─ Input de cantidad editable ──► api.php action=actualizar_cantidad
        │         └─ Botón eliminar ítem ──► api.php action=eliminar_item
        │
        ├─── Botón "Agregar Producto" ──► api.php action=agregar_pedido
        │         ├─ Si producto YA existe en el pedido: suma cantidad
        │         └─ Si NO existe: INSERT nuevo ítem
        │
        ├─── Botón "Imprimir Ticket" ──► window.open(imprimir_ticket.php?mesa=N)
        │         └─ [Ver paso 5]
        │
        ├─── Botón "Cerrar Mesa" ──► confirm() ──► imprimirTicket()
        │         └─ Actualmente hace lo MISMO que "Imprimir Ticket"
        │
        └─── Botón "Borrar Pedido" ──► confirm() ──► api.php action=borrar_pedido
                  └─ Elimina TODO el pedido sin descontar stock ni imprimir

[5] imprimir_ticket.php?mesa=N
        │
        ├─── Requiere autenticación
        ├─── Carga todos los ítems del pedido con JOIN
        ├─── Calcula total
        │
        ├─── DESCUENTA STOCK: UPDATE productos SET stock = stock - cantidad WHERE id IN (...)
        │         (por cada ítem del pedido)
        │
        ├─── ELIMINA PEDIDO: DELETE FROM `mesa pedido` WHERE mesa = N
        │
        ├─── Renderiza HTML con formato de ticket térmico 80mm
        │
        └─── window.print() automático ──► setTimeout window.close() 100ms

[6] reportes.php?tipo=dia|mes
        │
        ├─── tipo=dia:
        │         └─ Filtra por DATE(fecha_hora) = hoy
        │            Agrupa por mesa, ordena por mesa e ID
        │
        ├─── tipo=mes:
        │         └─ Filtra por DATE_FORMAT(fecha_hora, '%Y-%m') = mes actual
        │            Agrupa por producto, ordena por subtotal DESC
        │
        ├─── Calcula total con array_sum(array_column($items, 'subtotal'))
        ├─── Permite imprimir (window.print())
        └─── Botón "Limpiar y Reiniciar" ──► api.php action=limpiar_dia|limpiar_mes
                  └─ DELETE permanente de los registros del período

[7] logout.php
        └─── session_destroy() ──► Location: login.php
```

---

## 7. Reglas de Negocio

### 7.1 Autenticación
- **Si ambos campos (usuario y clave) están vacíos**, el sistema concede acceso inmediato con rol `admin`. Esta es una característica de desarrollo/demo, no un comportamiento seguro para producción.
- Las contraseñas pueden estar almacenadas en **bcrypt** (preferido) o en **texto plano** (legado). La detección es automática por longitud y prefijo `$2y$` o `$2a$`.
- No existe gestión de roles en la interfaz. El campo `nivel` existe en la tabla `usuario` pero no se verifica en ningún archivo PHP durante las operaciones.

### 7.2 Estado de Mesas
- El restaurante tiene exactamente **40 mesas**, numeradas del 1 al 40. Este número está hardcodeado en `menu_principal.php` (`for ($i = 1; $i <= 40; $i++)`).
- Una mesa está **ocupada** si tiene al menos un registro en `mesa pedido`. No existe un flag de estado explícito.
- Una mesa está **libre** si no tiene ninguna fila en `mesa pedido`.

### 7.3 Pedidos
- Cada ítem de pedido es una combinación única de `(mesa, producto_id)`.
- Si se agrega un producto que **ya existe** en el pedido de esa mesa, se **suma** la cantidad indicada en lugar de insertar una nueva fila.
- Si se agrega un producto **nuevo**, se inserta una nueva fila.
- La cantidad mínima permitida en el input es `1` (validación HTML `min="1"`), pero no hay validación del lado servidor en `api.php`.
- No hay límite máximo de cantidad ni de ítems por pedido.

### 7.4 Cierre de Mesa / Impresión de Ticket
- **Al imprimir el ticket** (`imprimir_ticket.php`): se descuenta el stock de cada producto y se eliminan los ítems del pedido de la BD. La mesa queda libre automáticamente.
- **El stock nunca puede ser negativo** a nivel de restricción de BD (no hay `CHECK CONSTRAINT`). Si el stock llega a 0 o negativo, el sistema no impide seguir vendiendo ese producto.
- El ticket incluye: nombre del restaurante, fecha y hora, número de mesa, lista de ítems con cantidad × precio y subtotal, y total final.

### 7.5 Reportes
- El **reporte del día** muestra todos los ítems agrupados por mesa, filtrando por `DATE(fecha_hora) = fecha_actual`.
- El **reporte del mes** muestra el consolidado por producto, sumando cantidades y subtotales, ordenado de mayor a menor venta.
- **"Limpiar y Reiniciar"** elimina **permanentemente** los registros del período consultado. No existe ningún mecanismo de archivo, historial o papelera.
- El reporte no captura los pedidos eliminados con "Borrar Pedido" ya que esos registros son eliminados sin haber sido registrados en ningún histórico.

### 7.6 Stock
- El stock se descuenta en **dos lugares distintos** del código (ver Bugs en sección 13): `imprimir_ticket.php` y `api.php action=cerrar_mesa`.
- No hay validación de stock al momento de agregar un producto al pedido (no se verifica si `stock > 0`).

---

## 8. Relaciones Entre Entidades

```
┌─────────────┐         ┌──────────────┐         ┌──────────────┐
│   usuario   │         │ mesa pedido  │         │  productos   │
│─────────────│         │──────────────│         │──────────────│
│ id (PK)     │         │ id (PK)      │         │ id (PK)      │
│ nombre      │ crea    │ mesa (1-40)  │ FK ───► │ nombre       │
│ contraseña  │ sesión  │ producto_id  │         │ tipo         │
│ nivel       │         │ cantidad     │         │ categoria    │
└─────────────┘         │ fecha_hora   │         │ precio       │
                        └──────────────┘         │ stock        │
                                                 └──────────────┘

Cardinalidades:
- Un usuario → crea → N pedidos (a través de la sesión, sin FK en la tabla)
- Una mesa (concepto lógico, no tabla) → tiene → N ítems en `mesa pedido`
- Un producto → aparece en → N ítems de `mesa pedido`
- Un ítem de `mesa pedido` → pertenece a → 1 producto (FK con CASCADE DELETE)
- Un ítem de `mesa pedido` → pertenece a → 1 mesa (número entero, sin tabla de mesas)
```

**Relaciones ausentes (a crear en Laravel):**
- No existe tabla `mesas` (sólo existe como número en `mesa pedido.mesa`).
- No existe tabla de `roles` ni de `permisos`.
- No existe tabla de `ventas` o `tickets` históricos; todo se elimina al cerrar.
- No existe tabla de `categorias` ni `tipos`; son VARCHAR libres en `productos`.

---

## 9. Procesos Importantes — Lógica Detallada

### 9.1 Proceso: Cargar estado de mesas (`menu_principal.php`)

```php
// Consulta exacta utilizada:
SELECT mesa, COUNT(*) as items
FROM `mesa pedido`
GROUP BY mesa

// Resultado: array indexado por número de mesa.
// Si $estadoMesas[5] existe => Mesa 5 ocupada.
// Si no existe => Mesa 5 libre.

// Estadísticas:
$libres   = 40 - count($estadoMesas);
$ocupadas = count($estadoMesas);
```

### 9.2 Proceso: Agregar ítem al pedido (`api.php → agregar_pedido`)

```php
// Paso 1: verificar si ya existe ese producto en esa mesa
SELECT id, cantidad FROM `mesa pedido`
WHERE mesa = ? AND producto_id = ?

// Paso 2a: si existe => sumar cantidad
UPDATE `mesa pedido`
SET cantidad = cantidad + ?
WHERE id = ?

// Paso 2b: si no existe => insertar nuevo
INSERT INTO `mesa pedido` (mesa, producto_id, cantidad)
VALUES (?, ?, ?)

// Nota: fecha_hora se asigna automáticamente por DEFAULT CURRENT_TIMESTAMP
```

### 9.3 Proceso: Imprimir ticket y cerrar mesa (`imprimir_ticket.php`)

```php
// Paso 1: Leer todos los ítems del pedido
SELECT mp.id, p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) as subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE mp.mesa = ?
ORDER BY mp.id

// Paso 2: Calcular total
$total = array_sum(array_column($items, 'subtotal'));

// Paso 3: Descontar stock (por cada ítem)
UPDATE productos SET stock = stock - ?
WHERE id IN (
    SELECT producto_id FROM `mesa pedido` WHERE id = ?
)
// NOTA: Este subquery referencia `mesa pedido` que ya podría haber sido
// eliminada en una ejecución anterior. Ver bug en sección 13.

// Paso 4: Eliminar pedido completo
DELETE FROM `mesa pedido` WHERE mesa = ?

// Paso 5: Renderizar HTML del ticket con estilos inline para impresora térmica 80mm
// Paso 6: window.print() automático al cargar
// Paso 7: window.close() con delay de 100ms
```

### 9.4 Proceso: Cerrar mesa desde API (`api.php → cerrar_mesa`)

```php
// Paso 1: Obtener ítems del pedido
SELECT mp.producto_id, mp.cantidad
FROM `mesa pedido` mp
WHERE mp.mesa = ?

// Paso 2: Descontar stock (por cada ítem)
UPDATE productos SET stock = stock - ?
WHERE id = ?

// Paso 3: Eliminar pedido
DELETE FROM `mesa pedido` WHERE mesa = ?

echo json_encode(['success' => true, 'message' => 'Mesa cerrada']);
```

> **Observación crítica:** Este proceso hace exactamente lo mismo que `imprimir_ticket.php` pero sin generar ni imprimir el ticket. Además, desde el código JavaScript de `ventana_pedido.php`, el botón "Cerrar Mesa" llama a `imprimirTicket()` (que usa `imprimir_ticket.php`), **no** a la acción `cerrar_mesa` de la API. Por lo tanto, la acción `cerrar_mesa` de la API existe pero no se invoca desde la interfaz PHP.

### 9.5 Proceso: Resumen del día (`reportes.php → obtenerResumenDia`)

```php
SELECT mp.mesa, p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) as subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE DATE(mp.fecha_hora) = ?   -- fecha actual: date('Y-m-d')
ORDER BY mp.mesa, mp.id
```

Muestra: columna Mesa + Producto + Cantidad + Precio + Subtotal.

### 9.6 Proceso: Resumen del mes (`reportes.php → obtenerResumenMes`)

```php
SELECT p.nombre, SUM(mp.cantidad) as cantidad, p.precio,
       SUM(mp.cantidad * p.precio) as subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE DATE_FORMAT(mp.fecha_hora, '%Y-%m') = ?   -- mes actual: date('Y-m')
GROUP BY p.id, p.nombre, p.precio
ORDER BY subtotal DESC
```

Muestra: Producto + Cantidad total + Precio unitario + Subtotal total. Ordenado de mayor a menor venta. No muestra mesa.

### 9.7 Proceso: Vista rápida de pedido (modal desde `menu_principal.php`)

```php
// Llamado via: api.php?action=ver_pedido_rapido&mesa=N (GET)
SELECT p.nombre, mp.cantidad, p.precio,
       (mp.cantidad * p.precio) as subtotal
FROM `mesa pedido` mp
JOIN productos p ON mp.producto_id = p.id
WHERE mp.mesa = ?
ORDER BY mp.id

// Devuelve HTML pre-renderizado en JSON:
// { "success": true, "html": "<table>...</table>" }
// Si no hay ítems: { "success": false, "message": "La mesa no tiene pedidos" }
```

### 9.8 Proceso: Limpiar período (`api.php → limpiar_dia / limpiar_mes`)

```php
// limpiar_dia: elimina todos los registros de hoy
DELETE FROM `mesa pedido` WHERE DATE(fecha_hora) = ?   -- date('Y-m-d')

// limpiar_mes: elimina todos los registros del mes actual
DELETE FROM `mesa pedido` WHERE DATE_FORMAT(fecha_hora, '%Y-%m') = ?   -- date('Y-m')
```

> **ADVERTENCIA:** Estas operaciones son **irreversibles**. No hay confirmación del servidor, sólo del cliente (`confirm()`). No hay mecanismo de backup previo.

### 9.9 Proceso: Borrar pedido (`api.php → borrar_pedido`)

```php
DELETE FROM `mesa pedido` WHERE mesa = ?
// NO descuenta stock.
// NO genera ningún registro de cancelación.
// La mesa queda libre inmediatamente.
```

### 9.10 Proceso: Actualizar cantidad de ítem (`api.php → actualizar_cantidad`)

```php
UPDATE `mesa pedido` SET cantidad = ? WHERE id = ?
// No valida que la cantidad sea > 0 en el servidor.
// Si se pone 0, el ítem permanece con cantidad 0 en la BD.
```

### 9.11 Proceso: Crear usuario admin (`create_admin.php`)

```php
// Verifica si existe
SELECT COUNT(*) as cnt FROM usuario WHERE nombre = 'admin'

// Si no existe, inserta con bcrypt
$hash = password_hash('admin', PASSWORD_DEFAULT);
INSERT INTO usuario (nombre, contraseña, nivel) VALUES ('admin', $hash, 1)
```

---

## 10. API Interna

**Archivo:** `api.php`  
**Respuesta:** siempre `Content-Type: application/json`  
**Autenticación:** NO requiere autenticación (no llama a `requireAuth()`). Cualquier petición HTTP directa a este endpoint puede ejecutar acciones sin sesión.

### Tabla de Acciones

| `action` | Método | Descripción | Parámetros |
|---|---|---|---|
| `obtener_productos` | GET | Lista todos los productos ordenados por nombre | — |
| `agregar_producto` | POST/JSON | Inserta un nuevo producto | `nombre`, `id_tipo`, `Id_tipo_producto`, `precio`, `stock` |
| `actualizar_producto` | POST/JSON | Actualiza un producto existente | `id`, `nombre`, `id_tipo`, `Id_tipo_producto`, `precio`, `stock` |
| `eliminar_producto` | POST/JSON | Elimina un producto por ID | `id` |
| `agregar_pedido` | POST/JSON | Agrega/suma ítem al pedido de una mesa | `mesa`, `producto_id`, `cantidad` |
| `actualizar_cantidad` | POST/JSON | Cambia la cantidad de un ítem específico | `item_id`, `cantidad` |
| `eliminar_item` | POST/JSON | Elimina un ítem específico del pedido | `item_id` |
| `borrar_pedido` | POST/JSON | Elimina todo el pedido de una mesa | `mesa` |
| `cerrar_mesa` | POST/JSON | Descuenta stock y elimina pedido (sin ticket) | `mesa` |
| `ver_pedido_rapido` | GET | Devuelve HTML pre-renderizado del pedido | `mesa` (GET param) |
| `obtener_resumen_dia` | GET/POST | Devuelve ítems del día actual como JSON | — |
| `obtener_resumen_mes` | GET/POST | Devuelve ítems del mes actual como JSON | — |
| `limpiar_dia` | POST/JSON | Elimina permanentemente pedidos de hoy | — |
| `limpiar_mes` | POST/JSON | Elimina permanentemente pedidos del mes | — |

**Inconsistencia importante:** Las acciones `agregar_producto` y `actualizar_producto` usan los campos `id_tipo` e `Id_tipo_producto`, que **no existen** en la tabla `productos` (cuya estructura real usa `tipo` y `categoria` como VARCHAR). Estas acciones devolverán error de SQL si se invocan. Esto sugiere que fueron escritas en base a un esquema anterior o provisional.

---

## 11. Frontend y JavaScript

### 11.1 Comunicación con la API
Todo el JavaScript usa **Fetch API nativa** sin librerías externas:

```javascript
// Ejemplo: agregar producto al pedido
fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        action: 'agregar_pedido',
        mesa: numeroMesa,
        producto_id: productoId,
        cantidad: cantidad
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        location.reload(); // recarga completa de la página
    } else {
        alert(data.message);
    }
});
```

Todas las acciones exitosas resultan en un `location.reload()`, lo que evita el manejo de estado en cliente.

### 11.2 Filtrado de Productos (Client-side)
El listado de productos se carga **una sola vez** al abrir `ventana_pedido.php` y se filtra en memoria:

```javascript
function filtrarProductos() {
    const busqueda = document.getElementById('busquedaRapida').value.toLowerCase();
    const productosFiltrados = productos.filter(p =>
        !busqueda || p.nombre.toLowerCase().includes(busqueda)
    );
    mostrarProductos(productosFiltrados);
}
```

No filtra por tipo ni categoría, sólo por nombre.

### 11.3 Selección de Producto
- **Click** en fila: selecciona el producto visualmente (clase CSS `seleccionado`).
- **Doble click** en fila: agrega directamente el producto con la cantidad del input.
- El botón "Agregar Producto" requiere haber seleccionado un producto previamente.

### 11.4 `scripts.js` — Utilidades Globales

| Función | Descripción |
|---|---|
| `formatCurrency(amount)` | Formatea número como moneda: `$1,234.56` |
| `confirmarAccion(mensaje)` | Wrapper sobre `confirm()` |
| `mostrarNotificacion(mensaje, tipo)` | Toast animado en esquina superior derecha (auto-desaparece en 3s) |
| `validarFormulario(formId)` | Resalta campos vacíos requeridos con borde rojo |
| `autoGuardar(key, data)` | Persiste datos en `localStorage` |
| `autoRecuperar(key)` | Recupera datos de `localStorage` |
| `imprimirContenido(elementId)` | Abre ventana emergente para imprimir un elemento específico |
| `actualizarReloj()` | Actualiza cada segundo todos los elementos `.reloj` |

Las animaciones CSS (`slideIn`, `slideOut`) se inyectan dinámicamente en el `<head>` por el propio script.

### 11.5 Teclas Rápidas
- **ESC:** cierra cualquier modal abierto (`.modal` con `display: block`).

### 11.6 Auto-refresh
`menu_principal.php` hace `location.reload()` cada 30 segundos:
```javascript
setInterval(() => { location.reload(); }, 30000);
```

---

## 12. Componente Java Paralelo

Los archivos `ModelPedidos.php` e `ItemPedido.php` contienen código **Java** (no PHP). Son parte de una aplicación de escritorio que accede a la misma base de datos MySQL.

### 12.1 `ItemPedido.java`
Clase POJO simple:
```java
public class ItemPedido {
    private int idProducto;
    private String nombreProducto;
    private double precioUnitario;
    private int cantidad;

    public double getSubtotal() {
        return precioUnitario * cantidad;
    }
}
```

### 12.2 `ModeloPedidos.java`
Clase estática que actúa como gestor de pedidos en memoria + acceso JDBC:

- `ConcurrentHashMap<Integer, List<ItemPedido>> pedidosPorMesa`: estado en RAM de los 40 pedidos.
- `Map<Integer, LocalDateTime> horaPrimerPedido`: registro temporal de cuándo se hizo el primer pedido en cada mesa.
- **Inicialización estática:** crea listas vacías para las 40 mesas al arrancar la JVM.
- `agregarOActualizarItem(int mesa, ItemPedido item)`: lógica equivalente a `api.php → agregar_pedido` (suma si existe, inserta si no).
- `borrarPedido(int mesa)` y `borrarPedidoMesa(int mesa)`: ambos hacen lo mismo (código duplicado).
- `tienePedido(int mesa)`: consulta directa a MySQL via JDBC.
- `cargarPedidoDesdeBD(int mesa)`: carga el pedido desde MySQL, útil al arrancar la app para sincronizar con datos existentes.
- `getHoraPrimerPedido(int mesa)`: consulta `MIN(hora_pedido)` — **nota:** el campo en BD se llama `fecha_hora`, no `hora_pedido`, lo que causaría un error SQL.

**Credenciales hardcodeadas en el código Java:**
```java
DriverManager.getConnection("jdbc:mysql://localhost:3306/los_troncos", "root", "")
```

---

## 13. Problemas de Arquitectura y Bugs Críticos

### Bug #1 — Doble descuento de stock (CRÍTICO)
**Ubicación:** `imprimir_ticket.php` y `api.php → cerrar_mesa`

Ambos archivos implementan el descuento de stock de forma independiente. Si por algún motivo se invocan los dos para la misma mesa, el stock se descontaría dos veces. Aunque el flujo actual de JS no invoca `cerrar_mesa` de la API, la puerta está abierta para llamadas directas.

**Solución en Laravel:** Usar una transacción atómica en un único `OrderService::close()`.

---

### Bug #2 — Subquery inválido en `imprimir_ticket.php` (CRÍTICO)
**Ubicación:** `imprimir_ticket.php`, líneas del descuento de stock

```php
UPDATE productos SET stock = stock - ? WHERE id IN (
    SELECT producto_id FROM `mesa pedido` WHERE id = ?
)
```

El `UPDATE` itera por cada `$item['id']` (ID del registro en `mesa pedido`). El subquery selecciona el `producto_id` de ese registro. Sin embargo, el `DELETE` posterior elimina toda la tabla para esa mesa. Si MySQL evalúa el subquery después del DELETE (en algunos contextos), este query retornaría vacío. En la práctica, como el UPDATE se ejecuta en un loop **antes** del DELETE, funciona correctamente, pero la estructura es confusa y frágil.

**Solución en Laravel:** Cargar los IDs en memoria primero, luego descontar, luego eliminar, todo en transacción.

---

### Bug #3 — API sin autenticación
**Ubicación:** `api.php`

El archivo `api.php` **no llama a `requireAuth()`**, por lo que cualquier petición HTTP directa puede:
- Listar todos los productos.
- Agregar, modificar o eliminar productos del catálogo.
- Borrar pedidos de mesas.
- Limpiar el registro de ventas de todo el mes.

**Solución en Laravel:** Usar middleware `auth` en el grupo de rutas de la API.

---

### Bug #4 — Bypass de autenticación con campos vacíos
**Ubicación:** `Login.php`

```php
if ($usuario === '' && $clave === '') {
    $_SESSION['usuario'] = 'admin';
    header('Location: menu_principal.php');
    exit;
}
```

Cualquier persona que acceda a `login.php` y presione "Ingresar" sin escribir nada entra directamente como admin.

**Solución en Laravel:** Eliminar este bypass. Usar `Auth::attempt()` con validación de campos requeridos.

---

### Bug #5 — Nombre de tabla con espacio (`mesa pedido`)
**Ubicación:** `database.sql` y todas las queries

El nombre `mesa pedido` es técnicamente válido en MySQL pero require backticks en cada uso. Es una fuente de errores frecuente (olvidar los backticks = query inválido).

**Solución en Laravel:** Renombrar la tabla a `mesa_pedido` o `items_pedido`.

---

### Bug #6 — Acciones `agregar_producto` y `actualizar_producto` usan campos inexistentes
**Ubicación:** `api.php`

```php
// Las queries referencian columnas que NO existen en la tabla `productos`
"INSERT INTO productos (nombre, id_tipo, Id_tipo_producto, precio, stock) VALUES ..."
"UPDATE productos SET nombre = ?, id_tipo = ?, Id_tipo_producto = ?, precio = ?, stock = ? ..."
```

La tabla `productos` tiene las columnas `tipo` y `categoria`, no `id_tipo` ni `Id_tipo_producto`. Estas acciones siempre fallan con un error SQL. No existe ninguna interfaz que las invoque actualmente.

---

### Bug #7 — Stock puede quedar negativo
**Ubicación:** `api.php → cerrar_mesa`, `imprimir_ticket.php`

No existe validación que impida vender más unidades de las que hay en stock. El campo `stock` en BD puede quedar en valor negativo.

**Solución en Laravel:** Validar antes de aceptar el pedido y/o usar `CHECK (stock >= 0)` en la migración.

---

### Bug #8 — `getHoraPrimerPedido()` referencia campo inexistente
**Ubicación:** `ModelPedidos.php` (Java)

```java
String sql = "SELECT MIN(hora_pedido) as primera_hora FROM `mesa pedido` WHERE mesa = ?";
```

El campo en la BD se llama `fecha_hora`, no `hora_pedido`. Este método lanzaría una excepción SQL.

---

### Problema de arquitectura #1 — Sin historial de ventas
Cuando se cierra una mesa (imprime ticket), los registros de `mesa pedido` se eliminan. Si el reporte del día se consulta **después** de cerrar todas las mesas del día, habrá registros porque `fecha_hora` del día permanece hasta que se hace "Limpiar". Pero si se hace "Limpiar", todo se pierde. No existe una tabla de ventas históricas, tickets archivados ni logs de transacciones.

---

### Problema de arquitectura #2 — Sin tabla de mesas
Las 40 mesas son sólo un número entero en `mesa pedido.mesa`. No hay entidad `Mesa` con atributos (capacidad, zona, estado, etc.).

---

### Problema de arquitectura #3 — Mezcla de lenguajes en el mismo directorio
Los archivos `ModelPedidos.php` e `ItemPedido.php` son Java con extensión `.php`. Esto es confuso y puede causar que el servidor intente parsearlos como PHP, aunque en la práctica son ignorados por el servidor web ya que no tienen etiquetas `<?php`.

---

### Problema de arquitectura #4 — Sin transacciones en operaciones críticas
El descuento de stock y la eliminación del pedido se hacen en queries separadas sin `BEGIN TRANSACTION`. Si el servidor falla entre ambas operaciones, el pedido podría quedar eliminado sin haber descontado stock (o viceversa).

---

### Problema de arquitectura #5 — Contraseñas en texto plano
La tabla `usuario` soporta contraseñas en texto plano (comparación directa en `Login.php`). Aunque también soporta bcrypt, no hay validación que fuerce el uso de hashing.

---

## 14. Guía de Migración a Laravel

### 14.1 Modelos recomendados

```php
// app/Models/Product.php
class Product extends Model {
    protected $table = 'products';
    protected $fillable = ['name', 'type', 'category', 'price', 'stock'];
}

// app/Models/Table.php (nueva entidad)
class Table extends Model {
    protected $table = 'tables';
    protected $fillable = ['number', 'capacity', 'status'];
    // status: 'free' | 'occupied'
}

// app/Models/Order.php
class Order extends Model {
    protected $table = 'orders';
    protected $fillable = ['table_id', 'user_id', 'opened_at', 'closed_at', 'total'];
    public function items() { return $this->hasMany(OrderItem::class); }
    public function table() { return $this->belongsTo(Table::class); }
    public function user() { return $this->belongsTo(User::class); }
}

// app/Models/OrderItem.php
class OrderItem extends Model {
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price'];
    public function product() { return $this->belongsTo(Product::class); }
    public function order() { return $this->belongsTo(Order::class); }
    // unit_price: guardado en el momento del pedido para conservar precio histórico
}
```

### 14.2 Migraciones recomendadas

```php
// tables: mesas del restaurante
Schema::create('tables', function (Blueprint $table) {
    $table->id();
    $table->unsignedTinyInteger('number')->unique(); // 1-40
    $table->unsignedSmallInteger('capacity')->default(4);
    $table->enum('status', ['free', 'occupied'])->default('free');
    $table->timestamps();
});

// products: catálogo de productos
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('type', 50);      // Bebida, Comida, Postre
    $table->string('category', 50);  // Gaseosa, Pizza, etc.
    $table->decimal('price', 10, 2);
    $table->unsignedInteger('stock')->default(0);
    $table->boolean('active')->default(true);
    $table->timestamps();
});

// orders: pedidos (uno por mesa abierta + histórico de cerrados)
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('table_id')->constrained();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->decimal('total', 10, 2)->default(0);
    $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');
    $table->timestamp('opened_at')->useCurrent();
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();
});

// order_items: ítems de cada pedido
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->unsignedInteger('quantity');
    $table->decimal('unit_price', 10, 2); // precio al momento del pedido
    $table->timestamps();
});
```

### 14.3 Rutas recomendadas

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/', [TableController::class, 'index']);               // grilla de mesas
    Route::get('/tables/{table}/order', [OrderController::class, 'show']);  // pedido de mesa
    Route::post('/orders', [OrderController::class, 'store']);        // crear pedido
    Route::post('/orders/{order}/items', [OrderItemController::class, 'store']); // agregar ítem
    Route::put('/orders/{order}/items/{item}', [OrderItemController::class, 'update']); // actualizar cantidad
    Route::delete('/orders/{order}/items/{item}', [OrderItemController::class, 'destroy']); // eliminar ítem
    Route::delete('/orders/{order}', [OrderController::class, 'cancel']); // borrar pedido
    Route::post('/orders/{order}/close', [OrderController::class, 'close']); // cerrar + ticket
    Route::get('/tickets/{order}', [TicketController::class, 'show']); // ver/imprimir ticket
    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);
});

// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tables', [TableController::class, 'apiIndex']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/orders/{order}/items', [OrderItemController::class, 'store']);
    // ...
});
```

### 14.4 Servicio de cierre de mesa (transacción correcta)

```php
// app/Services/OrderService.php
class OrderService {
    public function closeOrder(Order $order): void {
        DB::transaction(function () use ($order) {
            // 1. Descontar stock
            foreach ($order->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            // 2. Calcular total
            $total = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);

            // 3. Marcar como cerrado
            $order->update([
                'status' => 'closed',
                'total' => $total,
                'closed_at' => now(),
            ]);

            // 4. Liberar mesa
            $order->table->update(['status' => 'free']);
        });
    }
}
```

### 14.5 Consideraciones adicionales para la migración

- **Ticket de impresión:** Usar una vista Blade con CSS de impresora térmica. Generar PDF opcional con `barryvdh/laravel-dompdf`.
- **Reportes:** Usar Query Builder de Laravel con scopes o repositorios. Considerar exportar a Excel con `maatwebsite/excel`.
- **Autenticación:** Usar `laravel/breeze` o `laravel/fortify`. Eliminar el bypass de campos vacíos.
- **Roles:** Implementar `spatie/laravel-permission` para manejar el campo `nivel` de la tabla `usuario`.
- **WebSockets:** Reemplazar el auto-refresh de 30s con `laravel-echo` + `laravel-reverb` para actualización en tiempo real del estado de mesas.
- **Validación:** Usar Form Requests para validar inputs del servidor (cantidad > 0, stock suficiente, etc.).
- **API:** Usar Resources y Response de Laravel para retornar JSON consistente.
- **Historial:** Nunca eliminar registros de `order_items`. Sólo cambiar el estado de `Order` a `closed`.

---

*Documento generado el 21/04/2026 para la migración del sistema "Los Troncos" a Laravel.*
