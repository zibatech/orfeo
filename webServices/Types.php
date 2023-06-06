<?php
namespace Orpyca\webService;

use Exception;
use Orpyca\webService\Type\UserType;
use Orpyca\webService\Type\FuncionarioType;
use Orpyca\webService\Type\AnexoType;
use Orpyca\webService\Type\RadicadoType;
use Orpyca\webService\Type\EstadoRadicadoType;

use Orpyca\webService\Type\TipoDocumentalType;
use Orpyca\webService\Type\TrdType;
use Orpyca\webService\Type\SerieType;
use Orpyca\webService\Type\SubSerieType;

use Orpyca\webService\Type\Input\AnexoInputType;
use Orpyca\webService\Type\Input\FuncionarioInputType;
use Orpyca\webService\Type\Input\CiudadanoInputType;

use Orpyca\webService\Type\Enum\TipoDireccionEnumType;
use Orpyca\webService\Type\Enum\DocumentoEnumType;
use Orpyca\webService\Type\Enum\TipoRadicadoEnumType;

use Orpyca\webService\Type\Scalar\EmailType;
use Orpyca\webService\Type\Scalar\UrlType;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

class Types
{
    private static $types = [];
    const LAZY_LOAD_GRAPHQL_TYPES = true;

    public static function user() : callable { return static::get(UserType::class); }
    public static function funcionario() : callable { return static::get(FuncionarioType::class); }
    public static function radicado() : callable { return static::get(RadicadoType::class); }
    public static function anexo() : callable { return static::get(AnexoType::class); }
    public static function estadoRadicado() : callable { return static::get(EstadoRadicadoType::class); }
    public static function node() : callable { return static::get(NodeType::class); }

    public static function tipoDocumental() : callable { return static::get(TipoDocumentalType::class); }
    public static function trd() : callable { return static::get(TrdType::class); }
    public static function serie() : callable { return static::get(SerieType::class); }
    public static function subSerie() : callable { return static::get(SubserieType::class); }

    public static function email() : callable { return static::get(EmailType::class); }
    public static function url() : callable { return static::get(UrlType::class); }

    public static function anexoInput() : callable { return static::get(AnexoInputType::class); }
    public static function funcionarioInput() : callable { return static::get(FuncionarioInputType::class); }
    public static function ciudadanoInput() : callable { return static::get(CiudadanoInputType::class); }

    public static function radicadoEnum() : callable { return static::get(TipoRadicadoEnumType::class); }
    public static function documentoEnum() : callable { return static::get(DocumentoEnumType::class); }
    public static function direccionEnum() : callable { return static::get(TipoDireccionEnumType::class); }

    public static function get($classname)
    {
        return static::LAZY_LOAD_GRAPHQL_TYPES ? function() use ($classname) {
            return static::byClassName($classname);
        } : static::byClassName($classname);
    }

    protected static function byClassName($classname) {
        $parts = explode("\\", $classname);
        $cacheName = strtolower(preg_replace('~Type$~', '', $parts[count($parts) - 1]));
        $type = null;

        if (!isset(self::$types[$cacheName])) {
            if (class_exists($classname)) {
                $type = new $classname();
            }

            self::$types[$cacheName] = $type;
        }

        $type = self::$types[$cacheName];

        if (!$type) {
            throw new Exception("Unknown graphql type: " . $classname);
        }
        return $type;
    }

    public static function byTypeName($shortName, $removeType=true)
    {
        $cacheName = strtolower($shortName);
        $type = null;

        if (isset(self::$types[$cacheName])) {
            return self::$types[$cacheName];
        }

        $method = lcfirst($shortName);
        if(method_exists(get_called_class(), $method)) {
            $type = self::{$method}();
        }

        if(!$type) {
            throw new Exception("Unknown graphql type: " . $shortName);
        }
        return $type;
    }

    // Let's add internal types as well for consistent experience

    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
