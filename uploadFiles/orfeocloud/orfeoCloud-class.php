<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of orfeoCloud-class
 *
 * @author Deimont
 */
include_once "modeloOwncloud.php";

class orfeoCloud {

//put your code here
    private $rutaClou;
    private $ruta_raiz;
    private $modelo;
    private $userCloud;
    private $userLogin;

    public function getUserCloud() {
        return $this->userCloud;
    }

    public function setUserCloud($userCloud) {
        $this->userCloud = $userCloud;
    }

    public function getUserLogin() {
        return $this->userLogin;
    }

    public function setUserLogin($userLogin) {
        $this->userLogin = $userLogin;
    }

    public function getRutaClou() {
        return $this->rutaClou;
    }

    public function setRutaClou($rutaClou) {
        $this->rutaClou = $rutaClou;
    }

    public function __construct($ruta, $ruta_raiz) {
        $this->rutaClou = $ruta;
        $this->ruta_raiz = $ruta_raiz;
        $this->modelo = new modeloOwnCloud($ruta_raiz);
    }

    function dataUser() {
        $data = $this->modelo->consultar();
        // print_R($data);
        //$this->userCloud=$data['oUser'];
        //$this->userLogin=$data['login'];
        return $data;
    }

    function clouduser() {
        exec("ls  " . $this->rutaClou, $data);
        //print_r($data);
        for ($i = 0; $i < count($data); $i++) {
            if (substr($data[$i], 0, -1) == 'digitalizador' || $data[$i] == 'deimont')
                $option.="<option>" . $data[$i] . "</option>";
        }
        return "<select id='user'  name='user'>$option</select >";
    }

    function ListarImagenes($ruta_owonclod, $usA, $carpS, $user, $listaCombo) {
        $resp = '';
        for ($index = 0; $index < count($usA); $index++) {
            $data = '';
			//echo "ls -lh --full-time " . $ruta_owonclod . "/" . $usA[$index] . "/files/$user/$carpS/ | awk '{ print $9,$5,$6,$7 }'";
            exec("ls -lh --full-time " . $ruta_owonclod . "/" . $usA[$index] . "/files/$carpS/ | awk '{ print $9,$5,$6,$7,$10,$11 }'", $data);
            if (count($data) > 0)
            //$resp.= "<tr><td colspan=5 align='center'><b>" . $usA[$index] . "<b></td></tr>";
                $resp.= "<thead><tr  ><tH colspan=5 align='center'><b>" . $usA[$index] . "<b></tH></tr>"
                        . '<tr ><th id="headerName" >
                            <span class="name">Nombre</span>              </th>
                        <th id="headerSize">Tamaño</th>
                        <th id="headerDate" > <span id="modified">Fecha</span>	</th>
                        <th id="headerDate" > <span id="modified">Paginas</span>	</th>
                        <th id="headerDate" > <span id="modified">Descripción</span>	</th>
                        <th id="headerDate" > <span id="modified">Clasificacion Documental</span>  </th>
                        <th id="headerDate">  <span id="modified">Acción</span>	</th>
                    </tr>
                </thead>
                <tbody id="fileList">  <script type="text/javascript">
                        var publicListView = false;</script>';
            for ($i = 1; $i < count($data); $i++) {
                $g = explode(' ', $data[$i]);
                //$resp.=$g[0]." peso ".$g[1]."<br>";
                $name = trim($g[0]." ".$g[4]." ".$g[5]);
                $ext = substr($name, -3);
                $size = $g[1];
                $size2 = (integer) $g[1];
                $fecha = $g[2];
                $result = '';
            //    if ($ext == 'pdf' or $ext == 'PDF' or $ext == 'TIF' or $ext == 'tif') {
                    if (($i % 2) == 0)
                        $styyle = 'background :#fff';
                    else
                        $styyle = 'background : rgb(228, 232, 247)';
                    $tppeso = substr($size, -1);
                    //echo $tppeso;
                    switch ($tppeso) {
                        case 'K':
                            $tamano = $size2;
                            break;
                        case 'M':
                            $tamano = $size2 * 1024;
                            break;
                        case 'G':

                            $tamano = $size2 * 2048;
                            break;
                    }
                    //$script = " pdfinfo $ruta_owonclod/" . $usA[$index] . "/files/$user/$carpS/$name | grep Pages|awk '{print $2}'";
                   // exec($script, $result);
                    //$num_pag = $result[0];
                    if ($ext == 'pdf' or $ext == 'PDF') {
                            $result = '';
                             $script = " pdfinfo $ruta_owonclod/" . $usA[$index] . "/files/$carpS/$name | grep Pages|awk '{print $2}'";
                             $script = " pdfinfo '$ruta_owonclod/$user/files/$carpS/$name' | grep Pages|awk '{print $2}'";
                            exec($script, $result);
                            //var_dump($result);
                            $num_pag = $result[0]=='file'?1:$result[0];
                        } else {
                            $num_pag=1;
                        }
              
              $diatra = $this->diasTramite($fecha);
              $hora = substr($g[3], 0, 5);
              if ($listaCombo) {
                  $Combo = "<select id='tp$name' style='width: 170px;' name='tpdoc'><option value='-'>Seleccione TIPO DOC</option>
                <option value='0'>Indefinido</option>  $listaCombo</select>";

                  /*$Combo .= "<select id='comentario$name' style='width: 170px;' name='tpdoc'><option value='-'>Seleccione Comentario</option> 
                  <option >Anexo al radicado.</option>
                  <option >Documento entregado.</option>
                  <option >Documento devuelto. (Devolución).</option>
                  <option >Acuse de recibo.(Guía del envió).</option>
                  </select>";*/
                //$jsfuntion = "subir2('div$name','$name',0,'" . $usA[$index] . "','$tamano','tp$name','comentario$name')";
                $jsfuntion = "subir2('div$name','$name',0,'" . $usA[$index] . "','$tamano','tp$name')";
              } else {
            $Combo = "";
						$k=$i-1;
                        $jsfuntion = "subir('div$name','$name',$num_pag,'" . $usA[$index] . "','$tamano','3','$k')";
                    }
                    $resp.="<tr data-id='$i' data-file='$name' data-type='file' data-mime='application/pdf' style='$styyle' data-size='$size' >
				<td class='filename svg ui-draggable ui-droppable'><img width='22' src='".$this->ruta_raiz."/img/icono_pdf.jpg'>					
					<span class='nametext'> $name	</span> 
				</td>
				<td class='filesize' style='text-align:center' title='$size' >$size</td>
				<td class='date' style='text-align:center'><span class='modified' title='$fecha, $hora' >$fecha  ($diatra días)</span></td>
        <td class='pag' style='text-align:center'> $num_pag  </td>
        <td class='pag' style='text-align:center'> <input type='text' name='desc' id='desc' value=''> </td>
        <td class='pag' style='text-align:center'> <input type='text' name='tDocumento' size='50' id='tpDesc_".str_replace(".","",$name)."' value='' ></td>
        <input type='hidden' name='tDocumento' size='5'  id='tpCodigo_".str_replace(".","",$name)."' value='' >
        <td > <div id='div$name'>";
              //  if ($num_pag > 0) {
              $resp.="$Combo<input type='button' value='Subir' onclick=\"$jsfuntion;\">";
              //}
              $resp.="</div> </td></tr>";
              //  }
            }
        }
        //echo $i;
        echo $resp;
    }

    function ListarImagenesAvanzada($ruta_owonclod, $usA, $carpS, $user, $listaCombo) {
        //echo "$ruta_owonclod, $usA, $carpS, $user, $listaCombo";
	/*	echo "<pre>";
        var_dump($usA);
		echo "</pre>";
*/
        $resp = '';
		/*echo "<pre>";
        var_dump($usA);
		echo "</pre>";*/
        for ($index = 0; $index < count($usA); $index++) {
            $dataRadi = '';
            //echo  $ruta_owonclod . "<hr>";
            $comando = "ls -lh --full-time " . $ruta_owonclod . "/" . $usA[$index] . "/files/$user/$carpS/ | grep '-' | awk '{ print $9,$5,$6,$7 }'";
            $comando = "ls -lh --full-time " . $ruta_owonclod . "/".$usA[$index]."/files/$carpS/ | grep '-' | awk '{ print $9,$5,$6,$7 }'";
            //echo $comando;
            exec($comando, $data);
            //var_dump(  $data);
            //    print_r($data);
            //echo "ls -lh --full-time " . $ruta_owonclod . "/" . $usA[$index] . "/files/$user/$carpS/ | awk '{ print $9,$5,$6,$7 }' <hr >";
            if ($data) {
                $resp.= "<thead><tr  style='background :#00547C' ><tH colspan=5 align='center'><b>" . $usA[$index] . "<b></tH></tr>"
                        . '<tr style="background :#00547C" class="titulo1"><th id="headerName" style="width: 200px">
                            <span class="name">Nombre</span>              </th>
                        <th id="headerSize">Tamaño</th>
                        <th id="headerDate" style="width: 100px"> <span id="modified">Fecha</span>	</th>
                        <th id="headerDate" style="width: 100px"> <span id="modified">Paginas</span>	</th>
                        <th id="headerDate">  <span id="modified">Acción</span>	</th>
                    </tr>
                </thead>
                <tbody id="fileList">  <script type="text/javascript">
                        var publicListView = false;</script>';
                // print_r($data);

                $a = 1;
                $dataRadi = '';
                for ($i = 0; $i < count($data); $i++) {
                    $g = explode(' ', $data[$i]);
                    //  print_r ($g);
                    //    echo "<br>";
                    //$resp.=$g[0]." peso ".$g[1]."<br>";
                    $daddd = explode('.', $g[0]);
                    // print_r($daddd);
                    $radi = $daddd[0];
                    $radi2[$i] = $daddd[0];
                    $name[$i] = $g[0];
                    $ext[$i] = $daddd[1]; //substr($name[$i], -3);
                    $size[$i] = $g[1];
                    $size2[$i] = (integer) $g[1];
                    $fecha[$i] = $g[2];
                    //    echo is_int($radi)." * ".$radi;
                    if ($radi > 0) {
                        // if (substr($radi, -1) == 2 && $carpS == 'ENTRADA' || $carpS != 'ENTRADA') {
                         if ( $carpS == 'ENTRADA' || $carpS != 'ENTRADA') {
                        if ($a == 1) {
                                $dataRadi.="'".$radi."'";
                            } else
                                $dataRadi.=',' . "'".$radi."'";
                            $a = 2;
                        }
                    }
                    // echo $dataRadi;
                }
                $data = '';
                if ($dataRadi)
                    $dataRadIma = $this->modelo->consultarRadicados($dataRadi);
                //  print_r($dataRadIma);

                for ($pp = 0; $pp < $i; $pp++) {
                    // echo $ext[$pp]." == 'pdf' or ". $ext[$pp] ."== 'PDF' or". $ext[$pp]." == 'TIF' or ".$ext[$pp]." == 'tif'";
                    if ($ext[$pp] == 'pdf' or $ext[$pp] == 'PDF' or $ext[$pp] == 'TIF' or $ext[$pp] == 'tif' or $ext[$pp] == 'TIFF' or $ext[$pp] == 'tiff') {
                        //print_r($dataRadIma);
                        //echo $dataRadIma[$name[$pp]]."<br>";
                        //  $pdftext = file_get_contents();
                        if (($pp % 2) == 0)
                            $styyle = 'background :#fff';
                        else
                            $styyle = 'background : rgb(228, 232, 247)';
                        $tppeso = substr($size[$pp], -1);
                        //echo $tppeso;
                        switch ($tppeso) {
                            case 'K':
                                $tamano = $size2[$pp];
                                break;
                            case 'M':
                                $tamano = $size2[$pp] * 1024;
                                break;
                            case 'G':

                                $tamano = $size2[$pp] * 2048;
                                break;
                        }
                        //  echo "{$ext[$pp]} == 'pdf' or {$ext[$pp]} == 'PDF'";
                        if ($ext[$pp] == 'pdf' or $ext[$pp] == 'PDF') {
                            $result = '';
                            $script = " pdfinfo $ruta_owonclod/" . $usA[$index] . "/files/$user/$carpS/$name[$pp] | grep Pages|awk '{print $2}'";
                            $script = " pdfinfo $ruta_owonclod/".$usA[$index]."/files/$carpS/$name[$pp] | grep Pages -a |awk '{print $2}'";
                            exec($script, $result);
                            //print_r($result);
                            $num_pag = $result[0];
                        } else {
                            $num_pag = 1;
                        }
                        $diatra = $this->diasTramite($fecha[$pp]);
                        $hora = substr($g[3], 0, 5);
                        $Combo = "";

                        $resp.="<tr data-id='$pp' data-file='' data-type='file' data-mime='application/pdf' style='$styyle' data-size='{$size[$pp]}' >
				<td class='filename svg ui-draggable ui-droppable'><img src='".$this->ruta_raiz."/img/icono_pdf.jpg' width='22'>					
					<span class='nametext'> {$name[$pp]}	</span> 
				</td>
				<td class='filesize' style='text-align:center' title='{$size[$pp]}' >{$size[$pp]}</td>
				<td class='date' style='text-align:center'><span class='modified' title='{$fecha[$pp]}, $hora' >{$fecha[$pp]}  ($diatra días)</span></td>
                                <td class='pag' style='text-align:center'> $num_pag  </td>";
                        if ($num_pag > 0) {
                          //  echo $dataRadIma[$radi2[$pp]]['radi'];
                            if ($dataRadIma[$radi2[$pp]]['radi']) {
                                if ($dataRadIma[$radi2[$pp]]['path']) {
                                    $EXTdata = substr($dataRadIma[$radi2[$pp]]['path'], -3);
                                    if ($EXTdata == 'pdf' or $EXTdata == 'PDF' or $EXTdata == 'TIF' or $EXTdata == 'tif') {
                                        $accionX = 'Modificar';
                                        $aaX = 1;
                                        $color = 'red';
                                    } else {
                                        $accionX = 'Definitivo';
                                        $aaX = 2;
                                        $color = 'BLUE';
                                    }
                                } else {
                                    $accionX = 'Subir';
                                    $aaX = 0;
                                    $color = '';
                                }
                                $jsfuntion = "subir('div{$name[$pp]}','{$name[$pp]}',$num_pag,'" . $usA[$index] . "','$tamano',{$aaX})";
                                $resp.="<td bgcolor='$color' > <div id='div{$name[$pp]}'>$Combo<input type='button' value='{$accionX}' value='bt{$name[$pp]}' onclick=\"$jsfuntion;\"></div> </td>";
                                if ( $ext[$pp] == 'pdf' or $ext[$pp] == 'PDF') {
                                    $jsfuntion = "subirf('divf{$name[$pp]}','{$name[$pp]}',$num_pag,'" . $usA[$index] . "','$tamano',{$aaX})";
                                    //$resp.="<td bgcolor='$color' > <div id='divf{$name[$pp]}'>$Combo<input type='button' value='{$accionX} con Firma' value='bt{$name[$pp]}' onclick=\"$jsfuntion;\"></div> </td>";
                                }
                            } else {
                                $resp.="<td  > <div id='div{$name[$pp]}'>No valido</div> </td>";
                            }
                        }

                        $resp.="</tr></tbody>";
                    }
                }
            }
        }
        echo $resp;
    }

    function ListarRadSinImagenes($depe, $fechI, $fechF) {
        $data = $this->modelo->radSinIma($depe, $fechI, $fechF);
        $resp.="<table class='table table-bordered' ><tr >
            <th style='width:3px'>#</th>
            <th style='width:10px'>Radicado</th>
            <th style='width:10px;white-space: nowrap'>Radi fecha</th>
            <th>Asunto</th><th style='width:10px'>Radicador</th><th style='width:10px'>Depe. Actu</th></tr>";

        for ($i = 0; $i < count($data); $i++) {
            if (($i % 2) == 0)
                $styyle = 'background :';
            else
                $styyle = 'background :';
            $a = $i + 1;
            $resp.="<tr  style='$styyle' >
                          <td class='leidos' style='border:1px'>{$a}</td>
                          <td  class='leidos' style='border:1px'>{$data[$i]['radi']}</td>
                          <td class='leidos' style='border:1px'>{$data[$i]['fech']}</td>
                          <td class='leidos' style='border:1px solid white;white-space: pre-line'>{$data[$i]['asun']}</td>
                          <td class='leidos' style='border:1px'>{$data[$i]['login']}</td>
                          <td class='leidos' style='border:1px'>{$data[$i]['depeA']}</td></tr>";
        }
        echo $resp . "</table>";
    }

    function diasTramite($fech) {
//echo $fech;
        $fec = explode(' ', $fech);
        $fec2 = explode('-', $fec[0]);
        //defino fecha 1 
//print_r($fec);    
        $ano1 = $fec2['0'];
        $mes1 = $fec2['1'];
        $dia1 = $fec2['2'];

//defino fecha 2 
        $ano2 = date('Y');
        $mes2 = date('m');
        $dia2 = date('d');
//echo $ano1."-".$mes1."-".$dia1."----".    $ano2."-".$mes2."-".$dia2;
//calculo timestam de las dos fechas 
        $timestamp1 = mktime(0, 0, 0, $mes1, $dia1, $ano1);
        $timestamp2 = mktime(4, 12, 0, $mes2, $dia2, $ano2);

//resto a una fecha la otra 
        $segundos_diferencia = $timestamp1 - $timestamp2;
//echo $segundos_diferencia; 
//convierto segundos en días 
        $dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

//obtengo el valor absoulto de los días (quito el posible signo negativo) 
        $dias_diferencia = abs($dias_diferencia);

//quito los decimales a los días de diferencia 
        $dias_diferencia = floor($dias_diferencia);

        return $dias_diferencia;
    }

    function subir($rutaArch, $arch, $dependencia, $codusuario, $id_rol, $pages, $obs) {
        $rad = current(explode('.',$arch));
        //echo "** $rutaArch - $rad - $arch, $id_rol -  $codusuario - $dependencia***";
        /*         * * validacion ** */
        //if ($this->validar($rad) == 'no') {
        $r = "";
        if($_POST["r"]) $r = $_POST["r"];
        if(empty($r) || $r=="undefined"){
            $obs = "Asociar Imagen a Radicado."; 
            $codTx = 22;
         }else{
             $obs =  "Imagen Modificada: ".$r;
             $codTx = 23;
          }
            $this->modelo->actualizar($rutaArch, $rad, $arch, $id_rol, $codusuario, $dependencia, $pages, $obs, $codTx  );
            return "Actualizado con exito";
       // } else {
         //   return "Elradicado ya tiene imagen";
       // }
    }

    function subirf($rutaArch, $arch, $dependencia, $codusuario, $id_rol, $pages) {
        $rad = substr($arch, 0, 14);
        //echo "** $rutaArch - $rad - $arch, $id_rol -  $codusuario - $dependencia***";
        /*         * * validacion ** */
        
        
        
        if ($this->validar($rad) == 'no') {
            $this->modelo->actualizarf($rutaArch, $rad, $arch, $id_rol, $codusuario, $dependencia, $pages, 'Asociar Imagen a Radicado 3');
            return "Actualizado con exito";
        } else {
            return "Elradicado ya tiene imagen";
        }
    }

    function subirR($rutaArch, $arch, $dependencia, $codusuario, $id_rol, $pages, $peso, $obs) {
			$radi = substr($arch, 0, 14);
			//echo "** $rutaArch - $rad - $arch, $id_rol -  $codusuario - $dependencia***$obs";
			/*         * * validacion ** */
			if (!$obs) {
					$observacion = 'Asociar Imagen a Radicado';
			} else {
					$observacion = 'Modificar imagen Radicado ' . $obs;
			}
			if ($this->validar($radi)) {
				$a = $this->modelo->actualizar($rutaArch, $radi, $arch, $id_rol, $codusuario, $dependencia, $pages, $observacion, $observacion);
				//anexos
				if ($a == 'error')
						return "No se pudo realizar la operacion con  El radicado";
				$tipo = substr($radi, -1);
				$resp2 = $this->modelo->consultarNumAnex($radi);
				$anex_codi = $resp2['code'];
				$status = $resp2['status'];
				$ext = 'pdf';
				$auxsololect = "S";
				$anexDesc = $desc . 'Imagen del radicado ' . $radi;
				$anex = $this->modelo->anex_tipo_ext($ext);
				$auxnumero = substr($anex_codi, -5);
				$codigoExtension = $anex['codi'];
				$ext = strtolower($ext);

				$archivo = trim($radi . '_' . $auxnumero . '.' . $ext);
				$archivoconversion = $arch; //trim('1') . trim(trim($radi) . '_' . $auxnumero . '.' . trim($ext));
				$peso = 0;
				if ($status == 'Nuevo' && substr($radi, -1)!= '2')
						$anex = $this->modelo->grabarAnexo($radi, $anex_codi, $codigoExtension, $peso, $auxsololect, $_SESSION['krd'], $anexDesc, $auxnumero, $archivoconversion, $dependencia, $rutaArch, $arch, 0, $radi, 3, 1, 0, 1);
				elseif (substr($radi,-1)!='2')
						$this->modelo->actualizar3chulo($radi);
			} else {
					return "<font color=green >Elradicado ya tiene imagen</FONT>";
			}
			return "<font color=green >Actualizado con exito</FONT>";
    }

    function subirA($rutaArch, $arch, $dependencia, $codusuario, $id_rol, $peso, $tpDoc,$pages, $comentario) {
			$radi = current(explode('_',$arch));
			//validar radicado
			//echo "<hr>";
			$validar = $this->modelo->validarRad($radi);
			$auxnumero = '';
			$anex = '';
			if ($validar == 1) {
				//consultar consecutivo
				//echo 'entra';
				$anex_codi = $this->modelo->consultarNumAnex2($radi);
					$daddd = explode('.', $arch);
				$ext = end($daddd);
				$auxsololect = "S";
				$anexDesc = $desc . $comentario ;
				$anex = $this->modelo->anex_tipo_ext($ext);
	    if(!is_array($anex))
			 return $anex;
            $auxnumero = substr($anex_codi, -5);
           $codigoExtension = $anex['codi'];
           $ext = strtolower($ext);
            $archivo = trim($radi . '_' . $auxnumero . '.' . $ext);
            $archivoconversion = trim('1') . trim(trim($radi) . '_' . $auxnumero . '.' . trim($ext));
            //   echo 'entra2';
            //  echo "$anex=$this->modelo->grabarAnexo($radi, $anex_codi, $codigoExtension, $peso, $auxsololect, {$_SESSION['krd']}, $anexDesc, $auxnumero, $archivoconversion, $dependencia,$rutaArch,$arch,$tpdoc)";
            $anex = $this->modelo->grabarAnexo($radi, $anex_codi, $codigoExtension, $peso, $auxsololect, $_SESSION['krd'], $anexDesc, $auxnumero, $archivoconversion, $dependencia, $rutaArch, $arch, $tpDoc);
            $validarAnex = $this->modelo->validarAnex($anex);
            if ($validarAnex == 1){
                return "<font color='green' >Anexo Cargado Correctamente.</font> ($anex)";
            }else {
                return '<font color="red" >No se puede anexar </FONT>';
            }
        } else {
            return '<font color="red">No es un número valido de radicado</FONT>';
        }

//         //echo "$rutaArch,$arch,$dependencia,$codusuario,$id_rol,$pages";
        //echo "** $rutaArch - $rad - $arch, $id_rol -  $codusuario - $dependencia***";
        /*         * * validacion ** */
        /* if($this->validar($rad)=='no'){
          //$this->modelo->actualizar($rutaArch, $rad, $arch, $id_rol, $codusuario, $dependencia,$pages);
          return "Actualizado con exito";
          }
          else{ */
        /* return "Elradicado ya tiene imagen"; */
        //} 	
//Array ( [action] => Asubir [name] => 20129000000073-1.pdf [pages] => 3 [userOwn] => deimont )
//// /var/www/html/owncloud/data/deimont/files/clientsync/admon/ANEXOS/,20129000000073-1.pdf,900,1,1,3
        //$anex_codi=$this->modelo->consultarNumAnex($radi);
        //return $archivoconversion;
    }

    function validar($radi) {

        /*         * * validacion ** */
        $num = $this->modelo->validarImagen($radi);
        //print_r($num);
        //echo $num['path']." && ".$num['path']." !='null'";
        if ($num['path'] == NULL)
            $var = 'no';
        elseif ($num['path'] == "null")
            $var = 'no';
        else
            $var = 'si';
        // echo $var;
        return $var;
        //$this->modelo->actualizar($rutaArch, $rad, $arch, $id_rol, $codusuario, $dependencia);
    }

}
?>

