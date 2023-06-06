$(function() {
	$('.buscador_usuario').selectize({
		valueField: "ID",
		labelField: "NOMBRE",
		create: false,
		searchField: ["NOMBRE", "DOC"],
  		render: {
  			option: function(item, escape) {
  				return `
  					<div style="padding: 2px 12px;">
						<span>
							<strong>${item.NOMBRE.toUpperCase()}</strong> 
							<small>${item.DOC}</small>
						</span>
  					</div>
  				`
  			},
  			item: function(item, escape) {
  				return `
  					<div>
						<span>
							<strong>${item.NOMBRE.toUpperCase()}</strong> 
							<small>${item.DOC}</small>
						</span>
  					</div>
  				`
  			}
  		},
  		load: function(query, callback) {
  			if (!query.length) return callback();
  			var depe = '';

  			if ($('.buscador_usuario').data('dependencia'))
  				depe = '&depe='+$('.buscador_usuario').data('dependencia');

  			$.ajax({
		      url: "usuarios.php?q=" + encodeURIComponent(query) + depe,
		      type: "GET",
		      error: function () {
		        callback();
		      },
		      success: function (res) {
		        callback(res.results);
		      },
		    });
  		}
	})
});