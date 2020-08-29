//
// Acciones para validar formulario para posterior submit
// 

var VALIDARFORM = {

	// Validar formulario
	validar: function () {
		let valido = true,
		    mensaje = '';

		if ( this.isEmpty( $('#numeroCp').val() ) ) {
			valido = false;
			mensaje = "Debe ingresar número de Carta de Porte !!";
			$('#numeroCp').focus();

		} else if ( this.isEmpty( $('#nroCtg').val() ) ) {
			valido = false;
			mensaje = "Debe ingresar número C.T.G. !!";
			$('#nroCtg').focus();

		} else if ( this.isEmpty( $('#nroCee').val() ) ) {
			valido = false;
			mensaje = "Debe ingresar número C.E.E. !!";
			$('#nroCee').focus();

		} else if ( this.isEmpty( $('#fechaCarga').val() ) ) {
			valido = false;
			mensaje = "Debe ingresar fecha carga!!";
			$('#fechaCarga').focus();

		} else if ( this.isEmpty( $('#fechaVenc').val() ) ) {
			valido = false;
			mensaje = "Debe ingresar fecha vencimiento !!";
			$('#fechaVenc').focus();

		} else if ( $('#selTitular').children("option:selected").val() === 0 ) {
			valido = false;
			mensaje = "Debe seleccionar titular !!";
			$('#selTitular').focus();

		} else if ( $('#selInterm').children("option:selected").val() === 0 ) {
			valido = false;
			mensaje = "Debe seleccionar intermediario !!";
			$('#selInterm').focus();
		} 

		if (!valido) {		// Si no es valido mensaje de error
			toastr["error"]( mensaje, "ERROR !!" );
		}

		return valido;
	},

	isEmpty: function(str) {    // Verifica tipo string vacios
    	return (str.length === 0 || !str.trim());
	},

};
