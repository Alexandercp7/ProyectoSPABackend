<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['nombre','rfc','empresa','telefono','correo','dias_pago','limite_credito','politica_descuentos'];
    public function accountsPayable() { return $this->hasMany(AccountsPayable::class); }
    public function products() { return $this->hasMany(ContactProduct::class); }
    public function tags() { return $this->hasMany(ContactTag::class); }
}
