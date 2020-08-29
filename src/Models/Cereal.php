<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 
 * Cereales
 *
 */
class Cereal extends Model
{
	// Nombre de la tabla
	protected $table = 'ra_Cereales';

	// Lista de campos modificables:
	protected $fillable = [ 'Codigo', 
	                        'Descripcion', 
	                        'IdTipo',
	                        'CampVigente' ];

	/**
	 * Relación One To One con TipoDispenser
	 * 
	 * @return object Objeto de la relacion
	 */
	public function TipoGrano()
	{

		return $this->hasOne('App\Models\TipoGrano', 'Codigo', 'IdTipo');
	}


}