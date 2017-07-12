Canonical, a simple host redirector for Laravel
===============================================

Need your Laravel app to redirect from the root domain to `www.` or the other way around? Need your Laravel app to redirect all visitors to a HTTPS connection?

This is a small library for Laravel that provides a simple middleware that provides basic redirects for Laravel apps. You simply set the canonical host for your app and whether you want requests to be secured by default and Canonical will handle the rest.

This prevents duplicate content by ensuring that all visitors to your site are redirected to the correct host and upgraded to a secure connection if available.

You can configure this sort of stuff up in your web server as well, but there is an ease to keeping it inside your app so it's all kept in the same place.

## Installation
First run `composer require watson/canonical`.

Next, add `Watson\Canonical\CanonicalMiddleware` to your `app/Http/Kernel.php` file where you'd like it to run.

Please note that Canonical only supports Laravel 5.5 at this time.

## Configuration
You can configure Canonical through environment variables or by publishing the configuration file to your app with `php artisan vendor:publish --tags=config`.

Take a look at the configuration file that was created for you, `config/canonical.php`. Here you can set the default host name that you want to use for your app.

If your app receives a request from another host it will perform a permanent redirect to the canonical host you've set here, keeping the request path.

You can also opt to secure all requests too, so an insecure request will automatically be redirects to HTTPS if your site supports it.

Finally you are able to opt-out certain hosts if you don't want to redirect them - for example, an `api.` subdomain. Add any domains you wish to the `ignore` array.
