<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientCustody extends Model
{
    protected $table = 'client_custody';
    protected $fillable = ['work_order_id','client_id','item','foto_url','responsable','estado','fecha_ingreso'];

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function client() { return $this->belongsTo(Client::class); }
}
