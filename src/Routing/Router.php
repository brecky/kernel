<?php

namespace Orchestra\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

class Router extends BaseRouter
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public function auth()
    {
        // Authentication Routes...
        $this->get('login', 'Auth\AuthenticateController@show');
        $this->post('login', 'Auth\AuthenticateController@attempt');
        $this->get('logout', 'Auth\DeauthenticateController@logout');

        $this->get('register', 'Auth\RegisterController@show');
        $this->post('register', 'Auth\RegisterController@store');
    }

    /**
     * Register the typical password reset routes for an application.
     *
     * @return void
     */
    public function password()
    {
        // Password Reset Routes...
        $this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
        $this->post('password/reset', 'Auth\PasswordController@reset');
        $this->get('password/email', 'Auth\PasswordController@showLinkRequestForm');
        $this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     *
     * @return void
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(BaseResourceRegistrar::class)) {
            $registrar = $this->container->make(BaseResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }
}
