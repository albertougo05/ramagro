//
// Javascript
// 
var _idCarta = 0, 
    _cerrar = false;


function _setTableFilter() {
	//console.log('set filter');
	$('#contenTable').tableFilter({ tableID:    '#tablaBuscar', 
									filterID:   '#filter',
	                               	filterCell: '.filter-cell',
	                               	noResults:  'No se encontraron resultados.',
		                        	autofocus:  true });
	// Evento click para seleccionar y redireccionar 
	$('#tablaBuscar tr').on('click', function(){
		_idCarta = $(this).find('td:first').text();
		// Cierra el popup y vuelve a ventana de carta de porte
		_cerrar = true;
		//console.log('Cierra la ventana !!');
	});
}

function _setFecha(fecha) {
	const arFecha = fecha.split('-');

	return arFecha[2] + '/' + arFecha[1] + '/' + arFecha[0];
}

class RecargarTabla {
	constructor(desde, hasta) {
		this.desde = desde;
		this.hasta = hasta;
	}

	buscarDatos() {
		const esto = this;
		$.get( _pathListaCartas, { desde: this.desde, hasta: this.hasta } )
			.done(function( data ) {
    			//console.log( "Data Loaded: " + data );
    			esto.fillTabla( data );
  			})
  			.fail(function() {
    			toastr["error"]("No se obtuvieron datos !!", "Error !!");
  			});
	}

	fillTabla(listado) {
		const lista = JSON.parse(listado);
		const tipoCp = ['', 'Recibida', 'Recibida c/cambio dest.', 'Emitida'];

		const lineas = lista.map( function (elem) {
			let lin = '<tr><td>' + elem.id + '</td><td>' + _setFecha(elem.FechaCarga) + '</td>';
		    lin += '<td>' + tipoCp[elem.TipoCP] + '</td>';
		    lin += '<td id="nro" class="filter-cell cellRight">' + elem.NroCartaPorte + '</td>';
		    lin += '<td id="cee" class="cellRight">' + elem.NroCEE + '</td>';
		    lin += '<td id="ctg" class="cellRight">' + elem.NroCTG + '</td>';
		    lin += '<td>' + elem.LocProcedencia + '</td><td>' + elem.LocDestino + '</td></tr>';
		    return lin;
		});

		$("#tablaBuscar tbody").empty();		// Vaciar la tabla
		$.each(lineas, function(index, lin) {		// Llenar la tabla
			$("#tablaBuscar tbody").append( lin );
		});

		_setTableFilter();
	}



}


//
// jQuery
//

$(document).ready(function(){

	// Filtrado de tabla Clientes	
	_setTableFilter();

	// Click en radio buttons
	$('input[type=radio]').click(function (e) {
		const id = $(this).attr('id');
		const names = ['nro', 'tipo', 'ctg', 'cee', 'proc', 'dest'];

		names.forEach(function( elem ) {		// Remove class a todos
			$('td#' + elem).removeClass('filter-cell');
			//console.log('td#' + elem);
		});

		$('td#' + id).addClass('filter-cell');		//Agrego class al id seleccionado
		$('input#filter').val('').focus();			// Vacio input y doy foco
	});

	// Click en boton Cancelar. Cierra la ventana
	$('#btnCancel').click(function (e) {
		window.close();
	});

	/* Click en boton recargar */
	$('.btn-recargar').click( function(e) {
		const recargar = new RecargarTabla( $('#fechaDesde').val(), $('#fechaHasta').val() );
		recargar.buscarDatos();

	});

});