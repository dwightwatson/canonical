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
        // Force https when canonical.secure is set to true
        if ($this->isIncorrectScheme($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }
        
        // Redirect to canonical.host when current host is different and 
        // current host is not set in canonical.ignore
        if ($this->isIncorrectHost($request)) {
            return redirect(
                $request->getScheme().'://'.config('canonical.host').$request->getRequestUri(),
                301
            );
        }
        
        return $next($request);
    }

    /**
     * Determine whether the request has the incorrect host and current 
     * host is not set in canonical.ignore.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isIncorrectHost($request)
    {
        if (config('canonical.host') !== null 
            && $request->getHost() !== config('canonical.host') 
            && !in_array($request->getHost(), config('canonical.ignore'))
        ) {
            return true;
        }
        return false;
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
}
