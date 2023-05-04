<?php

namespace App\Http\Controllers\Admin\Account\Investment;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Investment\Investment;
use App\Models\Account\Investment\Investor;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Settings\DashboardSetting;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
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

                $auth = Auth::user();
                $user_role = $auth->roles->first();

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $investment = Investment::with('investor','warehouse')->latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    //$loan = Loan::with('author')->latest()->get();
                    $investment = Investment::with('investor','warehouse')->where('warehouse_id',$mystore->id)->latest()->get();
                }
                return DataTables::of($investment)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($investment) {

                        if ($investment->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }

                    })
                    ->addColumn('investor', function ($investment) {
                        return $investment->investor->name;
                    })
                    ->addColumn('warehouse', function ($investment) {
                        return $investment->warehouse->name;
                    })
                    ->addColumn('amount', function ($investment) {
                        return number_format($investment->amount,2);;
                    })
                    ->addColumn('due_amount', function ($investment) {
                        $invested_amount = Transaction::where('investment_id', $investment->id)
                            ->where('transaction_purpose', 9)
                            ->sum('amount');
                        $return_amount = Transaction::where('investment_id', $investment->id)
                            ->where('transaction_purpose', 10)
                            ->sum('amount');


                        $due = $invested_amount - $return_amount;
                        return number_format($due,2);
                    })
                    ->addColumn('action', function ($investment) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-primary text-white " style="cursor:pointer"href="' . route('admin.investment-return.show', $investment->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
                                    <a class="btn btn-sm btn-success text-white " style="cursor:pointer"href="' . route('admin.investment.edit', $investment->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>
                                    <a class="btn btn-sm btn-success text-white " style="cursor:pointer"href="' . route('admin.investment.invoice', $investment->id) . '" title="Voucher"><i class="bx bx-printer"></i></a>

                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentDeleteConfirm(' . $investment->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    //<a class="btn btn-sm btn-primary text-white " style="cursor:pointer"href="' . route('admin.investment.return.invoice', $investment->id) . '" title="Return Voucher"><i class="bx bx-printer"></i></a> <a class="btn btn-sm btn-warning text-white " style="cursor:pointer"href="' . route('admin.investment.profit.return.invoice', $investment->id) . '" title="Profit Return Voucher"><i class="bx bx-printer"></i></a>
                    ->rawColumns(['warehouse','amount','due_amount','transaction_way','investor','action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.index');
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
            $investors = Investor::where('status',1)->get();
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();

            $warehouses = InventoryWarehouse::get();
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $mystore = '';
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
            }

            return view('admin.account.investment.investment.create',compact('investors','bankAccounts','cash_account','bankAccounts','warehouses','mystore'));
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
            'investor_id' => 'required',
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
            'negotiator_id' => 'required',
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
        DB::beginTransaction();
        try {
             // investment
            $investment = new Investment();
            $investment->investor_id = $request->investor_id;
            $investment->date = $request->transaction_date;
            $investment->transaction_way = $request->transaction_way;
            $investment->negotiator_id = $request->negotiator_id;
            $investment->warehouse_id = $request->warehouse_id;
            $investment->note = $request->note;
            $investment->transaction_title = $request->transaction_title;
            $investment->amount = $request->amount;
            $investment->created_by = Auth::user()->id;
            $investment->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $investment->save();

            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;

            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $transaction->warehouse_id =  $request->warehouse_id;
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                $transaction->warehouse_id = $mystore->id;
            }

            $transaction->transaction_date = $request->transaction_date;
            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;
            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
            $transaction->transaction_purpose = 9;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =$request->note;
            $transaction->investment_id = $investment->id;
            $transaction->investor_id = $request->investor_id;
            $transaction->created_by = Auth::user()->id;
            $transaction->status = 1;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();
            DB::commit();
           return redirect()->route('admin.investment.show',$request->investor_id)->with('message', 'Investment successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
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
            $investor = Investor::findOrFail($id);
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            return view('admin.account.investment.investment.show',compact('investor','bankAccounts','cash_account','bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function InvestmentList(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $investment = Investment::with('investor')->where('investor_id',$id)->get();
                return DataTables::of($investment)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($investment) {

                        if ($investment->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }

                    })
                    ->addColumn('investor', function ($investment) {
                        return $investment->investor->name;
                    })
                    ->addColumn('action', function ($investment) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.investment-return.show', $investment->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investmentDeleteConfirm(' . $investment->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['transaction_way','investor','action'])
                    ->make(true);
            }
            return view('admin.account.investment.investment.show');
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
            $investors = Investor::where('status',1)->get();
            $investment = Investment::with('transaction')->findOrFail($id);
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $negotiators = Employee::all();
            $transaction = Transaction::where('investment_id', $id)->first();
            $availableBalance = AccountsRepository::postBalance($transaction->account_id);

            $warehouses = InventoryWarehouse::get();
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $mystore = '';
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
            }

            return view('admin.account.investment.investment.edit',compact('warehouses','mystore','investment','availableBalance','transaction','negotiators','cash_account','investors','bankAccounts'));
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
        $request->validate([
            'investor_id' => 'required',
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'negotiator_id' => 'required',
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
        DB::beginTransaction();
        try {
            // investment
            $investment = Investment::findOrFail($id);
            $investment->investor_id = $request->investor_id;
            $investment->date = $request->transaction_date;
            $investment->transaction_way = $request->transaction_way;
            $investment->negotiator_id = $request->negotiator_id;
            $investment->warehouse_id =  $request->warehouse_id;
            $investment->note = $request->note;
            $investment->transaction_title = $request->transaction_title;
            $investment->amount = $request->amount;
            $investment->updated_by = Auth::user()->id;
            $investment->update();

            //  Transaction Store
            $transaction = Transaction::where('investment_id', $investment->id)->first();
            $transaction->transaction_title = $request->transaction_title;

            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $transaction->warehouse_id =  $request->warehouse_id;
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                $transaction->warehouse_id = $mystore->id;
            }

            $transaction->transaction_date = $request->transaction_date;
            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;
            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
            $transaction->transaction_purpose = 9;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $transaction->investment_id = $investment->id;
            $transaction->investor_id = $request->investor_id;
            $transaction->status = 1;
            $transaction->updated_by = Auth::user()->id;
            $transaction->update();

            DB::commit();
           return redirect()->route('admin.investment.index')->with('message', 'Investment Update successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
                $investment = Investment::where('id', $id)->with('transaction')->first();
                $transactions = Transaction::where('investment_id', $investment->id)->get();

                foreach ($transactions as $key => $transaction) {
                    $transaction->delete();
                }
                $investment->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Investment Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function invoice(Request $request,$id)
    {
        try {
            $investment = Investment::with('investor', 'createdByUser','negotiator')->findOrFail($id);
            $transaction = Transaction::where('investment_id', $id)->with('bankAccount')->first();
            $bank = '';
            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }
            $dashboard_settings = DashboardSetting::first();
            $totalString =$this->convert_number_to_words($investment->amount) ;
            return view('admin.account.investment.investment.invoice', compact( 'investment', 'transaction', 'bank','dashboard_settings','totalString'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
            $investment = Investment::with('investor', 'createdByUser','negotiator')->findOrFail($id);
            $investmentReturn = Transaction::where('investment_id', $id)->where('transaction_purpose', 10)->get();
            $transaction = Transaction::where('investment_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }

            $dashboard_settings = DashboardSetting::first();
            $totalReturnAmount = $investmentReturn->sum('amount');
            $totalString =$this->convert_number_to_words($totalReturnAmount) ;

            return view('admin.account.investment.investment.investment-return.return-invoice', compact('totalReturnAmount','investment','investmentReturn','transaction', 'bank','dashboard_settings','totalString'));

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ProfitReturnInvoice(Request $request,$id)
    {
        try {
            $investment = Investment::with('investor', 'createdByUser','negotiator')->findOrFail($id);
            $investmentReturn = Transaction::where('investment_id', $id)->where('transaction_purpose', 11)->get();
            $transaction = Transaction::where('investment_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }

            $dashboard_settings = DashboardSetting::first();
            $totalReturnAmount = $investmentReturn->sum('amount');
            $totalString =$this->convert_number_to_words($totalReturnAmount) ;

            return view('admin.account.investment.investment.investment-return.profit-return-invoice', compact('totalReturnAmount','investment','investmentReturn','transaction', 'bank','dashboard_settings','totalString'));

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
    public function investorSearch(Request $request)
    {
        $result = Investor::query()
                 ->where('warehouse_id', $request->warehouse_id)
                 ->where('name', 'LIKE', "%{$request->search}%")
                 ->get(['name', 'id']);
        return $result;
    }
    public function employeeSearch(Request $request)
    {
        $result = Employee::query()
                 ->where('warehouse', $request->warehouse_id)
                 ->where('name', 'LIKE', "%{$request->search}%")
                 ->get(['name', 'id']);
        return $result;
    }


}
