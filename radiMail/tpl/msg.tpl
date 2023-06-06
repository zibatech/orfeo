<html>
<title>Radicador de correo electronico Orfeo 3.8.6</title>
<head>
<style type="text/css">
.border {
	border: 1px solid #377584;
	border-radius:5px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
}
.titulo {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	color:#000000;
	background-color: #e4e9ef;
	text-indent: 5pt;
	vertical-align: middle;
}
.listado {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	font-weight: bolder;
	text-transform: none;
	color: #000000;
	text-decoration: none;
	background-color: #e4e9ee;
	vertical-align: middle;
	height: 30px;
}
</style>
<script>

$(".replythis").click(function(){
	//loadURL("radiMail.php?uid={$uid}", $('#inbox-content > .table-wrap'));
	loadURL("radiMail.php?uid={$uid}", $('#radicador > .table-wrap'));
	//radicador=window.open('../radicacion/NEW.php?uid={$uid}&tipoMedio=eMail&ent=2', 'popup', 'width=900,height=800');
});

function filed(nurad,uid){
	loadURL("email-opened.php?uid="+uid+"&nurad="+nurad, $('#inbox-content > .table-wrap'));
	$('#radicador').hide();
}

function asociarMail(){
	numeroRad = parent.frames['formulario'].document.getElementById('numeroRadicado').value;
	if(numeroRad>=1){
		document.getElementById('numeroRadicado').value = numeroRad;
		document.getElementById('formAsociarMail').submit();
	}else{
		alert(" ? No se generado un Radicado ! ");
	}
}
function funlinkArchivo(numrad,rutaRaiz){
	nombreventana="linkVistArch";
	url=rutaRaiz + "/linkArchivo.php?"+"&numrad="+numrad;
	ventana = window.open(url,nombreventana,'scrollbars=1,height=50,width=250');
	return;
}
</script>
</head>

<body>
	<table width="100%" class="border">
		<tr class=titulo>
			<td align=right>
				<font size=1>
					<div class="btn-group text-left">
						{$buttonFiled}
					</div>
				</font>  
			</td>
		</tr>
	</table>

	<table width="100%" cellspacing="7" border="0" cellpadding="0" class="border" >
		<tr>
			<td width=60%>&nbsp;</td>
			<td>
				<span style="font-size: 84;font-family: 'Free 3 of 9'">*{$nurad}*</span>
				<br>
				Radicado No. {$nurad}<br>
				Fecha : {$fecha}<br>
				{$links}
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>
			</td>
		</tr>
	</table>
	<table class=border width=100%>
	<tr>
		<td class=titulo width=15%>Correo</td>
		<td class=listado>{$email}</td>
	</tr>
	<tr>
		<td class=titulo>Nombre</td>
		<td class=listado>{$name}</td>
	</tr>
	<tr>
		<td class=titulo> Fecha </td>
		<td class=listado>{$date}</td>
	</tr>
	<tr>
		<td class=titulo>Asunto </td>
		<td class=listado>{$subject}</td>
	</tr>
		<table class=border width=100%>
			<tr>
				<td>
					<div dir="ltr">{$body}</div>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
				</td>
			</tr>
		</table>
		<table class=border width=100%>
			<tr>
				<td class=titulo>
					Archivos Adjuntos
					<br>
					{$listaAdjuntos}
				</td>
			</tr>
		</table>
	</body>
</html>
