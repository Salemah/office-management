<?php

namespace App\Repositories\Admin\Account;

use App\Models\Account\Transaction;

class AccountsRepository
{
    public static function debitBalance($account_id)
    {
        return Transaction::where('account_id', $account_id)
            ->where('status',1)
            ->where('transaction_type', 1)
            ->get()
            ->sum('amount');
    }


    public static function creditBalance($account_id)
    {
        return Transaction::where('account_id', $account_id)
            ->where('status',1)
            ->where('transaction_type', 2)
            ->get()
            ->sum('amount');
    }

    public static function postBalance($account_id)
    {
        $credit = self::creditBalance($account_id);
        $debit  = self::debitBalance($account_id);
        return ($credit - $debit);
    }

    public static function transactionUpdateBalanceCheck($account_id,$transaction_id,$amount)
    {
        $transaction = Transaction::where('id', $transaction_id)->first();
        $credit      = self::creditBalance($account_id);
        $debit       = self::debitBalance($account_id);

        // For credit transaction
        if($transaction->transaction_type == 2){
            if($credit > $transaction->amount){
                $oldCredit = $credit - $transaction->amount;
            }else{
                $oldCredit =  $transaction->amount - $credit;
            }
            $credit = $oldCredit + $amount;
        }elseif ($transaction->transaction_type == 1){
            if($debit > $transaction->amount){
                $oldDebit = $debit - $transaction->amount;
            }else{
                $oldDebit =  $transaction->amount - $debit;
            }
            $debit = $oldDebit + $amount;
        }
        $updateBalance = $credit - $debit;
        return $updateBalance;
    }

    // credit Balance Check before Delete
    public static function balanceCheckBeforeDelete($account_id,$transaction_id){
        //  Transaction Data Get
        $transaction = Transaction::where('id', $transaction_id)->first();

        // Balance Check
        $credit = self::creditBalance($account_id);
        $debit  = self::debitBalance($account_id);
        if($transaction->transaction_type == 2){
            if($credit > $transaction->amount){
                $credit = $credit - $transaction->amount;
            }else{
                $credit =  $transaction->amount - $credit;
            }
        }
        elseif ($transaction->transaction_type == 1){
            if($debit > $transaction->amount){
                $debit = $debit - $transaction->amount;
            }else{
                $debit =  $transaction->amount - $debit;
            }
        }
        $updateBalance = $credit - $debit;
        return $updateBalance;
    }


    public static function balanceCheck($account_id, $update_amount)
    {

        $oldAmount = Transaction::where('account_id', $account_id)
            ->where('status',1)
            ->where('transaction_type', 1)
            ->first();

        $debit    = self::debitBalance($account_id);
        $oldDebit = $debit - $oldAmount->amount;

        $updateDebit   = $oldDebit + $update_amount;
        $credit        = self::creditBalance($account_id);
        $updateBalance = $credit - $updateDebit;

        return $updateBalance;
    }

    public static function previousDebitBalance($account_id, $date)
    {
        return Transaction::where('account_id', $account_id)
            ->where('status',1)
            ->where('transaction_type', 1)
            ->where('transaction_date', '<', $date)
            ->get()
            ->sum('amount');
    }

    public static function previousCreditBalance($account_id, $date)
    {
        return Transaction::where('account_id', $account_id)
                            ->where('status',1)
                            ->where('transaction_type', 2)
                             ->where('transaction_date', '<', $date)
                            ->get()
                            ->sum('amount');
    }

    public static function previousPostBalance($account_id, $date)
    {
        $credit = self::previousCreditBalance($account_id, $date);
        $debit = self::previousDebitBalance($account_id, $date);

        return ($credit - $debit);
    }
    public static function previousBalance( $date,$warehouse)
    {
        if($warehouse){
            $debitBalance = Transaction::where('status',1)
                            ->where('transaction_type', 1)
                            ->where('warehouse_id',$warehouse)
                            ->where('transaction_date', '<', $date)
                            ->get()
                            ->sum('amount');
            $creditBlance = Transaction::where('status',1)
                            ->where('transaction_type', 2)
                            ->where('warehouse_id',$warehouse)
                            ->where('transaction_date', '<', $date)
                            ->get()
                            ->sum('amount');
        }
        else{
            $debitBalance = Transaction::where('status',1)
                            ->where('transaction_type', 1)
                            ->where('transaction_date', '<', $date)
                            ->get()
                            ->sum('amount');
            $creditBlance = Transaction::where('status',1)
                            ->where('transaction_type', 2)
                            ->where('transaction_date', '<', $date)
                            ->get()
                            ->sum('amount');
        }

        $credit = $creditBlance;
        $debit  = $debitBalance;

        return ($credit - $debit);
    }
    public static function revenueBalance($account_id)
    {
        return Transaction::where('status',1)
            ->where('transaction_purpose', 3)
            ->get()
            ->sum('amount');
    }
    public static function expenseBalance($purpose)
    {
        return Transaction::where('status',1)
            ->where('transaction_purpose', 5)
            ->get()
            ->sum('amount');
    }
}

