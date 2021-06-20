<?php

namespace App\Services;

class MicroService
{
    protected $app;
    protected $module;

    public $request;
    public $requestURI;

    public function __construct($app)
    {
        $this->app = $app;
        $this->request = request();
    }

    public function router($callback = false)
    {
        if (isset($this->app['router']->getRoutes()[$this->request->method() . $this->request->getPathInfo()])) {
            return false;
        }

        if (!$module = $this->module()) return false;

        $this->module = $module;
        $this->requestURI = $this->requestURI();
        $this->requestHeaders = [];

        $method = $this->app['request']->method();
        $pathInfo = $this->app['request']->getPathInfo();
        $function = strtolower($method);

        if ($callback) $callback($this);

        $client = $this->app['http']->withHeaders(['accept' => 'Application/json']);
        if ($token = $this->request->bearerToken()) $client->withToken($token);

        $url  = $this->requestURI;
        $data = $this->request->all();

        $this->app['router']->addRoute($method, $pathInfo, function() use ($client, $function, $url, $data) {
            try {
                $response = $client->{$function}($url, $data);
                return response($response->getBody(), $response->getStatusCode(), $response->headers());
            }
            catch (\Throwable $t) {
                $info = env('APP_DEBUG', false) ? " [". $t->getMessage() .']' : '';
                return abort(504, "END POINT [SERVER] TIMEOUT.". $info);
            }
        });
    }

    protected function module()
    {
        $modules = explode(',', strtolower(config('microservice.modules', '')));
        $prefix = config('microservice.prefix', '');
        $prefix = str_starts_with($prefix, '/') ? $prefix : "/$prefix" ;

        $arr = explode('/', request()->getPathInfo(), 4);

        if ($module = $arr[($prefix == "/") ? 1 : 2])
        {
            if (in_array($module, $modules)) return $module;
        }

        return null;
    }

    protected function requestURI()
    {
        $module = $this->module ?? $this->module();
        return $this->requestDomain($module) . $this->requestPath();
    }

    protected function requestDomain($module)
    {
        $domains = env('MS_HOST_'. strtoupper($module)) ?? [];

        $domains = gettype($domains) === 'string' ? explode(',', $domains) : $domains;

        if ( sizeof($domains) > 0) return $domains[array_rand($domains)];

        throw new \Exception('Domain Microservice['. $module .'] undefined.');
    }

    protected function requestPath()
    {

        $module = $this->module ?? $this->module();

        if ($module) {
            $prefix = config('microservice.prefix', '');
            $prefix = str_starts_with($prefix, '/') ? $prefix : "/$prefix" ;

            $prefixModule = env("MS_PREFIX_". strtoupper($module), null) ?? config('microservice.prefix_module', '');
            $prefixModule = str_starts_with($prefixModule, '/') ? $prefixModule : "/$prefixModule";

            return $prefixModule . str_ireplace( "$prefix/$module", '', request()->getPathInfo());
        }

        throw new \Exception('Module Microservice['. $module .'] undefined.');


    }
}
