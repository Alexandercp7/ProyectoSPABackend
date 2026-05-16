<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KpiActivity extends Model
{
    protected $table = 'kpi_activities';
    protected $fillable = ['titulo','descripcion','role_asignado','status','prioridad','empleado_id','fecha_vencimiento'];

    public function employee() { return $this->belongsTo(Employee::class, 'empleado_id'); }
}
