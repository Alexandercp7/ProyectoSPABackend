<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $fillable = ['nombre','descripcion','role_responsable','meta','periodo','fecha_inicio','fecha_fin'];

    public function getProgresoAttribute(): int
    {
        $total = KpiActivity::where('role_asignado', $this->role_responsable)->count();
        if ($total === 0) return 0;
        $completadas = KpiActivity::where('role_asignado', $this->role_responsable)
            ->where('status', 'Completada')->count();
        return (int) round(($completadas / $total) * 100);
    }
}
