<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LoanResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'loan_term' => $this->loan_term,
            'status' => $this->status,
            'weekly_amount' => $this->weekly_amount,
            'amount_remaining' => $this->amount_remaining,
            'previous_weekly_amount' => $this->previous_weekly_amount,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'user' => $this->user,
            'installments' => $this->installments,
        ];
    }
}
