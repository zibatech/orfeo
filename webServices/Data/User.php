<?php
namespace Orpyca\webService\Data;

use GraphQL\Utils\Utils;

class User
{
    public $id;

    public $email;


    public function __construct(array $data)
    {
        Utils::assign($this, $data);
    }
}
