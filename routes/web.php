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
use App\Http\Controllers\SortingTwoController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProductController;



use App\Http\Controllers\FortuneController;
use App\Http\Controllers\FortuneTwoController;
use App\Http\Controllers\MusicClipCardController;
use App\Http\Controllers\MusicClipController;

Route::middleware(['admin.auth'])->group(function () {

    Route::get('/', [UserController::class, 'index'])->name('list');
    Route::get('/random_list', [UserController::class, 'random'])->name('random_list');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}/users', [SessionController::class, 'users'])->name('sessions.users');
    Route::post('/sessions/{session}/users', [SessionController::class, 'addUser'])->name('sessions.users.store');
    Route::patch('/sessions/{session}/activate', [SessionController::class, 'activate'])->name('sessions.activate');
    Route::patch('/sessions/{session}/deactivate', [SessionController::class, 'deactivate'])->name('sessions.deactivate');

    Route::get('/test', [TestController::class, 'showTestForm'])->name('test.show');
    Route::post('/test', [TestController::class, 'handleTestSubmission'])->name('test.handle');
    Route::get('/test/result/{team}', [TestController::class, 'showTestResult'])->name('test.result');

    Route::get('/sorting-2', [SortingTwoController::class, 'show'])->name('sorting2.show');
    Route::post('/sorting-2', [SortingTwoController::class, 'handle'])->name('sorting2.handle');
    Route::get('/sorting-2/result/{team}', [SortingTwoController::class, 'result'])->name('sorting2.result');



    Route::get('/votes', [VoteController::class, 'index'])->name('votes.index');
    Route::get('/votes/create', [VoteController::class, 'create'])->name('votes.create');
    Route::get('/votes/{voteUrl}/result', [VoteController::class, 'result'])->name('votes.result');
    Route::get('/votes/{voteUrl}/add-points', [VoteController::class, 'addPointsForm'])->name('votes.addPointsForm');
    Route::post('/votes/{voteUrl}/add-points', [VoteController::class, 'addPoints'])->name('votes.addPoints');
    Route::patch('/products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quick-update');
    Route::resource('products', ProductController::class);

    Route::patch('/clip/cards/{musicClipCard}/quick-update', [MusicClipCardController::class, 'quickUpdate'])->name('music-clip-cards.quick-update');
    Route::resource('/clip/cards', MusicClipCardController::class)
        ->parameters(['cards' => 'musicClipCard'])
        ->except(['show'])
        ->names('music-clip-cards');
});

Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
Route::get('/votes/success', [VoteController::class, 'success'])->name('votes.success');
Route::get('/votes/{voteUrl}', [VoteController::class, 'show'])->name('votes.show');
Route::post('/votes/{voteUrl}/authenticate', [VoteController::class, 'authenticate'])->name('votes.authenticate');
Route::get('/votes/{voteUrl}/vote/{userId}', [VoteController::class, 'vote'])->name('votes.vote');
Route::post('/votes/{voteUrl}/vote/{userId}', [VoteController::class, 'submitVote'])->name('votes.submitVote');


Route::get('/fortune', [FortuneController::class, 'index'])->name('fortune');
Route::post('/fortune/catch', [FortuneController::class, 'catch'])->name('fortune.catch');
Route::get('/fortune-2', [FortuneTwoController::class, 'index'])->name('fortune.two');
Route::post('/fortune-2/catch', [FortuneTwoController::class, 'catch'])->name('fortune.two.catch');
Route::get('/music-clip', [MusicClipController::class, 'index'])->name('music.clip');
Route::post('/music-clip/catch', [MusicClipController::class, 'catch'])->name('music.clip.catch');


use App\Http\Controllers\AdminAuthController;

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


use App\Http\Controllers\QuestController;

Route::get('/quest', [QuestController::class, 'show'])->name('quest.show');
Route::post('/quest', [QuestController::class, 'handle'])->name('quest.handle');
Route::get('/quest/result/{position}', [QuestController::class, 'result'])->name('quest.result');
