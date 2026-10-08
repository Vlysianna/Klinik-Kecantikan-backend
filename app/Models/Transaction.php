<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['invoice_number', 'total_amount'];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
