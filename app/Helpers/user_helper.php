<?php

if (! function_exists('profile_url')) {
    function profile_url(): string
    {
        $role = session('role_id');

        return match ($role) {
            1 => base_url('dashboard/superadmin/profil'),
            2 => base_url('dashboard/admin/profil'),
            3 => base_url('guru/profil'),
            default => base_url('logout'),
        };
    }
}

if (! function_exists('avatar_url')) {
    function avatar_url(): string
    {
        $role = session('role_id');
        $foto = session('foto') ?: 'default.png';

        $folder = match ($role) {
            1, 2 => 'uploads/admin/',
            3    => 'uploads/guru/',
            default => 'uploads/guru/',
        };

        return base_url($folder . $foto);
    }
}
