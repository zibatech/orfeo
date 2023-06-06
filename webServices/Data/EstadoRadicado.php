<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class EstadoRadicado
{
    public $radicado;

    public $estado;

    public $anexos;

    public $creador;

    public $dependenciaCreador;

    public $fechaRadicacionRespuesta;

    public $radicadoRespuesta;

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}