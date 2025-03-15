protected $middlewareGroups = [
    'web' = [
        AppHttpMiddlewareEncryptCookiesclass,
        IlluminateCookieMiddlewareAddQueuedCookiesToResponseclass,
        IlluminateSessionMiddlewareStartSessionclass,
        IlluminateViewMiddlewareShareErrorsFromSessionclass,
        AppHttpMiddlewareVerifyCsrfTokenclass,   Bu satırın olduğundan emin olun
        IlluminateRoutingMiddlewareSubstituteBindingsclass,
    ],
     ...
];