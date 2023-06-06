<?php
namespace Orpyca\webService\Type;
//Tabla de base de datos sgd_tpr_tpdcumento
use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class TipoDocumentalType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Tipos documentales',
            'fields' => function() {
                return [

                    'id' => [
                        'type' => Types::id(),
                        'description' => 'Numero del consecutivo del tipo documental',
                    ],

                    'nombre' => [
                        'type' => Types::string(),
                        'description' => 'Nombre del tipo documental',
                    ],

                    'termino' => [
                        'type' => Types::int(),
                        'description' => 'Tiempo de respuesta al ser asignado al documento',
                    ],

                    'listRadicado' => [
                        'type' => Types::string(),
                        'description' => 'Listado de tipos de radicados a los cuales aplica el tipo documental',
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }
}