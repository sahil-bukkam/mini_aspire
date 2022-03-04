<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    /**
     * Attributes that can be mass assigned
     *
     * @var array
     */
    protected $fillable = ['id', 'loan_id', 'amount_paid', 'created_at', 'updated_at'];

    /**
     * A installment belongs to a loan
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
