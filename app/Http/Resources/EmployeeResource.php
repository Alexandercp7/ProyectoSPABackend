<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class EmployeeResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'apellido' => $this->apellido,
            'email'    => $this->email,
            'telefono' => $this->telefono,
            'role'     => $this->role,
            'foto_url' => $this->foto_url,
            'activo'   => $this->activo,
            'user_id'  => $this->user_id,
        ];
    }
}
