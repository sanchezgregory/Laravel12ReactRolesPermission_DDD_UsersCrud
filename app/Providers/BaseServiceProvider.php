<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    /**
     * Registra una implementación de interfaz y la envuelve con un decorador.
     *
     * @param string $interface      El contrato o interfaz a registrar.
     * @param string $implementation La clase de implementación base.
     * @param string $decorator      La clase que actuará como decorador.
     * @return void
     */
    protected function decorate(string $interface, string $implementation, string $decorator): void
    {
        // 1. Registra la implementación base.
        $this->app->bind($interface, $implementation);

        // 2. Extiende la implementación para envolverla con el decorador.
        $this->app->extend($interface, function ($originalService, $app) use ($decorator) {
            return $app->make($decorator, ['decoratedService' => $originalService]);
        });
    }
}