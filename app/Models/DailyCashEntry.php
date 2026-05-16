<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyCashEntry extends Model
{
    protected $fillable = ['fecha','concepto','tipo','monto','metodo_pago','referencia','accounts_receivable_id','usuario_id'];
    public function accountsReceivable() { return $this->belongsTo(AccountsReceivable::class); }
    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
}
