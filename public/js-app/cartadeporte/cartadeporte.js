//
// cartadeporte.js
// 


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



var CARTA = {

	_idCarta: 0,		// Id carta de porte editando
	winBuscar: false,   // Variable para saber si está abierta la ventana de búsqueda
	objWinBuscar: {},   // Ventana de búsqueda

	// Comun para selects de Firma
	selectFirma: function (elem) {    // Cada change de los select Firma
		let name = elem.id;    // Id del select
        let val  = $(elem).children("option:selected").val();  // Valor ( Id de la Firma)
        let cuit = $(elem).children("option:selected").attr('data-cuit');   // Cuit
        let inputId = 'cuit' + name.substring(3);    // Nombre Id input cuit

		if (val == 0) {
			$('input#' + inputId).val('');
		} else {
        	$('input#' + inputId).val(cuit);    // Cargo el cuit en input relativo
		}
	},

	// Cambiar tipo de grano (segun select de cereal)
	cambiarTipo: function (tipo) {
		const sel = document.getElementById('selTipoGrano');
		const opts = sel.options;

		for (let opt, j = 0; opt = opts[j]; j++) {
			if (opt.value == tipo) {
      			sel.selectedIndex = j;
				break;
			}
		}
	},

	// Convierto string a float
	stringToFloat: function (strg) {
		let sinpuntos = strg.replace(".", "");   // Elimino los puntos de miles
		let pospunto = sinpuntos.indexOf(".");
		if (pospunto > 0) {		// Quito el otro punto
			sinpuntos = sinpuntos.replace(".", ""); 
		}

		return sinpuntos.replace(",", ".");      // La coma decimal en punto
	},

    // Seteos de Imputmask
    setsInputMask: function () {
		// Set horas mask
		let _horaArribo = document.getElementById("horaArribo");
		let _horaDescarga = document.getElementById("horaDescarga");
		let _cosecha = document.getElementById("cosecha");

		Inputmask("99:99", { numericInput: true, placeholder: "_", greedy: true }).mask(_horaArribo);
		Inputmask("99:99", { numericInput: true, placeholder: "_", greedy: false }).mask(_horaDescarga);
		Inputmask("99-99", { numericInput: true, placeholder: "_", greedy: true }).mask(_cosecha);

    	//  Para importes
		$('input.nroFloat').inputmask("numeric", {
			alias: "curency",
			radixPoint: ",",
			groupSeparator: ".",
			digits: 2,
			autoGroup: true,
			rightAlign: true,
			unmaskAsNumber: true, 
			allowPlus: false,
	    	allowMinus: true,
			oncleared: function () { self.value = ''; }
		});
    },

    // Scrol para cuando la página es muy larga
    scrollFunction: function (boton) {
		if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
			boton.style.display = "block";
		} else {
			boton.style.display = "none";
		}
	},

	// Calculo peso Neto desde Bruto
	calcPesoNetoDesdeBruto: function () {
		$('#pesoBruto2, #pesoBruto5').blur(function(event) {
			const idInput = event.target.id;
			const id = idInput.substr(-1);
			const kgsBruto = parseFloat( CARTA.stringToFloat( $(this).val() ));
			const kgsTara  = parseFloat( CARTA.stringToFloat( $('#pesoTara' + id).val() )); ;

			//console.log('Id: ' + id + ' - Valor bruto: ' + CARTA.stringToFloat(kgsBruto));

			//kgsTara = parseFloat( $('#pesoTara' + id).val() );
			if (kgsTara > 0) { 
				CARTA.pesoNeto2 = kgsBruto - kgsTara;
				$('#pesoNeto' + id).val(kgsBruto - kgsTara);
			}
		});
	},

	// Desde Tara
	calcPesoNetoDesdeTara: function () {
		$('#pesoTara2, #pesoTara5').blur( function (event) {
			const idInput = event.target.id;
			const id = idInput.substr(-1);
			const kgsTara = parseFloat( CARTA.stringToFloat( $(this).val() ));
			let kgsBruto = parseFloat( CARTA.stringToFloat( $('#pesoBruto' + id).val() ));

			//console.log('Id: ' + id + ' - Valor tara: ' + kgsTara);

			//kgsBruto = parseFloat( $('#pesoBruto' + id).val() );
			if (kgsBruto > 0) { 
				$('#pesoNeto' + id).val(kgsBruto - kgsTara);
			}
		});
	},

	winBuscarCarta: function (elem) {		// Crea ventana para buscar Carta
		const left   = (screen.width/2)-500,    // (width/2) - Para centrar
	          top    = (screen.height/2)-350;   //  (height/2) - Idem
		const params = "location=no,menubar=no,scrollbars=yes,resizable=no,titlebar=no,toolbar=no,top=" + top + ",left=" + left + ",width=900,height=700";

		if ( ! this.winBuscar ) {

			this.objWinBuscar = window.open(_pathBuscarCarta, '_blank', params);		// Abrir la ventana para buscar CdP...
			this.winBuscar = true;		// Variable para saber que está abierta la ventana de búsqueda

			this.objWinBuscar.addEventListener("click", function () {
				//console.log("click en ventana buscar...");
				if (this._cerrar) {
					//console.log("Se seleccionó en ventana buscar, id: " + idCdP );
					CARTA.winBuscar = false;
					this.close();		// Cierra la ventana de búsqueda
					console.log("Id carta de porte: " + this._idCarta);
					FILLFORM.buscarCdP( this._idCarta );		// Buscar datos de CdP y rellena el formulario
				}
			});
			this.objWinBuscar.addEventListener("beforeunload", function (e) {

				CARTA.winBuscar = false;
			});
		}
		return null;
	},



	armarArchivo: function () {		// Arma el archivo .txt y lo envia al usuario
		//const csrf = $('form#formCsrf').serialize();
		//const armArchivo = new Archivo();
		//const dataCdP = csrf + '&datacp=' + armArchivo.armarArrayJson();  // Devuelve json para ser enviado

// ENVIA UN GET A '/cartadeporte/archivoafip?ids=x,x,...' QUE POR AHORA... 
// RETORNA UN JSON CON EL NOMBRE DEL ARCHIVO PARA QUE LO DESCARGUE

		console.log('Id CP a enviar: ' + this._idCarta);



/*
		$.post( _pathArchAfip, dataCdP )
			.done(function(data) 
			{
				const dataRet = JSON.parse(data);	 //$.parseJSON(data);
				console.log( "Retorno: " + dataRet.status + ' - Nro CP: ' + dataRet.nroCP );
				// Descargar archivo
				const filePath = '/cartasporte/cp-' + dataRet.nroCP + '.txt';
				const fileName = 'cp-' + dataRet.nroCP + '.txt';
				const req = new XMLHttpRequest();

				req.open("GET", filePath, true);
				req.responseType = "blob";
				req.onload = function(event) {
					const blob = req.response;
					// Aquí hacemos que el archivo se descargue
					let link = document.createElement('a');
				    link.href = window.URL.createObjectURL(blob);
				    link.download = fileName;
				    link.click();
				};
        		req.send();
			})
			.fail(function(data, textStatus, jqXHR) 
			{
				const dataRet = JSON.parse(data);
				console.log( "Error: " + textStatus + ' - Status: ' + dataRet.status );
				toastr["error"]("No se pudo armar archivo de Carta de Porte !", "Error !!");
			}
		);

*/

	},



};




//
// Codigo jQuery:
// 
$(document).ready( function () {

	// Boton flecha arriba
	let mybutton = document.getElementById("scrollUp");
	// When the user clicks on the button, scroll to the top of the document
	mybutton.addEventListener("click", function () {
	  document.body.scrollTop = 0; // For Safari
	  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
	});

	// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function() { CARTA.scrollFunction(mybutton) };

	// Filtro para inputs que acepten solo numeros enteros
	$('.numero').keyup(function(e) {
	    if (/\D/g.test(this.value)) {
	    	// Filter non-digits from input value.
	    	this.value = this.value.replace(/\D/g, '');
	  	}
	});

	// set typeahead
    SETTYPEAHEAD.inicializar(),

	//CARTA.typeAhead();		// Todo para typeahead
	CARTA.setsInputMask();		// InputMask para importes y horas

	// Change del select de Granos
	const sel = document.getElementById('selGrano');
	sel.addEventListener('change', function (e) {
		const val = this.value;
		//const tipo = $(sel).children("option:selected").attr('data-tipo');   // Tipo
		const tipo = sel.options[sel.selectedIndex].dataset.tipo;
		// Cambio el item en select tipo de grano
		CARTA.cambiarTipo(tipo);
	});

	CARTA.calcPesoNetoDesdeBruto();		// Calculo peso Neto desde Bruto
	CARTA.calcPesoNetoDesdeTara();		// Calculo peso Neto desde Tara

	// Click link Buscar
	$('a.buscar').click(function (e) {

		CARTA.winBuscarCarta($(this));
	});

	// Click en boton confirmar y guardar
	$('#btnConfirma, .guardar').click(function (event) {
		let dataCP   = '';

		if ( VALIDARFORM.validar() ) {		// Validaciones

			SUBMITFORM.togglBtns(false);
			toastr["warning"]( "Espere...", "Guardando !!", { progressBar: true, timeOut: 2500 } );
			dataCP = SUBMITFORM.armarData();
			//console.log('Data: ' + dataCP);
			SUBMITFORM.submitCarta( dataCP );		// envio form por ajax...
		}
	});

	// Click boton cancela
	$('#btnCancela').click(function (e) {
		// Recarga la página en blanco
		location.assign( _pathCartaPorte );
	});

	// Evento antes de cerrar la ventana
	$(window).on("beforeunload", function(e) {
		//console.log( "Antes de salir.." + _winBuscar );
		//e.preventDefault();
		//event.returnValue = '';
		if ( CARTA.winBuscar) {		// Si está abierta la ventana de buscar firma..
			CARTA.objWinBuscar.close();    // ... la cierra
		}
	});

});  // Fin código jQuery
