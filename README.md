# Los Troncos — Sistema POS / KDS

Sistema de punto de venta para restaurante con panel de cocina en tiempo real.

**Stack:** Laravel 12, PHP 8.2, MySQL, Laravel Reverb (WebSockets), Vite, Tailwind CSS

---

## Roles

| Rol     | Acceso                                         |
|---------|------------------------------------------------|
| admin   | Todo: mesas, cocina, reportes, productos, usuarios |
| mozo    | Mesas y comandas                               |
| cocina  | Panel KDS (actualización de estados en tiempo real) |

---

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configurar DB en .env
php artisan migrate
npm run build
```

## Uso

```bash
# Terminal 1
php artisan serve

# Terminal 2 (WebSockets)
php artisan reverb:start
```

Acceder en `http://127.0.0.1:8000`

---

© Restaurante Los Troncos
