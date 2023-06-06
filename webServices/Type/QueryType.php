<?php
namespace Orpyca\webService\Type;

use Orpyca\webService\AppContext;
use Orpyca\webService\Types;
use Orpyca\webService\Data\DataSource;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;


class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'hello' => [
                    'type' => Types::string(),
                    'description' => 'Retorna saludo validando que el servicio funciona',
                    'args' => [
                        'entorno' => [
                            'type' => Types::string(),
                            'description' => 'Entorno de ejcución',
                        ]
                    ]
                ],

                'funcionario' => [
                    'type' => Types::funcionario(),
                    'args' => [
                        'funcionario' => Types::funcionarioInput()
                    ]
                ],

                'estadoRadicado'=> [
                    'type' => Types::estadoRadicado(),
                    'args' => [
                        'radicado' => Types::id()
                    ]
                ],

                'consultarAnexos'=> [
                    'type' => Types::anexo(),
                    'args' => [
                        'radicado' => Types::id()
                    ]
                ],

                'consultarRadicado'=> [
                    'type' => Types::radicado(),
                    'args' => [
                        'radicado' => Types::id()
                    ]
                ],

                'tiposDocumentalesPorSerieSubserie'=> [
                    'type' => Types::radicado(),
                    'args' => [
                        'dependencia' => Types::id(),
                        'serie' => Types::id(),
                        'subSerie' => Types::id()
                    ]
                ],

                'subSeries'=> [
                    'type' => Types::radicado(),
                    'args' => [
                        'dependencia' => Types::id(),
                        'serie' => Types::id()
                    ]
                ],


                'tipoDocumentalRadicar'=> [
                    'type' => Types::listOf(Types::tipoDocumental()),
                ],

                'user' => [
                    'type' => Types::user(),
                    'description' => 'Returns user by id (in range of 1-5)',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ]
                ]
            ],

            'resolveField' => function($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            }

        ];
        parent::__construct($config);
    }


    public function hello($rootValue, $args, AppContext $context)
    {
        $path = $context->rootUrl;
        $env  = '';

        if (array_key_exists('entorno', $args)) {
            $env  = "Entorno ->" . $args['entorno'];
        }

        return "Servicio URL -> {$path} {$env}";
    }

    public function funcionario($rootValue, $args, AppContext $context, $info)
    {
        $args = $args['funcionario'];
        return DataSource::encontrarFuncionario($context, $args);
    }

    public function estadoRadicado($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::estadoRadicado($context, $args);
    }

    public function consultarAnexos($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::consultarAnexos($context, $args);
    }

    public function consultarRadicado($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::consultarRadicado($context, $args);
    }

    public function tiposDocumentalesPorSerieSubserie($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::tiposDocumentales($context, $args);
    }

    public function subSeries($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::subSeries($context, $args);
    }

    public function tipoDocumentalRadicar($rootValue, $args, AppContext $context, $info)
    {
        return DataSource::tipoDocumentalRadicar($context, $args);
    }

    public function deprecatedField()
    {
        return 'You can request deprecated field, but it is not displayed in auto-generated documentation by default.';
    }
}
