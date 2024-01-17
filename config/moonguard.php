<?php

return [
    'user' => [
        /*
         * The user model to use.
         */
        'model' => \App\Models\User::class,
    ],
    'site' => [
        /*
         * The site model to use.
         */
        'model' => \App\Models\Site::class,
    ],
    'uptime_check' => [
        /*
         * Enable or disable uptime checks globally.
         */
        'enabled' => true,

        /*
         * The uptime check model to use.
         */
        'model' => \App\Models\UptimeCheck::class,

        /*
         * The number of consecutive failures before a notification should be sent.
         */
        'notify_failed_check_after_consecutive_failures' => 1,

        /*
         * How often a notification is resent after the uptime check fails
        */
        'resend_uptime_check_failed_notification_every_minutes' => 5,
    ],
    'ssl_certificate_check' => [
        /*
         * Enable or disable ssl certificate checks globally.
         */
        'enabled' => true,

        /*
         * The ssl certificate check model to use.
         */
        'model' => \App\Models\SslCertificateCheck::class,

        /*
         * The number of days before a certificate expires to send a notification.
         */
        'notify_expiring_soon_if_certificate_expires_within_days' => 7,
    ],
    'exception_deletion' => [
        /*
         * Enable or disable exception deletion globally.
         */
        'enabled' => false,

        /*
         * The age in minutes of the exceptions to delete.
         */
        'delete_exceptions_older_than_days' => 7,
    ],
    'exceptions' => [
        /*
         * Enable or disable exception logging globally.
         */
        'enabled' => true,

        /*
         * The number of minutes that should be waited before sending a notification about exception log group updates.
         */
        'notify_time_between_group_updates_in_minutes' => 15,

        'exception_log' => [
            /*
             * The exception log model to use.
             */
            'model' => \App\Models\ExceptionLog::class,
        ],

        'exception_log_group' => [
            /*
             * The exception log group model to use.
             */
            'model' => \App\Models\ExceptionLogGroup::class,
        ],
    ],
    'routes' => [
        /*
         * The prefix for the MoonGuard API routes.
         */
        'prefix' => 'moonguard/api',

        /*
         * The middleware for the MoonGuard API routes.
         */
        'middleware' => 'throttle:api',
    ],
    'events' => [
        /*
         * The events that can be listened for.
         * You can add your own listeners here.
         */
        'listen' => [
            \App\Events\UptimeCheckRecoveredEvent::class => [
                \App\Listeners\UptimeCheckRecoveredListener::class,
            ],
            \App\Events\UptimeCheckFailedEvent::class => [
                \App\Listeners\UptimeCheckFailedListener::class,
            ],
            \App\Events\RequestTookLongerThanMaxDurationEvent::class => [
                \App\Listeners\RequestTookLongerThanMaxDurationListener::class,
            ],
            \App\Events\SslCertificateExpiresSoonEvent::class => [
                \App\Listeners\SslCertificateExpiresSoonListener::class,
            ],
            \App\Events\SslCertificateCheckFailedEvent::class => [
                \App\Listeners\SslCertificateCheckFailedListener::class,
            ],
            \App\Events\ExceptionLogGroupCreatedEvent::class => [
                \App\Listeners\ExceptionLogGroupCreatedListener::class,
            ],
            \App\Events\ExceptionLogGroupUpdatedEvent::class => [
                \App\Listeners\ExceptionLogGroupUpdatedListener::class,
            ],
        ],
    ],
    'notifications' => [
        /*
         * The notification channels that are used by default.
         */
        'channels' => ['mail'],

        'slack' => [
            /*
             * The Slack webhook url setup.
             */
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
        ],
    ],
];
