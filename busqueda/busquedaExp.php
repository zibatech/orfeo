<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @author  YULLIE QUICANO
* @author Correlibre.org
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

OrfeoGpl Models are the data definition of OrfeoGpl Information System
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

$ruta_raiz = "../";
session_start();

require_once($ruta_raiz."include/db/ConnectionHandler.php");
 require_once($ruta_raiz."/tx/verLinkArchivo.php");

if (!$db){
    $db = new ConnectionHandler($ruta_raiz);
}

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

if (!$_SESSION['dependencia']){
  header ("Location: $ruta_raiz/cerrar_session.php");
}

$verLinkArchivo = new verLinkArchivo($db);

include ("common.php");
$fechah = date("ymd") . "_" . time("hms");

$params = session_name()."=".session_id()."&krd=$krd";

$sql = "SELECT 'Todas las dependencias' as DEPE_NOMB, 0 AS DEPE_CODI FROM DEPENDENCIA
    UNION  SELECT DEPE_NOMB, DEPE_CODI AS DEPE_CODI FROM DEPENDENCIA
    WHERE DEPE_CODI NOT IN (900,905,999,997)
    order by depe_nomb DESC";
$rsDep = $db->conn->Execute($sql);

if(!$s_DEPE_CODI) $s_DEPE_CODI= 0;
$depeSelect =  $rsDep->GetMenu2("dep", "$dep",false, false, 0," class='form-control' onChange='submit();'");

if(!$dep){
    $dep="0";
}


$sql = "(SELECT 'Todos los Usuarios' as USUA_NOMB, 0 AS USUA_CODI, USUA_DOC, DEPE_CODI FROM USUARIO where depe_codi=900 limit 1)
    UNION  SELECT USUA_NOMB, USUA_CODI, USUA_DOC, DEPE_CODI  FROM USUARIO
    WHERE DEPE_CODI NOT IN (900,905,999, 997) and depe_codi = $dep
    order by usua_nomb DESC";

$rsDep = $db->conn->Execute($sql);

if(!$s_DEPE_CODI){
 $s_DEPE_CODI= 0;
}

$usuaSelect = $rsDep->GetMenu2("usuaDoc","$usuaDoc",false, false, 0," class='form-control'");


if(!empty($_POST['Busqueda'])&& ($_POST['Busqueda']=="Busqueda")){


    $sqlDep = trim($_POST['dep']);
    $sqlUsu = trim($_POST['usuaDoc']);
    $sqlRad = trim($_POST['nume_radi']);
    $sqlPage = trim($_POST['page']);
    $sqlExp = strtoupper(trim($_POST['nume_expe']));
    $sqlNom = strtoupper(trim($_POST['nomexpe']));
    $records = 100;
    $paginate = '';

    $where=null;

    $where=(!empty($sqlDep))?(" WHERE S.DEPE_CODI= '{$sqlDep}'") :$where;

    $where=(!empty($sqlUsu))?(
        ($where!="")? $where." and E.USUA_CODI= '{$sqlUsu}'":" WHERE E.USUA_CODI= '{$sqlUsu}' ")
        :$where;

    $where=(!empty($sqlRad))?(
        ($where!="")? $where." and E.RADI_NUME_RADI = '{$sqlRad}'":" WHERE E.RADI_NUME_RADI = '{$sqlRad}' ")
        :$where;

    $where=(!empty($sqlExp))?(
        ($where!="")? $where." and S.SGD_EXP_NUMERO LIKE '%{$sqlExp}%'":" WHERE S.SGD_EXP_NUMERO LIKE '%{$sqlExp}%' ")
        :$where;

    $where=(!empty($sqlNom))?(
        ($where!="")? $where." and (s.sgd_sexp_parexp1||s.sgd_sexp_parexp2||s.sgd_sexp_parexp3||s.sgd_sexp_parexp4||s.sgd_sexp_parexp5) LIKE '%{$sqlNom}%'":" WHERE (s.sgd_sexp_parexp1||s.sgd_sexp_parexp2||s.sgd_sexp_parexp3||s.sgd_sexp_parexp4||s.sgd_sexp_parexp5) LIKE '%{$sqlNom}%'")
        :$where;

        $camposConcatenar = "concat(s.sgd_sexp_parexp1,' /',s.sgd_sexp_parexp2,' /',s.sgd_sexp_parexp3,' /',s.sgd_sexp_parexp4,' /',s.sgd_sexp_parexp5)";



        $isql= "select distinct( e.radi_nume_radi), tp.sgd_tpr_descrip, s.sgd_exp_numero, r.ra_asun, r.radi_fech_radi, dir.sgd_dir_nomremdes, u.usua_nomb,  r.radi_path,
            s.depe_codi
            , d.depe_nomb
            , E.SGD_EXP_FECH
            , $camposConcatenar as PARAMETRO,
            (se.sgd_srd_descrip||' - '||su.sgd_sbrd_descrip) AS SESUB
            ,s.sgd_cerrado
            ,u.usua_nomb
            from sgd_exp_expediente E
            INNER JOIN SGD_SEXP_SECEXPEDIENTES S ON E.sgd_exp_numero = S.sgd_exp_numero
            INNER JOIN 	RADICADO R ON E.RADI_NUME_RADI = R.RADI_NUME_RADI
            inner join sgd_dir_drecciones dir on dir.radi_nume_radi = r.radi_nume_radi,
            dependencia d,
            usuario u,
            sgd_srd_seriesrd se,
            SGD_TPR_TPDCUMENTO tp,
            sgd_sbrd_subserierd su
            {$where} and
        s.sgd_srd_codigo = se.sgd_srd_codigo and
        tp.sgd_tpr_codigo = R.tdoc_codi and
        s.sgd_sbrd_codigo = su.sgd_sbrd_codigo and
        s.sgd_srd_codigo  = su.sgd_srd_codigo and
        s.depe_codi = d.depe_codi and s.usua_doc_responsable = u.usua_doc and e.sgd_exp_estado <> 2
        order by SGD_EXP_NUMERO ASC, SGD_EXP_FECH DESC ";

    $rscount  = (!empty($where))?$db->conn->Execute("select count(*)  from ($isql) as foo"): null; 

    $total = $rscount->fields['COUNT'];

    if($total < 100)
        $start_from = 0;
    else
        $start_from = ($page>0)?($page-1) * $records:0;

    $total_pages = ceil($total / $records);

    if($page>$total_pages){
        $page = 1;
    }

    $paginate = "limit $records offset $start_from";

    $isql .= $paginate;

    //var_dump($isql);

    $rssql = (!empty($where))?$db->conn->Execute($isql): null;

}

?>
<!DOCTYPE html>
<html>
    <head>
        <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
        <title>Consultas Expedientes</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script language="JavaScript" src="<?=$ruta_raiz?>/js/formchek.js"></script>
    </head>

    <body>
        <div class="container-fluid">
            <div class="col-sm-12">
                <form action="busquedaExp.php?<?=$params?>"
                    method="post" enctype="multipart/form-data"
                    class="form-horizontal"
                    name="formSeleccion" id="formSeleccion">

                    <section id="widget-grid">
                          <article>
                            <!-- Widget ID (each widget will need unique ID)-->
                            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                              <header>
                                <h2>
                                  B&uacute;squeda de Expedientes y Radicados Asociados
                                </h2>
                              </header>
                              <!-- widget content -->
                              <div class="widget-body">
                                  <div style="padding-top:30px" class="panel-body" >
                                        <div class="col-md-6">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                  <label>Expediente:</label>
                                                  <input  class="form-control"
                                                          type="text"
                                                          name="nume_expe"
                                                          value="<?=$nume_expe?>"/>
                                                </div>

                                                <div class="form-group">
                                                    <label>Etiqueta o Nombre de Expediente</label>
                                                    <input  class="form-control"
                                                        type="text"
                                                        name="nomexpe"
                                                        maxlength="4000"
                                                        value="<?=$nomexpe?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Usuario Responsable</label>
                                                    <?=$usuaSelect?>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Radicado </label>
                                                    <input  class="form-control"
                                                          type="text"
                                                          name="nume_radi"
                                                          maxlength="17"
                                                          value="<?=$nume_radi?>"
                                                          size="25">
                                                </div>

                                                <input  class="form-control"
                                                          type="hidden"
                                                          name="page"
                                                          maxlength="17"
                                                          value="<?=$sqlPage?>"
                                                          size="25">

                                                <div class="form-group">
                                                    <label>Dependencia Responsable</label>
                                                    <?=$depeSelect?>
                                                </div>


                                                <div class="form-group">
                                                      <input
                                                          id="limpiar"
                                                          class="btn btn-default"
                                                          value="Limpiar"
                                                          type="button">

                                                      <input
                                                          class="btn btn-primary"
                                                          name="Busqueda"
                                                          type="submit"
                                                          id="envia22"
                                                          value="Busqueda">
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                              </div>
                            </div>
                          </article>
                    </section>

                    <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th align="center"></th>
                            <th align="center">Expediente</th>
                            <th align="center">Fecha asoc.</th>
                            <th align="center">Radicado</th>
                            <th align="center">Fecha rad</th>
                            <th align="center">Asunto</th>
                            <th align="center">Tipo doc</th>
                            <th align="center">Remitente/destino</th>
                            <th align="center">Nombre</th>
                            <th align="center">Serie/subserie</th>
                            <th align="center">Dependencia</th>
                            <th align="center">Usuario</th>
                            <th align="center">Estado</th>
                        </tr>
                    <?php
                    while($rssql && !$rssql->EOF){
                        $radi           = $rssql->fields['RADI_NUME_RADI'];
                        $fechradi       = $rssql->fields['RADI_FECH_RADI'];
                        $dir            = $rssql->fields['SGD_DIR_NOMREMDES'];
                        $asun           = $rssql->fields['RA_ASUN'];
                        $tipo_doc       = $rssql->fields['SGD_TPR_DESCRIP'];
                        $num_expediente = $rssql->fields['SGD_EXP_NUMERO'];
                        $fechexp        = $rssql->fields['SGD_EXP_FECH'];
                        $par            = $rssql->fields['PARAMETRO'];
                        $sesub          = $rssql->fields['SESUB'];
                        $depen          = $rssql->fields['DEPE_NOMB'];
                        $usua           = $rssql->fields['USUA_NOMB'];
                        $cerrado        = $rssql->fields['SGD_CERRADO'];

                        $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                        $valImg = $resulVali['verImg'];                        

                        $linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$radi&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>";
                        if($radi){
                            $sql2="select radi_nume_radi, sgd_spub_codigo from radicado where  radi_nume_radi='$radi'";
                            $rssql2 = $db->conn->Execute($sql2);
                            $priv= $rssql2->fields["SGD_SPUB_CODIGO"];
                        } ?>
                       <tr>
                        <td>
                            <input
                                type="button"
                                value="..."
                                class="btn btn-sm btn-info" onClick="verHistExpediente('<?=$num_expediente?>');">
                        </td>
                        <td>
                            <span class="leidos2">
                                <?=$num_expediente?>
                            </span>
                        </td>
                        <td><?=$fechexp?></td>
                       <?PHP if ($valImg != "SI"){ ?>
                        <td><?=$radi?></td>
                        <td><?= tohtml($fechradi)?></td>
                        <td>{Doc Privado}</td>
                       <?PHP }else{ ?>
                        <td><?=$radi?></td>
                        <td><?=$linkInfGeneral?><?= tohtml($fechradi)?></a></td>
                        <td><?=$asun?></td>
                        <?php } ?>
                        <td><?=$tipo_doc?></td>
                        <td><?=$dir?></td>
                        <td><?=$par?></td>
                        <td><?=$sesub?></td>
                        <td><?=$depen?></td>
                        <td><?=$usua?></td>
                        <td><?=$cerrado?></td>
                       </tr>
                        <?php
                            $rssql->MoveNext();
                        } ?>
                    </table>
                    <?php
                     for ($i=1; $i<=$total_pages; $i++) { 
                        if($page == $i){
                            
                            echo "<a data-page=".$i." class='yourclass' style='font-size: 19px;color: #fbf8f8;background-color: #57889c;border-radius: 24px;padding: 0px 3px 0px 3px;'>".$i."</a>&nbsp;&nbsp;"; 
                        }else{
                            echo "<a data-page=".$i." class='yourclass'>".$i."</a>&nbsp;&nbsp;"; 
                        }
                     }?>
                     <div>
                        <br>
                        <br>
                        <br>
                     </div>
                    </div>
                </form>
            </div>
        </div>

        <script language="JavaScript" type="text/JavaScript">

            function Consultar() {
                window.open("<?=$ruta_raiz?>/expediente/conExp.php?krd=<?=$krd?>&numRad=<?=$verrad?>&dependencia=<?=$dependencia?>","Consulta Expedientes Existentes","height=800,width=1500,scrollbars=yes");
            }

            function noPermiso(){
                alert ("No tiene permiso para acceder");
            }

            function verHistExpediente(numeroExpediente,codserie,tsub,tdoc,opcionExp) {
            <?php
            $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado
                WHERE RADI_NUME_RADI = '$numrad'";
            $rsDepR = $db->conn->Execute($isqlDepR);
            $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
            $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
            $ind_ProcAnex = "N";

            ?>
            window.open("<?=$ruta_raiz?>/expediente/verHistoricoExp.php?sessid=<?=session_id()?>&opcionExp="+opcionExp+"&numeroExpediente="+numeroExpediente+"&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>","HistExp<?=$fechaH?>","height=800,width=1060,scrollbars=yes");
            }

            $( document ).ready(function() {

                 $('body').delegate('.yourclass','click',function(){
                    let page = $(this).data('page');
                    $('input[name=page]').val(page);
                    $('#envia22').click();
                 });

                $('#limpiar').click(function(){
                    $(':input','#formSeleccion')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .removeAttr('checked')
                        .removeAttr('selected');
                });
            });

        </script>

    </body>
</html>
