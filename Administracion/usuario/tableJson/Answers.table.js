/**
*@author Cesar Buelvas
*@mail cejebuto@gmail.com
*@date 01/02/2017
*/
	tableJson = function(data) {

			var NumValues =        Number ( $( "input[name='numValues']" ).val() );  

			//divresponse.append(data);
			//divresponse.append("cejebuto");  
			//console.log(data);   
			var i = 0;
			
			$.each(data, function(key, value) {   

				//console.log(value[i]);    // de 0 a 16 .. o el tamaño de las  key
				var datos   = "	<tr style='cursor:pointer;'>";

				for (var i = 0; i <= NumValues; i++) {
					datos   = datos + " <td> "+ value[i] +" </td>";
				}
				datos   = datos + "	</tr>"; 

		 		divresponse.append(datos);

			}); // Fin each 
			

		} //Fin funcion tablejson