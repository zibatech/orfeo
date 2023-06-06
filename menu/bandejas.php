<?php
// Radicacion

	// Esta consulta selecciona las carpetas como Devueltos,  Entrada ,salida
	$link1      = $enlace."$fechah&nomcarpeta=General&carpeta=9999&tipo_carpt=0\"";
	$link1show .= "<li><a $link1 target=\"mainFrame\" >General (Todos)</a></li>";
	if ($codusuario == 0){$codusuario = $_SESSION["codusuario"];}
    if ($dependencia == 0){$dependencia = $_SESSION["dependencia"];}
  $isql = "SELECT c.CARP_CODI,c.CARP_DESC  CARP_DESC, '0' NRADS
						,'0' CONTADOR_NOLEIDOS
            FROM CARPETA c 
            where c.carp_codi<>11 
						ORDER BY c.CARP_CODI";
	$rs   = $db->query($isql);
  $auxdevueltos = 0;
  while(!$rs->EOF){
    $numdata    = trim($rs->fields["CARP_CODI"]);
    $rsCarpDesc = $db->query($sqlCarpDep);
    $desccarpt  = $rs->fields["CARP_DESC"];
    $nRads      = 0;
    $nRadsNoLeidos      = 0;
    if($nRadsNoLeidos>=1) $nRadsNoLeidos = "<b> $nRadsNoLeidos </b>/"; else $nRadsNoLeidos = " $nRadsNoLeidos /";
    
    if($numdata==0) $numdata = 9998;
    $data       = (empty($descripcionCarpeta))? trim($desccarpt) : $descripcionCarpeta;
    $link1      = $enlace."$fechah&nomcarpeta=$data&carpeta=$numdata&tipo_carpt=0&order=14\"";
	if($desccarpt=='Devueltos'){$auxdevueltos = 1;}
    $link1show .= "<li><a $link1 target=\"mainFrame\" id='carpetap_$numdata'></a></li>";
    $rs->MoveNext();
  }
//si no trajo devueltos, se los coloco
if ($auxdevueltos == 0){
$link1      = $enlace."$fechah&nomcarpeta=Devueltos&carpeta=12&tipo_carpt=0\"";
$link1show .= "<li><a $link1 target=\"mainFrame\" >Devueltos (0)</a></li>";
}

  // Se realiza la cuenta de radicados en Visto Bueno VoBo
  if ($numdata == 11) {
    if ($codusuario == 1) {
      $isql = "select count(*) as CONTADOR
											, count(CASE  WHEN (radi_leido =0) THEN 1 ELSE NULL END) CONTADOR_NOLEIDOS
                    from radicado
                    where carp_per = 0 and
                    carp_codi = $numdata and
                    radi_depe_actu = $dependencia and
                    radi_usua_actu = $codusuario";
    } else {
      $isql = "select count(*) as CONTADOR
                    from radicado
                    where carp_per = 0 and
                    carp_codi = $numdata and
                    radi_depe_actu = $dependencia and
                    (radi_usu_ante = '$krd' or
                    (radi_usua_actu = $codusuario and radi_depe_actu=$dependencia))";
    }
  } else {
    $isql   = "select count(*) as CONTADOR
								   , count(CASE  WHEN (radi_leido =0) THEN 1 ELSE NULL END) CONTADOR_NOLEIDOS
                  from radicado
                  where carp_per = 0 and
                  carp_codi = 11 and
                  radi_depe_actu = $dependencia and
                  radi_usua_actu = $codusuario";
    $addadm = "&adm=0";
  }

  // Cuenta los numero de radicados por visto bueno
  $data       = "Documenos para Visto Bueno";
 
 $rs         = $db->conn->query($isql);
  $numero_radicados = (!$rs->EOF)? $rs->fields['CONTADOR'] : 0;
  $numero_radicados_noleidos = (!$rs->EOF)? $rs->fields['CONTADOR_NOLEIDOS'] : 0;
  $link11      = $enlace."$fechah&nomcarpeta=$data&carpeta=11&tipo_carpt=0\"";
  $link11show .= "<li><a $link11 target=\"mainFrame\" >Visto Bueno ($numero_radicados / $numero_radicados_noleidos)</a></li>";
/**  //Agendado
  $isql        =" SELECT COUNT(1) AS CONTADOR
                  FROM SGD_AGEN_AGENDADOS agen
                  WHERE usua_doc='$usua_doc'
                  and agen.SGD_AGEN_ACTIVO=1
                  and (agen.SGD_AGEN_FECHPLAZO >= $sqlFechaHoy )";

  $rs       = $db->conn->query($isql);
  $num_exp  = $rs->fields["CONTADOR"];
  $data     = "Agendados no vencidos";
  $link2    = $enlace1."$fechah&nomcarpeta=$data&tipo_carpt=0\"";
  $link2show= "<li><a $link2 target=\"mainFrame\" >Agendado($num_exp)</a></li>";

  //Agendado  Vencido
  $isql="SELECT COUNT(1) AS CONTADOR
          FROM SGD_AGEN_AGENDADOS AGEN
          WHERE  USUA_DOC='$usua_doc'
          and agen.SGD_AGEN_ACTIVO=1
          and (agen.SGD_AGEN_FECHPLAZO <= $sqlFechaHoy)";

  $rs       = $db->conn->query($isql);

  $num_exp  = $rs->fields["CONTADOR"];
	$data     ="Agendados vencidos";
  $link3    = $enlace2."$fechah&nomcarpeta=$data&&tipo_carpt=0&adodb_next_page=1\"";
  $link3show= "<li><a $link3 target=\"mainFrame\" >Agendado Vencido (<font color='#990000'>$num_exp</font>)</a></li>";
**/

  //Informados
  $isql   =" SELECT COUNT(1) AS CONTADOR
               FROM INFORMADOS
             WHERE DEPE_CODI=$dependencia
              and usua_codi=$codusuario
              and info_conjunto=0";

  $rs1     = $db->conn->query($isql);
  $numerot = ($rs1)? $rs1->fields["CONTADOR"] : 0;
  $link4show= "<li><a $enlace3 target=\"mainFrame\" >Informados ($numerot)</a></li>";

  //Tramite conjunto
  $isql="SELECT COUNT(1) AS CONTADOR
         FROM INFORMADOS
         WHERE DEPE_CODI=$dependencia
          and usua_codi=$codusuario
          and info_conjunto>=1 ";
  $rs1=$db->query($isql);

  $numerot = ($rs1)? $rs1->fields["CONTADOR"] : 0;
  if($numerot>=1){
    // $link5show= "<li><a $enlace4 target=\"mainFrame\"> Tramite Conjunto ($numerot)</a></li>";
  }


  //Tramite conjunto
  $isql="SELECT COUNT(1) AS CONTADOR
         FROM TRAMITECONJUNTO
         WHERE DEPE_CODI=$dependencia
          and usua_codi=$codusuario
          and info_conjunto>=1 ";
  $rs1=$db->query($isql);

  $numerot = ($rs1)? $rs1->fields["CONTADOR"] : 0;
  if($numerot>=1){
    $link5show= "<li><a $enlace4 target=\"mainFrame\"> Tramite Conjunto ($numerot)</a></li>";
  }
  //Ultimas transacciones del usuario
  $data     ="Ultimas Transacciones del Usuario";
  $link6    = $enlace5."$fechah&nomcarpeta=$data&tipo_carpt=0\"";
  $link6show= "<li><a $link6 target=\"mainFrame\">Transacciones</a></li>";

  //Prioritarios
  /*
  $numeroP = 0;
  include ("include/query/queryCuerpoPrioritario.php");
  $rsP               = $db->conn->query($isqlPrioritario);
  $numeroP           = $rsP->fields["NUMEROP"];
  $clasePrioritarios = ($numeroP >= 1)? "titulosError" : "menu_princ";
  $link7             = $enlace6."$fechah&nomcarpeta=$data&tipo_carpt=0\"";
  $link7show         = "<li><a $link6 target=\"mainFrame\">Prioritarios ($numeroP)</a></li>";
*/
  //Enlace carpetas Personales
  $link8             = $enlace7."fechah=$fechah&adodb_next_page=1\"";
  $link8show         = "<a tabindex=\"-1\"  target=\"mainFrame\" onmouseover=\"cargarValoresCarpetasPersonales();\" > Personales </a>";
  $link9show        .= "<li><a tabindex=\"-1\" $link8 target=\"mainFrame\"> Nueva Carpeta <i class=\" fa fa-plus-circle\"></i></a></li>";

  //Carpetas Personales
  $isql ="SELECT
            DISTINCT CODI_CARP,
            DESC_CARP,
            NOMB_CARP
          FROM
            CARPETA_PER
          WHERE
            USUA_CODI=$codusuario AND
            DEPE_CODI=$dependencia ORDER BY CODI_CARP  ";

  $rs = $db->query($isql);
  while(!$rs->EOF){
		$data    = trim($rs->fields["NOMB_CARP"]);
		$numdata = trim($rs->fields["CODI_CARP"]);
		$detalle = trim($rs->fields["DESC_CARP"]);
		$data    = trim($rs->fields["NOMB_CARP"]);

		$isql    = "SELECT
									COUNT(1) AS CONTADOR
								FROM
									RADICADO
								WHERE
									CARP_PER=1 AND
									CARP_CODI = $numdata AND
									RADI_DEPE_ACTU = $dependencia AND
									RADI_USUA_ACTU=$codusuario ";

		$rs1     = $db->query($isql);
		$numerot = $rs1->fields["CONTADOR"];
		$datap   = "$data(Personal)";

		$link9       = $enlace8."fechah=$fechah&tipo_carp=1&carpeta=$numdata&nomcarpeta=$data\"";
		$link10show .= "<li><a tabindex=\"-1\" $link9 target=\"mainFrame\"  id='carpetaPersonal_$numdata'> $data($numerot) </a></li>";
		$rs->MoveNext();
  }
    
  $numdata++;$link9       = $enlace8."fechah=$fechah&tipo_carp=1&carpeta=$numdata&nomcarpeta=$data\"";
  $link10show .= "<li><a tabindex=\"-1\" $link9 target=\"mainFrame\"  id='carpetaPersonal_$numdata'>  </a></li>";
  $numdata++;$link9       = $enlace8."fechah=$fechah&tipo_carp=1&carpeta=$numdata&nomcarpeta=$data\"";
  $link10show .= "<li><a tabindex=\"-1\" $link9 target=\"mainFrame\"  id='carpetaPersonal_$numdata'>  </a></li>";
  $numdata++;$link9       = $enlace8."fechah=$fechah&tipo_carp=1&carpeta=$numdata&nomcarpeta=$data\"";
  $link10show .= "<li><a tabindex=\"-1\" $link9 target=\"mainFrame\"  id='carpetaPersonal_$numdata'>  </a></li>";    

  //Consultas
  $link21      = $enlace21."&etapa=1&s_Listado=VerListado&fechah=$fechah\"";
  $link24      = $enlace24."&etapa=1&s_Listado=VerListado&fechah=$fechah\"";
  $link25      = $enlace25."&etapa=1&s_Listado=VerListado&fechah=$fechah\"";
  $link26      = $enlace26."&etapa=1&s_Listado=VerListado&fechah=$fechah\"";
  if ( !$rrrrrr)
	$link21show  = '<li class="dropdown-submenu"><a tabindex="-1" target="mainFrame"> Consultas </a>
				<ul class="dropdown-menu">
					<li><a tabindex="-1"'. $link21 .'target="mainFrame"> Consulta Clasica</a></li>
					<li><a tabindex="-1"'. $link25 .'target="mainFrame"> Consulta Expedientes</a></li>
				</ul>
			</li>';
  else
	  $link21show  = "<li><a tabindex=\"-1\" $link21 target=\"mainFrame\"> Consultas </a></li>";
  //Estadisticas
  $link22      = $enlace22."&fechah=$fechah\"";
  $link22show  = "<li><a tabindex=\"-1\" $link22 target=\"mainFrame\"> Estadisticas </a></li>";
  //reportesCRA
  $link23      = $enlace23."&fechah=$fechah\"";
  $link23show  = "<li><a tabindex=\"-1\" $link23 target=\"mainFrame\"> reportesCRA </a></li>";
  $tiene_acceso_admin = in_array($krd, $usuarios_admin);
?>
