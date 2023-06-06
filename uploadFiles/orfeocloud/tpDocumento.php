<?php

/** 
 * tpDocumento es la clase encargada de gestionar las operaciones tipos documentales
 * @author Hardy Deimont Niño  Velasquez
 * @name series
 * @version	1.0
 */ 
if (! $ruta_raiz)
	$ruta_raiz = '../../../..';
    include_once "$ruta_raiz/core/Modulos/trd/modelo/modeloTpDoc.php";
	//==============================================================================================	
	// CLASS tpDocumento
	//==============================================================================================	
	
	/**
	 *  Objecto tpDocumento 
	 */ 

class tpDocumento  {
	private $modelo;
    private $codtdocI;
    private $detatipod;
    private $terminot;
    private $Arraytp;
	 
	
	function __construct($ruta_raiz) {
		$this->modelo = new modeloTpDoc( $ruta_raiz );
	}
	

	/**
	 * Consulta los datos de las series 
	 * @return   void
	 */
	function consultar() {
			$rs = $this->modelo->consultar ();
			return $rs;	
	}
	
	/**
	 * Consulta los datos de las series 
	 * @return   void
	 */
	function consultarLimit($offset) {
			$rs = $this->modelo->consultarLimit ($offset);
			return $rs;	
	}
	/**
	 * Consulta el tamaño de de la tabla 
	 * @return   void
	 */
	function consultarTama() {
			$rs = $this->modelo->consultartama ();
			return $rs;	
	}
	
	function buscar() {
		$combi = $this->modelo->Buscar( '',$this->detatipod,0);
			if($combi  ["ERROR"]=='OK'){
				return $combi;
			}
		return 'No se encotro la Descripción';
		
	}

	/**
	 * crea usuario
	 */
	function crear() {
		$combi2 = $this->modelo->Buscar('',$this->detatipod,1);
		if($combi2["ERROR"]!='OK'){
		 	return $this->modelo->crear( $this->detatipod, $this->terminot, $this->Arraytp);
		}
		else 
		return 'Ya existe el codigo';
	}
	/**
	 * actualiza  usuario
	 */
	function actualizar() {

			$combi2 = $this->modelo->Buscar($this->codtdocI, $this->detatipod,2);
			if($combi2  ["ERROR"]!='OK'){
		 	return $this->modelo->actualizar($this->codtdocI, $this->detatipod, $this->terminot, $this->Arraytp);
			}
			else
			return 'Ya existe la Descripción';
		
		
	}
 	
	/**
	 * @return the $codtdocI
	 */
	public function getCodtdocI() {
		return $this->codtdocI;
	}

	/**
	 * @return the $detatipod
	 */
	public function getDetatipod() {
		return $this->detatipod;
	}

	/**
	 * @return the $terminot
	 */
	public function getTerminot() {
		return $this->terminot;
	}

	/**
	 * @return the $Arraytp
	 */
	public function getArraytp() {
		return $this->Arraytp;
	}

	/**
	 * @param $codtdocI the $codtdocI to set
	 */
	public function setCodtdocI($codtdocI) {
		$this->codtdocI = $codtdocI;
	}

	/**
	 * @param $detatipod the $detatipod to set
	 */
	public function setDetatipod($detatipod) {
		$this->detatipod = $detatipod;
	}

	/**
	 * @param $terminot the $terminot to set
	 */
	public function setTerminot($terminot) {
		$this->terminot = $terminot;
	}

	/**
	 * @param $Arraytp the $Arraytp to set
	 */
	public function setArraytp($Arraytp) {
		$this->Arraytp = $Arraytp;
	}

	function __destruct() {
			
	}
	
}

?>