<?php
include_once("../include/db/ConnectionHandler.php");
include_once("../config.php");
include_once("ORFEOConnect.php");


$serverUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"").
"://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$options = [
    'uri' => $serverUrl,
];

$server = new \Laminas\Soap\Server(null, $options);

if (isset($_GET['wsdl'])) {

    $soapAutoDiscover = new \Laminas\Soap\AutoDiscover(new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
    $soapAutoDiscover->setBindingStyle(array('style' => 'document'));
    $soapAutoDiscover->setOperationBodyStyle(array('use' => 'literal'));
    $soapAutoDiscover->setClass('ORFEOConnect');
    $soapAutoDiscover->setUri($serverUrl);

    header("Content-Type: text/xml");
    echo $soapAutoDiscover->generate()->toXml();

} else {
    $soap = new \Laminas\Soap\Server($serverUrl . '?wsdl');
    $soap->setObject(new \Laminas\Soap\Server\DocumentLiteralWrapper(new ORFEOConnect()));
    $soap->handle();
}
