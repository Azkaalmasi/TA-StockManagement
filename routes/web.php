<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\InStockController;
use App\Http\Controllers\OutStockController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DamagedStockController;

 
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

 
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::middleware(['auth', 'role:admin,superadmin,manager'])->get('/', [DashboardController::class, 'index'])->name('dashboard');

// Route view-only
Route::middleware(['auth', 'role:admin,superadmin,manager'])->group(function () {
    // Produk view
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Barang Masuk view
    Route::get('/in-stocks-index', [InStockController::class, 'index'])->name('in-stocks.index');

    // Barang Keluar view
    Route::get('/out-stocks-index', [OutStockController::class, 'index'])->name('out-stocks.index');

    //Barang Rusak View
    Route::get('/damaged-stocks/index', [DamagedStockController::class, 'index'])->name('damaged-stocks.index');

    //Export PDF
      Route::get('/products/{id}/export-pdf', [App\Http\Controllers\ProductController::class, 'exportPdf'])->name('products.export.pdf');
});

// Route   CRUD 
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    // Produk CRUD
    Route::get('/products-create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products-create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Barang Masuk 
    Route::get('/in-stocks', [InStockController::class, 'create'])->name('in-stocks.create');
    Route::post('/in-stocks', [InStockController::class, 'store'])->name('in-stocks.store');
    Route::post('/in-stocks/batch', [InStockController::class, 'batchStore'])->name('in-stocks.batchStore');
    Route::post('/in-stocks/preview-excel', [InStockController::class, 'previewExcel'])->name('in-stocks.previewExcel');

    // Barang Keluar 
    Route::get('/out-stocks', [OutStockController::class, 'create'])->name('out-stocks.create');
    Route::post('/out-stocks', [OutStockController::class, 'store'])->name('out-stocks.store');
    Route::post('/out-stocks/batch', [OutStockController::class, 'batchStore'])->name('out-stocks.batchStore');
    Route::post('/out-stocks/preview-excel', [OutStockController::class, 'previewExcel'])->name('out-stocks.previewExcel');
    Route::get('/out-stocks/getbatches', [OutStockController::class, 'getBatches'])->name('out-stocks.getBatches');

    // Damaged Stocks
    Route::get('/damaged-stocks', [DamagedStockController::class, 'create'])->name('damaged-stocks.create');
    Route::post('/damaged-stocks', [DamagedStockController::class, 'store'])->name('damaged-stocks.store'); // optional if needed
    Route::post('/damaged-stocks/batch', [DamagedStockController::class, 'batchStore'])->name('damaged-stocks.batchStore');
    Route::get('/damaged-stocks/getbatches', [DamagedStockController::class, 'getBatches'])->name('damaged-stocks.getBatches');
    
    // Kategori
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Distributor
    Route::resource('manufacturers', ManufacturerController::class)->except(['show']);


});

// Superadmin only
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});
