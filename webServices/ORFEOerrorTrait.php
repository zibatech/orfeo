<?php
namespace Orpyca\webService;

/**
 * ORFEOConnect - ORFEO creation and transport class.
 *
 * @see https://repo.correlibre.org/argopublico/argogpl
 * The ORFEOConnect OrfeoGPL project
 *
 * @author    cesar.gonzalez@hdsas.co
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

trait ORFEOerrorTrait {

    private $errors = [];

    /**
     * Listado de errores
     * @var string[]
     */
    private $ORConnect_Error = [
        'radicado_invalido'  => 'El numero de radicado es invalido.',
        'radicado_noexiste'  => 'El numero de radicado no existe.',
        'archivo_ruta'       => 'No existe la ruta, o no se puede escribir en ella.',
        'extension_invalida' => 'El archivo no tiene una extensión valida.',
        'archivo_escritura'  => 'No se puedo grabar el documento.',
        'insertar'           => 'No se pudo crear el registro.',
        'existente'          => 'Ya existe el registro.',
        'arg_insuficientes'  => 'No se completo la accion, parametros insuficientes.',
        'correo_novalido'    => 'Correo electronico nulo o no valido.',
        'usuario_noexiste'   => 'El ususario no existe o se encuentra deshabilitado.',
        'secuencia_nocreada' => 'Error no genero un Numero de Secuencia.',
        'solicitud_anulado'  => 'El radicado no fue solicitado para anulación.',
        'error_evnioemail'   => 'No se pudo enviar el correo electronico.',
    ];

	/**
     * Retorna el arreglo de errores encontrados en la ejecución de
     * la clase. Funciona como un acomulador de errores  si se
     * ejecuta desde la misma instaciar.
     * @return array
     */
    public function getError()
    {
        return $this->errors;
    }

    /**
     * Retorna el listado de errores
     * @return type
     */
	public function getListError()
    {
		return $this->ORConnect_Error;
    }

    /**
     * Guarda el nuevo error en la lista de errores
     */
	public function setError(string $errorname)
    {
        $nameFn = debug_backtrace()[1]['function'];
		$this->errors[] = $this->ORConnect_Error[$errorname] . '=> Función: ' . $nameFn;
    }

}
