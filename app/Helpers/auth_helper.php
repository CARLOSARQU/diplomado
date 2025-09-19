<?php

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return session()->has('user_id');  // ✅ YA ESTÁ BIEN
    }
}

if (!function_exists('user')) {
    function user(): ?array
    {
        if (!isLoggedIn()) return null;
        
        $userModel = new \App\Models\UserModel();
        return $userModel->find(session('user_id'));  // ✅ YA ESTÁ BIEN
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        $rol = session('rol_id');
        return in_array($rol, [1, 2]); // 1 superadmin, 2 admin
    }
}

if (!function_exists('getRoleLabel')) {
    function getRoleLabel($roleId): string
    {
        return match((int)$roleId) {
            1 => 'Superadmin',
            2 => 'Admin', 
            3 => 'Usuario',
            default => 'Invitado',
        };
    }
}