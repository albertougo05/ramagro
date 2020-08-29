<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 
 * Tipos de grano
 * 
 */
class TipoGrano extends Model
{
	// Nombre de la tabla
	protected $table = 'ra_TiposGrano';

	// Lista de campos modificables:
	protected $fillable = [ 'Codigo', 
							'Descripcion' ];
}