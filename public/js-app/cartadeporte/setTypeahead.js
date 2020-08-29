//
// Sets para typeahead
//

//
// 	// set typeahead
//     typeAhead: funTypeahead(),
//

var SETTYPEAHEAD = {

	bloodHount: new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: _pathLocalidades + '?search=%QUERY',
            wildcard: '%QUERY',
    	}
	}),

	inicializar: function () {
	    // Initializing the typeahead with remote dataset without highlighting
	    $('.typeahead').typeahead(
	    	{ 
	    		hint: true,
	    		minLength: 3,
	        	highlight: true },
	        { 
	        	name: 'localidades',
	          	source: this.bloodHount,
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

		// Evento select del typeahead...
		$('.typeahead').bind('typeahead:select', function(ev, suggestion) {

			SETTYPEAHEAD.setCodToInputs($(this).prop('name'), suggestion);
		});

		// Evento autocomplete del typeahead...
		$('.typeahead').bind('typeahead:autocomplete', function(ev, suggestion) {

			SETTYPEAHEAD.setCodToInputs($(this).prop('name'), suggestion);
		});
	},

	setCodToInputs: function (name, suggest) {
		let idInput = '';
		let nextFoc = '';

		if (name === 'localidadProc') {
			idInput = '#provinciaProc';
			nextFoc = '#direccionDest';
		} else if (name === 'localidadDest') {
			idInput = '#provinciaDest';
			nextFoc = '#pagadorFle';
		}

		$(idInput).val(suggest.provincia + ' (' + suggest.cod_prov + ')')
							.prop('disabled', true);
		$(nextFoc).focus();
	}

};
