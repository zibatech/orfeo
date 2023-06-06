<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;
use Orpyca\webService\Data\Funcionario;

class FuncionarioType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Funcionario o Empleado registrados en la aplicación que hacen parte activa del proceso documental',
            'fields' => function() {
                return [
                    'email' => [
                        'type' => Types::email(),
                        'description' => 'Correo previamente registrado en la aplicación',
                    ],

                    'login' => [
                        'type' => Types::string(),
                        'description' => 'Nombre asignado en la aplicación',
                    ],

                    'codusuario' => [
                        'type' => Types::int(),
                        'description' => 'Numero de identificación del usuario en la aplicación',
                    ],

                    'nivel' => [
                        'type' => Types::int(),
                        'description' => 'Asignación de nivel de seguridad a los radicados que se generan esta
                        comprendido entre 1 y 5 siendo 1 el mas bajo. Si un usuario es 1 y el radicado es dos este
                        no podra ser leido',
                    ],

                    'dependencia' => [
                        'type' => Types::int(),
                        'description' => 'Código de la dependencia a la que pertenece el usuario',
                    ],

                    'documento' => [
                        'type' => Types::int(),
                        'description' => 'Numero de identificación del usuario como cedula, pasaporte, etc',
                    ],

                    'nombre' => [
                        'type' => Types::string(),
                        'description' => 'Nombre completo con apellidos del funcionario o empleado',
                    ],

                    'nombre_dependencia' => [
                        'type' => Types::string(),
                        'description' => 'Grupo en la jerarquia intitucional al cual pertenece',
                    ],

                    'fieldWithError' => [
                        'type' => Types::string(),
                        'resolve' => function() {
                            throw new \Exception("This is error field");
                        }
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }


}