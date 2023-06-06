<?php

//ini_set('display_errors', 1);
/**
 * @module index_frames
 *
 * @author Cesar Gonzalez <cesar.gonzalez@hdsas.co> - Fundacion Correlibre.
 * @author Jairo Losada   <jlosada@gmail.com> - Fundacion Correlibre.
 * @author Osmar Castillo <oacastillol@gmail.com> - Fundacion Correlibre.
 *
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 *
 * @copyleft
 * Desarrollos tomados de Orfeo version qeu Inicia en la SuperServicios año 2003

OrfeoGPL Models are the data definition of OrfeoGPL Information System
Copyright (C) 2010 Correlibre.org.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

$ruta_raiz = ".";
include_once "include/tx/sanitize.php";
if (!$_SESSION['dependencia'] || $_GET['close']) {
  header("Location: $ruta_raiz/login.php");
  echo "<script>parent.frames.location.reload();top.location.reload();</script>";
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/processConfig.php";
require_once "include/tx/Menus.php";
require_once($ruta_raiz . "/constanciaEjecutoria/tx/constanciaEjecutoria.php");
$db = new ConnectionHandler($ruta_raiz);
$menuObject = new Menus($db);
$constancia = new ConstanciaEjecutoria($db);

$cambioKrd = $_GET["cambioKrd"];
if ($cambioKrd && $krd != $cambioKrd) {
  $_SESSION["krd"] = $_GET["cambioKrd"];
  $krd = $cambioKrd;
  $_SESSION["dependencia"] = $_GET["cambioDepeCodi"];
  $_SESSION["codusuario"]  = $_GET["cambioUsuaCodi"];
  $_SESSION["tpDepeRad"][1]  = $_GET["cambioDepeCodi"];
  $_SESSION["tpDepeRad"][2]  = $_GET["cambioDepeCodi"];
  $_SESSION["tpDepeRad"][3]  = $_GET["cambioDepeCodi"];
  include "session_orfeo.php";
  session_regenerate_id();
}
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$tip3Nombre     = $_SESSION["tip3Nombre"];
$tip3desc       = $_SESSION["tip3desc"];
$tpDescRad      = $_SESSION["tpDescRad"];
$tip3img        = $_SESSION["tip3img"];
$ESTILOS_PATH   = $_SESSION["ESTILOS_PATH"];
$nombUser       = $_SESSION["usua_nomb"];
$tpNumRad       = $_SESSION["tpNumRad"];
$tpPerRad       = $_SESSION["tpPerRad"];
$fechah         = date("Ymdhms");
$phpsession     = "c=$fechah&";
$ruta_raiz      = ".";
$sqlFechaHoy    = $db->conn->DBTimeStamp(time());

if (
  $_SESSION["usua_perm_envios"]    >= 1 /*|| $_SESSION["usua_perm_adminflujos"]== 1*/
  || $_SESSION["usua_perm_modifica"]  >= 1 || $_SESSION["usua_perm_intergapps"] == 1
  || $_SESSION["usua_perm_impresion"] >= 1 || ($_SESSION["usua_perm_anu"] == 3 or $_SESSION["usua_perm_anu"] == 1)
  || $_SESSION["usua_perm_trd"]       == 1 || $_SESSION["usua_admin_archivo"]   >= 1
  || $_SESSION["usua_perm_prestamo"]  == 1 || $_SESSION["usua_perm_dev"]        == 1
) {
  $menuAcciones = 1;
  $acciones     = array();

  if ($_SESSION["usua_perm_anu"] == 3 or $_SESSION["usua_perm_anu"] == 1) {
    $anulacion = array(
      'subMenu' => 0,
      'url' => "anulacion/cuerpo_anulacion.php?$phpsession&tpAnulacion=1&fechah=$fechah",
      'nombre' => "Anulaci&oacute;n"
    );
    $acciones['anulacion'] = $anulacion;
  }

  if ($_SESSION["usua_perm_envios"] >= 1) {
    $envios = array(
      'subMenu' => 0,
      'url' => "radicacion/formRadEnvios.php?$phpsession&fechah=$fechah&usr=" . md5($dep) . "&primera=1&ent=1",
      'nombre' => "Envios"
    );
    $acciones['envios'] = $envios;
  }


  if ($_SESSION["usua_perm_adminasig"] >= 1) {
    $asignar = array(
      'subMenu' => 0,
      'url' => "radicacion/adminAsignar.php?$phpsession",
      'nombre' => "Reasignar"
    );
    $acciones['asignar'] = $asignar;
  }

  if (
    $_SESSION['contratistas_administrar_contratos'] >= 1 ||
    $_SESSION['contratistas_supervision'] >= 1 ||
    $_SESSION['contratistas_mis_contratos'] >= 1 ||
    $_SESSION['contratistas_certificacion'] >= 1 ||
    $_SESSION['contratistas_procesar_pagos'] >= 1 ||
    $_SESSION['contratistas_director_contratos'] >= 1
  ) {

    $sub = [];

    if ($_SESSION['contratistas_administrar_contratos']) {
      $sub[] = [
        'subMenu' => 0,
        'url' => "contratistas/contratos.php?$phpsession",
        'nombre' => "Contratos"
      ];
    }

    if ($_SESSION['contratistas_certificacion']) {
      $sub[] = [
        'subMenu' => 1,
        'url' => "contratistas/certificar.php?$phpsession",
        'nombre' => "Certificación de pagos"
      ];
    }

    if ($_SESSION['contratistas_supervision']) {
      $sub[] = [
        'subMenu' => 3,
        'url' => "contratistas/supervision.php?$phpsession",
        'nombre' => "Supervisión"
      ];
    }

    if ($_SESSION['contratistas_mis_contratos']) {
      $sub[] = [
        'subMenu' => 2,
        'url' => "contratistas/mis_contratos.php?$phpsession",
        'nombre' => "Mis contratos"
      ];
    }

    if ($_SESSION['contratistas_procesar_pagos']) {
      $sub[] = [
        'subMenu' => 4,
        'url' => "contratistas/pagar.php?$phpsession",
        'nombre' => "Vo Bo Dirección"
      ];
    }

    if ($_SESSION['contratistas_director_contratos']) {
      $sub[] = [
        'subMenu' => 4,
        'url' => "contratistas/consulta_informes_pagados.php?$phpsession",
        'nombre' => "Consultar contratos"
      ];
    }

    $contratoMenu = array('subMenu' => 1, 'url' => '#', 'nombre' => 'Contratistas', 'sub' => $sub);
    $acciones["contratistas"] = $contratoMenu;
  }


  if ($_SESSION["usua_perm_admin_email_masive"] >= 1) {
    $asignar = array(
      'subMenu' => 0,
      'url' => "envios/adminMasivaSend.php",
      'nombre' => "Masiva Email"
    );
    $acciones['masiveEmail'] = $asignar;
  }


  if ($_SESSION["usua_perm_trd"] >= 1) {
    $sub = array(
      'series' => array(
        'subMenu' => 0,
        'url' => "./trd/admin_series.php?$phpsession &fechah=$fechah",
        'nombre' => 'Series'
      ),
      'subSeries' => array(
        'subMenu' => 0,
        'url' => "./trd/admin_subseries.php?$phpsession&fechah=$fechah",
        'nombre' => 'Subseries'
      ),
      'matrizRelacion' => array(
        'subMenu' => 0,
        'url' => "./trd/cuerpoMatriTRD.php?$phpsession&fechah=$fechah",
        'nombre' => 'Matriz Relaci&oacute;n'
      ),
      'tiposDocumentales' => array(
        'subMenu' => 0,
        'url' => "./trd/admin_tipodoc.php?$phpsession&fechah=$fechah",
        'nombre' => 'Tipos Documentales'
      ),
      'modificacionTRD' => array(
        'subMenu' => 0,
        'url' => "./trd/procModTrdArea.php?$phpsession&fechah=$fechah",
        'nombre' => 'Modificacion TRD Area'
      ),
      'ListadoTablas' => array(
        'subMenu' => 0,
        'url' => "./trd/informe_trd.php?$phpsession&fechah=$fechah",
        'nombre' => 'Listado Tablas de Retencion Documental'
      ),
    );
    $trdMenu = array('subMenu' => 1, 'url' => "#", 'nombre' => 'TRD', 'sub' => $sub);
    $acciones["trd"] = $trdMenu;
  }

  if ($_SESSION["usua_perm_impresion"] >= 1) {
    if (!isset($usua_perm_impresion)) {
      $usua_perm_impresion = "";
    }
    $enviar = array(
      'subMenu' => 0,
      'url' => "envios/cuerpoMarcaEnviar.php?porEnviar=1&$phpsession&fechaf=$fechah&usua_perm_impresion=$usua_perm_impresion&carpeta=8&nomcarpeta=Documentos pendientes de Env&iacute;o&orderTipo=desc&orderNo=3",
      'nombre' => "Por Enviar"
    );
    $acciones["enviar"] = $enviar;
  }
  if ($_SESSION["usua_perm_modifica"] >= 1) {
    $modificacion = array(
      'subMenu' => 0,
      'url' => "radicacion/edtradicado.php?$phpsession&fechah=$fechah&primera=1&ent=2",
      'nombre' => "Modificaci&oacute;n"
    );
    $acciones["modificacion"] = $modificacion;
  }
  if ($_SESSION["usua_perm_prestamo"] == 1) {
    $sub = array(
      "prestamoDocumentos" => array(
        'subMenu' => 0,
        'url' => "./prestamo/prestamo.php?opcionMenu=1",
        'nombre' => 'Prestamo de documentos'
      ),
      "devolucionDocumentos" => array(
        'subMenu' => 0,
        'url' => "./prestamo/prestamo.php?opcionMenu=2",
        'nombre' => 'Devolucion de documentos'
      ),
      "generacionReportes" => array(
        'subMenu' => 0,
        'url' => "./prestamo/prestamo.php?opcionMenu=0",
        'nombre' => 'Generacion de reportes'
      ),
      "cancelarSolicitudes" => array(
        'subMenu' => 0,
        'url' => "./prestamo/prestamo.php?opcionMenu=3",
        'nombre' => 'Cancelar solicitudes'
      )
    );
    $prestamo = array('subMenu' => 1, 'url' => "#", 'nombre' => "Prestamo", 'sub' => $sub);
    $acciones["prestamo"] = $prestamo;
  }

  if ($_SESSION["USUA_PERM_RAD_ESPECIAL"] >= 1) {
    $reasignarEspecial = array(
      'subMenu' => 0,
      'url' => "reasigna_rad_new.php?$phpsession&fechah=$fechah&usr=" . md5($dep) . "&primera=1&ent=1",
      'nombre' => "Reasignar Radicado Especial"
    );
    $acciones["reasignarEspecial"] = $reasignarEspecial;
  }

  if ($_SESSION["USUA_PERM_TRANS_RAD"] >= 1) {
    $trasladarRadicados = array(
      'subMenu' => 0,
      'url' => "Administracion/usuario/trasladar_radicados.php?$phpsession&fechah=$fechah &usr=" . md5($dep) . "&primera=1&ent=1",
      'nombre' => "Transladar Radicados"
    );
    $acciones["trasladarRadicados"] = $trasladarRadicados;
  }

  if ($_SESSION["usua_admin_archivo"] >= 1) {
    $num_exp = $menuObject->getArchiveCount();
    $sub = array(
      "archivo" => array(
        'subMenu' => 0,
        'url' => "./expediente/cuerpo_exp.php?$phpsession&fechaf=$fechah&carpeta=8&nomcarpeta=Expedientes&orno=1&adodb_next_page=1",
        'nombre' => "1. Archivo ($num_exp)"
      )
    );
    if ($_SESSION["usua_admin_archivo"] != 3 and $_SESSION["usua_admin_archivo"] != 4) {
      $sub["busquedaAvanzada"] = array(
        'subMenu' => 0,
        'url' => "archivo/busqueda_archivo.php?$phpsession&dep_sel=$dep_sel&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&tipo_archivo=$tipo_archivo&carpeta",
        'nombre' => '2. Busqueda Avanzada'
      );
    }
    $sub["reporteArchivados"] = array(
      'subMenu' => 0,
      'url' => "archivo/reporte_archivo.php?$phpsession&adodb_next_page&nomcarpeta&fechah=$fechah&$orno&carpeta&tipo=1",
      'nombre' => '3. Reporte por Radicados Archivados'
    );
    if ($_SESSION["usua_admin_archivo"] != 3 and $_SESSION["usua_admin_archivo"] != 4) {
      $sub["cambioColeccion"] = array(
        'subMenu' => 0,
        'url' => "archivo/inventario.php?$phpsession&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&carpeta&tipo=2",
        'nombre' => '4.Cambio de Coleccion'
      );
      $sub["inventarioConsolidado"] = array(
        'subMenu' => 0,
        'url' => "archivo/inventario.php?$phpsession&fechah=$fechah&$orno&nomcarpeta&carpeta&tipo=1",
        'nombre' => '5.Inventario Consolidado Capacidad'
      );
      $sub["formatoInventario"] = array(
        'subMenu' => 0,
        'url' => "archivo/formatoUnico.php?$phpsession&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '6.Formato Unico De Inventario Documental'
      );
      $sub["radicadosSinExpediente"] = array(
        'subMenu' => 0,
        'url' => "archivo/sinexp.php?$phpsession&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&carpeta&tipo=3",
        'nombre' => '7.Radicados Archivados Sin Expediente'
      );
      $sub["alertaExpedientes"] = array(
        'subMenu' => 0,
        'url' => "archivo/alerta.php?$phpsession&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '8.Alerta Expedientes'
      );
    }
    $sub["buquedaGeneral"] = array(
      'subMenu' => 0,
      'url' => "archivo/busqueda_central.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
      'nombre' => '9.Busqueda Archivo Central'
    );
    $sub["buquedaFondo"] = array(
      'subMenu' => 0,
      'url' => "archivo/busqueda_Fondo_Gestion.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
      'nombre' => '10.Busqueda Archivo Fondo Gestion'
    );
    if ($_SESSION["usua_admin_archivo"] == 3 or $_SESSION["usua_admin_archivo"] == 5) {
      $sub["insertarCentral"] = array(
        'subMenu' => 0,
        'url' => "archivo/insertar_central.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '11.Insertar Archivo Central'
      );
    }
    if ($_SESSION["usua_admin_archivo"] >= 4) {
      $sub["insertarFondo"] = array(
        'subMenu' => 0,
        'url' => "archivo/insertar_Fondo_Gestion.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '12.Insertar Archivo Fondo Gestion'
      );
    }
    if ($_SESSION["usua_admin_archivo"] == 2 or $_SESSION["usua_admin_archivo"] == 5) {
      $sub["administracionEdificios"] = array(
        'subMenu' => 0,
        'url' => "archivo/adminEdificio.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '13.Administraci&oacute;n de Edificios'
      );
      $sub["administracionRelacion"] = array(
        'subMenu' => 0,
        'url' => "archivo/adminDepe.php?$phpsession&krd=$krd&fechah=$fechah&$orno&adodb_next_page",
        'nombre' => '14.Administracion de Relaci&oacute;n Dependencia-Edificios'
      );
    }
    $archivoMenu = array('subMenu' => 1, 'url' => "#", 'nombre' => "Archivo", 'sub' => $sub);
    $acciones["archivo"] = $archivoMenu;
  }

  //Menu para funcionarios del grupo de notificaciones que administrar constancia ejecutoria
  $esRolRevisor = $constancia->esRolRevisor($dependencia, $codusuario);
  $esRolAprobador = $constancia->esRolAprobador($dependencia, $codusuario);
  $esRolFirmante = $constancia->esRolFirmante($dependencia, $codusuario);

  if ($esRolRevisor || $esRolAprobador || $esRolFirmante) {
    if ($esRolRevisor) {
      $subConstancia["rolRevisor"] = array(
        'subMenu' => 0,
        'url' => "./constanciaEjecutoria/revisor.php",
        'nombre' => 'Rol Revisor'
      );
    }
    if ($esRolAprobador) {
      $subConstancia["rolAprobador"] = array(
        'subMenu' => 0,
        'url' => "./constanciaEjecutoria/aprobador.php",
        'nombre' => 'Rol Aprobador'
      );
    }
    if ($esRolFirmante) {
      $subConstancia["rolFirmante"] = array(
        'subMenu' => 0,
        'url' => "./constanciaEjecutoria/firmante.php",
        'nombre' => 'Rol Firmante'
      );
    }

    $constanciaMenu = array('subMenu' => 1, 'url' => "#", 'nombre' => "Constancia Ejecutoria", 'sub' => $subConstancia);
    $acciones["constanciaMenu"] = $constanciaMenu;
  }
} else {
  $menuAcciones = 0;
}

if ($_SESSION["usua_admin_sistema"] >= 1 || $tiene_acceso_admin) {
  $menuAdministracion = 1;
  $administracion = array();
  $sub = array(
    "crearFlujo" => array(
      'subMenu' => 0,
      'url' => "./Administracion/usuario/cuerpoConsulta.php",
      'nombre' => "Permisos TRD"
    ),
    "editarFlujo" => array(
      'subMenu' => 0,
      'url' => "./Administracion/usuario/index.php?$sendSession",
      'nombre' => "Usuarios y Perfiles"
    )
  );
  $usuarios = array('subMenu' => 1, 'url' => "#", 'nombre' => "Usuarios y permisos", 'sub' => $sub);
  $administracion['permisosPermisos'] = $usuarios;
  $tarifas = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_tarifas.php?$sendSession",
    'nombre' => "Tarifas"
  );
  $administracion["tarifas"] = $tarifas;
  $dependencias = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_dependencias.php?$sendSession",
    'nombre' => "Dependencias"
  );
  $administracion["dependencias"] = $dependencias;
  $diasHabiles = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_nohabiles.php?$sendSession",
    'nombre' => "Dias no habiles"
  );
  $administracion["diasNoHabiles"] = $diasHabiles;
  $correspondencia = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_fenvios.php?$sendSession",
    'nombre' => "Env&iacute;o de correspondencia"
  );
  $administracion["envioCorrespondencia"] = $correspondencia;
  $msjRapido = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_mensajeRapido.php?$sendSession",
    'nombre' => "Mensajes Rapidos"
  );
  $administracion["mensajesRapidos"] = $msjRapido;
  $tablaSencilla = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_tsencillas.php?$sendSession",
    'nombre' => "Tablas sencillas"
  );
  $administracion["tablasSencillas"] = $tablaSencilla;
  $tiposRadic = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_trad.php?$sendSession",
    'nombre' => "Tipos de radicaci&oacute;n"
  );
  $administracion["tiposRadicados"] = $tiposRadic;
  $paises = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_paises.php?$sendSession?",
    'nombre' => "Pa&iacute;ses"
  );
  $administracion["paises"] = $paises;
  $departamentos = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_dptos.php?$sendSession",
    'nombre' => "Departamentos"
  );
  $administracion["departamentos"] = $departamentos;
  $municipios = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_mcpios.php?$sendSession",
    'nombre' => "Municipios"
  );
  $administracion["municipios"] = $municipios;
  $plantillas = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/adm_plantillas.php?$sendSession",
    'nombre' => "Plantillas"
  );
  $administracion["plantillas"] = $plantillas;
  $reasigmas = array(
    'subMenu' => 0,
    'url' => "./reasignar/rea_aut_rad.php?$sendSession",
    'nombre' => "Reasignación masiva"
  );
  $administracion["reasigmas"] = $reasigmas;
  $administracion["sys_config"] = array(
    'subMenu' => 0,
    'url' => "./Administracion/tbasicas/sys_config.php?$sendSession",
    'nombre' => "configuración"
  );
} else {
  $menuAdministracion = 0;
}

$opciones = array(
  "ayuda" => array(
    'subMenu' => 0,
    'url' => "$url_ayuda",
    'nombre' => 'Ayuda'
  ),
  "plantillas" => array(
    'subMenu' => 0,
    'url' => "plantillas.php?fechah=$fechah&info=false",
    'nombre' => 'Plantillas Más Comunes'
  ),
);

$usuario = array(
  'perfil' => array('subMenu' => 0, 'url' => "mod_datos.php?&fechah=$fechah&info=false", 'nombre' => 'Perfil'),
);

$rs = $menuObject->getUsers($_SESSION["usua_email"]);

while (!$rs->EOF) {
  $cambioKrd = $rs->fields["USUA_LOGIN"];
  $cambioUsuaCodi = $rs->fields["USUA_CODI"];
  $cambioDepeCodi = $rs->fields["DEPE_CODI"];
  $cambioDepeNomb = $rs->fields["DEPE_NOMB"];
  $emailUs = $rs->fields["USUA_EMAIL"];
  $usuario["usuario$cambioDepeCodi"] = array(
    'subMenu' => 0,
    'url' => "index_frames.php?&fechah=$fechah&info=false&cambioKrd=$cambioKrd&cambioUsuaCodi=$cambioUsuaCodi&cambioDepeCodi=$cambioDepeCodi",
    "nombre" => "$cambioDepeCodi - $cambioDepeNomb ($cambioKrd) - $emailUs",
    "noframe" => true
  );
  $rs->MoveNext();
}

if ($_SESSION["autentica_por_LDAP"] != 1) {
  $usuario["cambioDeClave"] = array('subMenu' => 0, 'url' => "contraxx.php?&fechah=$fechah", 'nombre' => "Cambio de clave");
}

$usuario["salir"] = array('subMenu' => 0, 'url' => "cerrar_session.php?", 'nombre' => " Salir ", "noframe" => true);

$radicacion = array();
$menuRadicacion = 0;
$i = 0;

if ($_SESSION["USUA_PRAD_TP1"]) {
  $menuRadicacion = 1;
  $enlace2   = "radicacion/chequear.php?$phpsession&primera=1&ent=1&depende=$dependencia";
  $radicacion["radica2"] = array('subMenu' => 0, 'url' => "$enlace2", 'nombre' => "$tpDescRad[0]");
}

if ($_SESSION["USUA_PRAD_TP2"]) {
  $menuRadicacion = 1;
  $enlace0   = "radicacion/chequear.php?$phpsession&primera=1&ent=2&depende=$dependencia";
  $radicacion["radica0"] = array('subMenu' => 0, 'url' => "$enlace0", 'nombre' => "$tpDescRad[1]");
}

if ($_SESSION["usua_masiva"] == 1) {
  $menuRadicacion = 1;
  $sub = array(
    "masivaExterna" =>
    array(
      'subMenu' => 0,
      'url' => "./radsalida/masiva3/upload2PorExcel.php?$phpsession&fechah=$fechah",
      'nombre' => "Masiva externa"
    ),

    "recuperarListado" => array(
      'subMenu' => 0,
      'url' => "./radsalida/cuerpo_masiva_recuperar_listado.php?$phpsession&fechah=$fechah",
      'nombre' => "Recuperar Listado"
    )
  );
  $radicacion["masiva"] = array('subMenu' => 1, 'url' => "#", 'nombre' => 'Masiva', 'sub' => $sub);
}

if ($_SESSION["USUA_PRAD_TP3"]) {
  $menuRadicacion = 1;
  $enlace1   = "radicacion/NEW.php?$phpsession&dependencia=$dependencia&ent=3&depende=$dependencia";
  $radicacion["radica3"] = array('subMenu' => 0, 'url' => "$enlace1", 'nombre' => "$tpDescRad[2]");
}

#Menu de Notificaciones
$menuRadicacion = 1;
$sub = array();

if ($_SESSION["USUA_PRAD_TP4"]) {
  if ($tpDescRad[3]) {
    $sub["notificacionCircInt"] = array(
      'subMenu' => 0,
      'url' => "radicacion/NEW.php?$phpsession&dependencia=$dependencia&ent=4&depende=$dependencia",
      'nombre' => "$tpDescRad[3]"
    );
  }
}

if ($_SESSION["USUA_PRAD_TP5"]) {
  if ($tpDescRad[4]) {
    $sub["notificacionCircExt"] = array(
      'subMenu' => 0,
      'url' => "radicacion/chequear.php?$phpsession&primera=1&ent=5&depende=$dependencia",
      'nombre' => "$tpDescRad[4]"
    );
  }
}

if ($_SESSION["USUA_PRAD_TP6"]) {
  if ($tpDescRad[5]) {
    $sub["notificacionResolucion"] = array(
      'subMenu' => 0,
      'url' => "radicacion/chequear.php?$phpsession&primera=1&ent=6&depende=$dependencia",
      'nombre' => "$tpDescRad[5]"
    );
  }
}

if ($_SESSION["USUA_PRAD_TP7"]) {
  if ($tpDescRad[6]) {
    $sub["notificacionAuto"] = array(
      'subMenu' => 0,
      'url' => "radicacion/chequear.php?$phpsession&primera=1&ent=7&depende=$dependencia",
      'nombre' => "$tpDescRad[6]"
    );
  }
}


$radicacion["notificaciones"] = array('subMenu' => 1, 'url' => "#", 'nombre' => 'Notificaciones', 'sub' => $sub);


if ($_SESSION["USUA_PRAD_TP8"]) {
  $menuRadicacion = 1;
  $enlace7   = "radicacion/NEW.php?$phpsession&dependencia=$dependencia&ent=8&depende=$dependencia";
  $radicacion["radica8"] = array('subMenu' => 0, 'url' => "$enlace7", 'nombre' => "$tpDescRad[7]");
}


if ($_SESSION["usua_perm_owncloud"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["ownCloud"] = array(
    'subMenu' => 0,
    'url' => "uploadFiles/orfeocloud/orfeoCloud.php?$phpsession&primera=1&ent=2&depende=$dependencia",
    'nombre' => 'Cargar Archivos Nube Propia'
  );
}

if ($_SESSION["perm_radi"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["asociarImagenes"] = array(
    'subMenu' => 0,
    'url' => "uploadFiles/uploadFileRadicado.php?$phpsession&primera=1&ent=2&depende=$dependencia",
    'nombre' => 'Asociar Imagenes'
  );
  $radicacion["subirAnexos"] = array('subMenu' => 0, 'url' => "uploadFiles/uploadAnexRadicado.php?$phpsession&primera=1&ent=2&depende=$dependencia", 'nombre' => 'Subir anexos');
}

if ($_SESSION["USUA_PERM_EXPORTEXP"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["exportExpediente"] = array('subMenu' => 0, 'url' => "expediente/expedienteZip.php", 'nombre' => 'Exportar Expediente');
}

if ($_SESSION["PERM_DESCARGAEXP"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["descargaExpediente"] = array('subMenu' => 0, 'url' => "expediente/expedienteDownload.php", 'nombre' => 'Descargar Expediente');
}
if ($_SESSION["usuaPermRadEmail"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["email"] = array('subMenu' => 0, 'url' => "radiMail/index.php", 'nombre' => 'e-Mail');
  //$radicacion["email"]=array('subMenu'=>0,'url'=>"radiMail_3/index.php",'nombre'=>'e-Mail');
  //$radicacion["email"]=array('subMenu'=>0,'url'=>"radiMail_2/index.php",'nombre'=>'Client Mail');
  //$radicacion["email"]=array('subMenu'=>0,'url'=>"uploadFiles/uploadAnexRadicado.php?$phpsession&primera=1&ent=2&depende=$dependencia" ,'nombre'=>'Subir anexo');
}

if ($_SESSION["USUA_PERM_RADEMAIL_AUTO"] >= 1) {
  $menuRadicacion = 1;
  $radicacion["emailAuto"] = array('subMenu' => 0, 'url' => "radiMail_3/index.php", 'nombre' => 'e-Mail Automatico');
}

$bandejas = array();
$menuBandejas = 1;
$sub = array(
  "consultaClasica" => array(
    'subMenu' => 0,
    'url' => "busqueda/busquedaPiloto.php?$phpsession&&etapa=1&s_Listado=VerListado&fechah=$fechah",
    'nombre' => 'Consulta Clasica'
  ),
  "consultaExpedientes" => array(
    'subMenu' => 0,
    'url' => "expediente/MiExp.php?consul=1",
    'nombre' => 'Consulta Expedientes'
  ), //busqueda/busquedaExp.php?$phpsession&&etapa=1&s_Listado=VerListado&fechah=$fechah

);

$bandejas["consultas"] = array('subMenu' => 1, 'url' => "#", 'nombre' => 'Consultas', 'sub' => $sub);
$bandejas["estadisticas"] = array(
  'subMenu' => 0,
  'url' => "estadisticas/vistaFormConsulta.php?$phpsession&&fechah=$fechah",
  'nombre' => ' Estadísticas'
);
$bandejas["general"] = array(
  'subMenu' => 0,
  'url' => "cuerpo.php?$phpsession$fechah&nomcarpeta=General&carpeta=9999&tipo_carpt=0",
  'nombre' => 'General (Todos)'
);

require_once "include/tx/Bandejas.php";
$bandeja = new Bandejas($db);
$bandeja->codUsuario = $codusuario;
$bandeja->depeCodi = $dependencia;

$bandejasGenerales = $bandeja->getCarpetasGenerales();

if ($_SESSION["USUA_JEFE_DE_GRUPO"] || $_SESSION["USUA_TRAMITADOR"]) {
  $mostrarCarpetaJefe = true;
} else {
  $mostrarCarpetaJefe = false;
}

foreach ($bandejasGenerales as $key => $value) {
  $valor = substr($value, 0, strpos($value, '(') - 1);

  if (($mostrarCarpetaJefe == false) && ($valor == "Jefe de Area")) {
    continue;
  }

  $bandejas["bandeja_$key"] = array(
    'subMenu' => 0,
    'url' => "\"cuerpo.php?$phpsession$fechah&nomcarpeta=$valor&carpeta=$key&tipo_carpt=0&order=14\"",
    'nombre' => "$value",
    'id' => "carpetap_$key"
  );
};

if ($_SESSION['usua_assign_trd']) {
  $bandejas["TRD"] = array(
    'subMenu' => 0,
    'url' => "\"cuerpoTRD.php?$phpsession&$fechah&nomcarpeta=$data&tipo_carpt=0\"",
    'nombre' => 'TRD'
  );
}

//VoBo se encuentra incluido en el foreach
$cont = $bandeja->getContadorInformados($codusuario, $dependencia);
$bandejas["informados"] = array(
  'subMenu' => 0,
  'url' => "\"bandejaInformados.php?$phpsession&mostrar_opc_envio=1&orderNo=2&carpeta=8&nomcarpeta=Informados&orderTipo=desc&adodb_next_page=1\"",
  'nombre' => "Informados ($cont)"
);

//VoBo se encuentra incluido en el foreach
$cont = $bandeja->getContadorTramiteConjunto($codusuario, $dependencia);
$bandejas["bandeja_18"] = array(
  'subMenu' => 0,
  'url' => "\"bandejaconjuntos.php?$phpsession&mostrar_opc_envio=1&orderNo=2&carpeta=18&nomcarpeta=Informados&orderTipo=desc&adodb_next_page=1\"",
  'nombre' => "Memorando Multiple ($cont)"
);

//tramite conjunto se encuentra comentado en la versión actual
$data     = "Ultimas Transacciones del Usuario";

$bandejas["transacciones"] = array(
  'subMenu' => 0,
  'url' => "\"cuerpoTx.php?$phpsession&$fechah&nomcarpeta=$data&tipo_carpt=0\"",
  'nombre' => 'Transacciones'
);

$bandejas["expedientes"] = array(
  'subMenu' => 0,
  'url' => "\"cuerpoExp.php?$phpsession&$fechah&nomcarpeta=$data&tipo_carpt=0\"",
  'nombre' => 'Expedientes'
);

$cont = $bandeja->getContadorRespuestaConjunta($codusuario, $dependencia);
if ($cont > 0) {
  $bandejas["Respuesta conjunta"] = array(
    'subMenu' => 0,
    'url' => "\"bandejaColaboradores.php?$phpsession&$fechah&nomcarpeta=Respuesta conjunta&tipo_carpt=0\"",
    'nombre' => "Respuesta conjunta ($cont)"
  );
}

$sql_rol = "SELECT autg_id FROM autm_membresias WHERE autg_id=37 AND autu_id=" . $_SESSION['usua_id'];
$rs_rol = $db->conn->query($sql_rol);
if ($rs_rol->fields['AUTG_ID'] == 37) {
  $bandejas["tramitadores"] = array(
    'subMenu' => 0,
    'url' => "\"cuerpoJefe.php?$phpsession&$fechah&nomcarpeta=$data&tipo_carpt=0\"",
    'nombre' => 'Tramitadores'
  );
}

//Carpetas personales, si el usuario tiene el
//permiso usua_carp_personales mostrar en el menu
//la opción para crearlas y administrarlas

if ($_SESSION["USUA_CARP_PERSONALES"]) {

  $sub = array(
    'nuevaCarpeta' => array(
      'subMenu' => 0,
      'url' => "\"crear_carpeta.php?$phpsession&fechah=$fechah&adodb_next_page=1\"",
      'nombre' => ' Nueva Carpeta '
    )
  );

  $bandeja->codUsuario = $codusuario;
  $bandeja->depeCodi = $dependencia;
  $bandejasPersonales = $bandeja->getCarpetasPersonales();

  if (is_array($bandejasPersonales)) {
    foreach ($bandejasPersonales as $key => $value) {
      $data = substr($value, 0, strpos($value, '(') - 1);
      $sub["bandeja_$key"] = array(
        'subMenu' => 0,
        'url' => "\"cuerpo.php?$phpsession&fechah=$fechah&
                tipo_carp=1&carpeta=$key&nomcarpeta=$data\"",
        'nombre' => "$value",
        'id' => "carpetaPersonal_$key"
      );
    }
  }

  //  $bandejas["personales"] = array('subMenu'=>1,'url'=>"#",'nombre'=>' Personales ', 'sub'=>$sub);
}

$valor = $acciones;
$acciones = array('nombre' => 'Acciones', 'menu' => $valor);
$valor = $administracion;
$administracion = array('nombre' => 'Administraci&oacute;n', 'menu' => $valor);
$valor = $usuario;
$usuario = array('nombre' => "$nombUser", 'menu' => $valor);
$valor = $radicacion;
$radicacion = array('nombre' => "Radicación", 'menu' => $valor);
$valor = $bandejas;
$bandejas = array('nombre' => "Bandejas", 'menu' => $valor);
$valor = $opciones;
$opciones = array('nombre' => "Opciones", 'menu' => $valor);

$menus = array(
  'acciones' => $acciones,
  'administracion' => $administracion,
  'usuario' => $usuario,
  'radicacion' => $radicacion,
  'bandejas' => $bandejas,
  'opciones' => $opciones
);

$urlCargaValores = "\"include/tx/json/getRegistrosCarpetaGen.php?codUsuario=$codusuario&depeCodi=$dependencia&carpetaPer=\"";

try {

  $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));
  $commitDate = trim(exec('git log --pretty="%ci" -n1 HEAD'));

  $lastUpdate = sprintf('V.%s', date_format(date_create($commitDate), 'Y-m-d-H-i'));
  $lastCommit = sprintf('%s', strtoupper($commitHash));
} catch (Exception $ex) {
  error_log("No se encontro versionamiento! " . $e->getMessage(), 0);
}


include(SMARTY_DIR . 'Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = "./themes/$theme";
$smarty->compile_dir = $CONTENT_PATH . '/tmp/';
$smarty->config_dir = './configs/';
$smarty->cache_dir = $CONTENT_PATH . '/tmp/';
$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';
$smarty->assign("urlCargaValores", $urlCargaValores);
$smarty->assign("menuAcciones", $menuAcciones);
$smarty->assign("menuAdministracion", $menuAdministracion);
$smarty->assign("menuRadicacion", $menuRadicacion);
$smarty->assign("menuBandejas", $menuBandejas);
$smarty->assign("colorFondo", $colorFondo);
$smarty->assign("ambiente", $ambiente);
$smarty->assign("entidad", $entidad);
$smarty->assign("tema", $theme);
$smarty->assign("lastUpdate", $lastUpdate);
$smarty->assign("lastCommit", $lastCommit);
$smarty->assign("entidad_largo", $entidad_largo);
$smarty->assign("logoEntidad", './estilos/images/logo.png');
$smarty->assign("favicon", './bodega' . $favicon);

if ($menuAcciones) {
  $smarty->assign("acciones", $acciones);
}
if ($menuAdministracion) {
  $smarty->assign("administracion", $administracion);
}
if ($menuRadicacion) {
  $smarty->assign("radicacion", $radicacion);
}
if ($menuBandejas) {
  $smarty->assign("bandejas", $bandejas);
}
$smarty->assign("opciones", $opciones);
$smarty->assign("usuario", $usuario);
$smarty->assign("menus", $menus);
$smarty->display('index.tpl');
