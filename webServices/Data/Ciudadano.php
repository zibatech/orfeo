<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class Ciudadano
{
    public $tipoDocumento;

    public $documento;

    public $email;

    public $nombre;

    public $apellido;

    public $direccion;

    public $telefono;

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}