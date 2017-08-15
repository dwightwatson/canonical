<?php

namespace Watson\Canonical;

use Closure;
use Illuminate\Contracts\Config\Repository;

class CanonicalMiddleware
{
    /**
     * The config repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->config->has('canonical.host')) {
            if ($this->isIncorrectHost($request)) {
                if (in_array($request->header('Host'), (array) $this->config->get('canonical.ignore'))) {
                    return;
                }

                return $this->redirect($request);
            }

            if ($this->isIncorrectScheme($request)) {
                return $this->redirect($request, true);
            }
        }

        return $next($request);
    }

    /**
     * Determine whether the request has the incorrect host.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isIncorrectHost($request)
    {
        return $request->header('Host') !== $this->config->get('canonical.host');
    }

    /**
     * Determine whether the request has the incorrect scheme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isIncorrectScheme($request)
    {
        return $this->config->get('canonical.secure') && ! $request->secure();
    }

    /**
     * Redirect the request to the canonical host, secure if neccessary.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool $secure
     * @return \Illuminate\Http\Redirect
     */
    protected function redirect($request, $secure = false)
    {
        $headers = ['Host' => $this->config->get('canonical.host')];

        return redirect()->to($request->path(), 301, $headers, $secure);;
    }
}
