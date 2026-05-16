<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContactProduct extends Model
{
    protected $fillable = ['contact_id','nombre','precio'];
    public function contact() { return $this->belongsTo(Contact::class); }
}
