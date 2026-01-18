<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders', function ($user) {
    if (! $user) {
        return false;
    }

    // Usa la misma lógica de autorización que Filament/Policies.
    // Si el usuario puede ver pedidos en el panel, puede suscribirse.
    if (method_exists($user, 'hasRole') && $user->hasRole((string) config('filament-shield.super_admin.name', 'super_admin'))) {
        return true;
    }

    return $user->can('viewAny', Order::class);
});
