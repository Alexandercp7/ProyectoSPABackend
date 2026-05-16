<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClientPaymentState extends Model
{
    protected $fillable = ['client_id','work_order_id','estado'];
    public function client() { return $this->belongsTo(Client::class); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
