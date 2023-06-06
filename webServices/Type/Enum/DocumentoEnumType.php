<?php
namespace Orpyca\webService\Type\Enum;

use GraphQL\Type\Definition\EnumType;

class DocumentoEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'description' => 'Tipos de documentos de identificación de los usuarios',
            'values' => [

                'CC' => [
                    'value' => 0,
                    'description' => 'Cedula'
                ],

                'TI' => [
                    'value' => 1,
                    'description' => 'Tarjeta de identidad'
                ],

                'CE' => [
                    'value' => 2,
                    'description' => 'Cedula de Extrangeria'
                ],

                'PA' => [
                    'value' => 3,
                    'description' => 'Pasaporte'
                ],

                'NI' => [
                    'value' => 4,
                    'description' => 'Numero de identificación tributaria'
                ],

                'RC' => [
                    'value' => 12,
                    'description' => ''
                ],

                'PJ' => [
                    'value' => 8,
                    'description' => ''
                ],

                'EO' => [
                    'value' => 9,
                    'description' => ''
                ],

                "RM" => [
                    'value' => 10,
                    'description' => ''
                ],

                "ANONIMO" => [
                    'value' => 11,
                    'description' => 'El usuario realiza el registro de forma anonima'
                ],
            ]
        ];

        parent::__construct($config);

    }
}