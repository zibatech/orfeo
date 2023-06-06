El archivo index_frames.php accedera al archivo index.tpl que se
encuentra en la carpeta themes/<NombreTema>/index.tpl, desde donde
se puede acceder a los siguientes atributos

Banderillas de existencia de los menus, 1 si existe o si no
menuAcciones,
menuAdministracion,
menuRadicacion,
menuBandejas
Variable guardadas en config.php
colorFondo
ambiente
entidad
entidad_largo
tema
Se puede acceder a los siguientes menus individualmente
acciones,
administracion,
radicacion,
bandejas,
opciones,
usuario

Acontinuación se encuentra el arbol con todos los atributos a los
que se puede acceder desde menus, siempre y cuando los permisos
de los usuarios lo permitan.
Para acceder por medio de smarty para crear su tema como lo desee
ubique en el archivo index.tpl
ej. <!--{$menus.acciones.flujos.nombre}--> mostrara en la platilla
"flujos"
<cuerpo> = {subMenu,url,nombre}
Donde subMenu es una banderilla que 0 indica que no tiene submenu
y 1 que si se puede acceder al atributo sub,
ej. quiere decir que flujos tiene dicha bandera en 1 en cambio
envios la tiene en 0
bandeja_# = Donde # es el id relacionado a la bandeja
ej. (Entrada,bandeja_9998)
se recomienda usar el atributo id en caso que se desee usar
el javascript en el tema CorrelibreNavBarUp/index.tpl
usuario# = Donde # es el codigo de la dependencia.
menus{acciones{	nombre
		menu{anulacion{<cuerpo>}
		flujos{<cuerpo>
			sub{crearFlujo{<cuerpo>}
			    editarFlujo{<cuerpo>}
			}
		}
		envios{<cuerpo>}
		trd{<cuerpo>
		    sub	{series{<cuerpo>}
			 subSeries{<cuerpo>}
			 matrizRelacion{<cuerpo>}
			 tiposDocumentales{<cuerpo>}
			 modificacionTRD{<cuerpo>}
			 ListadoTablas{<cuerpo>	}
			}
		}
		enviar{<cuerpo>}
		modificacion{<cuerpo>}
		prestamo{<cuerpo>
			 sub{prestamoDocumentos{<cuerpo>}
			     devolucionDocumentos{<cuerpo>}
			     generacionReportes{<cuerpo>}
			     cancelarSolicitudes{<cuerpo>}
			    }
			}
		archivo	{<cuerpo>
			 sub{archivo{<cuerpo>}
			     reporteArchivados{<cuerpo>}
			     buquedaGeneral{<cuerpo>}
			     buquedaFondo{<cuerpo>}
			     insertarFondo{<cuerpo>}
			     }
			}
      	      }
     }
     administracion{nombre
		    menu{usuariosPerfiles{<cuerpo>}
		         tarifas{<cuerpo>}
		    	 dependencias{<cuerpo>}
		   	 diasNoHabiles{<cuerpo>}
			 envioCorrespondencia{<cuerpo>}
			 mensajesRapidos{<cuerpo>}
			 tablasSencillas{<cuerpo>}
			 tiposRadicados{<cuerpo>}
			 paises{<cuerpo>}
			 departamentos{<cuerpo>}
			 municipios{<cuerpo>}
			 plantillas{<cuerpo>}
			 }
     }
     usuario{nombre
	     menu{perfil{<cuerpo>}
		  usuario#{<cuerpo>
			   noframe} //para diferenciar los enlaces que no se deben mostrar en en mainFrame
		  cambioDeClave{<cuerpo>}
		  salir{<cuerpo>}
		  }
     }
     radicacion{nombre
	        menu{radica0{<cuerpo>}
		     radica1{<cuerpo>}
		     masiva{<cuerpo>
		            sub{masivaExterna{<cuerpo>}
			        recuperarListado{<cuerpo>}
		     	    }
	             }
		     ownCloud{<cuerpo>}
		     asociarImagenes{<cuerpo>}
		     email{<cuerpo>}
		}
     }
     bandejas{nombre
	      menu{consultas{<cuerpo>
			     sub{consultaClasica{<cuerpo>}
		       	         consultaExpedientes{<cuerpo>}
		   	     }
		   }
		   estadisticas{<cuerpo>}
		   general{<cuerpo>}
		   bandeja_#{<cuerpo>
				id
		   }
		   informados{<cuerpo>}
		   transacciones{<cuerpo>}
		   personales{<cuerpo>
		   	      sub{nuevaCarpeta{<cuerpo>}
				  bandeja_#{<cuerpo>
					    id
 				  }
			      }			      
		   }
	      }
      }
      opciones{nombre
	       menu{plantillas{<cuerpo>}
		    ayuda{<cuerpo>}
		    formatos{<cuerpo>}
	       }
      }
}
