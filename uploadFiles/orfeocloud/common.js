//permite  enviar post
function  partes(url,div,parameter,getpara){
	var ajaxRequest;  // The variable that makes Ajax possible!
	try{// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){// Internet Explorer Browsers
		try{ 			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var ajaxDisplay = document.getElementById(div);
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
		}
	}
	//alert(url+div+parameter+getpara);
	getpara2='';
	if(getpara){
		getpara2="?"+getpara;
	}
	var string=url+getpara2;
	var String2=parameter;
//	alert(String2)
	ajaxRequest.open("POST",string, true);
	ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajaxRequest.send(String2); 
	//alert(url+div+parameter+getpara);
};

//function trim

function trim(cadena)
{
	for(i=0; i<cadena.length; )
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(i+1, cadena.length);
		else
			break;
	}

	for(i=cadena.length-1; i>=0; i=cadena.length-1)
	{
		if(cadena.charAt(i)==" ")
			cadena=cadena.substring(0,i);
		else
			break;
	}

	return 	cadena.length;
}

function vistaFormUnitid(mostrar,accion){
	if(accion==1)
	document.getElementById(mostrar).style.display='block'; //.style.visibility='visible';
	if(accion==2)
	document.getElementById(mostrar).style.display='none';

}
function vistaFormUnitid2(mostrar,num){
	if(num==1){
	document.getElementById(mostrar).style.display='block'; //.style.visibility='visible';
        document.getElementById(mostrar+'2').style.display='none';
        }
	if(num==2){
            document.getElementById(mostrar+''+num).style.display='block'; //.style.visibility='visible';
	document.getElementById(mostrar).style.display='none';
}
}
function menu(){
	document.getElementById('correspondecia').innerHTML = "<br><center><img src='imagenes/nuevo.gif'></center><br> ";
	partes('core/vista/carpetas.php','correspondecia','','');
}

///hd 28
function compare_dates(fecha, fecha2)  
{  
  var xMonth=fecha.substring(3, 5);  
  var xDay=fecha.substring(0, 2);  
  var xYear=fecha.substring(6,10);  
  var yMonth=fecha2.substring(3, 5);  
  var yDay=fecha2.substring(0, 2);  
  var yYear=fecha2.substring(6,10);  
  if (xYear> yYear)  
  {  
      return(true)  
  }  
  else  
  {  
    if (xYear == yYear)  
    {   
      if (xMonth> yMonth)  
      {  
          return(true)  
      }  
      else  
      {   
        if (xMonth == yMonth)  
        {  
          if (xDay> yDay)  
            return(true);  
          else  
            return(false);  
        }  
        else  
          return(false);  
      }  
    }  
    else  
      return(false);  
  }  
}  

function vistaFecha(f1,f2) {   
	fechaI=document.getElementById(f1).value;
	fechaF=document.getElementById(f2).value;	 
	
	 pasa='ok';
	 if(fechaI == '' ){
           alert('Tiene que  elegir la fecha de inicio o  le falta algun campo de la fecha de inicio.');
           pasa='no';
		 }

	 if (compare_dates(fechaI, fechaF)){  
	   alert("La fecha  de inicio no puede ser mayor a la fecha final");
	   pasa='no';  
	 }
	 miFecha = new Date() 
	 mes=miFecha.getMonth()+1
	 if(mes<10){
		 mes='0'+mes;
	 }
	 dia=miFecha.getDate();
	 if(dia<10){
		 dia='0'+dia;
	 }
	 fechaActual=dia+"/"+mes+"/"+miFecha.getFullYear();
//	 alert(fechaActual+" @ "+fechaF); 
	// alert(fechaI+" hasta "+fechaF+" Fecha Actual "+fechaActual);
	 if (compare_dates(fechaF, fechaActual)){
		 
		   alert("La fecha  hasta no puede ser mayor a la fecha actual");
		   pasa='no';  
		 }
	 /*else{  
	   alert("fecha1 es menor a fecha2");  
	 }*/
     if (pasa=='ok'){
        return 'ok';
	 }
     return 'bad';
	 
}

function verDocumento(ruta,radicado) {   
     window.open(ruta+'/core/Modulos/radicacion/vista/image.php?radicado='+radicado,'image');
     return 'bad';
	 
}
///fin hd 28