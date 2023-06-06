<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class TipoDocumental
{
    public $id;

    public $nombre;

    public $termino;

    public $listRadicado;

    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }

}