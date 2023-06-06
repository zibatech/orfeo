<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class RadicadoType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Radicado',
            'fields' => function() {
                return [

                    'radicado' => [
                        'type' => Types::id(),
                        'description' => 'Numero del consecutivo de radicación',
                    ],

                    'usuarioActual' => [
                        'type' => Types::string(),
                        'description' => 'Nombre del funcionario actual que tiene asignado el radicado',
                    ],

                    'codigoUsuarioActual' => [
                        'type' => Types::int(),
                        'description' => 'Codigo del funcionario actual que tiene asignado el radicado',
                    ],

                    'dependenciaActual' => [
                        'type' => Types::string(),
                        'description' => 'Nombre de la dependencia actual en la cual esta el radicado',
                    ],

                    'codigoDependenciaActual' => [
                        'type' => Types::int(),
                        'description' => 'Codigo de la dependencia actual en la cual esta el radicado',
                    ],

                    'tipoRadicado' => [
                        'type' => Types::int(),
                        'description' => 'Codigo del tipo de radicado como entrada 2 salida 1 memorando 3',
                    ],

                    'tipoDocumental' => [
                        'type' => Types::int(),
                        'description' => 'Codigo del tipo de documento asignado',
                    ],

                    'asunto' => [
                        'type' => Types::string(),
                        'description' => 'Descripción del radicado.',
                    ],

                    'cuentaInterna' => [
                        'type' => Types::string(),
                        'description' => 'Identificación del radicado si procede de otra entidad con otro consecutivo',
                    ],

                    'fechaRadicacion' => [
                        'type' => Types::string(),
                        'description' => 'Fecha en que fue creado el documento',
                    ],

                    'medioRecepcion' => [
                        'type' => Types::int(),
                        'description' => 'Codigo de identificación del medio de recipción como fisico,
                        correo, red social',
                    ],

                    'radicadoPadre' => [
                        'type' => Types::string(),
                        'description' => 'Identifica la relación con otro documento previamente radicado',
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }


}