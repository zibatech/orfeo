<?php
namespace Orpyca\webService\Type;
//Tabla en al base de dato sgd_sbrd_subserierd
use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class SubSerieType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Funcionario o Empleado registrados en la aplicación que hacen parte activa del proceso documental',
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Types::id(),
                        'description' => 'Identificación de la sbu-serie documental',
                    ],

                    'nombre' => [
                        'type' => Types::string(),
                        'description' => 'Descripción',
                    ],

                    'procedimiento' => [
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

                    'serie' => [
                        'type' => Types::serie(),
                        'description' => 'Relacion con la serie documental',
                    ],

                ];
            }
        ];
        parent::__construct($config);
    }


}