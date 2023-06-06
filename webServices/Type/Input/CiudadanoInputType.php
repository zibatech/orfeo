<?php
namespace Orpyca\webService\Type\Input;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\InputObjectType;

class CiudadanoInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [

                'tipoDocumento' => [
                    'type' => Types::nonNull(Types::documentoEnum()),
                    'description' => 'Identificación de que tipo de documento se crea'
                ],

                'documento' => [
                    'type' => Types::string(),
                    'description' => 'Consecutivo del documento'
                ],

                'email' => [
                    'type' => Types::email(),
                    'description' => 'Correo electronico'
                ],

                'nombre' => [
                    'type' => Types::nonNull(Types::string()),
                    'defaultValue' => 'ANONIMO',
                    'description' => 'Nombre que representara al documento para el usuario o entidad'
                ],

                'apellido' => [
                    'type' => Types::string(),
                    'description' => 'Apellido que representara al documento para el usuario'
                ],

                'direccion' => [
                    'type' => Types::string(),
                    'description' => 'Apellido que representara al documento para el usuario'
                ],

                'telefono' => [
                    'type' => Types::string(),
                    'description' => 'Apellido que representara al documento para el usuario'
                ],
            ]

        ];

        parent::__construct($config);

    }
}