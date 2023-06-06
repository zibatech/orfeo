<?php
require_once '../vendor/autoload.php';

use \Orpyca\webService\Type\QueryType;
use \Orpyca\webService\Type\MutationType;
use \Orpyca\webService\Types;
use \Orpyca\webService\AppContext;
use \GraphQL\Type\Schema;
use \GraphQL\GraphQL;
use \GraphQL\Error\FormattedError;
use \GraphQL\Error\DebugFlag;

$ruta_raiz = '..';

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/Historico.php");
include_once("$ruta_raiz/include/tx/Expediente.php");
include_once("$ruta_raiz/include/tx/Tx.php");
include_once("$ruta_raiz/include/tx/Radicacion.php");
include_once("$ruta_raiz/class_control/Municipio.php");

// Disable default PHP error reporting - we have better one for debug mode (see below)
ini_set('display_errors', 1);

$debug = DebugFlag::NONE;
$dbdebug = false;
if (!empty($_GET['debug'])) {
    set_error_handler(function ($severity, $message, $file, $line) use (&$phpErrors) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
    $dbdebug = true;
}

try {
    $serverUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") .
        "://" . $_SERVER['SERVER_NAME'] .
        (!empty($_SERVER['SERVER_PORT']) ? ":" . $_SERVER['SERVER_PORT'] : "") .
        $_SERVER['REQUEST_URI'];

    $dbx = new ConnectionHandler($ruta_raiz);

    $objOrfeo = [
        'dbx' => $dbx,
        'hist' => new Historico($dbx),
        'tmp_mun' => new Municipio($dbx),
        'rad' => new Radicacion($dbx),
        'expe' => new Expediente($dbx)
    ];

    $appContext = new AppContext($objOrfeo, $dbdebug);
    $appContext->rootUrl = $serverUrl;
    $appContext->request = $_REQUEST;

    // Parse incoming query and variables
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true) ?: [];
    } else {
        $data = $_REQUEST;
    }

    $data += ['query' => null, 'variables' => null];

    if (null === $data['query']) {
        $data['query'] = '{hello}';
    }

    // GraphQL schema to be passed to query executor:
    $schema = new Schema([
        'query' => new QueryType(),
        'mutation' => new MutationType(),
        'typeLoader' => function ($name) {
            return Types::byTypeName($name, true);
        }
    ]);

    $result = GraphQL::executeQuery(
        $schema,
        $data['query'],
        null,
        $appContext,
        (array)$data['variables']
    );

    $output = $result->toArray($debug);
    $httpStatus = 200;
} catch (\Exception $error) {
    $httpStatus = 500;
    $output['errors'] = [
        FormattedError::createFromException($error, $debug)
    ];
}

header('Content-Type: application/json', true, $httpStatus);
echo json_encode($output);
