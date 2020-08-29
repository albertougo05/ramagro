/*
 * informe.js
 */

INFO.paramString = function () {
	let paramStr = '?';

	paramStr += 'desde=' + $('#fechaDesde').val();
	paramStr += '&hasta=' + $('#fechaHasta').val();
	paramStr += '&idprod=' + $('#selProductor').val();
	paramStr += '&iddest=' + $('#selDestino').val();
	paramStr += '&idcere=' + $('#selCereal').val();
	paramStr += '&idcamp=' + $('#selCampania').val();
	paramStr += '&idtipo=' + $('#selTipo').val();

	return paramStr;
};



/*
 * Codigo jQuery:
 */ 
$(document).ready( function () {

	// Click boton 'Generar listado FORM'
	$('#btnGenList').click(function (event) {
		const paramString = INFO.paramString();

console.log('Params: ' + paramString);

		// Envio de datos a controller por get...
		window.open(INFO.pathImprime + paramString, '_blank');
	});


});
