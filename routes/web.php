<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RAGController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes - Landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

// Authentication Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->name('logout');
});

// Registration Routes
Route::controller(RegisterController::class)->group(function () {
    Route::get('register', 'showRegistrationForm')->name('register');
    Route::post('register', 'register');
});

// Email Verification Routes
Route::prefix('email')->middleware('auth')->group(function () {
    Route::get('verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification-resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Protected Routes (require authentication and email verification)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bot Management Routes
    Route::resource('bots', BotController::class);
    Route::post('bots/{bot}/regenerate-api-key', [BotController::class, 'regenerateApiKey'])->name('bots.regenerate-api-key');

    // Channel Management Routes
    Route::prefix('bots/{bot}/channels')->group(function () {
        Route::get('/', [ChannelController::class, 'index'])->name('bots.channels.index');
        Route::post('/', [ChannelController::class, 'store'])->name('bots.channels.store');
        Route::put('/{channel}', [ChannelController::class, 'update'])->name('bots.channels.update');
        Route::delete('/{channel}', [ChannelController::class, 'destroy'])->name('bots.channels.destroy');
    });

    // Chat Routes
    Route::prefix('bots/{bot}')->group(function () {
        Route::get('/live-chat', [ChatController::class, 'liveChat'])->name('bots.live-chat');
        Route::get('/history', [ChatController::class, 'history'])->name('bots.history');
        Route::post('/send-reply', [ChatController::class, 'sendAdminReply'])->name('bots.send-reply');
        Route::get('/poll', [ChatController::class, 'pollMessages'])->name('bots.poll');
        Route::get('/sessions-list', [ChatController::class, 'getSessionsList'])->name('bots.sessions-list');
        Route::post('/clear-session', [ChatController::class, 'clearSession'])->name('bots.clear-session');
        Route::post('/clear-all-chats', [ChatController::class, 'clearAllChats'])->name('bots.clear-all-chats');
        Route::get('/export-session', [ChatController::class, 'exportSession'])->name('bots.export-session');
        Route::get('/unread-count', [ChatController::class, 'unreadCount'])->name('bots.unread-count');
        Route::get('/session-details/{sessionId}', [ChatController::class, 'sessionDetails'])->name('bots.session-details');
    });

    // RAG (Knowledge Base) Routes
    Route::prefix('bots/{bot}/rag')->group(function () {
        Route::get('/', [RAGController::class, 'index'])->name('bots.rag.index');
        Route::post('/', [RAGController::class, 'store'])->name('bots.rag.store');
        Route::delete('/{ragDocument}', [RAGController::class, 'destroy'])->name('bots.rag.destroy');
    });

    // Statistics Routes
    Route::get('bots/{bot}/statistics', [StatisticsController::class, 'index'])->name('bots.statistics');

    // Leads Routes
    Route::prefix('bots/{bot}/leads')->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('bots.leads');
        Route::get('/export', [LeadController::class, 'export'])->name('bots.leads.export');
        Route::get('/{lead}', [LeadController::class, 'show'])->name('bots.leads.show');
        Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('bots.leads.destroy');
        Route::post('/bulk-delete', [LeadController::class, 'bulkDelete'])->name('bots.leads.bulk-delete');
    });

    // User Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('profile');
        Route::put('/', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    });
});

// API Routes for Embed and Webhooks (Public)
Route::prefix('api/saas/v1')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->group(function () {
    // Embed JavaScript
    Route::get('embed.js', [WebhookController::class, 'serveEmbed'])->name('embed.js');
    Route::get('session-status', [WebhookController::class, 'sessionStatus'])->name('api.session-status');

    // Chat API Endpoints
    Route::post('chat', [WebhookController::class, 'chat'])->name('api.chat');
    Route::get('poll', [WebhookController::class, 'poll'])->name('api.poll');
    Route::post('capture-lead', [WebhookController::class, 'captureLead'])->name('api.capture-lead');

    // Social Channel Webhooks (Dynamic routes)
    Route::match(['get', 'post'], 'fb-webhook', [WebhookController::class, 'handleFacebook'])->name('api.webhook.fb');
    Route::match(['get', 'post'], 'zalo-webhook', [WebhookController::class, 'handleZalo'])->name('api.webhook.zalo');
    Route::match(['get', 'post'], 'tiktok-webhook', [WebhookController::class, 'handleTikTok'])->name('api.webhook.tiktok');
    Route::match(['get', 'post'], 'shopee-webhook', [WebhookController::class, 'handleShopee'])->name('api.webhook.shopee');
    Route::match(['get', 'post'], 'zalo-personal-webhook', [WebhookController::class, 'handleZaloPersonal'])->name('api.webhook.zalo-personal');
    Route::match(['get', 'post'], 'whatsapp-webhook', [WebhookController::class, 'handleWhatsApp'])->name('api.webhook.whatsapp');
});

// Fallback route for 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
