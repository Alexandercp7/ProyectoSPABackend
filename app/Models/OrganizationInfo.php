<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrganizationInfo extends Model
{
    protected $table = 'organization_info';
    protected $fillable = ['mision','vision','valores'];
    protected $casts = ['valores' => 'array'];
}
