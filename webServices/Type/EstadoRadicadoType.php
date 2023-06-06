<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class EstadoRadicadoType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Muestra el tramite de un radicado y que elementos tiene y en que parte del proceso esta',
            'fields' => function() {
                return [

                    'radicado' => [
                        'type' => Types::nonNull(Types::id()),
                        'description' => 'Numero del radicado',
                    ],


                    'estado' => [
                        'type' => Types::nonNull(Types::string()),
                        'description' => 'Descripción del estado del documento',
                    ],

                    'anexos' => [
                        'type' => Types::int(),
                        'description' => 'Numero de anexos que lo conforman',
                    ],

                    'creador' => [
                        'type' => Types::nonNull(Types::string()),
                        'description' => 'Funcionario que genero el documento',
                    ],

                    'dependenciaCreador' => [
                        'type' => Types::nonNull(Types::string()),
                        'description' => 'Nombre de la dependencia a la cual pertenece el creador del documento',
                    ],

                    'fechaRadicacionRespuesta' => [
                        'type' => Types::nonNull(Types::string()),
                        'description' => 'Fecha en la que el documento fue creado',
                    ],

                    'radicadoRespuesta' => [
                        'type' => Types::id(),
                        'description' => 'Login del usuario que creo el anexo.',
                    ],

                ];
            }
        ];
        parent::__construct($config);
    }


}