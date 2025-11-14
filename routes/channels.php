<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private channel cho thông báo của từng user
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private channel cho từng role
Broadcast::channel('role.{role}', function ($user, $role) {
    return $user->role === $role;
});

// Public channel cho thông báo chung
Broadcast::channel('notifications.public', function ($user) {
    return true; // Tất cả user đã login đều có thể nghe
});

