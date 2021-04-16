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
        if ($this->redirectSecure($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }
        
        // Redirect to canonical.host when current host is different and 
        // current host is not set in canonical.ignore
        if ($redirect_host = $this->redirectHost($request)) {
            return redirect(
                $request->getScheme().'://'.$redirect_host.$request->getRequestUri(),
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
     * @return mixed
     */
    protected function redirectHost($request)
    {
        // Determine configured canonical host
        $canonical_host = (config('canonical.host') === true) 
            ? parse_url(config('app.url'), PHP_URL_HOST) 
            : config('canonical.host');

        if ($canonical_host !== null 
            && $request->getHost() !== $canonical_host 
            && !in_array($request->getHost(), config('canonical.ignore'))
        ) {
            return $canonical_host;
        }
        return false;
    }

    /**
     * Determine whether the request has the incorrect scheme.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function redirectSecure($request)
    {
        return config('canonical.secure') && ! $request->secure();
    }
}
