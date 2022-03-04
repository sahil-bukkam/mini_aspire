<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /**
     * Attributes that can be mass assigned
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'amount', 'loan_term', 'weekly_amount', 'amount_remaining', 'previous_weekly_amount', 'status', 'created_at', 'updated_at'];

    /**
     * A Loan is belongs to a user
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Loan a can have many installments
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
