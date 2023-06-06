<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class TrdType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Funcionario o Empleado registrados en la aplicación que hacen parte activa del proceso documental',
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Types::id(),
                        'description' => 'Identificación de la relacion serie, sub-serie, tipo documental',
                    ],

                    'serie' => [
                        'type' => Types::serie(),
                        'description' => 'Nombre asignado en la aplicación',
                    ],

                    'subSerie' => [
                        'type' => Types::subSerie(),
                        'description' => 'Numero de identificación del usuario en la aplicación',
                    ],

                    'tipoDocumental' => [
                        'type' => Types::tipoDocumental(),
                        'description' => 'Asignación de nivel de seguridad a los radicados que se generan esta
                        comprendido entre 1 y 5 siendo 1 el mas bajo. Si un usuario es 1 y el radicado es dos este
                        no podra ser leido',
                    ],
                ];
            }
        ];
        parent::__construct($config);
    }


}