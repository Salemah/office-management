<?php

namespace App\Http\Controllers\Admin\Account\Loan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Loan\Loan;
use App\Models\Account\Loan\Loan_Authority;
use App\Models\Account\Transaction;
use App\Models\Settings\DashboardSetting;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;
use DateTime;

class LoanReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $transaction = Transaction::where('transaction_purpose', 14)->orWhere('transaction_purpose', 15)->get();

                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('note', function ($transaction) {
                        return $transaction->description;
                    })
                    ->addColumn('action', function ($transaction) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanReturnDeleteConfirm(' . $transaction->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.loan.return.invoice', $transaction->id) . '" title="Return Voucher"><i class="bx bx-printer"></i></a>
                        </div>';
                    })
                    ->rawColumns(['note', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.loan.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $loanauthorities = Loan_Authority::get();
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.account.loan.loan.create', compact('loanauthorities', 'bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_author_id' => 'required',
            'loan_type' => 'required',
            'return_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }
        try {
            // investment
            $loan = Loan::findOrFail($request->loan_id);
            $transaction = new Transaction();
            $transaction->transaction_title = $request->return_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->loan_author_id = $request->loan_author_id;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
            }
            else if($request->transaction_way == 1){
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }

            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 14;
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_purpose = 15;
                $transaction->transaction_type = 1;
            }

            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = strip_tags($request->note);
            $transaction->loan_id = $loan->id;
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();



            return redirect()->route('admin.loan-return.show', $request->loan_id)->with('message', 'Loan successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::where('loan_id', $id)->first();
            $loan = Loan::with('author')
                ->where('id', $id)
                ->first();
            $loan_amount = $loan->loan_amount;
            if ($transaction->transaction_purpose == 12) {
                $return_amount = Transaction::where('loan_id', $id)
                    ->where('transaction_purpose', 14)
                    ->sum('amount');

            } else {
                $return_amount = Transaction::where('loan_id', $id)
                    ->where('transaction_purpose', 15)
                    ->sum('amount');
            }
            //$interest = ($loan->interest_rate/ 100) * $loan_amount;
          //  $due = $loan_amount- $return_amount;
            $authority = Loan_Authority::where('id', $loan->loan_author_id)->first();
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();


            $today=date("Y/m/d");
            $loanDate=$loan->loan_date;

            $ts1 = strtotime($loanDate);
            $ts2 = strtotime($today);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1); // Total Months from Loan Date;


            $yearly_interst=($loan->loan_amount * $loan->interest_rate) / 100;
            $monthly_interest=$yearly_interst/12;

            $interest_till_today=$monthly_interest*$diff;

            $amount_withinterest=$loan->loan_amount + $interest_till_today;

            $due=$amount_withinterest - $return_amount;

            $total_year = $diff /12;


            $date1 = new DateTime($loanDate);
            $date2 = new DateTime($today);
            $interval = $date1->diff($date2); // get year

             if($loan->status== 1 && $interval->y > 0 )
            {
                $p = $loan->loan_amount;
                $t = $interval->y;
                $r = $loan->interest_rate;
                $CI = NULL;
                $CI = $p * (pow((1 + $r / 100), $t));
                $due=$CI- $return_amount;
                $interest_till_today = $CI -$loan->loan_amount;
            }

            return view('admin.account.loan.loan.loan-return.show',compact('loan','authority','bankAccounts','loan_amount', 'return_amount', 'due','cash_account','interest_till_today'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function LoanReturnList(Request $request,$id)
    {
        try {
            if ($request->ajax()) {
                // $loan = Loan::findOrFail($id)->first();
                $transaction = Transaction::where('loan_id','=', $id)->where('transaction_purpose', 15)->orWhere('transaction_purpose', 14)->get();

                // $transaction =  $transactions::where('loan_id','=', $id)->get();
                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('note', function ($transaction) {
                        return $transaction->description;
                    })
                    ->addColumn('loan-amount', function ($transaction) {
                        $loanAmount = Transaction::where('loan_id',$transaction->loan_id)->where('transaction_purpose', 13)->orWhere('transaction_purpose', 12)->first();
                        //$loanAmount = $transaction->where('transaction_purpose',12)->orWhere('transaction_purpose',13)->first();
                        return $loanAmount->amount;

                    })
                    ->addColumn('action', function ($transaction) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                     <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanReturnDeleteConfirm(' . $transaction->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                     <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.loan.return.invoice', $transaction->id) . '" title="Return Voucher"><i class="bx bx-printer"></i></a>
                                     </div>';
                    })
                    ->rawColumns(['loan-amount','note', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.loan.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try {

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $transaction = Transaction::findOrFail($id);
                $transaction->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Loan Return Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public static function convert_number_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        // $separator   = ', ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
    public function LoanReturnInvoice(Request $request,$id)
    {
        try {

            // $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();
            // $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
            // if($loan->loan_type == 1){
                $loanReturn = Transaction::where('id', $id)->first();
            // }
            // else if($loan->loan_type == 2){
            //     $loanReturn = Transaction::where('id', $id)->where('transaction_purpose', 14)->first();
            // }
            $loan = Loan::with('author', 'createdByUser','negotiator')->findOrFail($loanReturn->loan_id);
            $transaction = Transaction::where('id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }
            $dashboard_settings = DashboardSetting::first();
            $totalReturnAmount = $loanReturn->amount;
            $totalString =$this->convert_number_to_words($totalReturnAmount) ;

            return view('admin.account.loan.status.return-invoice', compact( 'totalReturnAmount','loan','loanReturn','transaction', 'bank','dashboard_settings','totalString'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
}
