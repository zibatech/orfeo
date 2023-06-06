<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class Funcionario
{
    public $email;

    public $login;

    public $codusuario;

    public $nivel;

    public $dependencia;

    public $documento;

    public $nombre;

    public $nombre_dependencia;


    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}