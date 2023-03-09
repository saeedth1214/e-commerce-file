<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return fractal()
            ->item($transaction)
            ->withResourceName('transactions')
            ->transformWith(new TransactionTransformer())
            ->respond();
    }

    /**
     * Display the specified resource by uuid.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackingByUuid($uuid)
    {
        $transaction = Transaction::query()->findByUuid($uuid)->first();

        return  $transaction ? fractal()
            ->item($transaction)
            ->withResourceName('transactions')
            ->transformWith(new TransactionTransformer())
            ->respond() : [];
    }
}
