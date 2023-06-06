<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\AppContext;
use Orpyca\webService\Data\DataSource;
use Orpyca\webService\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;


class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [

                'asignarImagen' => [
                    'type' => Types::string(),
                    'description' => 'Asigna o cambia la imagen del radicado',
                    'args' => [

                        'archivoBase64' => [
                            'type' => Types::nonNull(Types::string()),
                            'description' => 'Documento codificado en base64'
                        ],

                        'radicado' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Numero de radicado'
                        ],

                        'extensionArchivo' => [
                            'type' => Types::nonNull(Types::string()),
                            'description' => 'Extensión del documento'
                        ],

                    ]
                ],

                'asociarRadicado' => [
                    'type' => Types::string(),
                    'description' => 'Asigna un radicado de salida a un radicado de entrada',
                    'args' => [
                        'radicadoEntrada' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Numero de radicado de entrada'
                        ],

                        'radicadoSalida' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Numero de radicado de salida que
                            se quiere asociar a la entrada'
                        ],
                    ]
                ],

                'crearAnexo' => [
                    'type' => Types::anexo(),
                    'description' => 'Crea un anexo a un radicado existente',
                    'args' => [
                        'in' => Types::anexoInput()
                    ]
                ],

                'firmaDigitalGSE' => [
                    'type' => Types::string(),
                    'description' => 'Firmar un documento',
                    'args' => [
                        'documento' => Types::string()
                    ]
                ],

                'crearRadicado' => [
                    'type' => Types::radicado(),
                    'description' => 'Genera un nuevo numero de radicado',
                    'args' => [

                        'funcionario' => Types::funcionarioInput(),

                        'tipoRadicado' => [
                            'type' => Types::nonNull(Types::radicadoEnum()),
                            'description' => 'Tipo de radicación, interna, externa, temporal, comunicación'
                        ],

                        'tipoDocumental' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Clasificación del documento con el tipo documental'
                        ],

                        'asunto' => [
                            'type' => Types::nonNull(Types::string()),
                            'description' => 'Asunto del radicado que resume el contenido de la cumunicación'
                        ],

                        'referencia' => [
                            'type' => Types::string(),
                            'description' => 'Consecutivo de la comunicación que hace referencia a otro sistema que
                            origino previamente el documento'
                        ],

                        'ciudadano' => [
                            'type' => Types::ciudadanoInput(),
                            'description' => 'Consecutivo de la comunicación que hace referencia a otro sistema que
                            origino previamente el documento'
                        ],

                    ]
                ],

                'firmaDigital' => [
                    'type' => Types::string(),
                    'description' => 'Retorna un documento firmado',
                    'args' => [

                        'archivoBase64' => [
                            'type' => Types::nonNull(Types::string()),
                            'description' => 'Documento codificado en base64'
                        ],

                        'nombreArchivo' => [
                            'type' => Types::string(),
                            'description' => 'nombre del documento con la extensión'
                        ],
                    ]
                ],

                'actualizarTRD' => [
                    'type' => Types::trd(),
                    'description' => 'Actulizar la trd para un radicado',
                    'args' => [
                        'dependencia' => Types::nonNull(Types::id()),
                        'serie' =>  Types::nonNull(Types::id()),
                        'subSerie' => Types::nonNull(Types::id()),
                        'tipoDocumental' =>  Types::nonNull(Types::id()),
                        'radicado' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Numero de radicado al cual se asiganra la nueva trd'
                        ],
                        'funcionario' => Types::funcionarioInput(),
                    ]
                ],

                'reasignarRadicado'=> [
                    'type' => Types::radicado(),
                    'args' => [
                        'usuarioOrigen' => Types::funcionarioInput(),
                        'usuarioDestino' => Types::funcionarioInput(),
                        'radicado' => Types::id()
                    ]
                ],

                'crearExpediente'=> [
                    'type' => Types::string(),
                    'args' => [

                        'radicado' => [
                            'type' => Types::int(),
                            'description' => 'Numero de documento con el cual se crea el expediente'
                        ],

                        'funcionario' => [
                            'type' => Types::funcionarioInput(),
                            'description' => 'Responsable de la creación del expediente y de la administración'
                        ],

                        'ano' => [
                            'type' => Types::int(),
                            'description' => 'Año al cual pertenece el expediente. Hace parte del numero del expediente '
                        ],

                        'fechaExpediente' => [
                            'type' => Types::string(),
                            'description' => 'Fecha de creación de la carpeta fisica del documento. '
                        ],

                        'serie' => Types::int(),

                        'subSerie' => Types::int(),

                        'trd' => Types::int(),

                        'etiqueta' => Types::string(),

                    ]
                ],

            ],

            'resolveField' => function($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            }

        ];
        parent::__construct($config);
    }

    public function crearAnexo($rootValue, $args, AppContext $context, $info)
    {
        $args = $args['in'];
        return DataSource::crearAnexo($context, $args);
    }

    public function firmaDigitalGSE($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::firmaDigitalGSE($context, $args);
    }

    public function asignarImagen($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::asignarImagen($context, $args);
    }

    public function asociarRadicado($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::asignarImagen($context, $args);
    }

    public function crearRadicado($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::crearRadicado($context, $args);
    }

    public function firmaDigital($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::firmarDocumento($context, $args);
    }

    public function reasignarRadicado($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::reasignarRadicado($context, $args);
    }

    public function actualizarTRD($rootValue, $args, AppContext $context, $info)
    {
        //return DataSource::actualizarTrd($context, $args);
        return array();
    }

}