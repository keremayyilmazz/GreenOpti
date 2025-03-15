<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    // ... existing code ...

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'custom' => [  // Yeni eklenen kanal
            'driver' => 'single',
            'path' => storage_path('logs/custom.log'),
            'level' => 'debug',
            'formatter' => \Monolog\Formatter\JsonFormatter::class,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
            'formatter' => \Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
                'includeStacktraces' => true,
            ],
        ],

        // ... diğer mevcut kanallar ...

        'debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/debug.log'),
            'level' => 'debug',
            'days' => 14,
            'replace_placeholders' => true,
            'formatter' => \Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
                'includeStacktraces' => true,
            ],
        ],
    ],
];