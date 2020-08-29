/*
 * informe_print.js
 */

// Set toastr options
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};

async function _makeAfipFile(endpoint) {
	const res = await fetch(endpoint);
	let data  = await res.json();

	//console.log(data);

	if (data.status === 'Ok') {
		_descargarArchivo(data.file);
	} else {
		toastr["error"]("No se pudo crear archivo de Carta de Porte !", "Error !!");
	} 
}

function _descargarArchivo(file) {
	//const filePath = '/cartasporte/cp-' + dataRet.nroCP + '.txt';
	const idx = file.lastIndexOf('/') + 1;
	const fileName = file.substr(idx);
	const filePath = '/cartasporte/' + fileName;
	const req = new XMLHttpRequest();

	//req.open("GET", filePath, true);
	req.open("GET", filePath, true);
	req.responseType = "blob";
	req.onload = function(event) {
		const blob = req.response;
		// Aquí hacemos que el archivo se descargue
		let link  = document.createElement('a');
	    link.href = window.URL.createObjectURL(blob);
	    link.download = fileName;
	    link.click();
	};
	req.send();
}



// Codigo jQuery
$(document).ready(function(){

	// Para boton volver
	$('#btnVolver').click( function() {
    	window.close();
    });

	// Boton Imprimir
	$('#btnImprimir').click( function() {
        window.print();
	});

	// Boton Archivo Afip
	$('#btnDescargar').click(function(event) {
		const url = _pathMakeAfipFile + _paramsAfipFile;

		_makeAfipFile(url);		// Envio url para crear archivo de afip en el server...
	});
});