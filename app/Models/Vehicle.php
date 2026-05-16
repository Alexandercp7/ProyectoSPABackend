<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['client_id','marca','modelo','anio','placas','vin','kilometraje_actual'];

    public function client() { return $this->belongsTo(Client::class); }
    public function workOrders() { return $this->hasMany(WorkOrder::class); }
}
