<?php

namespace App\Providers;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!defined('FCPATH')) {
            define('FCPATH', public_path() . DIRECTORY_SEPARATOR);
        }

        Store::macro('set', function (array|string $key, mixed $value = null): void {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    $this->put($k, $v);
                }
                return;
            }

            $this->put($key, $value);
        });

        Store::macro('destroy', function (): void {
            $this->flush();
            $this->invalidate();
        });

        Store::macro('setFlashdata', function (string $key, mixed $value): void {
            $this->flash($key, $value);
        });

        Store::macro('getFlashdata', function (string $key, mixed $default = null): mixed {
            return $this->get($key, $default);
        });

        Store::macro('regenerate', function (bool $destroy = false): void {
            request()->session()->regenerate($destroy);
        });

        Gate::define('role', static function ($user = null, int|string $roleId): bool {
            return (string) session('role_id') === (string) $roleId;
        });
    }
}
