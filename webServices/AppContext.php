<?php
namespace Orpyca\webService;

/**
 * Instance available in all GraphQL resolvers as 3rd argument
 */
class AppContext
{
    /**
     * @var string
     */
    public $rootUrl;

    /**
     * @var User
     */
    public $viewer;

    /**
     * @var mixed
     */
    public $request;

    /**
     * @ORFEOConnect
     */
    public $oc;


    function __construct($objOrfeo, $debug) {
        $ruta_raiz = '../';
        require($ruta_raiz."processConfig.php");
        $this->oc = new ORFEOConnect($objOrfeo, $debug);
        $this->oc->setContentPath($CONTENT_PATH);
        $this->oc->setAmbiente($ambiente);
        $this->oc->setSizeRad(15);
        /**
        $this->oc->setGseConfig(array(
            "GSE_API_USER" => $GSE_API_USER,
            "GSE_API_PASS" => $GSE_API_PASS,
            "GSE_FIRMA_USER" => $GSE_FIRMA_USER,
            "GSE_FIRMA_PASS" => $GSE_FIRMA_PASS
        ));
        **/
    }

}

