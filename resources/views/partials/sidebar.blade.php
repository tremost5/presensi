<?php
$role = session()->get('role_id');

if ($role == 1) {
    echo view('partials/sidebar_superadmin');
} elseif ($role == 2) {
    echo view('partials/sidebar_admin');
} elseif ($role == 3) {
    echo view('partials/sidebar_guru');
}
