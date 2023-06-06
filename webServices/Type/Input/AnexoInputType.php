<?php
namespace Orpyca\webService\Type\Input;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\InputObjectType;

class AnexoInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'radicado' => [
                    'type' => Types::nonNull(Types::id()),
                    'description' => 'Numero de radicado'
                ],

                'archivoBase64' => [
                    'type' => Types::nonNull(Types::string()),
                    'description' => 'Documento codificado en base64'
                ],

                'nombreArchivo' => [
                    'type' => Types::nonNull(Types::string()),
                    'description' => 'Nombre que representara al documento para el usuario'
                ],

                'funcionario' => [
                    'type' => Types::nonNull(Types::funcionarioInput()),
                    'description' => 'Usuario quien registra la acción'
                ],

                'descripcion' => [
                    'type' => Types::string(),
                    'description' => 'Texto que explica el contenido documento anexado ',
                    'dafaultValue' => 'Archivo creado de forma dinamica desde el generador de servicios'
                ],
            ]

        ];

        parent::__construct($config);

    }
}