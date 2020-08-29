<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Carta de Porte
 *
 * @var $guarded array
 */
class CartaDePorte extends Model
{
	// Nombre de la tabla
	protected $table = 'ra_CartaDePorte';

	// Lista de campos NO modificables:
	protected $guarded = [];

	/**
	 * Relación One To One con ClienteDomicilio
	 * 
	 * @return object Objeto de la relacion
	 */
	public function Nombre()
	{
		return $this->hasOne('App\Models\Firma', 'id', 'idTitular');
	}
}
