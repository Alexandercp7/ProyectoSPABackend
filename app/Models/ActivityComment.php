<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityComment extends Model
{
    protected $fillable = ['activity_id','usuario_id','texto'];
    public function activity() { return $this->belongsTo(Activity::class); }
    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
}
