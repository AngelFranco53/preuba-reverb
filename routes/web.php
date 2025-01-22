<?php

use App\Events\MessageSent;
use App\Events\UpdateOrder;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/users', function () {
    return Inertia::render('Users', [
        'users' => User::query()
            ->where('id', '!=', auth()->id())
            ->get()
    ]);
})->middleware(['auth'])->name('users');

Route::get('/chat/{friend}', function (User $friend) {
    return Inertia::render('Chats', [
        'currentUser' => auth()->user(),
        'friend' => $friend
    ]);
})->middleware(['auth'])->name('chat');

Route::get('/messages/{friend}', function (User $friend) {
    return ChatMessage::query()
        ->where(function ($query) use ($friend) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $friend->id);
        })
        ->orWhere(function ($query) use ($friend) {
            $query->where('sender_id', $friend->id)
                ->where('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->orderBy('id', 'asc')
        ->get();
})->middleware(['auth']);

Route::post('/messages/{friend}', function (User $friend) {
    $message = ChatMessage::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $friend->id,
        'text' => request()->input('message')
    ]);
 
    broadcast(new MessageSent($message));
 
    return $message;
});


Route::get('/orders', function () {
    return Inertia::render('Orders', [
        'orders' => Order::all()
    ]);
})->middleware(['auth'])->name('orders');

Route::get('/orders/{order}', function (Order $order) {
    return Inertia::render('OrderUpdate', [
        'order' => $order
    ]);
})->middleware(['auth'])->name('order.update');

Route::post('/orders/{order}', function (Order $order) {
    
    $order->update(request()->input('order')); 

    broadcast(new UpdateOrder());

    return redirect()->route('orders');
})->middleware(['auth'])->name('order.update');

Route::get('/orders-get', function() {
    return Order::all();
});

require __DIR__.'/auth.php';
