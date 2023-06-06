<?php
namespace Orpyca\webService\Data;

class DataSource
{
    public static function encontrarFuncionario($Orp, $args){
        $data = null;
        if (array_key_exists('email', $args)) {
            $data  = $args['email'];
        }
        if (array_key_exists('documento', $args)) {
            $data  = $args['documento'];
        }

        try {
            $usuario = $Orp->oc->getUsuarioSelect($data);
            if($usuario){
                return new Funcionario(
                    array(
                        'email' => $usuario['email'],
                        'login' => $usuario['login'],
                        'codusuario' => $usuario['codusuario'],
                        'nivel' => $usuario['nivel'],
                        'dependencia' => $usuario['dependencia'],
                        'documento' => $usuario['documento'],
                        'nombre' => $usuario['nombre'],
                        'nombre_dependencia' => $usuario['nombre_dependencia'])
                );
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }

    public static function estadoRadicado($Orp, $args){
        if (array_key_exists('radicado', $args)) {
            $data  = $args['radicado'];
        }

        try {
            $estado = $Orp->oc->darEstado($data);

            if($estado){
                return new EstadoRadicado(
                    array(
                        'radicado' => $data,
                        'estado' => $estado['estado'],
                        'anexos' => $estado['anexos'],
                        'creador' => $estado['creador'],
                        'dependenciaCreador' => $estado['dependencia_creador'],
                        'fechaRadicacionRespuesta' => $estado['fecha'],
                        'radicadoRespuesta' => $estado['radicado_respuesta'])
                );
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }

    public static function consultarAnexos($Orp, $args){
        return null;
    }

    public static function consultarRadicado($Orp, $args){

        $noRadicado = $args['radicado'];

        $rad = $Orp->oc->consultarRadicado($noRadicado);

        if($rad){
            return new Radicado($rad);
        }else{
            return null;
        }
    }

    public static function firmaDigitalGSE($Orp, $args){

        $documento = $args['documento'];

        $token = $Orp->oc->firmaDigitalGSE($documento);

        if($token){
            return $token;
        }else{
            return null;
        }
    }

    public static function crearAnexo($Orp, $args){

        $data = null;
        $func = $args['funcionario'];
        if (array_key_exists('email', $func)) {
            $data  = $func['email'];
        }
        if (array_key_exists('documento', $func)) {
            $data  = $func['documento'];
        }

        try {
            $anexo = $Orp->oc->crearAnexo($args['radicado'],
                $args['archivoBase64'],
                $args['nombreArchivo'],
                $data,
                $args['descripcion']);

            if($anexo){
                return new Anexo($anexo);
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public static function asignarImagen($Orp, $args){
        return $Orp->oc->cambiarImagenRad(
              $args['radicado']
            , $args['extensionArchivo']
            , $args['archivoBase64']
        );
    }

    public static function asociarRadicado($Orp, $args){
        $total = array();
        try {
            $anexo = $Orp->oc->asociarRadicado(
                  $args['radicadoEntrada']
                , $args['radicadoSalida']
            );

            if($anexo && count($anexo) > 1 ){
                foreach ($anexo as $value){
                    $total[] = new Anexo($value);
                }
            }else{
                $total[] = new Anexo($anexo);
            }

        } catch (Exception $e) {
            return null;
        }
    }

    public static function crearRadicado($Orp, $args){

        $func = $args['funcionario'];
        if (array_key_exists('email', $func)) {
            $funcionario  = $func['email'];
        }
        if (array_key_exists('documento', $func)) {
            $funcionario  = $func['documento'];
        }

        $tipoRadicado = $args['tipoRadicado'];

        $tipoDocumental = $args['tipoDocumental'];

        $asunto = $args['asunto'];

        $referencia = $args['referencia'];

        $ciudadano = $args['ciudadano'];

        try {
            $noRadicado = $Orp->oc->crearRadicado(
                $funcionario,
                $tipoRadicado,
                $tipoDocumental,
                $asunto,
                $referencia,
                $ciudadano
            );

            $rad = $Orp->oc->consultarRadicado($noRadicado);

            if($rad){
                return new Radicado($rad);
            }else{
                return null;
            }

        } catch (Exception $e) {
            return null;
        }

    }

    public static function firmarDocumento($Orp, $args){
        try {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function tipoDocumentalRadicar($Orp, $args){
        $total = array();
        try {
            $tipoDoc = $Orp->oc->tipoDocumentalParaRadicar();

            if($tipoDoc && count($tipoDoc) > 1 ){
                foreach ($tipoDoc as $value){
                    $total[] = new TipoDocumental($value);
                }
            }else{
                $total[] = new TipoDocumental($tipoDoc);
            }

            return $total;

        } catch (Exception $e) {
            return null;
        }
    }
}
