<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Installment;
use Illuminate\Http\Request;
use App\Http\Resources\InstallmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    /**
     * Store a newly created installments in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Loan $loan
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Loan $loan)
    {
        $validator = Validator::make($request->all(), [
            'amount_paid' => 'required|numeric'
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        if (auth()->user()->id != $loan->user_id) {
            return response()->json(['error' => 'Wrong user'], 403);
        }

        if($loan->amount_remaining <= 0) {
            return response()->json(['error' => 'You have already paid your all installments.'], 400);
        }

        if($loan->weekly_amount != $request->amount_paid) {
            return response()->json(['error' => 'Amount given does not match with the weekly installment amount'], 400);
        }

        if($loan->status != 1) {
            return response()->json(['error' => 'Your loan is not approved yet.'], 400);
        }

        $weeklyAmount = $loan->weekly_amount;

        $remainingAmount = $loan->amount_remaining - $request->amount_paid;

        if($remainingAmount!=0 && $remainingAmount < (2*$loan->weekly_amount)){
            $weeklyAmount = $remainingAmount;
        }

        DB::beginTransaction();

        $installment = Installment::create(
            [
                'loan_id' => $loan->id,
                'amount_paid' => $request->amount_paid
            ]
        );

        if(!$installment){
            DB::rollback();
            return response()->json(['error' => 'Loan installment failed'], 500);
        }

        if(!$loan->update(["amount_remaining" => $remainingAmount, "weekly_amount" => $weeklyAmount])){
            DB::rollback();
            return response()->json(['error' => 'Remaining amount update failed'], 500);
        }

        DB::commit();

        return new InstallmentResource($installment);
    }
}
