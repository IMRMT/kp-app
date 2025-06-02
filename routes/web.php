<?php

//use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\NotabeliController;
use App\Http\Controllers\satuanController;
use App\Http\Controllers\NotajualController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfiltokoController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\TipeProdukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Session;

// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

// Route::get('/home', function () {
//     return view('home.index');
// })->name('home');

Route::post('/logout', function () {
    // Forget cart session
    Session::forget('cart');

    // Log the user out
    Auth::logout();
    return redirect()->route('welcome');
})->name('logout');



// Route::get('/register', function () {
//     // dd(Auth::user()->tipe_user);
//     if (Auth::user()->tipe_user == 'admin') {
//         return view('auth.register');
//     }
//     abort(403, 'No authorization');
// })->middleware('auth')->name('register');

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::resource('users', UserController::class);
    Route::get('user/uploadImage/{id}', [UserController::class, 'uploadImage']);
    Route::post('user/simpanImage', [UserController::class, 'simpanImage']);
    Route::get('/user', [UserController::class, 'index'])->name('user');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('distributors', DistributorController::class);
    Route::resource('notajuals', NotajualController::class);
    Route::resource('notabelis', NotabeliController::class);
    Route::resource('satuans', SatuanController::class);
    Route::resource('tipeproduks', TipeProdukController::class);
    Route::resource('produks', ProdukController::class);
    Route::resource('parcels', ParcelController::class);
    Route::resource('gudangs', GudangController::class);
    Route::resource('users', UserController::class);
    Route::resource('profiltokos', ProfiltokoController::class);
    Route::get('/distributor', [DistributorController::class, 'index'])->name('distributor');
    Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan');
    Route::get('/tipeproduk', [TipeProdukController::class, 'index'])->name('tipeproduk');
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
    Route::get('/parcel', [ParcelController::class, 'index'])->name('parcel');
    Route::get('parcel/komposisi/{id}', [ParcelController::class, 'komposisi'])->name('parcels.komposisi');
    Route::get('parcel/notaparcel', [ParcelController::class, 'notaParcel'])->name('parcels.notaParcel');
    Route::delete('parcel/destroyKomposisi/{parcels_id}/{produks_id}', [ParcelController::class, 'destroyKomposisi'])->name('parcels.destroyKomposisi');
    Route::get('produk/terimaBatch/{id}', [ProdukController::class, 'terimaBatch'])->name('produks.terimaBatch');
    Route::put('produk/updateTerimaBatch/{id}', [ProdukController::class, 'updateTerimaBatch'])->name('produks.updateTerimaBatch');
    Route::get('produk/editBatch/{id}', [ProdukController::class, 'editBatch'])->name('produks.editBatch');
    Route::put('produk/updateBatch/{id}', [ProdukController::class, 'updateBatch'])->name('produks.updateBatch');
    Route::delete('produk/destroyBatch/{id}', [ProdukController::class, 'destroyBatch'])->name('produks.destroyBatch');
    Route::delete('produk/destroyTerima/{id}', [ProdukController::class, 'destroyTerima'])->name('produks.destroyTerima');
    Route::get('produk/daftarTerima', [ProdukController::class, 'daftarTerima'])->name('produks.daftarTerima');
    Route::get('produk/uploadImage/{id}', [ProdukController::class, 'uploadImage']);
    Route::post('produk/simpanImage', [ProdukController::class, 'simpanImage']);
    Route::get('/gudang', [GudangController::class, 'index'])->name('gudang');
    Route::get('/user/profile', [UserController::class, 'detail'])->name('profile');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [ProdukController::class, 'homeProduk'])->name('homeProduk');
    Route::get('produk/batch/{id}', [ProdukController::class, 'batch'])->name('produks.batch');
    Route::get('produk/batch/{id}/print', [ProdukController::class, 'print'])->name('produks.print');
    Route::post('/notajuals/cart', [NotajualController::class, 'addToCart'])->name('notajuals.cart');
    Route::delete('/notajuals/cart/delete/{id}', [NotajualController::class, 'deleteFromCart'])->name('notajualscart.delete');
    Route::get('/notajuals/{id}/print', [NotajualController::class, 'print'])->name('notajuals.print');
    Route::post('/notabelis/cart', [NotabeliController::class, 'addToCart'])->name('notabelis.cart');
    Route::delete('/notabelis/cart/delete/{id}', [NotabeliController::class, 'deleteFromCart'])->name('notabeliscart.delete');
    Route::post('/notabelis/beliProdukBaru', [NotabeliController::class, 'beliProdukBaru'])->name('notabelis.beliProdukBaru');
    Route::get('/notabelis/{id}/print', [NotabeliController::class, 'print'])->name('notabelis.print');
    Route::get('profiltoko/uploadImage/{id}', [ProfiltokoController::class, 'uploadImage']);
    Route::post('profiltoko/simpanImage', [ProfiltokoController::class, 'simpanImage']);
    Route::get('transaksi/report/reportPenjualan', [NotajualController::class, 'report'])->name('notajuals.report');
    Route::get('transaksi/report/reportPembelian', [NotabeliController::class, 'report'])->name('notabelis.report');
    Route::get('/transaksi', function () {
        return view('transaksi.tipe');
    })->name('transaksi');
});


Auth::routes(['register' => false]);
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
Route::get('/', [ProdukController::class, 'welcomeProduk'])->name('welcome');
Route::get('/profiltoko', [ProfiltokoController::class, 'index'])->name('profiltoko');
