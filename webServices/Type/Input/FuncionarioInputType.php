<?php
namespace Orpyca\webService\Type\Input;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\InputObjectType;

class FuncionarioInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'email' => [
                    'type' => Types::email(),
                    'description' => 'Correo Electronico'
                ],

                'documento' => [
                    'type' => Types::id(),
                    'description' => 'Numero de documento'
                ],

            ]

        ];

        parent::__construct($config);

    }
}