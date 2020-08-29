<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Firmas
 */
class Firma extends Model
{
	// Nombre de la tabla
	protected $table = 'ra_Firmas';

	// Lista de campos modificables:
	protected $fillable = [
		'Nombre', 
		'Fantasia',
		'Direccion', 
		'Localidad',
		'CodLoc',
		'Provincia',
		'CodProv',
		'CodPostal',
		'Telefono',
		'Celular',
		'CUIT',
		'CondicionFiscal',
		'Email',
		'Estado',
		'Productor',
		'Acopio',
		'Entregador',
		'Fletero',
		'Chofer'
	];

}