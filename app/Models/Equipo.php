<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    protected $fillable = [
        'nombre', 'tipo', 'marca', 'serie', 'estado', 'falla', 'responsable', 'user_id', 'telefono', 'cliente_id'
    ];

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }

    public function comentarios()
    {
        return $this->hasMany(\App\Models\Comentario::class);
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\User::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
