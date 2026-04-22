<?php

use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('tables.index'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return auth()->user()->isCocina()
            ? redirect()->route('kitchen')
            : redirect()->route('tables.index');
    })->name('dashboard');

    // Mesas (mozo + admin)
    Route::middleware('role:admin,mozo')->group(function () {
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
        Route::get('/tables/{table}/order', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/api/tables/{table}/summary', [OrderController::class, 'summary'])->name('orders.summary');
        Route::get('/api/tables/statuses', [TableController::class, 'statuses'])->name('tables.statuses');
        Route::post('/orders/{order}/close', [OrderController::class, 'close'])->name('orders.close');
        Route::patch('/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::delete('/orders/{order}', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/tables/{table}/items', [OrderItemController::class, 'store'])->name('order-items.store');
        Route::put('/order-items/{item}', [OrderItemController::class, 'update'])->name('order-items.update');
        Route::patch('/order-items/{item}/note', [OrderItemController::class, 'updateNote'])->name('order-items.note');
        Route::delete('/order-items/{item}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
    });

    Route::middleware('role:admin,mozo')->get('/api/products', [ProductController::class, 'index'])->name('products.json');

    // Cocina (cocina + admin)
    Route::middleware('role:admin,cocina')->group(function () {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen');
        Route::put('/kitchen/items/{item}/status', [KitchenController::class, 'updateItemStatus'])->name('kitchen.item.status');
        Route::patch('/kitchen/orders/{order}/status', [KitchenController::class, 'updateOrderStatus'])->name('kitchen.order.status');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::delete('/reports/daily', [ReportController::class, 'clearDaily'])->name('reports.clearDaily');
        Route::delete('/reports/monthly', [ReportController::class, 'clearMonthly'])->name('reports.clearMonthly');
        Route::resource('admin/products', ProductAdminController::class)->names('admin.products');
        Route::resource('admin/users', AdminUserController::class)->names('admin.users');
    });

    // Tickets térmicos
    Route::get('/tickets/{order}',        [TicketController::class, 'show'])  ->name('tickets.show');
    Route::get('/tickets/{order}/raw',    [TicketController::class, 'raw'])   ->name('tickets.raw');
    Route::get('/tickets/{order}/escpos', [TicketController::class, 'escpos'])->name('tickets.escpos');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
