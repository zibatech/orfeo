<?php
namespace Orpyca\webService\Type\Enum;

use GraphQL\Type\Definition\EnumType;

class TipoDireccionEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'description' => 'Tipos de registros de direcciones',
            'values' => [

                'remitente' => [
                    'value' => 1,
                    'description' => 'Usuario que realiza la radicación directamente'
                ],

                'apoderado' => [
                    'value' => 5,
                    'description' => 'Realiza la radicación para un tercero'
                ],

                'empresa_privada' => [
                    'value' => 6,
                    'description' => 'Realiza la radicación para un tercero'
                ],

                'empresa_publica' => [
                    'value' => 6,
                    'description' => 'Realiza la radicación para un tercero'
                ],
            ]
        ];

        parent::__construct($config);

    }
}