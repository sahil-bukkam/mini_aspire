<?php

namespace App\Http\Controllers;

use App\Loan;
use Illuminate\Http\Request;
use App\Http\Resources\LoanResource;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateInterval;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of loans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check if user is admin
        if (auth()->user()->type != 1) {
            $loans = Loan::where(["user_id" => auth()->user()->id])->get();
        }else{
            $loans = Loan::get();
        }
        return LoanResource::collection($loans);
    }

    /**
     * Store a newly created loan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'loan_term' => 'required|integer'
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        // check if user is customer
        if (auth()->user()->type != 2) {
            return response()->json(['error' => 'Only Customer can request a loan.'], 403);
        }

        //Note loan term is in months logic
        /* $now = new DateTime();
        $end_date = clone $now;
        $interval = new DateInterval('P'.$request->loan_term.'M');
        $lastDay = $end_date->add($interval);
        $weeks = round(floor($now->diff($lastDay)->days/7));
        $weeklyAmount = $request->amount/$weeks; */

        //Note loan term is in weeks logic
        $weeklyAmount = round($request->amount/$request->loan_term,2);

        $loan = Loan::create([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'loan_term' => $request->loan_term,
            'weekly_amount' => $weeklyAmount,
            'previous_weekly_amount' => $weeklyAmount,
            'status' => 0,
            'amount_remaining' => $request->amount
        ]);

        return new LoanResource($loan);
    }

    /**
     * Display the specified loan.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        // check if user is admin
        if (auth()->user()->type != 1 and auth()->user()->id != $loan->user_id) {
            return response()->json(['error' => 'Customer cannot see details of loan belonging to others'], 403);
        }
        return new LoanResource($loan);
    }

    /**
     * Update the specified loan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:1,2'
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        // check if user is admin
        if (auth()->user()->type != 1) {
            return response()->json(['error' => 'Only Admin can change the status of all loans.'], 403);
        }

        if($loan->amount_remaining < $loan->amount) {
            return response()->json(['error' => 'You cannot approve/reject the loan application as installments are already started.'], 400);
        }

        $loan->update($request->only(['status']));

        return new LoanResource($loan);
    }

}
