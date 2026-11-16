<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio'
    ];

    //Relación con la tabla Stock_Almacen
    public function cantidad()
    {
        return $this->hasMany(Stock_Almacen::class,'producto_id','id');
    }
    //Relación con la tabla Categorias
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
