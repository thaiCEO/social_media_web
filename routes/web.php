<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserPostController;
use App\Http\Controllers\User\CommentController;
use App\Http\Controllers\User\FriendshipController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\LikeController;
use App\Http\Controllers\User\PostController;
use App\Http\Controllers\User\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Middleware\RoleMiddleware;


Route::get('/search', function () {
    return Inertia::render('User/SearchAccount');
})->name('search');


Route::get('/' , function() {
    return redirect()->route('posts.index'); 
});


// ✅ PUBLIC HOMEPAGE for all visitors (guest or auth)
Route::get('/user', function () {
    return Inertia::render('User/HomePage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home.page'); 



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('User/HomePage');
    })->name('dashboard');
});



Route::get('/post', [PostController::class, 'index'])->name('posts.index');


Route::middleware('auth' )->group(function () {

    Route::post('/post', [PostController::class, 'store'])->name('post.store');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/posts/{post}/image/{index}', [PostController::class, 'showImage'])
    ->name('posts.image');

    Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');
    Route::post('/comments/{comment}/like', [CommentController::class, 'like'])->name('comment.like');
    Route::delete('/comments/{comment}/like', [CommentController::class, 'unlike'])->name('comment.unlike');

    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('/user/update-image', [UserController::class, 'updateImage'])->name('user.updateImage');
    Route::post('/user/update-cover-image', [UserController::class, 'updateCoverImage'])->name('user.updateCoverImage');

    Route::post('/posts/{post}/like', [LikeController::class, 'store'])->name('posts.like');
    Route::delete('/posts/{post}/like', [LikeController::class, 'destroy'])->name('posts.unlike');

    //friend request
    Route::get('/friend-requests', [FriendshipController::class, 'incomingRequests'])->name('friendship.incomingRequests');
    Route::post('/friend-request/send/{receiverId}', [FriendshipController::class, 'sendRequest'])->name('friendship.sendRequest');
    Route::delete('/friend-request/cancel/{receiverId}', [FriendshipController::class, 'cancelRequest'])->name('friendship.cancelRequest');
    Route::post('/friend-request/accept/{senderId}', [FriendshipController::class, 'acceptRequest'])->name('friendship.acceptRequest');
    Route::post('/friend-request/decline/{senderId}', [FriendshipController::class, 'declineRequest'])->name('friendship.declineRequest');
    Route::delete('/friend/remove/{friendId}', [FriendshipController::class, 'removeFriend'])->name('friendship.removeFriend');
    Route::get('/friends/search', [FriendshipController::class, 'search'])->name('friends.search');


});





Route::prefix('admin')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login.submit');

    // Protected routes
 Route::middleware(['auth', 'is_admin'])->group(function () {
            
      Route::get('dashboard', [DashboardController::class, 'index'])
          ->name('dashboard.index');
  
      // User routes
      Route::resource('user-post', UserPostController::class)->except(['store', 'update' , 'show']);


      // Protect IsAdmin List
      Route::get('is_admin', [UserPostController::class, 'showAdmin'])
          ->middleware('permission:users.view')
          ->name('user-post.is_Admin');
  
      // Protect store (create)
      Route::post('user-post', [UserPostController::class, 'store'])
          ->middleware('permission:users.create')
          ->name('user-post.store');
  
      // Protect update (edit)
      Route::put('user-post/{user_post}', [UserPostController::class, 'update'])
          ->middleware('permission:users.edit')
          ->name('user-post.update');
  
      // Protect show (show)
      Route::get('user-post/{user_post}', [UserPostController::class, 'show'])
          ->middleware('permission:users.view')
          ->name('user-post.show');
  
      Route::post('user/select-delete', [UserPostController::class, 'selectDelete'])
          ->name('users.select-delete');

      //logout 
      Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');
  
      // Role routes (ONLY accessible by admin role)
    
          Route::middleware(['role:Admin'])->group(function () {
          Route::resource('roles', RoleController::class);
          Route::post('roles/select-delete', [RoleController::class, 'selectDelete'])
              ->name('roles.select-delete');
      });
      });

});


