<html>
<head>
<title>Incluir Radicado en expediente</title>
</head>
<body class=" yui-skin-sam">
    <form id="masiva" name="masiva"  method="POST">
    	<input type="hidden" value="<!--{$krd}-->" 	name="krd">				
		<table width="96%" align="center" margin="4">      
			<tr>
				<td  class="titulos4" colspan="2" align="center" valign="middle">
					<b>Incluir radicado en expediente</b>
				</td>
			</tr>
						
			<!-- INICIO Buscar Expediente -->					
			<tr height="40px">
		        <td>* Expediente</td>
				<td>							
					<div class="searchNomAutoCom">								
						<input  type="text" name="nomb_Expe_search" class="select_crearExp"	id="nomb_Expe_search"/>
					    <div id="contnExpSearch"></div>
					</div>
					<div class="inpuNoExp"><button class="botones" type="button" id="buscNomExp"> Buscar </button></div>																					                    
				</td>
		    </tr>					
			<!-- FIN Buscar Expediente -->
						
			<!-- INICIO Nombre Actual del expediente -->
			<tr height="40px">
		        <td valign="top">Nombre del Expediente:</td>
				<td valign="center" width="100%">
					<textarea readonly="READONLY"  class="select_crearExp nombActuExp" name="nombActuExp" id="nombActuExp"></textarea>																					                    
				</td>
		    </tr>
			<!-- FIN Nombre Actual del expediente -->			
			
			<!--INICIO Radicados seleccionados-->
			<tr height="40px">
				<td valign="top" align="left">
					Radicados:<br/>
				</td>
				<td>
					<textarea name="radicados" id="radicados" readonly="READONLY"  class="select_crearExp nombActuExp"><!--{$radicados}--></textarea>
				</td>
			</tr>
			<!--FIN Radicados seleccionados-->			
			
			<!--INICIO Radicados Hijos de los seleccionados-->
			<!--{if $rad_hijos eq ''}-->
			   &nbsp;
			<!--{else}-->
			<tr height="40px">
				<td valign="top" align="left">
					Radicados hijos:<br/>
				</td>
				<td>
					<textarea id="rad_hijos"  name="rad_hijos" readonly="READONLY"  class="select_crearExp nombActuExp" ><!--{$rad_hijos}--></textarea>
					Incluir los radicados hijos.
					<input type="checkbox" name="cambExiTrd" value="444"> 
				</td>
			</tr>
			<!--{/if}-->
			<!--FIN Radicados Hijos de los seleccionados-->			
			
			<!--INICIO Botones-->
			<tr height="40px">		                
				<td colspan="2" valign="center" align="center">
					<button class="botones" type="button" id="incluirEnExpMass"> Incluir </button>
				</td>
		    </tr>	
			<!--FIN Botones-->			
        </table>
    </form>
	
	<!--INICIO Respuesta -->
	<table id="respuestaTrdMass"  class="yui-hidden2"  width="100%" align="center" margin="4">
		<tr>
			<td  class="titulos4" colspan="2" align="center" valign="middle">
				<center><b>Se incluyeron los siguientes radicados<b></center>
			</td>
		</tr>		
		<tr height="40px">
			<td valign="center" align="left" width="40%">
				<b>Numero del Expediente:</b><br/>
			</td>
			<td>
				<div id="numExpResul"></div>
			</td>
		</tr>		
		<tr height="40px">
			<td valign="top" align="left">
				<b>Radicados incluidos:</b><br/>
			</td>
			<td>
				<textarea id="radiIncluidos" readonly="READONLY"  class="select_crearExp nombActuExp"></textarea>
			</td>
		</tr>
		<tr height="40px">
			<td valign="top" align="left">
				<b>Radicados No incluidos:</b><br/>
			</td>
			<td>
				<textarea id="radiNoIncluidos" readonly="READONLY"  class="select_crearExp nombActuExp"></textarea>
			</td>
		</tr>

			<tr height="40px">		                
				<td colspan="2" valign="center" align="center">
					<button class="botones" type="button" id="cerraryactualizar"> Cerrar y Acualizar </button>
				</td>
		    </tr>	
	</table>
	<!--FIN Respuesta -->	
</body>
</html>
