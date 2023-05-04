<?php

namespace App\Http\Controllers\Admin\Account\Investment;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Investment\Investment;
use App\Models\Account\Investment\Investor;
use App\Models\Account\Transaction;
use App\Models\Settings\DashboardSetting;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentReturnController extends Controller
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
                $investment_return = Transaction::with('investor')
                ->where('transaction_purpose', 10)
                ->orWhere('transaction_purpose', 11);

                return DataTables::of($investment_return)
                    ->addIndexColumn()
                    ->addColumn('transaction_type', function ($investment_return) {
                        if ($investment_return->transaction_type == 1) {
                            return "Debit";
                        } else {
                            return "Credit";
                        }
                    })
                    ->addColumn('name', function ($investment_return) {
                            return $investment_return->investor->name;

                    })
                    ->addColumn('return_type', function ($investment_return) {
                        if ($investment_return->transaction_purpose == 11) {
                            return "Profit-Return";
                        } else if ($investment_return->transaction_purpose == 10) {
                            return "Investment-Return";
                        }
                    })
                    ->addColumn('action', function ($investment_return) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentReturnDeleteConfirm(' . $investment_return->id . ')"title="Delete"><i class="bx bxs-trash"></i></a>
                                    <a class="btn btn-sm btn-primary text-white " style="cursor:pointer"href="' . route('admin.investment.return.invoice', $investment_return->id) . '" title="Return Voucher"><i class="bx bx-printer"></i></a>
                                    </div>';
                    })
                    ->rawColumns(['name','note', 'action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.investment-return.show');
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
            return view('admin.account.investment.investor.create');
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
            'return_title' => 'required',
            'transaction_purpose' => 'required',
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


            $account_id = 0;
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->amount;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
                //  Transaction Store
                $transaction = new Transaction();
                $transaction->transaction_title = $request->return_title;
                $transaction->investor_id = $request->investor_id;
                $transaction->investment_id = $request->investment_id;
                $transaction->transaction_date = $request->transaction_date;

                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                    $transaction->transaction_account_type = 2;

                } else {
                    $transaction->account_id = $request->cash_account_id;
                    $transaction->transaction_account_type = 1;
                }

                $transaction->transaction_purpose = $request->transaction_purpose;
                $transaction->transaction_type = 1;
                $transaction->amount = $request->amount;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->description = $request->note;
                $transaction->created_by = Auth::user()->id;
                $transaction->status = 1;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transaction->save();

                return redirect()->route('admin.investment-return.show',$request->investment_id)->with('message', 'Investment Return Successfully.');

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
            $invested_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 9)
                ->sum('amount');
            $return_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 10)
                ->sum('amount');
            $profit_amount = Transaction::where('investment_id', $id)
                ->where('transaction_purpose', 11)
                ->sum('amount');

            $due = $invested_amount - $return_amount;

            $investment = Investment::findOrFail($id);
            $investor = Investor::where('id', $investment->investor_id)->first();
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();

            return view('admin.account.investment.investment.investment-return.show',compact('investment','investor','bankAccounts','cash_account','invested_amount','return_amount','due','profit_amount'));
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                $return = Transaction::where('id', $id)->first();
                $return->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Return Deleted Successfully.',
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
    public function returnInvoice(Request $request,$id)
    {
        try {

            $investmentReturn = Transaction::where('id', $id)->first();

            $transaction = Transaction::where('id', $id)->with('bankAccount')->first();
            $investment = Investment::with('investor', 'createdByUser','negotiator')->findOrFail($transaction->investment_id);
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }

            $dashboard_settings = DashboardSetting::first();
            $totalReturnAmount = $investmentReturn->amount;
            $totalString =$this->convert_number_to_words($totalReturnAmount) ;

            return view('admin.account.investment.investment.investment-return.return-invoice', compact('totalReturnAmount','investment','investmentReturn','transaction', 'bank','dashboard_settings','totalString'));

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
