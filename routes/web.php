<?php

use App\Events\StatusRekamUpdate;
use App\Http\Controllers\Auth\OtpAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\IcdController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranObatController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\RekamController;
use App\Http\Controllers\RekamGigiController;
use App\Http\Controllers\RekamPemeriksaanController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\PenggunaController; // Add this use statement
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class, 'page_login'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('login.auth');
Route::get('/register', [AuthController::class, 'page_register'])->name('register.page');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/login/otp', [OtpAuthController::class, 'showLoginForm'])->name('otp.login');
Route::post('/login/otp/generate', [OtpAuthController::class, 'generateOtp'])->name('otp.generate');
Route::post('/login/otp/verify', [OtpAuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/login/verify-otp', [AuthController::class, 'verifyOtp'])->name('login.verify-otp');

Route::get('test', function () {
    StatusRekamUpdate::dispatch("5","REG002","INI TEST AJA","http://sss","25 05 1993");
	// event(new App\Events\StatusRekamUpdate("082240300501"));
	return "Event has been sent!";
});

Route::get('/odontogram/{id}', [RekamGigiController::class, 'odontogram'])->name('odontogram');

Route::get('/loaddata', [RekamPemeriksaanController::class, 'insertToTableNew'])->name('loaddata');

Route::group(['middleware' => 'auth'], function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/poliklinik', [PoliController::class, 'index'])->name('poli');
    Route::post('/poliklinik', [PoliController::class, 'store'])->name('poli.store');
    Route::post('/poliklinik/{id}/update', [PoliController::class, 'update'])->name('poli.update');
    Route::get('/poliklinik/{id}/delete', [PoliController::class, 'delete'])->name('poli.delete');

    Route::get('/getDokter', [DokterController::class, 'getDokter'])->name('getDokter');

    Route::get('/dokter', [DokterController::class, 'index'])->name('dokter');
    Route::post('/dokter/store', [DokterController::class, 'store'])->name('dokter.store');
    Route::post('/dokter/{id}/update', [DokterController::class, 'update'])->name('dokter.update');
    Route::get('/dokter/{id}/delete', [DokterController::class, 'delete'])->name('dokter.delete');
    Route::post('/dokter/{id}/gantipassword', [DokterController::class, 'updatepassword'])->name('dokter.gantipassword');

    Route::post('/gantipassword/{id}', [AuthController::class, 'updatepassword'])->name('gantipassword');
    Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas');
    Route::post('/petugas/store', [PetugasController::class, 'store'])->name('petugas.store');
    Route::post('/petugas/{id}/update', [PetugasController::class, 'update'])->name('petugas.update');
    Route::get('/petugas/{id}/delete', [DokterController::class, 'delete'])->name('petugas.delete');

    Route::get('/getNoRM', [PasienController::class, 'getLastRM'])->name('getNoRM');

    Route::get('/pasien', [PasienController::class, 'index'])->name('pasien');
    Route::get('/pasien/add', [PasienController::class, 'add'])->name('pasien.add');
    Route::get('/pasien/{id}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::get('/pasien/{id}/delete', [PasienController::class, 'delete'])->name('pasien.delete');
    Route::get('/pasien/json', [PasienController::class, 'json'])->name('pasien.json');
    Route::get('/pasien/{id}/file', [PasienController::class, 'file'])->name('pasien.file');

    Route::post('/pasien/store', [PasienController::class, 'store'])->name('pasien.store');
    Route::post('/pasien/{id}/update', [PasienController::class, 'update'])->name('pasien.update');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran');

    Route::get('/obat/json', [ObatController::class, 'data'])->name('obat.data');
    Route::get('/obat', [ObatController::class, 'index'])->name('obat');
    Route::post('/obat/store', [ObatController::class, 'store'])->name('obat.store');
    Route::post('/obat/{id}/update', [ObatController::class, 'update'])->name('obat.update');
    Route::get('/obat/{id}/delete', [ObatController::class, 'delete'])->name('obat.delete');

    Route::get('/icd/json', [IcdController::class, 'data'])->name('icd.data');
    Route::get('/icd', [IcdController::class, 'index'])->name('icd');
    Route::post('/icd/store', [IcdController::class, 'store'])->name('icd.store');
    Route::post('/icd/{id}/update', [IcdController::class, 'update'])->name('icd.update');
    Route::get('/icd/{id}/delete', [IcdController::class, 'delete'])->name('icd.delete');

    Route::get('/tindakan', [TindakanController::class, 'index'])->name('tindakan');
    Route::post('/tindakan/store', [TindakanController::class, 'store'])->name('tindakan.store');
    Route::post('/tindakan/{id}/update', [TindakanController::class, 'update'])->name('master.tindakan.update');
    Route::get('/tindakan/{id}/delete', [TindakanController::class, 'delete'])->name('tindakan.delete');

    // Routes for Pengguna (User Management) - accessible only via controller middleware check
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');
    Route::post('/pengguna/{user}/update', [PenggunaController::class, 'update'])->name('pengguna.update'); // Use POST for simplicity

    Route::get('/rekam', [RekamController::class, 'index'])->name('rekam');
    Route::get('/rekam/add', [RekamController::class, 'add'])->name('rekam.add');
    Route::get('/rekam/{id}/edit', [RekamController::class, 'edit'])->name('rekam.edit');

    Route::post('/rekam/pasie/store', [RekamController::class, 'store'])->name('rekam.store');
    Route::get('/rekam/pasien/{id}', [RekamController::class, 'detail'])->name('rekam.detail');

    Route::get('/rekam/{id}/delete', [RekamController::class, 'delete'])->name('rekam.delete');
    Route::post('/rekam/pasien/{id}/update', [RekamController::class, 'update'])->name('rekam.update');

    Route::get('/rekam/gigi/{id}', [RekamGigiController::class, 'index'])->name('rekam.gigi.add');
    Route::post('/rekam/gigi/{id}/store', [RekamGigiController::class, 'store'])->name('rekam.gigi.store');
    Route::get('/rekam/gigi/{id}/delete', [RekamGigiController::class, 'delete'])->name('rekam.gigi.delete');
    Route::get('/rekam/gigi/{id}/odontogram', [RekamGigiController::class, 'odontogram'])->name('rekam.gigi.odontogram');

    Route::post('/rekam/pemeriksaan/update', [RekamPemeriksaanController::class, 'pemeriksaan'])->name('pemeriksaan.update');
    Route::post('/rekam/tindakan/update', [RekamPemeriksaanController::class, 'tindakan'])->name('tindakan.update');
    Route::post('/rekam/diagnosa/update', [RekamPemeriksaanController::class, 'diagnosa'])->name('diagnosa.update');
    Route::post('/rekam/resep-obat/update', [RekamPemeriksaanController::class, 'resep'])->name('resep.update');

    Route::get('/rekam/diagnosa/delete/{id}', [RekamPemeriksaanController::class, 'diagnosa_delete'])->name('rekam.diagnosa.delete');

    Route::get('/rekam/status/{id}/{status}/update', [RekamController::class, 'rekam_status'])->name('rekam.status');


    Route::get('/rekam/pasien/resep', [RekamController::class, 'detail'])->name('rekam.upload');

    Route::post('/rekam/decrypt', [RekamController::class, 'decryptContent'])->name('rekam.decrypt');
    // Add this route with your other rekam routes
    Route::post('/rekam/decrypt-row', [RekamController::class, 'decryptRow'])->name('rekam.decrypt.row');

    Route::get('/obat/resep', [PengeluaranObatController::class, 'resep'])->name('obat.resep');
    Route::get('/obat/resep/pengeluaran/{id}', [PengeluaranObatController::class, 'pengeluaran'])->name('obat.pengeluaran');
    Route::post('/obat/pengeluaran/store', [PengeluaranObatController::class, 'store'])->name('obat.pengeluaran.store');
    Route::get('/obat/riwayat', [PengeluaranObatController::class, 'riwayat'])->name('obat.riwayat');
    Route::get('/obat/pengeluaran/{rekam_id}/pdf', [PengeluaranObatController::class, 'exportPdf'])->name('obat.pengeluaran.pdf');
    Route::post('/obat/pengeluaran/verify-password', [PengeluaranObatController::class, 'verifyPasswordAndExportPdf'])->name('obat.pengeluaran.verify-password');


    Route::get('/rekam/file/{id}/{type}', [RekamPemeriksaanController::class, 'file'])->name('pem.file');
    // Patient routes
    Route::delete('/pasien/{pasien}/file/{file}', [\App\Http\Controllers\PasienController::class, 'deleteFile'])->name('pasien.file.delete');
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/users', 'ChatController@users')->name('chat.users');
    Route::get('/chat/staff', 'ChatController@staffChat')->name('chat.staff');
    Route::get('/chat/messages', 'ChatController@messages')->name('chat.messages');
    Route::post('/chat/send', 'ChatController@send')->name('chat.send');
    Route::post('/chat/mark-read', 'ChatController@markAsRead')->name('chat.mark-read');
    Route::post('/chat/decrypt-message', 'ChatController@decryptMessage')->name('chat.decrypt-message');
});

Route::get('/chat/admin', function() {
    if (Auth::user()->role == 1) {
        return view('chat.admin');
    }
    abort(403);
})->middleware('auth')->name('chat.admin');


Route::post('/chat/mark-as-read', [ChatController::class, 'markAsRead'])->name('chat.markAsRead')->middleware('auth');
Route::post('/chat/decrypt-message', 'ChatController@decryptMessage')->name('chat.decrypt');


