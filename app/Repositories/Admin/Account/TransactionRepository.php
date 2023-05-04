<?php

namespace App\Repositories\Admin\Account;


class TransactionRepository
{
    public static function type($transactions){

        if ($transactions->transaction_purpose == 1) {
            return '<span class="text-danger text-bold"><b>Withdraw - </b></br> ' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 2) {
            return '<span class="text-success text-bold"><b>Deposit -</b> </br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 3) {
            return '<span class="text-success text-bold"><b>Revenue - </b></br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 4) {
            return '<span class="text-success text-bold"><b>Given Payment - </b></br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 5) {
            return '<span class="text-danger text-bold"><b>Expense -</b> </br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 6) {
            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In) </br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 7) {
            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></br>' . $transactions->description . '</span>';
        } else if ($transactions->transaction_purpose == 8) {
            return '<span class="text-success text-bold"><b>Cash In -</b></br> <span class="text-danger ">' . $transactions->description . '</span> </span>';
        } else if ($transactions->transaction_purpose == 9) {
            return '<span class="text-success text-bold"><b>Investment - </b></br>'.$transactions->description.'</span>';
        } else if ($transactions->transaction_purpose == 10) {
            return '<span class="text-success text-bold"><b>Investment return - </b></br>'.$transactions->description.'</span>';
        } else if ($transactions->transaction_purpose == 11) {
            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></br>'.$transactions->description.'</span>';
        } else if ($transactions->transaction_purpose == 12) {
            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
        } else if ($transactions->transaction_purpose == 13) {
            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
        } else if ($transactions->transaction_purpose == 14) {
            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
        } else if ($transactions->transaction_purpose == 15) {
            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
        }
         else if ($transactions->transaction_purpose == 16) {
            return '<span class="text-success text-bold"></b>Project Budget Reciept - </b></br>'.$transactions->description.'</span>';
        }
        else {
            return '...';
        }
    }
}
