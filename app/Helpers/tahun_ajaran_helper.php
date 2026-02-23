<?php

use App\Models\TahunAjaranModel;

if (!function_exists('tahunAjaranAktif')) {
    function tahunAjaranAktif()
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $model = new TahunAjaranModel();
        $cache = $model->getActive();

        return $cache;
    }
}

if (!function_exists('tahunAjaranIdAktif')) {
    function tahunAjaranIdAktif()
    {
        $ta = tahunAjaranAktif();
        return $ta['id'] ?? null;
    }
}

if (!function_exists('isTahunAjaranAktif')) {
    function isTahunAjaranAktif(): bool
    {
        $ta = tahunAjaranAktif();
        return $ta && ($ta['is_active'] ?? 0) == 1;
    }
}
