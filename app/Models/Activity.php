<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['titulo','descripcion','asignado_a_id','fecha_limite','prioridad','etiqueta','estado','created_by'];

    public function asignadoA() { return $this->belongsTo(User::class, 'asignado_a_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function comments() { return $this->hasMany(ActivityComment::class); }
}
