<?php

namespace Watson\Canonical;

use Closure;

class CanonicalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('canonical.host') !== null) {
            if ($this->isIncorrectHost($request)) {
                if (in_array($request->header('Host'), (array) config('canonical.ignore'))) {
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
        return $request->header('Host') !== config('canonical.host');
    }

    /**
     * Determine whether the request has the incorrect scheme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isIncorrectScheme($request)
    {
        return config('canonical.secure') && ! $request->secure();
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
        $headers = ['Host' => config('canonical.host')];

        return redirect()->to($request->path(), 301, $headers, $secure);;
    }
}
