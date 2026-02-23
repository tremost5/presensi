<?php

if (!function_exists('kelasMap')) {
    function kelasMap()
    {
        return [
            1 => ['label' => 'PG',  'color' => 'secondary'],
            2 => ['label' => 'TKA', 'color' => 'info'],
            3 => ['label' => 'TKB', 'color' => 'primary'],
            4 => ['label' => '1',   'color' => 'success'],
            5 => ['label' => '2',   'color' => 'success'],
            6 => ['label' => '3',   'color' => 'success'],
            7 => ['label' => '4',   'color' => 'warning'],
            8 => ['label' => '5',   'color' => 'warning'],
            9 => ['label' => '6',   'color' => 'danger'],
        ];
    }
}

if (!function_exists('kelasLabel')) {
    function kelasLabel($kelasId)
    {
        return kelasMap()[$kelasId]['label'] ?? '-';
    }
}

if (!function_exists('kelasBadge')) {
    function kelasBadge($kelasId)
    {
        $map = kelasMap();
        if (!isset($map[$kelasId])) {
            return '<span class="badge badge-secondary">-</span>';
        }

        return '<span class="badge badge-' . $map[$kelasId]['color'] . '">' .
            $map[$kelasId]['label'] .
        '</span>';
    }
}
