<?php
/**
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
$ruta_raiz = "..";

header('Content-Type: application/json');
if (!$_SESSION['dependencia']) {
    echo json_encode(['error' => 'session']);
    exit;
}

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/usuario.php");
include_once("$ruta_raiz/include/tx/notificacion.php");
require_once("$ruta_raiz/include/tx/Bandejas.php");

$db     = new ConnectionHandler("$ruta_raiz");


if(isset($_POST['search']) || isset($_GET['search'])){
    $usuario= new Usuario($db);
    $trans  = json_decode($_POST['search'], true);

    $search['tdoc'] = $trans['tdoc'];
    $search['docu'] = $trans['docu'];
    $search['name'] = $trans['name'];
    $search['tele'] = $trans['tele'];
    $search['mail'] = $trans['mail'];

    //Filtro por el tipo de usuario
    $result = $usuario->buscarPorParametros( $search );

    if($result){
        echo json_encode($usuario->result);
    }else{
         echo json_encode([]);
    }
    die;
}

if(isset($_POST['searchDestinatarios']) || isset($_GET['searchDestinatarios'])){
    $notificacion = new Notificacion($db);
    $trans  = json_decode($_POST['searchDestinatarios'], true);

    $destinatarios['tdoc'] = $trans['tdoc'];
    $destinatarios['name'] = $trans['name'];

    $result = $notificacion->buscarDestinatarios($destinatarios);

    if($result){
        echo json_encode($result);
    }
    die;
}

if(isset($_POST['addUser'])){
    $data    = $_POST['addUser'];
    $usuario = new Usuario($db);
    $result  = $usuario->agregarUsuario($data);
    if($result){
        echo json_encode(array($result));
    }
    die;
}

if(isset($_POST['addDestinatariosCircular'])){
    $data    = $_POST['addDestinatariosCircular'];
    $notificacion = new Notificacion($db);
    $result  = $notificacion->agregarDestinatarios($data);
    if($result){
        echo json_encode(array($result));
    }
    die;
}

if(isset($_POST['searchUserInDep'])){
    $data    = $_POST['searchUserInDep'];
    $usuario = new Usuario($db);

    //OBTENEMOS EL JEFE DE LA DEPENDENCIA
    $query = "select u.usua_codi, u.usua_nomb from usuario u, autm_membresias m where u.id = m.autu_id
	AND m.autg_id = 2 AND depe_codi = $data";
    $rsjefe    = $db->conn->query($query);
    $option = '';
    if(!$rsjefe->EOF){
        $codi_jefe             = $rsjefe->fields["USUA_CODI"];
        $nomb_jefe             = $rsjefe->fields["USUA_NOMB"];
        $option .= "<option value='".$codi_jefe."'>JEFE DE DEPENDENCIA (".$nomb_jefe.") </option>";
    }
    $result  = $usuario->usuariospordependencias($data);

    if($_SESSION["entidad"]!='ANM'){
        if($result){
            foreach ($usuario->result as $valor){
                $nomb = $valor[0];
                $codi = $valor[1];
                $option .= "<option value='".$codi."'>".$nomb."</option>";
            }
            echo  json_encode(array($option));
        };
    }else{
        echo  json_encode(array($option));
    }
    die;
}

if(isset($_POST['updateUserFolders'])){

    $dependencia = $_SESSION["dependencia"];
    $codusuario = $_SESSION["codusuario"];

    $bandeja= new Bandejas($db);
    $bandeja->codUsuario=$codusuario;
    $bandeja->depeCodi=$dependencia;
    echo  json_encode($bandeja->getCarpetasGenerales());
    die;

}

if(isset($_POST['MsearchUserInDep'])){
    $data   = $_POST['MsearchUserInDep'];
    $usuario = new Usuario($db);
    for ($i=0;$i<count($data);$i++){
        //OBTENEMOS LOS USUARIOS QUE TIENEN PERMISO DE REASIGNAR
        $query = "select AUTU_ID from AUTM_MEMBRESIAS where AUTG_ID = 126";
        $rsmem    = $db->conn->Execute($query);
        while(!$rsmem->EOF){
            $id_usuario = $rsmem->fields["AUTU_ID"];
            $esql = "select usua_codi,usua_nomb from usuario where ID = $id_usuario  and USUA_ESTA = 1 and depe_codi = $data[$i]";
            $rsuse = $db->conn->Execute($esql);
            while(!$rsuse->EOF){

                $codi_jefe             = $rsuse->fields["USUA_CODI"];
                $nomb_jefe             = $rsuse->fields["USUA_NOMB"];

                $value_check = $data[$i]."_".$codi_jefe;

                $option .= "<label class='radio userinfo'><input type='checkbox'  checked name='radio[]' value='".$value_check."'><i></i>".$nomb_jefe."</label>";
                //$option .= "<option value='".$codi_jefe."'> ".$nomb_jefe." </option>";

                $rsuse->MoveNext();
            }
            $rsmem->MoveNext();
        }

    }
    echo  json_encode(array($option));
    die;
}
