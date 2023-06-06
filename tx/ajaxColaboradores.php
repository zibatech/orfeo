<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$ruta_raiz=".."; 

if (!$_SESSION['dependencia']) {
    header ("Location: $ruta_raiz/cerrar_session.php");
    exit();
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/include/tx/Historico.php");
$db = new ConnectionHandler("$ruta_raiz");
$hist = new Historico($db);

function df($a, $b) {
    foreach ($a as $i=>$k) {
        foreach ($b as $j=>$l) {
            $t[$i] = 0;
            if ($k['DEPE_CODI'] == $l['DEPE_CODI'] && $k['USUA_CODI'] == $l['USUA_CODI']) {
                $t[$i] = 1;
                break;
            }
        }
    }
    return $t;
}

$accion = $_REQUEST['accion'] ?? null;
$rt = [];
if ($accion) {
    switch ($accion) {
    case 'save':
        $usr = $_POST['usr'] ?? null;

        $query = "select * from colaboradores where radi_nume_radi = {$_POST['rad']}";
        $rs = $db->conn->query($query);
        $dbusr = $rs->GetArray();
        $a = df($usr, $dbusr);
        $b = df($dbusr, $usr);

        $db->conn->BeginTrans();
        $ret = true;
        if ($usr)
        foreach ($usr as $i => $u) {
            if ($a[$i] == 0) {
                $sql = "insert into colaboradores (radi_nume_radi,usua_codi,depe_codi,obse) values ({$_POST['rad']},{$u['USUA_CODI']},{$u['DEPE_CODI']},'{$u['OBSE']}')";
                $ret = $db->conn->Execute($sql);
                if (!$ret) break;
                $hist->insertarHistorico([$_POST['rad']], $_SESSION["dependencia"], $_SESSION["codusuario"], $u['DEPE_CODI'], $u['USUA_CODI'], "Se asigna a colaborador: {$u['OBSE']}", 104);
            }
        }
        foreach ($dbusr as $i => $u) {
            if ($b[$i] == 0) {
                $sql = "delete from colaboradores where radi_nume_radi={$_POST['rad']} and usua_codi={$u['USUA_CODI']} and depe_codi={$u['DEPE_CODI']}";
                $ret = $db->conn->Execute($sql);
                if (!$ret) break;
                $hist->insertarHistorico([$_POST['rad']], $_SESSION["dependencia"], $_SESSION["codusuario"], $u['DEPE_CODI'], $u['USUA_CODI'], "Se borra colaborador", 104);
            }
        }

        if (!$ret) {
            $db->conn->RollbackTrans();
            $rt = ['err' => true];
        }
        else {
            $db->conn->CommitTrans();
        }
        break;
    case 'list':
        $usr = $_POST['usr'] ?? null;
        $dep = [];
        if (!$usr) break;
        foreach ($_POST['usr'] as $u) {
            $dep[$u['DEPE_CODI']][] = $u['USUA_CODI'];
        }
        foreach ($dep as $d => $u) {
            $t = join(',', $u);
            $w[] = "(u.depe_codi = $d and u.usua_codi in ($t))";
        }
        $sql = "select d.depe_nomb, u.usua_nomb, u.depe_codi, u.usua_codi, c.obse
            from usuario u
            join dependencia d on d.depe_codi = u.depe_codi
            left join colaboradores c on c.depe_codi = u.depe_codi and c.usua_codi = u.usua_codi
            where (" . join(' or ', $w) . ")";
        $rs = $db->conn->Execute($sql);
        $rt = $rs->GetArray();
        break;
    case 'usuarios':
        $id = $_POST['id'] ?? 0;
        $sql = "select u.usua_nomb, u.usua_login, u.usua_codi from usuario u
            where u.depe_codi = $id and u.usua_esta='1'
            order by u.usua_nomb desc";
        $rs = $db->conn->Execute($sql);
        $rt = $rs->GetArray();
        break;
    }
    header("Content-Type: application/json");
    echo json_encode($rt);
}
?>
