import { buscarFirma } from './buscarFirma.js';

//
// firmas.js
//

FIRMAS.winBuscar = false;    // Variable para saber que está abierta la ventana de búsqueda
FIRMAS.objWinBuscar = {};    // Ventana de búsqueda

// Constructs the suggestion engine
FIRMAS.bloodHount = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: FIRMAS._pathLocalidades + '?search=%QUERY',
            wildcard: '%QUERY',
        }
});

FIRMAS.setTypeahead = function () {    // Initializing the typeahead with remote dataset without highlighting
    $('.typeahead').typeahead(
    	{ 
    		hint: true,
    		minLength: 3,
        	highlight: true },
        { 
        	name: 'localidades',
          	source: FIRMAS.bloodHount,
          	display: function(item) {            // Lo que muestra en el input !!
				return item.localidad + ' (' + item.codigo + ')';
          	},
			limit: 20, /* Specify max number of suggestions to be displayed */
		    templates: {         // Lo que muestra en menú de sugerencias !!
		        suggestion: function(item) {
		            return '<div>' + item.localidad + ' / ' + item.provincia +'</div>';
		        }
		    }
    	});
    this.typeaheadBind();
};

FIRMAS.typeaheadBind = function () {
	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {			// Evento select del typeahead...
		//console.log('Select localidad: ' + suggestion.codigo + ' - ' + suggestion.localidad);
		$('#CodLoc').val( suggestion.codigo );
		$('#Provincia').val(suggestion.provincia + ' (' + suggestion.cod_prov + ')');
		$('#CodProv').val( suggestion.cod_prov );
		$('#CodPostal').focus();
	});

	$('.typeahead').bind('typeahead:autocomplete', function(ev, suggestion) {		// Evento autocomplete del typeahead...
		//console.log('Autocomp... : ' + suggestion.id + ' - ' + suggestion.localidad);
		$('#CodLoc').val( suggestion.codigo );
		$('#Provincia').val(suggestion.provincia + ' (' + suggestion.cod_prov + ')');
		$('#CodProv').val( suggestion.cod_prov );
		$('#CodPostal').focus();
	});
};

FIRMAS.isEmpty = function(str) {    // Verifica string vacios
    return (str.length === 0 || !str.trim());
};

FIRMAS.validateEmail = function (email) {   // Valida string con email
	let re = /\S+@\S+\.\S+/;
	return re.test(email);
};

FIRMAS.winBuscarFirma = function (elem) {		// Crea ventana para buscar Firma  top=500,left=500,
	const left   = (screen.width/2)-450,    // (width/2)
          top    = (screen.height/2)-350;   //  (height/2);
	const params = "toolbar=no,localtion=no,menubar=no,scrollbars=yes,resizable=no,top=" + top + ",left=" + left + ",width=900,height=700";

	if ( !this.winBuscar ) {		// Abrir la ventana para buscar firma...

		this.objWinBuscar = window.open(this._pathBuscarFirma, '_blank', params);
		this.winBuscar = true;		// Variable para saber que está abierta la ventana de búsqueda

		this.objWinBuscar.addEventListener("click", function(){		// Agego evento click a la ventana de Buscar
			let cerrar = this._cerrar;
			let dato = '';
			//console.log("click en ventana buscar...");
			if (cerrar) {
				dato = this._idFirma;
				// console.log("Se cierra la ventana buscar... (" + dato + ")");
				this.close();
				FIRMAS.winBuscar = false;
				document.getElementById("formFirmas").reset();   // Reset del form
				buscarFirma( dato ); 				// En archivo importado buscarFirma.js gestiona la busqueda de la firma (dato = id firma)
			}
		});
		this.objWinBuscar.addEventListener("beforeunload", function (e) {
			FIRMAS.winBuscar = false;
		});
	}
	return null;
};

FIRMAS.validaFormulario = function () {
	const email  = $('#Email').val();
	let valido = true;
	//console.log('Email: ' + email);
	if ( this.isEmpty( $('#Nombre').val() ) ) {
		valido = false;
		toastr["error"]("Debe ingresar un Nombre !!", "Error en Firma");
		$('#Nombre').focus();

	} else if ( this.isEmpty( $('#Direccion').val() ) ) {
		valido = false;
		toastr["error"]("Debe ingresar una Dirección !!", "Error en Firma");
		$('#Direccion').focus();

	} else if ( this.isEmpty($('#Localidad').val()) ) {
		valido = false;
		toastr["error"]("Debe ingresar una Localidad !!", "Error en Firma");
		$('#Localidad').focus();

	} else if ( this.isEmpty( $('#CodLoc').val() ) ) {
		valido = false;
		toastr["error"]("Error en código Localidad !!", "Error en Firma");
		$('#Localidad').val('').focus();

	} else if ( this.isEmpty( $('#Provincia').val() ) ) {
		valido = false;
		toastr["error"]("Debe ingresar una Provincia !!", "Error en Firma");
		$('#Provincia').focus();

	} else if ( this.isEmpty( $('#CodProv').val() ) ) {
		valido = false;
		toastr["error"]("Error en código Provincia !!", "Error en Firma");
		$('#Provincia').val('').focus();

	} else if ( !this.isEmpty(email) ) {    // Si no está vacio en email
		if ( !this.validateEmail(email) ) {
			valido = false;
			toastr["error"]("Ingrese dirección mail correcta !!", "Error en Firma");
			$('#Email').focus();
		}
	}
	//console.log('CodLoc es: ' + valido);

	return valido;
};

FIRMAS.enviarForm = function () {
	let retorno = true;
	const datafirma  = $('form#formFirmas').serialize();
	event.preventDefault(); 
	event.stopPropagation();

	$.ajax(
    {
        url : FIRMAS._pathGuardarFirma,
        type: "POST",
        data: datafirma,
        //dataType: 'json',
        success: function(data, textStatus, jqXHR) 
        {
			let dataObj = $.parseJSON(data);
			//console.log('Status: ' + textStatus + 'Data enviada: ' + data);
			toastr["success"]("Firma guardada con éxito !!", "Firma");
			retorno = true;
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            // if fails
            console.log('Status de error: ' + textStatus);
            toastr["error"]("Error al guardar datos !!", "Firma");
            retorno = false;
        }
    });
    return retorno;
};





// Set toastr options
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-full-width",
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



// Codigo jQuery:
// 
$(document).ready( function () {

	FIRMAS.setTypeahead();

	$('#CUIT').inputmask("99-99999999-9"); 

	// Click link Buscar
	$('#a_buscar').click(function (e) {

		FIRMAS.winBuscarFirma($(this));
	});

	// Evento Click boton SUBMIT y link Guardar
	$('#btnConfirma, a#guardar').click(function (event) {
		let guardaOk = true,
		    timeWait = 4000;

		// Validaciones
		if ( FIRMAS.validaFormulario() ) {

			$('#spinnerGuardar').show();		// Muestra el spinner...
			guardaOk = FIRMAS.enviarForm();		// envio form por ajax...
			$('#spinnerGuardar').hide();		// Oculta el spinner...

			// Salir y recargar la página. Espera 4 segundos
			// si hay error espera mas tiempo
			if ( !guardaOk ) { timeWait = 10000; }

	    	setTimeout( function () {
	    		location.assign( FIRMAS._pathFirmas );
	    	}, timeWait );

		} else {
			// Retorna al from con mesaje de error (en validadForm)
			return false;			
		}
	});

	// Evento antes de cerrar la ventana
	$(window).on("beforeunload", function()
	{
		if (FIRMAS.winBuscar) {		// Si está abierta la ventana de buscar firma..
			FIRMAS.objWinBuscar.close();    // ... la cierra
		}
		
	});

	// Si tilda Fletero habilita inputs Chasis y Acoplado
	$('#Fletero').click(function (e) {

		if($(this).is(":checked")){
			$('.patentes').show();
		} else $('.patentes').hide();
	});



});  // Fin código jQuery
