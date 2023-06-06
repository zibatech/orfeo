<?php
namespace Orpyca\webService\Type;
//Tabla en al base de dato sgd_srd_seriesrd

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class SerieType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Funcionario o Empleado registrados en la aplicación que hacen parte activa del proceso documental',
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Types::id(),
                        'description' => 'Identificación de la serie documental',
                    ],

                    'nombre' => [
                        'type' => Types::string(),
                        'description' => 'Descripción',
                    ],

                    'fechaInicio' => [
                        'type' => Types::string(),
                        'description' => 'Fecha de inicio, periodo de vigencia del documento',
                    ],

                    'fechaFin' => [
                        'type' => Types::string(),
                        'description' => 'Fecha de terinacion, periodo de vigencia del documento ',
                    ],

                ];
            }
        ];
        parent::__construct($config);
    }


}