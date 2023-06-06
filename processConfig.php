<?php

/*
 * Procesa las diferentes variables necesarias para la configuración
 * del sistema estas variables están en la tabla sgd_config y es usada
 * para modificar elementos como conexiones a otros sistemas
 * parámetros de configuración propias al uso de la aplicación y
 * generación de elementos particulares como claves de seguridad o
 * variables que necesitan ser modificadas por el administrador he
 * instalador en algún momento.
 */

if (!isset($ruta_raiz) || empty($ruta_raiz)) {
  $ruta_raiz = __DIR__;
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";

$dbx = new ConnectionHandler("$ruta_raiz");
$dbx->conn->SetFetchMode(ADODB_FETCH_NUM);
$dbx->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$sql = "select
            conf_nombre,
            conf_valor,
            conf_constante,
            conf_arreglo
        from
            sgd_config;";

$rs = $dbx->query($sql);

/*
 * Inicializacion de variables Array
 */
if (!function_exists('deepArray')) {

  function deepArray($element, $value)
  {
    $varHead = array_shift($element);

    if (count($element) >= 1) {
      $valArr = deepArray($element, $value);
      return array($varHead => $valArr);
    }

    return array($varHead => $value);
  }
}


if (!$rs->EOF) {
  while (!$rs->EOF) {
    $nombre = $rs->fields["CONF_NOMBRE"];
    $valor = $rs->fields["CONF_VALOR"];
    $iscon = $rs->fields["CONF_CONSTANTE"];
    $isarr = $rs->fields["CONF_ARREGLO"];

    if ($iscon  == 1) {
      defined($nombre) or define($nombre, $valor);
    } elseif ($isarr == 1) {
      $namesArray = explode("_", $nombre);
      $varHead = array_shift($namesArray);

      if (!isset(${$varHead}) && !is_array(${$varHead})) {
        if (count($namesArray) >= 1) {
          $valArr = deepArray($namesArray, $valor);
          ${$varHead} = $valArr;
        } else {
          ${$varHead} = $valor;
        }
      }
    } else {
      if (!isset(${$nombre})) {
        ${$nombre} = $valor;
      }
    }

    $rs->MoveNext();
  }
}
