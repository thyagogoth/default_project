<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
header('Content-Type: text/html; charset=UTF-8', true);

/**
 * Composer autoload
 */
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Init Uteis lib
 */
use Arrilot\DotEnv\DotEnv;

/**
 * Load environment variables
 */
use System\Uteis;
DotEnv::load(__DIR__ . '/.env.php');

/**
 * Definition of Execution Mode
 * TRUE: Ambiente de produção
 * FALSE: Ambiente de desenvolvimento e teste
 */
DEFINE('DEVELOPMENT_MODE', DotEnv::get('DEVELOPMENT_MODE'));
DEFINE('HTTPS_MODE', DotEnv::get('HTTPS_MODE'));

DEFINE('DISPLAY_ERRORS', DotEnv::get('DISPLAY_ERRORS'));

ini_set('display_errors', 0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
if (DEVELOPMENT_MODE == true && DISPLAY_ERRORS == true) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
/** END definition of execution mode */

/**
 * Start sentry logging
 */
DEFINE('SENTRY_LOGS', DotEnv::get('SENTRY_LOGS'));
if (SENTRY_LOGS == true) {
    Sentry\init(['dsn' => DotEnv::get('SENTRY_DNS')]);
    try {
        $this->functionFailsForSure();
    } catch (\Throwable $exception) {
        Sentry\captureException($exception);
    }
}

/**
 * Database Configuration
 */
DEFINE('DBHOST', DotEnv::get('DBHOST'));
DEFINE('DBUSER', DotEnv::get('DBUSER'));
DEFINE('DBPASS', DotEnv::get('DBPASS'));
DEFINE('DBNAME', DotEnv::get('DBNAME'));
/** END Definicoes do Banco de Dados */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'business.php';

/**
 * Default definitions
 */
/**
 * Defines the ROOT dir for the application
 */
DEFINE('ROOT', DotEnv::get('ROOT'));

/**
 * Defines the BASE URL for the application
 */
DEFINE('SERVER', Uteis::getNameServer());

/**
 * Defines Mode for execution URLs
 */
DEFINE('RESTRICT_MODE', DotEnv::get('RESTRICT_MODE'));

DEFINE('SITE_URL', str_replace(DIRECTORY_SEPARATOR . ROOT, '', Uteis::getNameServer()));

/**
 * Defines the MAIN URL for a WEBSITE
 */
DEFINE('TEMPLATE_DIR', DotEnv::get('TEMPLATE_DIR'));

/**
 * Defines the Template HTML used in Login Page
 */
DEFINE('LOGIN_TEMPLATE', DotEnv::get('LOGIN_TEMPLATE'));

/**
 * session_id() | php
 */
DEFINE('SESSION_ID', session_id());

DEFINE('__CONTEXTPATH__', DotEnv::get('__CONTEXTPATH__'));

/**
 * Defines initial directory for uploads
 */
DEFINE('UPLOAD_DIR', DotEnv::get('UPLOAD_DIR'));

/**
 * Defines uploads dir name
 */
DEFINE('UPLOAD_DIRNAME', DotEnv::get('UPLOAD_DIRNAME'));

/**
 * ITEMS PER PAGE ON PAGINATION
 * */
DEFINE('PER_PAGE_LIMIT', DotEnv::get('PER_PAGE_LIMIT'));

/**
 * Definitions of E-mail configs
 */
DEFINE('MAIL_CONFIG', DotEnv::get('MAIL_CONFIG'));
DEFINE('REPLY_MAIL', DotEnv::get('REPLY_MAIL'));
DEFINE('REPLY_NAME', DotEnv::get('REPLY_NAME'));

/**
 * FUNCTIONS
 */
$_SESSION['system_configs'] = unserialize(getSystemConfigs());
foreach ($_SESSION['system_configs'] as $id => $itemConfig) {
    if (!empty($_SESSION['system_configs'][$id])) {
        if ($id == 'info_parcelas_sem_juros') {
            if ($_SESSION['system_configs'][$id] == 1 || $_SESSION['system_configs'][$id] == 0) {
                unset($_SESSION['system_configs'][$id]);
            }
        }
        if (!is_numeric($id)) {
            if (!empty($_SESSION['system_configs'][$id])) {
                DEFINE('config_' . $id, $_SESSION['system_configs'][$id]);
            }
        }
    }
}

function requireModule($module, $file = null)
{
    if (empty($file)) {
        $file = 'business';
    }
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . $file . '.php';
}

function getSystemConfigs()
{
    $config = ConfigBusiness::lastConfig();
    if (!empty($config['logotipo_topo'] && file_exists('uploads' . DIRECTORY_SEPARATOR . 'logotipo' . DIRECTORY_SEPARATOR . $config['logotipo_topo']))) {
        $config['logotipo_topo'] = 'uploads' . DIRECTORY_SEPARATOR . 'logotipo' . DIRECTORY_SEPARATOR . '' . $config['logotipo_topo'];
    } else {
        $config['logotipo_topo'] = 'assets' . DIRECTORY_SEPARATOR . 'versions' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo-wide.png';
    }
    if (!empty($config['logotipo_rodape'])) {
        $config['logotipo_rodape'] = 'uploads' . DIRECTORY_SEPARATOR . 'logotipo' . DIRECTORY_SEPARATOR . '' . $config['logotipo_rodape'];
    } else {
        $config['logotipo_rodape'] = 'assets' . DIRECTORY_SEPARATOR . 'versions' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo-wide-white.png';
    }
    return serialize($config);
}

function dd($var, $exit = true)
{
    echo "<pre>Retorno: " . PHP_EOL;
    print_r($var);
    echo "</pre>";
    if ($exit) {
        die();
    }
}
