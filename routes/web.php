<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\TestController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProductController;



use App\Http\Controllers\FortuneController;

Route::middleware(['admin.auth'])->group(function () {

    Route::get('/', [UserController::class, 'index'])->name('list');
    Route::get('/random_list', [UserController::class, 'random'])->name('random_list');


    Route::get('/test', [TestController::class, 'show'])->name('test.show');
    Route::post('/test', [TestController::class, 'submit'])->name('test.submit');
    Route::get('/test/result/{team}', [TestController::class, 'result'])->name('test.result');


    Route::get('/votes', [VoteController::class, 'index'])->name('votes.index');
    Route::get('/votes/create', [VoteController::class, 'create'])->name('votes.create');
    Route::get('/votes/{voteUrl}/result', [VoteController::class, 'result'])->name('votes.result');
    Route::get('/votes/{voteUrl}/add-points', [VoteController::class, 'addPointsForm'])->name('votes.addPointsForm');
    Route::post('/votes/{voteUrl}/add-points', [VoteController::class, 'addPoints'])->name('votes.addPoints');
    Route::resource('products', ProductController::class);
});

Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
Route::get('/votes/success', [VoteController::class, 'success'])->name('votes.success');
Route::get('/votes/{voteUrl}', [VoteController::class, 'show'])->name('votes.show');
Route::post('/votes/{voteUrl}/authenticate', [VoteController::class, 'authenticate'])->name('votes.authenticate');
Route::get('/votes/{voteUrl}/vote/{userId}', [VoteController::class, 'vote'])->name('votes.vote');
Route::post('/votes/{voteUrl}/vote/{userId}', [VoteController::class, 'submitVote'])->name('votes.submitVote');


Route::get('/fortune', [FortuneController::class, 'index'])->name('fortune');
Route::post('/fortune/catch', [FortuneController::class, 'catch'])->name('fortune.catch');


use App\Http\Controllers\AdminAuthController;

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
