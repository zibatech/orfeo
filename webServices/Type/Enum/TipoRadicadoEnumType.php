<?php
namespace Orpyca\webService\Type\Enum;

use GraphQL\Type\Definition\EnumType;

class TipoRadicadoEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'description' => 'Tipos de radicado existentes',
            'values' => [

                'entrada' => [
                    'value' => 2,
                    'description' => 'Documentos que llegan a la entidad'
                ],

                'salida' => [
                    'value' => 1,
                    'description' => 'Documentos que salen de la entidad'
                ],

                'memorando' => [
                    'value' => 3,
                    'description' => 'Documentos de circulación interna'
                ],

                'PQRD' => [
                    'value' => 9,
                    'description' => 'Documentos que llegan a la entidad con tiempos especiales'
                ],
            ]
        ];

        parent::__construct($config);

    }
}
