<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class InstallmentResource extends Resource
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
            'loan_id' => $this->loan_id,
            'amount_paid' => $this->amount_paid,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'loan' => $this->loan,
        ];
    }
}
