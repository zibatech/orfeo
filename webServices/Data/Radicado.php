<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class Radicado
{
    public $radicado;

    public $usuarioActual;

    public $codigoUsuarioActual;

    public $dependenciaActual;

    public $codigoDependenciaActual;

    public $tipoDocumental;

    public $asunto;

    public $cuentaInterna;

    public $fechaRadicación;

    public $radicadoPadre;

    public $archivo_b64;

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}