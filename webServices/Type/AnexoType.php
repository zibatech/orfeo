<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;

class AnexoType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Documento anexo un radicado',
            'fields' => function() {
                return [

                    'anexCodigo' => [
                        'type' => Types::id(),
                        'description' => 'Codigo del anexo con el que se identifica en la aplicación',
                    ],

                    'anexRadiNume' => [
                        'type' => Types::string(),
                        'description' => 'Numero de radicado al cual se le anexo el documento',
                    ],

                    'anexTipo' => [
                        'type' => Types::int(),
                        'description' => 'Identifica  el anexo con el tipo de documento como pdf, imagenes, hoja de calculo etc.',
                    ],

                    'anexTamano' => [
                        'type' => Types::int(),
                        'description' => 'Tamaño del archivo',
                    ],

                    'anexSoloLect' => [
                        'type' => Types::int(),
                        'description' => 'Identifica si el documento podra ser radicado o solamente ser anexo de lectura.',
                    ],

                    'anexCreador' => [
                        'type' => Types::int(),
                        'description' => 'Login del usuario que creo el anexo.',
                    ],

                    'anexDesc' => [
                        'type' => Types::string(),
                        'description' => 'Descripción del anexo. Observación que el usuario registra para identificar 
                        el documento.',
                    ],

                    'nombreDependencia' => [
                        'type' => Types::string(),
                        'description' => 'Nombre de la dependencia donde fue creado el anexo.',
                    ],

                    'anexNumero' => [
                        'type' => Types::string(),
                        'description' => 'Nombre de la dependencia donde fue creado el anexo.',
                    ],

                    'anexNombArchivo' => [
                        'type' => Types::string(),
                        'description' => 'Nombre del archivo.',
                    ],

                    'anexEstado' => [
                        'type' => Types::string(),
                        'description' => 'Identifica si el anexo esta radicado, firmado, impreso, enviado. ',
                    ],

                    'sgdRemDestino' => [
                        'type' => Types::string(),
                        'description' => 'Código que identifica si el anexo tiene una dirección
                        principal o es una copia',
                    ],

                    'anexFechAnex' => [
                        'type' => Types::string(),
                        'description' => 'Fecha cuando el documento fue anexado',
                    ],

                    'anexBorrado' => [
                        'type' => Types::string(),
                        'description' => 'Si el anexo es borrado no se elimina, pero si se marca con 1',
                    ],

                ];
            }
        ];
        parent::__construct($config);
    }


}