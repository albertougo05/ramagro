//
// Funcion para buscar Firma y llenar formulario
// 

export function buscarFirma ( id ) {

	//console.log('Class BF - Id: ' + id);
	$.ajax(
    {
        url : FIRMAS._pathGetFirma,
        type: "GET",
        data: { id: id },
        success: function(data, textStatus, jqXHR) 
        {
			let dataObj = $.parseJSON(data);
			//console.log('Id: ' + dataObj.id + 'Nombre: ' + dataObj.Nombre);
			_datosAlFormulario(dataObj);		//	Llenar lo campos del from  -- NO SE PUEDE LLAMAR A FUNCIONES INTERNAS ?
			_tildarCheckBoxes(dataObj);
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            console.log('Status de error: ' + textStatus);
            toastr["error"]("Error al buscar datos !!", "Firma");
        }
    });

	return null;
}

//
// Funciones privadas
//

// Llenar los campos del formulario...    --  SERIA UNA FUNCION INTERNA Y NO LA LLAMA (??)
let _datosAlFormulario = function (data) { 
	$('input#id').val(data.id);
	$('input#Nombre').val(data.Nombre);
	$('input#Fantasia').val(data.Fantasia);
	$('input#Direccion').val(data.Direccion);
	$('input#Localidad').val(data.Localidad);
	$('input#CodLoc').val(data.CodLoc);
	$('input#Provincia').val(data.Provincia);
	$('input#CodProv').val(data.CodProv);
	$('input#CodPostal').val(data.CodPostal);
	$('input#Telefono').val(data.Telefono);
	$('input#Celular').val(data.Celular);
	$('input#CUIT').val(data.CUIT);
	$('input#CondicionFiscal').val(data.CondicionFiscal);
	$('input#Email').val(data.Email);
	$('input#Estado').val(data.Estado);

	return null;
}

// Tildar checkBoxes de opciones
let _tildarCheckBoxes = function (data) {
	if (data.Productor === 1) {
		document.getElementById("Productor").checked = true;
	}
	if (data.Acopio === 1) {
		document.getElementById("Acopio").checked = true;
	}
	if (data.Entregador === 1) {
		document.getElementById("Entregador").checked = true;
	}
	if (data.Fletero === 1) {
		document.getElementById("Fletero").checked = true;
		document.getElementById("patentes").style.display = 'initial';
	}
	if (data.Chofer === 1) {
		document.getElementById("Chofer").checked = true;
	}

	return null;
}


