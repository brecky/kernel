<?php

namespace Orchestra\Http;

use Closure;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Contracts\Http\RouteManager as RouteManagerContract;

abstract class RouteManager implements RouteManagerContract
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Application router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Route handler implementation.
     *
     * @var \Orchestra\Http\RouteResolver
     */
    protected $resolver;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app, RouteResolver $resolver = null)
    {
        if (is_null($resolver)) {
            $resolver = new RouteResolver($app);
        }

        $this->app      = $app;
        $this->router   = $this->resolveApplicationRouter($app);
        $this->resolver = $resolver;
    }

    /**
     * Resolve application router.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return mixed
     */
    protected function resolveApplicationRouter(Application $app)
    {
        return $app->make('router');
    }

    /**
     * Return route group dispatch for a package/app.
     *
     * @param  string  $name
     * @param  string  $default
     * @param  array|\Closure  $attributes
     * @param  \Closure|null  $callback
     *
     * @return array
     */
    public function group($name, $default, $attributes = [], Closure $callback = null)
    {
        $route = $this->route($name, $default);

        if ($attributes instanceof Closure) {
            $callback   = $attributes;
            $attributes = [];
        }

        $attributes = array_merge($attributes, $route->group());

        if (! is_null($callback)) {
            $this->router->group($attributes, $callback);
        }

        return $attributes;
    }

    /**
     *  Return locate handles configuration for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return array
     */
    public function locate($path, array $options = [])
    {
        return $this->resolver->locate($path, $options);
    }

    /**
     *  Return handles URL for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return string
     */
    public function handles($path, array $options = [])
    {
        return $this->resolver->to($path, $options);
    }

    /**
     *  Return if handles URL match given string.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function is($path)
    {
        return $this->resolver->is($path);
    }

    /**
     * Get installation status.
     *
     * @return bool
     */
    abstract public function installed();

    /**
     * Get application status.
     *
     * @return string
     */
    public function mode()
    {
        return $this->resolver->mode();
    }

    /**
     * Get extension route.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Orchestra\Contracts\Extension\RouteGenerator
     */
    public function route($name, $default = '/')
    {
        return $this->resolver->route($name, $default);
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  string  $path
     * @param  mixed   $listener
     *
     * @return void
     */
    public function when($path, $listener)
    {
        return $this->whenOn($path, RouteMatched::class, $listener);
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  string  $path
     * @param  string  $on
     * @param  mixed   $listener
     *
     * @return void
     */
    public function whenOn($path, $on, $listener)
    {
        $events   = $this->app->make('events');
        $listener = $events->makeListener($listener);

        $events->listen($on, function (...$payloads) use ($events, $listener, $path) {
            if ($this->is($path) && $this->installed()) {
                $listener($events, $payloads);
            }
        });
    }
}
