<?php

include_once($ruta_raiz . "/include/tx/Historico.php");
include_once($ruta_raiz . "/class_control/Param_admin.php");

class Tx extends Historico {
	/** Aggregations: */
	/** Compositions: */
	/*	 * * Attributes: ** */

	/**
	 * Clase que maneja los Historicos de los documentos
	 *
	 * @param int     Dependencia Dependencia de Territorial que Anula
	 * @param number  usuaDocB    Documento de Usuario
	 * @param number  depeCodiB   Dependencia de Usuario Buscado
	 * @param varchar usuaNombB   Nombre de Usuario Buscado
	 * @param varcahr usuaLogin   Login de Usuario Buscado
	 * @param number	usNivelB	Nivel de un Ususairo Buscado..
	 * @db 	Objeto  conexion
	 * @access public
	 */
	var $db;
	var $tomarNivel = 'si';
	var $regsTrdFalse; //  Array que almacena los radicados qeu no cumplen condicion TRD False
	var $regsATrdFalse; //  Array que almacena los radicados anexos que no cumplen condicion TRD False

	function __construct($db) {

		/**
		 * Constructor de la clase Historico
		 * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
		 *
		 */
		$this->db = $db;
	}

	/**
	 * Metodo que trae los datos principales de un usuario a partir del codigo y la dependencia
	 *
	 * @param number $codUsuario
	 * @param number $depeCodi
	 *
	 */
	function datosUs($codUsuario, $depeCodi) {
		$sql = "SELECT
				USUA_DOC
				,USUA_LOGIN
				,CODI_NIVEL
				,USUA_NOMB
			FROM
				USUARIO
			WHERE
				DEPE_CODI=$depeCodi
				AND USUA_CODI=$codUsuario";
		# Busca el usuairo Origen para luego traer sus datos.


		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $this->db->query($sql);
		//$usNivel = $rs->fields["CODI_NIVEL"];
		//$nombreUsuario = $rs->fields["USUA_NOMB"];
		$this->usNivelB = $rs->fields['CODI_NIVEL'];
		$this->usuaNombB = $rs->fields['USUA_NOMB'];
		$this->usuaDocB = $rs->fields['USUA_DOC'];
	}

// MODIFICADO PARA GENERAR ALERTAS
// JUNIO DE 2009
	function getRadicados($tipo, $usua_cod) {

		$con = $this->db->driver;

		switch ($con) {
			case'oci8':
				$query = "SELECT " . $tipo . " FROM SGD_NOVEDAD_USUARIO WHERE USUA_DOC=$usua_cod";
				break;
			case 'postgres':
				$query = "SELECT $tipo FROM SGD_NOVEDAD_USUARIO WHERE USUA_DOC='$usua_cod'";
				break;
		}

		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $this->db->query($query);

		if ($rs) {
			return $rs->fields["$tipo"];
		}
	}

// MODIFICADO PARA GENERAR ALERTAS
// JUNIO  DE 2009
	function registrarNovedad($tipo, $docUsuarioDest, $numRad, $ruta_raiz = "..") {
		// busco la información de radicados informados pendientes de alerta
		// Busco info del campo NOV_INFOR de la tabla SGD_NOVEDAD_USUARIO

		$param = Param_admin::getObject($this->db, '%', 'ALERT_FUNCTION');
		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($param->PARAM_VALOR == "1") {
			$rads = $this->getRadicados($tipo, $docUsuarioDest);

			if ($rads != "") {
				$rads .= ",";
			}

			$rads .= $numRad;

			$con = $this->db->driver;

			switch ($con) {
				case'oci8':
					$xarray['USUA_DOC'] = $docUsuarioDest;
					$xarray[$tipo] = $rads;

					$tipo1 = $tipo;
					$valor = $rads;

					$qs = "Select count(1) as contador from SGD_NOVEDAD_USUARIO where USUA_DOC=$docUsuarioDest";
					$rs = $this->db->conn->query($qs);

					if ($rs->fields['CONTADOR'] == 0) {
						$qu = "INSERT INTO SGD_NOVEDAD_USUARIO (USUA_DOC,$tipo1) values ($docUsuarioDest,$valor)";
						$this->db->conn->query($qu);
					} else {
						$this->db->conn->query("UPDATE SGD_NOVEDAD_USUARIO SET $tipo1 = $valor where USUA_DOC'$docUsuarioDest'");
					}

					break;

				case 'postgres':

					$xarray['USUA_DOC'] .= '"';
					$xarray['USUA_DOC'] .= $docUsuarioDest;
					$xarray['USUA_DOC'] .= '"';

					$tipo1 .= $tipo;

					$xarray["$tipo"] .= "'";
					$xarray["$tipo"] .= $rads;
					$xarray["$tipo"] .= "'";

					$valor = $xarray[$tipo];

					$campo = 'USUA_DOC';
					$qs = "Select count(1) as contador from SGD_NOVEDAD_USUARIO where $campo='$docUsuarioDest'";
					$rs = $this->db->conn->query($qs);

					if ($rs->fields['CONTADOR'] == 0) {
						$qu = "INSERT INTO SGD_NOVEDAD_USUARIO ($campo,$tipo1) values ('$docUsuarioDest',$valor)";
						$this->db->conn->query($qu);
					} else {
						$this->db->conn->query("UPDATE SGD_NOVEDAD_USUARIO SET $tipo1 = $valor where $campo='$docUsuarioDest'");
					}
					break;
			}
		}
	}

	function informar($radicados, $loginOrigen, $depDestino, $depOrigen, $codUsDestino, $codUsOrigen, $observa, $idenviador = null, $ruta_raiz = "", $infConjunto = 0, $SendMail = false) {

		$ruta_raiz = "..";
		$sql = "SELECT
        USUA_DOC
        ,USUA_LOGIN
        ,CODI_NIVEL
        ,USUA_NOMB
        FROM
        USUARIO
        WHERE
        DEPE_CODI=$depOrigen
        AND USUA_CODI=$codUsOrigen";

		$rs = $this->db->query($sql);
		$docUsuarioOrigen = $rs->fields["USUA_DOC"];
		$LoginUsuarioOrigen = $rs->fields["USUA_LOGIN"];

		$sql = "SELECT
        USUA_DOC
        ,USUA_LOGIN
        ,CODI_NIVEL
        ,USUA_NOMB
        ,USUA_EMAIL
        FROM
        USUARIO
        WHERE
        DEPE_CODI=$depDestino
        AND USUA_CODI=$codUsDestino";

		$rs = $this->db->query($sql); # Ejecuta la busqueda
		$usNivel = $rs->fields["CODI_NIVEL"];
		$usLoginDestino = $rs->fields["USUA_LOGIN"];
		$nombreUsuario = $rs->fields["USUA_NOMB"];
		$docUsuarioDest = $rs->fields["USUA_DOC"];
		$emailUsuaDest = $rs->fields["USUA_EMAIL"];

		$codTx = 8;
		$tomarNivel = $this->tomarNivel;
		if ($tomarNivel == "si") {
			$whereNivel = ",CODI_NIVEL=$usNivel";
		}

		//$observa = "A: $usLoginDestino - $observa";
		$observa = "A: $nombreUsuario - $observa";
		$observacion = $observa;

		if (!$idenviador)
			$idenviador = $docUsuarioOrigen;

		$tmp_rad = array();
		$informaSql = true;
		while ((list(, $noRadicado) = each($radicados)) and $informaSql) {
			if (strstr($noRadicado, '-'))
				$tmp = explode('-', $noRadicado);
			else
				$tmp = $noRadicado;
			if (is_array($tmp)) {
				$record["RADI_NUME_RADI"] = $tmp[1];
			} else {
				$record["RADI_NUME_RADI"] = $noRadicado;
			}

			if ($SendMail == true && !empty($emailUsuaDest)) {
				//LLENO LAS VARIABLES NECESARIAS PARA INFORMAR
				$krd = $LoginUsuarioOrigen;
				$radicadosSelText = $record["RADI_NUME_RADI"];
				$usuaCodiMail = $codUsDestino;
				$depeCodiMail = $depDestino;
				$codTx = 8;
				$_show_mensaje = 1; //1 , no imprime ningun eco
				$ruta_raiz = "..";
				require("$ruta_raiz/include/mail/GENERAL.mailInformar.php");
				ob_clean();
			}

			# Asignar el valor de los campos en el registro
			# Observa que el nombre de los campos pueden ser mayusculas o minusculas
			$record["DEPE_CODI"] = $depDestino;
			$record["USUA_CODI"] = $codUsDestino;
			$record["INFO_CONJUNTO"] = $infConjunto;
			$record["INFO_CODI"] = $idenviador;
			$record["INFO_DESC"] = "'$observacion '";
			$record["USUA_DOC"] = "'$docUsuarioDest'";
			$record["INFO_FECH"] = $this->db->conn->OffsetDate(0, $this->db->conn->sysTimeStamp);
			$record["USUA_DOC_ORIGEN"] = "'$docUsuarioOrigen'";

			# Mandar como parametro el recordset vacio y el arreglo conteniendo los datos a insertar
			# a la funcion GetInsertSQL. Esta procesara los datos y regresara un enunciado SQL
			# para procesar el INSERT.
			if ($infConjunto >= 1)
				$codTxNew = 89;
			else
				$codTxNew = 8;
			if ($noRadicado) {
				$informaSql = $this->db->conn->Replace("INFORMADOS", $record, array('RADI_NUME_RADI', 'INFO_CODI', 'USUA_DOC', 'USUA_DOC_ORIGEN', 'INFO_CONJUNTO'), false);
				if ($informaSql)
					$tmp_rad = array($record["RADI_NUME_RADI"]);
				$radicad = $this->insertarHistorico($tmp_rad, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTxNew);
			}
		}
		return $nombreUsuario;
	}

	function tramiteConjunto($radicados, $loginOrigen, $depDestino, $depOrigen, $codUsDestino, $codUsOrigen, $observa, $idenviador = null, $ruta_raiz = "", $infConjunto = 0, $SendMail = false) {

		$ruta_raiz = "..";
		$sql = "SELECT
        USUA_DOC
        ,USUA_LOGIN
        ,CODI_NIVEL
        ,USUA_NOMB
        FROM
        USUARIO
        WHERE
        DEPE_CODI=$depOrigen
        AND USUA_CODI=$codUsOrigen";

		$rs = $this->db->query($sql);
		$docUsuarioOrigen = $rs->fields["USUA_DOC"];
		$LoginUsuarioOrigen = $rs->fields["USUA_LOGIN"];

		$sql = "SELECT
        USUA_DOC
        ,USUA_LOGIN
        ,CODI_NIVEL
        ,USUA_NOMB
        ,USUA_EMAIL
        FROM
        USUARIO
        WHERE
        DEPE_CODI=$depDestino
        AND USUA_CODI=$codUsDestino";

		$rs = $this->db->query($sql); # Ejecuta la busqueda
		$usNivel = $rs->fields["CODI_NIVEL"];
		$usLoginDestino = $rs->fields["USUA_LOGIN"];
		$nombreUsuario = $rs->fields["USUA_NOMB"];
		$docUsuarioDest = $rs->fields["USUA_DOC"];
		$emailUsuaDest = $rs->fields["USUA_EMAIL"];

		$codTx = 18;
		$tomarNivel = $this->tomarNivel;
		if ($tomarNivel == "si") {
			$whereNivel = ",CODI_NIVEL=$usNivel";
		}

		//$observa = "A: $usLoginDestino - $observa";
		$observa = "A: $nombreUsuario - $observa";
		$observacion = $observa;

		if (!$idenviador)
			$idenviador = $docUsuarioOrigen;

		$tmp_rad = array();
		$informaSql = true;
		while ((list(, $noRadicado) = each($radicados)) and $informaSql) {
			if (strstr($noRadicado, '-'))
				$tmp = explode('-', $noRadicado);
			else
				$tmp = $noRadicado;
			if (is_array($tmp)) {
				$record["RADI_NUME_RADI"] = $tmp[1];
			} else {
				$record["RADI_NUME_RADI"] = $noRadicado;
			}

			if ($SendMail == true && !empty($emailUsuaDest)) {
				//LLENO LAS VARIABLES NECESARIAS PARA INFORMAR
				$krd = $LoginUsuarioOrigen;
				$radicadosSelText = $record["RADI_NUME_RADI"];
				$usuaCodiMail = $codUsDestino;
				$depeCodiMail = $depDestino;
				$codTx = 18;
				$_show_mensaje = 1; //1 , no imprime ningun eco
				$ruta_raiz = "..";
				require("$ruta_raiz/include/mail/GENERAL.mailInformar.php");
				ob_clean();
			}

			# Asignar el valor de los campos en el registro
			# Observa que el nombre de los campos pueden ser mayusculas o minusculas
			$record["DEPE_CODI"] = $depDestino;
			$record["USUA_CODI"] = $codUsDestino;
			$record["INFO_CONJUNTO"] = $infConjunto;
			$record["INFO_CODI"] = $idenviador;
			$record["INFO_DESC"] = "'$observacion '";
			$record["USUA_DOC"] = "'$docUsuarioDest'";
			$record["INFO_FECH"] = $this->db->conn->OffsetDate(0, $this->db->conn->sysTimeStamp);
			$record["USUA_DOC_ORIGEN"] = "'$docUsuarioOrigen'";

			# Mandar como parametro el recordset vacio y el arreglo conteniendo los datos a insertar
			# a la funcion GetInsertSQL. Esta procesara los datos y regresara un enunciado SQL
			# para procesar el INSERT.
		
			if ($noRadicado) {
				$informaSql = $this->db->conn->Replace("TRAMITECONJUNTO", $record, array('RADI_NUME_RADI', 'INFO_CODI', 'USUA_DOC', 'USUA_DOC_ORIGEN', 'INFO_CONJUNTO'), false);
				if ($informaSql)
					$tmp_rad = array($record["RADI_NUME_RADI"]);
				$radicad = $this->insertarHistorico($tmp_rad, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa,100);
			}
		}
		return $nombreUsuario;
	}

	function borrarInformado($radicados, $loginOrigen, $depDestino, $depOrigen, $codUsDestino, $codUsOrigen, $observa, $infConjunto = 0) {
		$tmp_rad = array();
		$deleteSQL = true;

		while ((list(, $noRadicado) = each($radicados)) and $deleteSQL) {
			//foreach($radicados as $noRadicado)
			# Borrar el informado seleccionado
			if (stripos($noRadicado, "-")) {
				$record["USUA_CODI"] = $codUsOrigen;
				$record["DEPE_CODI"] = $depOrigen;
				$tmp = explode('-', $noRadicado);
				($tmp[0]) ? $wtmp = ' and INFO_CODI = ' . $tmp[0] : $wtmp = ' and INFO_CODI IS NULL ';
				$tmp[1] = str_replace(",", "", $tmp[1]);
				$record["RADI_NUME_RADI"] = $tmp[1];
				$deleteSQL = $this->db->conn->Execute("DELETE FROM INFORMADOS WHERE RADI_NUME_RADI=" . $tmp[1] . " and USUA_CODI=" . $codUsDestino . " and DEPE_CODI=" . $depDestino . $wtmp);
				if ($deleteSQL)
					$tmp_rad[] = $record["RADI_NUME_RADI"];
			} else {
				$instruccion = "DELETE FROM INFORMADOS WHERE RADI_NUME_RADI=" . $noRadicado . " and USUA_CODI=" . $codUsDestino . " and DEPE_CODI=" . $depDestino;
				$deleteSQL = $this->db->conn->Execute($instruccion);
				if ($deleteSQL)
					$tmp_rad[] = $noRadicado;
			}
		}

		if ($infConjunto == 1)
			$codTxNew = 87;
		else
			$codTxNew = 7;
		if ($deleteSQL) {
			$this->insertarHistorico($tmp_rad, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTxNew, $observa);
			return $tmp_rad;
		} else {
			return $deleteSQL;
		}
	}

	function borrarTramiteConjunto($radicados, $loginOrigen, $depDestino, $depOrigen, $codUsDestino, $codUsOrigen, $observa, $infConjunto = 0) {
		$tmp_rad = array();
		$deleteSQL = true;

		while ((list(, $noRadicado) = each($radicados)) and $deleteSQL) {
			//foreach($radicados as $noRadicado)
			# Borrar el informado seleccionado
			if (stripos($noRadicado, "-")) {
				$record["USUA_CODI"] = $codUsOrigen;
				$record["DEPE_CODI"] = $depOrigen;
				$tmp = explode('-', $noRadicado);
				($tmp[0]) ? $wtmp = ' and INFO_CODI = ' . $tmp[0] : $wtmp = ' and INFO_CODI IS NULL ';
				$tmp[1] = str_replace(",", "", $tmp[1]);
				$record["RADI_NUME_RADI"] = $tmp[1];
				$deleteSQL = $this->db->conn->Execute("DELETE FROM INFORMADOS WHERE RADI_NUME_RADI=" . $tmp[1] . " and USUA_CODI=" . $codUsDestino . " and DEPE_CODI=" . $depDestino . $wtmp);
				if ($deleteSQL)
					$tmp_rad[] = $record["RADI_NUME_RADI"];
			} else {
				$instruccion = "DELETE FROM TRAMITECONJUNTO WHERE RADI_NUME_RADI=" . $noRadicado . " and USUA_CODI=" . $codUsDestino . " and DEPE_CODI=" . $depDestino;
				$deleteSQL = $this->db->conn->Execute($instruccion);
				if ($deleteSQL)
					$tmp_rad[] = $noRadicado;
			}
		}

		if ($infConjunto == 1)
			$codTxNew = 87;
		else
			$codTxNew = 101;
		if ($deleteSQL) {
			$this->insertarHistorico($tmp_rad, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa, 101, $observa);
			return $tmp_rad;
		} else {
			return $deleteSQL;
		}
	}

	function excluirfisico($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {
		$systemDate = $db->conn->sysTimeStamp;
		$radicadosIn = join(",", $radicados);
		$sql = "delete from sgd_exp_expediente
  		  where radi_nume_radi IN ($radicadosIn)
  		  and sgd_exp_estado=2";

		$rs = $this->db->conn->Execute($sql);
		if ($rs) {
			$sqlu = "update sgd_exp_expediente
  		  set sgd_exp_estado=1, sgd_exp_fech_arch=SYSDATE, radi_usua_arch='$loginOrigen'
  		  where radi_nume_radi IN ($radicadosIn)
  		  and sgd_exp_estado=0 and sgd_exp_fech_arch is null";

			$rsu = $this->db->conn->Execute($sqlu);
		}

		$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, 999, 1, $observa, 60);
		return $sql;
	}

	function cierre_exp($expedientes, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {
		$systemDate = $db->conn->sysTimeStamp;
		$expedientesIn = join(",", $expedientes);
		$sql = "update sgd_sexp_secexpedientes
  		  set SGD_CERRADO=1
  		  where SGD_EXP_NUMERO IN ($expedientesIn)";

		$rs = $this->db->conn->Execute($sql);
		$this->insertarHistoricoExp($expedientes, $radicados, $depOrigen, $codUsOrigen, $observa, 58, 0);
		return $sql;
	}

	function archifisico($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {
		$systemDate = $db->conn->sysTimeStamp;
		$radicadosIn = join(",", $radicados);
		$sql = "update sgd_exp_expediente
  		  set SGD_EXP_ESTADO=1,
  		  RADI_USUA_ARCH='$loginOrigen',
  		  SGD_EXP_FECH_ARCH= SYSDATE
  		  where radi_nume_radi IN ($radicadosIn)
  		  and sgd_exp_estado <> 2";

		$rs = $this->db->conn->Execute($sql);
		$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, 999, 1, $observa, 57);
		return $sql;
	}

	function changeFolder($radicados, $usuaLogin, $carpetaDestino, $carpetaTipo, $tomarNivel, $observa) {
		//$this->db->conn->debug =true; //Mantener comentado

		$whereNivel = "";
		$sql = "SELECT
					b.USUA_DOC
					,b.USUA_LOGIN
					,b.CODI_NIVEL
					,b.DEPE_CODI
					,b.USUA_CODI
					,b.USUA_NOMB
				FROM
					 USUARIO b
				WHERE
					b.USUA_LOGIN = '$usuaLogin'";
		# Busca el usuairo Origen para luego traer sus datos.
		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $this->db->conn->query($sql); # Ejecuta la busqueda

		$usNivel = $rs->fields["CODI_NIVEL"];
		$depOrigen = $rs->fields["DEPE_CODI"];
		$codUsOrigen = $rs->fields["USUA_CODI"];
		$nombOringen = $rs->fields["USUA_NOMB"];
		$codTx = "10";
		$radicadosIn = $radicados;
		$sql = "update radicado
					set
					  CARP_CODI=$carpetaDestino
					  ,CARP_PER=$carpetaTipo
					  ,radi_fech_agend=null
					  ,radi_agend=null
					  $whereNivel
				 where RADI_NUME_RADI in($radicadosIn)";

		//$this->conn->Execute($isql);
		$rs = $this->db->conn->query($sql);
		$retorna = 1;
		if (!$rs) {
			$retorna = -1;
		}
		if ($retorna != -1) {
			$tmp_rad = array();
			//SE RECORREN LOS RADICADOS SELECCIONADOS Y A CADA UNO SE LE INSERTA SU PROCESO EN HISTORICO
			$isql = "select radi_nume_radi from radicado where RADI_NUME_RADI in($radicadosIn)";
			$irs = $this->db->conn->Execute($isql);

			while (!$irs->EOF) {
				unset($tmp_rad);
				$numeroRadicado = $irs->fields["RADI_NUME_RADI"];
				$tmp_rad[] = $numeroRadicado;
				$this->insertarHistorico($tmp_rad, $depOrigen, $codUsOrigen, $depOrigen, $codUsOrigen, $observa, $codTx);
				$irs->MoveNext();
			}
		}
		return $retorna;
	}

	function cambioCarpeta($radicados, $usuaLogin, $carpetaDestino, $carpetaTipo, $tomarNivel, $observa) {
		$whereNivel = "";
		$sql = "SELECT
					b.USUA_DOC
					,b.USUA_LOGIN
					,b.CODI_NIVEL
					,b.DEPE_CODI
					,b.USUA_CODI
					,b.USUA_NOMB
				FROM
					 USUARIO b
				WHERE
					b.USUA_LOGIN = '$usuaLogin'";
		# Busca el usuairo Origen para luego traer sus datos.
		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $this->db->query($sql); # Ejecuta la busqueda

		$usNivel = $rs->fields["CODI_NIVEL"];
		$depOrigen = $rs->fields["DEPE_CODI"];
		$codUsOrigen = $rs->fields["USUA_CODI"];
		$nombOringen = $rs->fields["USUA_NOMB"];
		if ($tomarNivel == "si") {
			$whereNivel = ",CODI_NIVEL=$usNivel";
		}
		$codTx = "10";

		$radicadosIn = join(",", $radicados);
		$sql = "update radicado
					set
					  CARP_CODI=$carpetaDestino
					  ,CARP_PER=$carpetaTipo
					  ,radi_fech_agend=null
					  ,radi_agend=null
					  $whereNivel
				 where RADI_NUME_RADI in($radicadosIn)";

		//$this->conn->Execute($isql);
		$rs = $this->db->query($sql); # Ejecuta la busqueda
		$retorna = 1;
		if (!$rs) {
			echo "<center><font color=red>Error en el Movimiento ... A ocurrido un error y no se ha podido realizar la Transaccion</font> <!-- $sql -->";
			$retorna = -1;
		}
		if ($retorna != -1) {

			$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, $depOrigen, $codUsOrigen, $observa, $codTx);
		}
		return $retorna;
	}

	function reasignar($radicados, $loginOrigen, $depDestino, $depOrigen, $codUsDestino, $codUsOrigen, $tomarNivel, $observa, $codTx, $carp_codi, $valUsuarioActual = false) {

		$whereNivel = "";

		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

		$sql = "SELECT
				USUA_DOC
				,USUA_LOGIN
				,CODI_NIVEL
				,USUA_NOMB
			FROM
				USUARIO
			WHERE
				DEPE_CODI=$depDestino
				AND USUA_CODI=$codUsDestino";
		# Busca el usuairo Origen para luego traer sus datos.

		$rs = $this->db->query($sql);
		//$usNivel = $rs->fields["CODI_NIVEL"];
		//$nombreUsuario = $rs->fields["USUA_NOMB"];
		$usNivel = $rs->fields['CODI_NIVEL'];
		$nombreUsuario = $rs->fields['USUA_NOMB'];
		$docUsuaDest = $rs->fields['USUA_DOC'];
		if ($tomarNivel == "si") {
			$whereNivel = ",CODI_NIVEL=$usNivel";
		}


		//Start::multiple
		if(count($radicados) ==1){
			
			$es_multiple = false;
			$es_multiple_radicado = false;
			$iSqlMemorandoMultipleCuerpo= "SELECT 
				count(*)	as TOTAL,
				string_agg(DISTINCT SGD_DIR_DRECCIONES.sgd_dir_nombre, ',') AS DESTINATARIOS,
				(SELECT count(*) FROM ANEXOS WHERE ANEXOS.radi_nume_salida::text ='$radicados[0]' AND ANEX_ESTADO >= 2 ) AS RADICADO 
			FROM
				SGD_DIR_DRECCIONES 
			WHERE
				radi_nume_radi = '$radicados[0]' 
				AND radi_nume_radi::text LIKE'%3' ";
			
			$rsMemorandoMultipleCuerpo = $this->db->conn->query($iSqlMemorandoMultipleCuerpo);
			$tieneAsignacion = 0;
			if ($rsMemorandoMultipleCuerpo) {
				if($rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1){
					$remitenteRadicado = $rsMemorandoMultipleCuerpo->fields["DESTINATARIOS"];
					$es_multiple = true;
					if($rsMemorandoMultipleCuerpo->fields["RADICADO"]>0){
						$es_multiple_radicado = true;
						//goto siguiente;
					}
				}
			}
		}
	
	 	//End::multiple

		$radicadosIn = join(",", $radicados);
		$proccarp = "Reasignar";
		$carp_per = 0;
		$whereValUsuarioActual = "";
		if ($valUsuarioActual == true ) {
			$whereValUsuarioActual = "AND  radi_depe_actu=$depOrigen AND radi_usua_actu=$codUsOrigen";
		}
		//Start::mulitple
		if ($es_multiple) {
			$whereValUsuarioActual = "";
		}
		//End::multiple
		$isql = "update radicado
				set
				  RADI_USU_ANTE='$loginOrigen'
				  ,RADI_DEPE_ACTU=$depDestino
				  ,RADI_USUA_ACTU=$codUsDestino
				  ,CARP_CODI=$carp_codi
				  ,CARP_PER=$carp_per
				  ,RADI_LEIDO=0
				  , radi_fech_agend=null
				  ,radi_agend=null
				  $whereNivel
			 where RADI_NUME_RADI in($radicadosIn)
           $whereValUsuarioActual
				    ";
				
		// MODIFICADO PARA GENERAR ALERTAS
		// JUNIO DE 2009
		foreach ($radicados as $rad) {
			// $this->registrarNovedad('NOV_REASIG', $docUsuaDest, $rad);
		}

		$this->db->conn->Execute($isql); # Ejecuta la busqueda
		$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa, $codTx);
		return $nombreUsuario;
	}

	public static function recursiveAnex(array $radicados, $db) {
		$allRad = array();
		$radicadosIn = join(",", $radicados);

		$anex = "select
							radi_nume_salida as RADI
						from
							anexos
						where
							anex_radi_nume in ($radicadosIn)"
				. "and radi_nume_salida is not null";

		$rs = $db->conn->Execute($anex);

		while ($rs && !$rs->EOF) {
			if(!in_array($rs->fields['RADI'], $radicados)){
				$allRad[] = $rs->fields['RADI'];
			}			
			$rs->MoveNext();
		}

		if (!empty($allRad)){
			$recursive = Tx::recursiveAnex($allRad, $db);
		}
		
		$resultado = array_merge($radicados
				,!empty($allRad)? $allRad : array()
				,!empty($recursive)? $recursive : array());
		
		return array_unique($resultado);
	}

	function archivar($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {

		$allRad = Tx::recursiveAnex($radicados, $this->db);
		
		$radicadosIn = join(",", $allRad);

		$carp_codi = $carp_per = 0;
		$usua_depe = '999';

		/* obtener usuario jefe de la dependencia 999 */
		$sql_usu_id = "SELECT
				u.usua_nomb,
				u.usua_codi
			FROM
				autm_membresias m,
				usuario u
			WHERE
				m.autg_id = '2'
				AND u.usua_esta = '1'
				AND u.id = m.autu_id
				AND u.depe_codi = $usua_depe";
		
		$usua = $this->db->conn->getRow($sql_usu_id);
		$usua_codi = $usua['USUA_CODI'];
		$usua_nomb = $usua['USUA_NOMB'];
/*
		$isql = "update radicado
      set
        RADI_USU_ANTE='$loginOrigen'
        ,RADI_DEPE_ACTU=$usua_depe
        ,RADI_USUA_ACTU=$usua_codi
        ,CARP_CODI=$carp_codi
        ,CARP_PER=$carp_per
        ,RADI_LEIDO=0
        ,radi_fech_agend=null
        ,radi_agend=null
        ,CODI_NIVEL=0
        ,SGD_SPUB_CODIGO=0
      where radi_depe_actu=$depOrigen
          AND radi_usua_actu=$codUsOrigen
          AND RADI_NUME_RADI in($radicadosIn)";
*/
$sql_rol="SELECT autg_id FROM autm_membresias WHERE autg_id=37 AND autu_id=".$_SESSION['usua_id'];
$rs_rol = $this->db->conn->Execute($sql_rol);
if($rs_rol->fields['AUTG_ID']==37)
	{
		$isql = "update radicado
      set
        RADI_USU_ANTE='$loginOrigen'
        ,RADI_DEPE_ACTU=$usua_depe
        ,RADI_USUA_ACTU=$usua_codi
        ,CARP_CODI=$carp_codi
        ,CARP_PER=$carp_per
        ,RADI_LEIDO=0
        ,radi_fech_agend=null
        ,radi_agend=null
        ,CODI_NIVEL=0
        ,SGD_SPUB_CODIGO=0
      where RADI_NUME_RADI in($radicadosIn)";
	}
else
	{

		$isql = "update radicado
      set
        RADI_USU_ANTE='$loginOrigen'
        ,RADI_DEPE_ACTU=$usua_depe
        ,RADI_USUA_ACTU=$usua_codi
        ,CARP_CODI=$carp_codi
        ,CARP_PER=$carp_per
        ,RADI_LEIDO=0
        ,radi_fech_agend=null
        ,radi_agend=null
        ,CODI_NIVEL=0
        ,SGD_SPUB_CODIGO=0
      where radi_depe_actu=$depOrigen
          AND radi_usua_actu=$codUsOrigen
          AND RADI_NUME_RADI in($radicadosIn)";

     }

		$this->db->conn->Execute($isql); # Ejecuta la busqueda
		$this->insertarHistorico($allRad, $depOrigen, $codUsOrigen, $usua_depe, $usua_codi, $observa, 13);
		return $usua_nomb;
	}

	// Hecho por Fabian Mauricio Losada
	function nrr($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {
		$whereNivel = "";
		$radicadosIn = join(",", $radicados);
		$carp_codi = substr($depOrigen, 0, 2);
		$carp_per = 0;
		$carp_codi = 0;
		$isql = "update radicado
      set
      RADI_USU_ANTE='$loginOrigen'
      ,RADI_DEPE_ACTU=999
      ,RADI_USUA_ACTU=15
      ,CARP_CODI=$carp_codi
      ,CARP_PER=$carp_per
      ,RADI_LEIDO=0
      ,radi_fech_agend=null
      ,radi_agend=null
      ,CODI_NIVEL=1
      ,SGD_SPUB_CODIGO=0
      ,RADI_NRR=1
      where radi_depe_actu=$depOrigen
      AND radi_usua_actu=$codUsOrigen
      AND RADI_NUME_RADI in($radicadosIn)";
		//$this->conn->Execute($isql);
		$this->db->conn->Execute($isql); # Ejecuta la busqueda

		$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, 999, 1, $observa, 65);
		return $isql;
	}

	/**
	 * Nueva Funcion para agendar.
	 * Este metodo permite programar un radicado para una fecha especifica, el arreglo con la version anterior
	 * , es que no se borra el agendado cuando el radicado sale del usuario actual.
	 *
	 * @author JAIRO LOSADA JUNIO 2006
	 * @version 3.5.1
	 *
	 * @param array int $radicados
	 * @param varchar $loginOrigen
	 * @param numeric $depOrigen
	 * @param numeric $codUsOrigen
	 * @param varchar $observa
	 * @param date $fechaAgend
	 * @return boolean
	 */
	function agendar($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa, $fechaAgend, $depDestino = '', $usuaCodiDestino = '') {
		$isql = false;
		$whereNivel = "";
		$radicadosIn = join(",", $radicados);
		$carp_codi = substr($depOrigen, 0, 2);
		$carp_per = 1;
		$sqlFechaAgenda = $this->db->conn->DBDate($fechaAgend);
		if ($depDestino != '' and $usuaCodiDestino != '') {
			$this->datosUs($usuaCodiDestino, $depDestino);
			$depIn = $depDestino;
		} else {
			$this->datosUs($codUsOrigen, $depOrigen);
			$depIn = $depOrigen;
		}
		$usuaDocAgen = $this->usuaDocB;
		//return $usuaDocAgen;
		$observa = "Agendado para el $fechaAgend - " . $observa;
		foreach ($radicados as $noRadicado) {
			# Busca el usuairo Origen para luego traer sus datos.
			$rad = array();
			if ($usuaDocAgen) {
				$record["RADI_NUME_RADI"] = $noRadicado;
				$record["DEPE_CODI"] = $depIn;
				$record["SGD_AGEN_OBSERVACION"] = "'$observa '";
				$record["USUA_DOC"] = "'$usuaDocAgen'";
				$record["SGD_AGEN_FECH"] = $this->db->conn->OffsetDate(0, $this->db->conn->sysTimeStamp);
				$record["SGD_AGEN_FECHPLAZO"] = $sqlFechaAgenda;
				$record["SGD_AGEN_ACTIVO"] = 1;
				$insertSQL = $this->db->insert("SGD_AGEN_AGENDADOS", $record, "true");
				$radH[0] = $noRadicado;
				$this->insertarHistorico($radH, $depOrigen, $codUsOrigen, $depOrigen, $codUsOrigen, $observa, 14);
				$sqlUpRad = "UPDATE radicado set fech_vcmto=$sqlFechaAgenda where radi_nume_radi=$noRadicado";
				$this->db->query($sqlUpRad);
				$isql = true;
			}
		}
		//$this->conn->Execute($isql);
		return $isql;
	}

	/**
	 * Metodo que sirve para sacar uno o varios radicados de agendado
	 *
	 * @param array $radicados
	 * @param unknown_type $loginOrigen
	 * @param unknown_type $depOrigen
	 * @param unknown_type $codUsOrigen
	 * @param unknown_type $observa
	 * @return unknown
	 */
	function noAgendar($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $observa) {
		$this->datosUs($codUsOrigen, $depOrigen);
		$usuaDocAgen = $this->usuaDocB;
		$whereNivel = "";
		$radicadosIn = join(",", $radicados);
		$carp_codi = substr($depOrigen, 0, 2);
		$isql = "update sgd_agen_agendados
					set
					  SGD_AGEN_ACTIVO=0
				 where
				   RADI_NUME_RADI in($radicadosIn)
				   AND USUA_DOC=" . "'" . $usuaDocAgen . "'";

		//print($isql);
		$this->db->conn->Execute($isql); # Ejecuta la busqueda
		$this->insertarHistorico($radicados, $depOrigen, $codUsOrigen, $depOrigen, $codUsOrigen, $observa, 15);
		return $isql;
	}

	function devolver($radicados, $loginOrigen, $depOrigen, $codUsOrigen, $tomarNivel, $observa) {
		$whereNivel = "";
		$retorno = "";
		$this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
		foreach ($radicados as $noRadicado) {
			$sql = "SELECT
				b.USUA_DOC
				,b.USUA_LOGIN
				,b.CODI_NIVEL
				,b.DEPE_CODI
				,b.USUA_CODI
				,b.USUA_NOMB
				,b.USUA_DOC
				,a.RADI_USU_ANTE
			FROM
				RADICADO a, USUARIO b
			WHERE
				a.RADI_USU_ANTE=b.USUA_LOGIN
				AND b.usua_esta='1'
				AND a.RADI_NUME_RADI = $noRadicado";
			# Busca el usuairo Origen para luego traer sus datos.
			$rs = $this->db->conn->Execute($sql); # Ejecuta la busqueda
			$codUsDestino = $rs->fields['USUA_CODI'];
			//echo "<hr> $codUsDestino <hr>";
			if (!$codUsDestino) {
				$isql = "SELECT
				b.USUA_DOC
				,b.USUA_LOGIN
				,b.CODI_NIVEL
				,b.DEPE_CODI
				,b.USUA_CODI
				,b.USUA_NOMB
				,b.USUA_DOC
			FROM
			HIST_EVENTOS h, USUARIO b
			WHERE
				h.DEPE_CODI=b.DEPE_CODI
				AND h.sgd_ttr_codigo=2
				and h.usua_codi=b.usua_codi
		and b.usua_esta='1'
        AND h.RADI_NUME_RADI = $noRadicado";
				$rs = $this->db->conn->Execute($isql); # Ejecuta la busqueda
				$codUsDestino = $rs->fields['USUA_CODI'];
				//  echo "$codUsDestino ....". $isql;
			}
			$usNivel = $rs->fields['CODI_NIVEL'];
			$depDestino = $rs->fields['DEPE_CODI'];
			$codUsDestino = $rs->fields['USUA_CODI'];
			$nombDestino = $rs->fields['USUA_NOMB'];
			$docUsuaDest = $rs->fields['USUA_DOC'];
			$rad = array();

			if ($codUsDestino) {
				if ($tomarNivel == "si") {
					$whereNivel = ",CODI_NIVEL=$usNivel";
				}
				$radicadosIn = join(",", $radicados);
				$proccarp = "Dev. ";
				$carp_codi = 12;
				$carp_per = 0;
				$isql = "update radicado
						set
						  RADI_USU_ANTE='$loginOrigen'
						  ,RADI_DEPE_ACTU=$depDestino
						  ,RADI_USUA_ACTU=$codUsDestino
						  ,CARP_CODI=$carp_codi
						  ,CARP_PER=$carp_per
						  ,RADI_LEIDO=0
						  , radi_fech_agend=null
						  ,radi_agend=null
						  $whereNivel
					 where radi_depe_actu=$depOrigen
						   AND radi_usua_actu=$codUsOrigen
						   AND RADI_NUME_RADI = $noRadicado";
				$this->db->conn->Execute($isql); # Ejecuta la busqueda
				$rad[] = $noRadicado;
				$this->insertarHistorico($rad, $depOrigen, $codUsOrigen, $depDestino, $codUsDestino, $observa, 12);
				array_splice($rad, 0);
				$retorno = $retorno . "$noRadicado ------> $nombDestino <br>";
				$this->registrarNovedad('NOV_DEV', $docUsuaDest, $noRadicado);
			} else {
				$retorno = $retorno . "<font color=red>$noRadicado ------> Usuario Anterior no se encuentra o esta inactivo</font><br>";
			}
		}
		return $retorno;
	}

	/** valida si el radicado se puede Enviar a otro Usuario
	 * @reg Array de registros/Radicados enviados
	 *
	 */
	function validateTrdSend($regs) {

		$searchRegs = implode(",", array_keys($regs));


		$iSql = "SELECT RADI_NUME_RADI FROM RADICADO
  		        WHERE
  		        radi_nume_radi in ($searchRegs)
  		        and RADI_NUME_RADI NOT IN
  						(SELECT radi_nume_radi
  						FROM sgd_rdf_retdocf where radi_nume_radi in ($searchRegs))";

		$rs = $this->db->conn->Execute($iSql); # Ejecuta la busqueda
		$validate = true;
		if ($rs) {
			while (!$rs->EOF) {
				$this->regsTrdFalse[] = $rs->fields['RADI_NUME_RADI'];
				$rs->MoveNext();
				$validate = false;
			}
		}

		$aSql = "SELECT RADI_NUME_SALIDA, ANEX_RADI_NUME FROM ANEXOS A, RADICADO R
            WHERE
            A.ANEX_RADI_NUME in ($searchRegs)
            AND A.anex_SALIDA=1 and A.RADI_NUME_SALIDA IS NOT NULL
            and A.ANEX_RADI_NUME<>A.RADI_NUME_SALIDA
            and  R.RADI_NUME_RADI = A.RADI_NUME_SALIDA
            and (R.SGD_EANU_CODIGO not in (2) or R.SGD_EANU_CODIGO is null)
            AND A.RADI_NUME_SALIDA NOT IN
            (SELECT RDF.radi_nume_radi
            FROM sgd_rdf_retdocf RDF where RDF.radi_nume_radi in (
            SELECT RADI_NUME_SALIDA FROM ANEXOS A
            WHERE
            A.ANEX_RADI_NUME in ($searchRegs)
            and A.ANEX_RADI_NUME<>A.RADI_NUME_SALIDA
            AND A.anex_SALIDA=1 and A.RADI_NUME_SALIDA IS NOT NULL
            ))";

		$rsA = $this->db->conn->Execute($aSql); # Ejecuta la busqueda
		if ($rsA) {
			while (!$rsA->EOF) {
				$this->regsATrdFalse[] = $rsA->fields['RADI_NUME_SALIDA'] . " (de:" . $rsA->fields['ANEX_RADI_NUME'] . ")";
				$rsA->MoveNext();
				$validate = false;
			}
		}
		return $validate;
	}

	/*
	 *
	 * valida si los formularios del radicado estan llenos
	 * @regs registro/radicado enviado (solo uno)
	 * Retorna array con los campos del formulario que estan vacios
	 */

	function verifyForm($regs) {
		//array para guardar el radicado y los campos que estan vacios
		//se guarda en la primera posicion el numero del radicado
		$campos = array();
		array_push($campos, $regs);

		/*		 * ********Genera un array con las RESPUESTAS de los formularios que tiene el radicado************************************************************* */
		$formRespuestas = "	select rad_meta_datos from sgd_rad_metadatos where radi_nume_radi in ($regs) ";
		$rsF = $this->db->conn->Execute($formRespuestas);
		$campos1 = array();
		if ($rsF) {
			while (!$rsF->EOF) {
				foreach ($rsF as $valor1) {
					foreach ($valor1 as $clave1) {
						$clave1 = json_decode($clave1, true);
						$campos1 = array_merge($campos1, $clave1);
					}
				}
				$rsF->MoveNext();
			}
		}
		/*		 * ***********Genera un array con las PREGUNTAS de los formularios que tiene el radicado************************************************************ */
		$formSQL = "select field_id from fields where estado=1 and project_id in (select id from projects where proceso in(select sgd_prc_codigo from sgd_radp_proceso where radi_nume_radi in ($regs)))";
		$rsF2 = $this->db->conn->Execute($formSQL);
		$campos2 = array();
		if ($rsF2) {
			while (!$rsF2->EOF) {
				foreach ($rsF2 as $valor2) {
					foreach ($valor2 as $clave2 => $valorP) {
						array_push($campos2, $valorP);
					}
				}
				$rsF2->MoveNext();
			}
		}

		/*		 * ************Busca si hay una pregunta sin respuesta y la agrega al array****************************************************** */
		foreach ($campos2 as $miv) {
			if ($campos1[$miv]) {
				
			} else {
				array_push($campos, $miv);
			}
		}

		return $campos;
	}

	/*
	 * Esta funcion verifica los formularios de cada registro/radicado
	 * por cada registro/radicado llama la funcion verifyForm
	 * @regsC array de registros/radicados enviados
	 *  @key registro unico
	 * Retorna string con los campos de los formularios que estan vacios
	 * por cada radicado
	 */

	function validateForms($regsC) {
		$res = array();
		foreach ($regsC as $key => $value) {

			$mm = $this->verifyForm($key);

			if (count($mm) > 1) {
				array_push($res, $mm);
			}
		}

		$respForms = "";
		foreach ($res as $key) {
			foreach ($key as $clave => $value) {
				if ($clave == 0) {
					$respForms .= "<br>Preguntas vacias del radicado: $value <br>";
				} else {
					$respForms .= "- $value ";
				}
			}
			$respForms .= "<br>";
		}

		return $respForms;
	}

}

?>
