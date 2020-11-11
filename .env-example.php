<?php

return [
    'DEVELOPMENT_MODE' => FALSE,
    'HTTPS_MODE_MODE' => FALSE,
    'RESTRICT_MODE' => FALSE,

    # Database Configs
    'DBHOST' => '',
    'DBUSER' => '',
    'DBPASS' => '',
    'DBNAME' => '',

    'ROOT' => 'admin',
    '__CONTEXTPATH__'=> dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR,
    'UPLOAD_DIR' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'uploads',
    'UPLOAD_DIRNAME' => 'uploads',
    
    'MAIL_CONFIG' => [
        'php_mailer_host' => '',
        'php_mailer_username' => '',
        'php_mailer_password' => ''
    ],
    'REPLY_MAIL' => '',
    'REPLY_NAME' => '',
    
    'PER_PAGE_LIMIT' => 20,
    
    'TEMPLATE_DIR' => 'assets'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR,
    'LOGIN_TEMPLATE' => 'login-primary',

    'SENTRY_LOGS'=> FALSE,
    'SENTRY_DNS' => ''
];
