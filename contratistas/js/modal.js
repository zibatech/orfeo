$(function() {
	window.modal = function(ruta, opciones, recargar_al_cierre = true) {
		if(this.modal_id)
			this.modal_id = 0
		else
			this.modal_id ++
		
		doc = document;
		
		var _this = this;
		window['modal_'+this.modal_id] = window.open(ruta, 'importwindow', opciones);
		
		if (recargar_al_cierre)
		{
			window['modal_'+this.modal_id].onload = function() {
				window['modal_'+_this.modal_id].onunload = function() {
		            doc.location.reload(true);
		        }
			}
		}
	}
});