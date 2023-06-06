imagenesButton();

function imagenesButton(){

	//Obtenemos todos los selectores
	var selector = document.getElementsByClassName("cc-selector");
	//Recorremos todos los selectores 
	jQuery.each(selector, function( index ) {

		$imagen = $( this ).children("input").attr("imagen");
		$id = $( this ).children("input").attr("id");

		//Se implementa la imagen
		$('.'+$id).css("background-image","url("+$imagen+")"); 

	});


}