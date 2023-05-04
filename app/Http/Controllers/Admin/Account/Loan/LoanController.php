<?php

namespace App\Http\Controllers\Admin\Account\Loan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Loan\Loan;
use App\Models\Account\Loan\Loan_Authority;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseDetails;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Settings\DashboardSetting;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
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
                    $loan = Loan::with('author')->latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $loan = Loan::with('author')->where('warehouse_id',$mystore->id)->latest()->get();

                }
                return DataTables::of($loan)
                    ->addIndexColumn()
                    ->addColumn('author', function ($loan) {
                        return $loan->author->name;
                    })
                    ->addColumn('loan_type', function ($loan) {
                        if ($loan->loan_type == 1) {
                            return "Taking";
                        }
                        return "Giving";
                    })
                    ->addColumn('transaction_way', function ($loan) {
                        if ($loan->transaction_way == 1) {
                            return "Cash";
                        }
                        return "Bank";
                    })
                    ->addColumn('action', function ($loan) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-warning text-white " title="Show" style="cursor:pointer"
                                    href="' . route('admin.loan.details', $loan->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a class="btn btn-sm btn-primary text-white " style="cursor:pointer" href="' . route('admin.loan-return.show', $loan->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
                                    <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.loan.edit', $loan->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanDeleteConfirm(' . $loan->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['author', 'loan_type', 'action'])
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

            return view('admin.account.loan.loan.create', compact('loanauthorities', 'bankAccounts','cash_account','warehouses','mystore'));
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
            'loan_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
            'note' => 'nullable|string',
            'interest_rate' => 'required',
            'flatrate' => 'required',
            'duration' => 'required',
            'negotiator_id' => 'required',
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
            $account_id = 0;
            if ($request->loan_type == 2) {
                if($request->account_id!=null){
                    $account_id = $request->account_id;
                }
              if($request->cash_account_id!=null){
                    $account_id = $request->cash_account_id;
                }
                // $request->loan_type == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);
                $balance = $balance - $request->amount;
                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }
            $loan = new Loan();
            $loan->loan_author_id = $request->loan_author_id;
            $loan->loan_date = $request->transaction_date;
            $loan->transaction_way = $request->transaction_way;
            $loan->loan_type = $request->loan_type;
            $loan->note = $request->note;
            $loan->negotiator_id = $request->negotiator_id;
            $loan->warehouse_id = $request->warehouse_id;
            $loan->duration= $request->duration;
            $loan->interest_rate= $request->interest_rate;
            $loan->status= $request->flatrate;
            $loan->loan_title = $request->loan_title;
            $loan->loan_amount = $request->amount;
            $loan->created_by = Auth::user()->id;
            $loan->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $loan->save();

            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->loan_title;
            $transaction->loan_author_id = $request->loan_author_id;
            $transaction->transaction_date = $request->transaction_date;

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
            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;

            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 12;
                $transaction->transaction_type = 1;
            } else {
                $transaction->transaction_purpose = 13;
                $transaction->transaction_type = 2;
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $transaction->status = 1;
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));;
            $transaction->loan_id = $loan->id;

            $transaction->save();
            DB::commit();
            return redirect()->route('admin.loan.show', $request->loan_author_id)->with('message', 'Loan successfully.');
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
            $authority = Loan_Authority::findOrFail($id);
            $bankAccounts = BankAccount::where('status', 1)->get();
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
            return view('admin.account.loan.loan.show', compact('authority', 'bankAccounts','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function LoanList(Request $request,$id)
    {
        try {
            if ($request->ajax()) {
                $loan = Loan::with('author')
                ->where('loan_author_id',$id)->latest()
                ->get();
                return DataTables::of($loan)
                    ->addIndexColumn()
                    ->addColumn('loan_type', function ($loan) {
                        if ($loan->loan_type == 1) {
                            return "Taking";
                        }
                        return "Giving";
                    })
                    ->addColumn('transaction_way', function ($loan) {
                        if ($loan->transaction_way == 1) {
                            return "Cash";
                        }
                        return "Bank";
                    })
                    ->addColumn('action', function ($loan) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.loan-return.show', $loan->id) . '" title="Return"><i class="bx bx-subdirectory-left"></i></a>
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanDeleteConfirm(' . $loan->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                        </div>';
                    })
                    ->rawColumns([ 'loan_type', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.loan.show');
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
            $negotiators = Employee::all();
            $loan = Loan::with('author', 'transaction')->findOrFail($id);
            $loanauthorities = Loan_Authority::get();
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $transaction = Transaction::where('loan_id', $id)->first();
            // $total = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $availableBalance = '';
            if ($transaction) {
                $availableBalance = AccountsRepository::postBalance($transaction->account_id);
            }
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
            return view('admin.account.loan.loan.edit', compact('loanauthorities', 'bankAccounts', 'loan','negotiators','cash_account','transaction','availableBalance','warehouses','mystore'));
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
            'loan_author_id' => 'required',
            'loan_type' => 'required',
            'loan_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
            'note' => 'nullable|string',
            'interest_rate' => 'required',
            'flatrate' => 'required',
            'duration' => 'required',
            'negotiator_id' => 'required',
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
            $loan = Loan::findOrFail($id);
            $loan->loan_author_id = $request->loan_author_id;
            $loan->loan_date = $request->transaction_date;
            $loan->transaction_way = $request->transaction_way;
            $loan->loan_type = $request->loan_type;
            $loan->note = $request->note;
            $loan->negotiator_id = $request->negotiator_id;
            $loan->warehouse_id = $request->warehouse_id;
            $loan->duration= $request->duration;
            $loan->interest_rate= $request->interest_rate;
            $loan->status= $request->flatrate;
            $loan->loan_title = $request->loan_title;
            $loan->loan_amount = $request->amount;
            $loan->updated_by = Auth::user()->id;
            $loan->update();


            $transaction = Transaction::where('loan_id', $loan->id)->first();
            $transaction->transaction_title = $request->loan_title;
            $transaction->loan_author_id = $request->loan_author_id;
            $transaction->transaction_date = $request->transaction_date;

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

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;

            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }

            if ($request->loan_type == 2) {
                $transaction->transaction_purpose = 12;
                $transaction->transaction_type = 1;
            } else {
                $transaction->transaction_purpose = 13;
                $transaction->transaction_type = 2;
            }

            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->note;
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));;
            $transaction->loan_id = $loan->id;

            $transaction->update();

            DB::commit();

            return redirect()->route('admin.loan.index')->with('message', 'Loan Update successfully.');
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
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $loan = Loan::where('id', $id)->with('transaction')->first();
                $transactions = Transaction::where('loan_id', $loan->id)->get();

                foreach ($transactions as $key => $transaction) {
                    $transaction->delete();
                }
                $loan->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Loan Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function LoanDetails(Request $request, $id)
    {
            try {
                $loan = Loan::with('author')->findOrFail($id);
                $transaction = Transaction::with('bankAccount')->where('loan_id', $loan->id)->first();
                $returnAmount = Transaction::where('loan_id', $loan->id)->where('transaction_purpose', 15)->orWhere('transaction_purpose', 14)->sum('amount');
                $returnList = Transaction::with('bankAccount')->where('loan_id', $loan->id)->where('transaction_purpose', 15)->orWhere('transaction_purpose', 14)->get();


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

                    $due=$amount_withinterest - $returnAmount;

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
                        $due=$CI-$returnAmount;
                        $interest_till_today = $CI -$loan->loan_amount;
                    }
                return \view('admin.account.loan.loan.details',compact('returnList','loan','transaction','returnAmount','due'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
    }
    public function LoanListStatus(Request $request)
    {
        try {
            if ($request->ajax()) {
                $loan = Loan::with('author')->latest()->get();
                return DataTables::of($loan)
                    ->addIndexColumn()
                    ->addColumn('author', function ($loan) {
                        return $loan->author->name;
                    })
                    ->addColumn('loan_amount', function ($loan) {
                        return number_format($loan->loan_amount,2) ;
                    })
                    ->addColumn('loan_type', function ($loan) {
                        if ($loan->loan_type == 1) {
                            return "Taking";
                        }
                        return "Giving";
                    })
                    ->addColumn('return_amount', function ($loan) {
                        $transaction = Transaction::where('loan_id', $loan->id)->where('transaction_purpose', 15)->orWhere('transaction_purpose', 14)->sum('amount');
                        return number_format($transaction,2);
                    })
                    ->addColumn('due_amount', function ($loan) {
                        $returnAmount = Transaction::where('loan_id', $loan->id)->where('transaction_purpose', 15)->orWhere('transaction_purpose', 14)->sum('amount');
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

                            $due=$amount_withinterest - $returnAmount;

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
                                $due=$CI- $returnAmount;
                                $interest_till_today = $CI -$loan->loan_amount;
                            }
                            return number_format($due,2);
                    })
                    ->addColumn('action', function ($loan) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-warning text-white " title="Show" style="cursor:pointer"
                                    href="' . route('admin.loan.details', $loan->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a class="btn btn-sm btn-primary text-white " style="cursor:pointer" href="' . route('admin.loan.invoice', $loan->id) . '" title="Voucher"><i class="bx bx-printer"></i></a>
                                </div>';
                    })
                    // <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="loanDeleteConfirm(' . $loan->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                    ->rawColumns(['due_amount','return_amount','author', 'loan_type', 'action'])
                    ->make(true);
            }
            return view('admin.account.loan.status.index');
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
    public function LoanInvoice(Request $request,$id)
    {
        try {

            $loan = Loan::with('author', 'createdByUser','negotiator')->findOrFail($id);



            // $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();

            // $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $transaction = Transaction::where('loan_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }
            $dashboard_settings = DashboardSetting::first();
            $totalString =$this->convert_number_to_words($loan->loan_amount) ;
            return view('admin.account.loan.status.invoice', compact( 'loan', 'transaction', 'bank','dashboard_settings','totalString'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function authorSearch(Request $request)
    {
        $result = Loan_Authority::query()
                 ->where('warehouse_id', $request->warehouse_id)
                 ->where('name', 'LIKE', "%{$request->search}%")
                 ->get(['name', 'id']);
        return $result;
    }


    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
}
