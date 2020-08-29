<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 
 * Localidades segun Afip
 * 
 */
class Localidades extends Model
{
	// Nombre de la tabla
	protected $table = 'ra_Localidades';

	// Cambio la primary key de Eloquent
	protected $primaryKey = 'Codigo';

	// Lista de campos modificables (NO SON MODIFICABLES)
#	protected $fillable = [ 'Codigo', 
#							'Localidad',
#							'CodProv',
#							'Provincia' ];

	// Cancelo el registro en campos 'created_at' y 'updated_at' por defecto de Eloquent
	public $timestamps = false;
}