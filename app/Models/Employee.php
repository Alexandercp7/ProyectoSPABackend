<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['nombre','apellido','email','telefono','role','foto_url','user_id','activo'];
    protected $casts = ['activo' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
