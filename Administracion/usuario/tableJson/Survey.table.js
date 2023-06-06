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
       //console.log(value);

        //console.log(value[i]);    // de 0 a 16 .. o el tamaño de las  key 
        var datos   = " <tr style='cursor:pointer;'>";
          /* */
          datos   = datos + ' <td width="40px"> ';
              datos   = datos + '<div class="btn-group">';
                datos   = datos + '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="caret"></span></button>';
                  datos = datos + '<ul class="dropdown-menu" style="z-index: 999999; position: absolute;" role="menu">';
                    datos = datos + '<li><a href="index.php?mod=survey&controlador=Survey&accion=editForm&id='+value['ID']+'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar '+value['ID']+'</a></li>';
                    //datos = datos + '<li><a href="index.php?mod=Survey&controlador=FieldSurvey&accion=newForm&idProyecto='+value['ID']+'"><i class="fa fa-pencil-square" aria-hidden="true"></i> Editar preguntas</a></li>';
                    //datos = datos + '<li><a href="index.php?mod=survey&controlador=FieldSurvey&accion=listResponse&id='+value['ID']+'"><i class="fa fa-eye" aria-hidden="true"></i> Ver Respuestas </a></li> ';
                    //datos = datos + '<li class="divider"></li>';
                    //datos = datos + '<li><a href="index.php?mod=survey&controlador=Survey&accion=view&id='+value['ID']+'"><i class="fa fa-check-square-o" aria-hidden="true"></i> Diligenciar el estudio</a></li> ';
                    //datos = datos + '<li><a href="index.php?mod=survey&controlador=Survey&accion=tclone&id='+value['ID']+'"> <i class="fa fa-files-o" aria-hidden="true"></i> Clonar Encuesta </a></li> ';
                    //datos = datos + '<li class="divider"></li>';
                    //datos = datos + '<li><a target="_blank" href="public/index.php?estudio='+value['ID']+'"><i class="fa fa-share" aria-hidden="true"></i> Acceso público</a></li>';
                    //datos = datos + '<li class="divider"></li>';
                    //datos = datos + '<li class="list-group-item-danger" ><a href="index.php?mod=survey&controlador=Survey&accion=delete&id='+value['ID']+'"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</a></li>';
                  datos = datos + '</ul>';
              datos   = datos + '</div>';
          datos   = datos + '</td>';

        $.each(value, function(key2,value2){
          switch(key2.toUpperCase()) {
              case 'ID':
                  //No se muestra
                  break;
              case 'CODIGO':
                  //No se muestra
                  break;
              case 'LOGIN':
              //console.log(value['CODIGO']);
                if (value['CODIGO']=='1') {
                     datos   = datos + " <td > "+value2.toUpperCase()+" <span class='tag label label-info'>Jefe</span> </td>"; 
                }else{
                     datos   = datos + " <td > "+value2.toUpperCase()+" </td>";               
                }
                  break;
              case 'NUEVO':
                  if (value2=='1') {
                    
                    datos   = datos + "<td width='7%'><span class='tag label label-primary'>Actual</span></td>";
                  }else{
                     datos   = datos + "<td width='7%'><span class='tag label label-warning'>Nuevo</span></td>";
                  }
                  break;
              case 'ESTADO':
                  if (value2=='1') {
                    
                    datos   = datos + "<td width='7%'><span class='tag label label-success'>Activo</span></td>";
                  }else{
                     datos   = datos + "<td width='7%'><span class='tag label label-danger'>Inactivo</span></td>";
                  }
                  break;
              default:
                  datos   = datos + " <td > "+value2.toUpperCase()+" </td>";
          }

        });

        datos   = datos + " </tr>"; 

        divresponse.append(datos);

      }); // Fin each 
      

    } //Fin funcion tablejson


