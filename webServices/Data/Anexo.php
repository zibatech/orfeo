<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class Anexo
{
    public $anexCodigo;

    public $anexRadiNume;

    public $anexTipo;

    public $anexTamano;

    public $anexSoloLect;

    public $anexCreador;

    public $anexDesc;

    public $anexNumero;

    public $anexNombArchivo;

    public $anexEstado;

    public $sgdRemDestino;

    public $anexFechAnex;

    public $anexBorrado;


    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}