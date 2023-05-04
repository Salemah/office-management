<?php

namespace App\Http\Controllers\Admin\Account;

use App\Models\Inventory\Settings\InventoryWarehouse;
use Carbon\Carbon;
use App\Models\Account\Bank;
use Illuminate\Http\Request;
use App\Models\CRM\Client\Client;
use Illuminate\Support\Facades\DB;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
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
                    $bankAccounts = BankAccount::with('bank', 'warehouse_rel')->latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $bankAccounts = BankAccount::with('bank', 'warehouse_rel')->where('warehouse',$mystore->id)->latest()->get();
                }


                return DataTables::of($bankAccounts)
                    ->addIndexColumn()

                    ->addColumn('status', function ($bankAccounts) {
                        if ($bankAccounts->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $bankAccounts->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $bankAccounts->id . ')"
                            class="btn btn-sm btn-warning">In Active</button>';
                        }
                    })

                    ->addColumn('bankinfo', function ($bankAccounts) {
                        if ($bankAccounts->type == 2) {
                            return '<span class="badge bg-info">' . $bankAccounts->branch_name . '</span> <span class="badge bg-primary">' . $bankAccounts->bank->bank_name . '</span>';
                            // return $bankAccounts->branch_name ,$bankAccounts->bank;
                        } else {
                            return '<span class="badge bg-success text-light">Cash</span>';
                        }
                    })

                    ->addColumn('accountInfo', function ($bankAccounts) {
                        $name = $bankAccounts->name;
                        $number = $bankAccounts->account_number;
                        return $number . ' | ' . $name;
                    })

                    ->addColumn('action', function ($bankAccounts) {

                        $button = '';

                        $report = ' <li>
                        <a class="dropdown-item" href="' . route('admin.account.bank-account.report', $bankAccounts->id) . ' " ><i class="bx bxs-file" ></i> report</a></li>';

                        $revenue = '<li><a class="dropdown-item" href="' . route('admin.account.bank-account.revenue', $bankAccounts->id) . ' " ><i class="bx bx-money-withdraw" ></i> revenue</a></li>';

                        $expense = '<li><a class="dropdown-item" href="' . route('admin.account.bank-account.expense', $bankAccounts->id) . ' " ><i class="bx bx-minus-circle" ></i> expense</a></li>';

                        $edit = '<li><a class="dropdown-item" href="' . route('admin.account.bank-account.edit', $bankAccounts->id) . ' "><i class="bx bxs-edit text-green"></i> edit</a></li>';

                        $delete = '<li><a class="dropdown-item btn-delete" href="#" data-remote=" ' . route('admin.account.bank-account.destroy', $bankAccounts->id) . ' "><i class="bx bxs-trash"></i> delete</a></li>';

                        $button = $report . $revenue . $expense . $edit . $delete;

                        return '
                        <div class="nav-item">
                            <span class="badge bg-primary nav-link dropdown-toggle" role="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Actions
                            <i class="ik ik-chevron-down mr-0 align-middle"></i>
                        </span>

                        <ul class="dropdown-menu dropdown-menu-end pt-0" role="menu" style="width: auto; min-width: auto; position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -162px, 0px);" x-placement="top-start">
                        ' . $button . '

                        </ul>
                        </div>';
                    })
                    ->rawColumns(['action', 'status', 'bankinfo', 'accountInfo'])
                    ->make(true);
            }
            return view('admin.account.bank_account.index');
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
            $banks = Bank::where('status', 1)->get();
            $warehouses = InventoryWarehouse::all();
            return view('admin.account.bank_account.create', compact('banks', 'warehouses'));
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
            'warehouse' => 'required',
            'account_number' => 'required|unique:bank_accounts,account_number',
            'account_name' => 'required|unique:bank_accounts,name',
            'status' => 'required',
            'initial_balance' => 'required|numeric',
            'initial_balance' => 'required|numeric',

        ]);
        if ($request->type == 2) //account 1 == cash ,2 == bank
        {
            $request->validate([
                'bank_id' => 'required',
                'branch_name' => 'required',
            ]);
        }
        DB::beginTransaction();
        try {
            // Bank Account Store
            $bankAccount = new BankAccount();
            $bankAccount->warehouse = $request->warehouse;
            if ($request->type == 2) {
                $bankAccount->branch_name = $request->branch_name;
                $bankAccount->bank_id = $request->bank_id;
            }
            $bankAccount->type = $request->type;
            $bankAccount->account_number = $request->account_number;
            $bankAccount->routing_no = $request->routing_no;
            $bankAccount->initial_balance = $request->initial_balance;
            $bankAccount->name = $request->account_name;
            $bankAccount->status = $request->status;
            $bankAccount->note = $request->description;
            $bankAccount->created_by = Auth::user()->id;
            $bankAccount->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $bankAccount->save();

            $transaction = new Transaction();
            $transaction->transaction_title = 'Initial Balance Deposit';
            $transaction->account_id = $bankAccount->id;
            $transaction->warehouse_id = $request->warehouse;
            $transaction->transaction_date = Carbon::now();
            $transaction->transaction_purpose = 0;
            $transaction->transaction_type = 2;
            $transaction->transaction_account_type = $request->type;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->amount = $request->initial_balance;
            $transaction->status = $request->status;

            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();

            DB::commit();
            return redirect()->route('admin.account.bank-account.index')->with('message', 'Add successfully.');
        } catch (\Exception $exception) {

            DB::rollBack();
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        //
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
            $parameter = "account-list";
            $banks = Bank::where('status', 1)->get();
            $bankAccount = BankAccount::where('id', $id)->first();
            $Client = '';
            $warehouses = InventoryWarehouse::all();
            return view('admin.account.bank_account.edit', compact('bankAccount', 'banks', 'parameter', 'Client','warehouses'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
    }
    public function bankAccountUpdate(Request $request, $id)
    {
        $request->validate([
            'account_number' => 'required',
            'account_name' => 'required',
            'status' => 'required',
            'initial_balance' => 'required',
            'warehouse' => 'required',
        ]);
        if ($request->type == 2) //account 1 == cash ,2 == bank
        {
            $request->validate([
                'bank_id' => 'required',
                'branch_name' => 'required',
            ]);
        }
        DB::beginTransaction();
        try {
            // Bank Account Store
            $bankAccount = BankAccount::find($id);

            if ($request->type == 2) {
                $bankAccount->branch_name = $request->branch_name;
                $bankAccount->bank_id = $request->bank_id;
            }
            $bankAccount->warehouse = $request->warehouse;
            $bankAccount->type = $request->type;
            $bankAccount->account_number = $request->account_number;
            $bankAccount->routing_no = $request->routing_no;
            $bankAccount->initial_balance = $request->initial_balance;
            $bankAccount->name = $request->account_name;
            $bankAccount->status = $request->status;
            $bankAccount->note = $request->description;
            if ($request->client_id) {
                $bankAccount->user_id = $request->client_id;
                $bankAccount->account_type = 3; //type 3 == client bank account
            }
            $bankAccount->updated_by = Auth::user()->id;
            $bankAccount->update();

            $transaction = Transaction::where('account_id', $bankAccount->id)->first();
            $transaction->transaction_title = 'Initial Balance Deposit';
            $transaction->account_id = $bankAccount->id;
            $bankAccount->warehouse_id = $request->warehouse;
            $transaction->transaction_date = Carbon::now();
            $transaction->transaction_purpose = 0;
            $transaction->transaction_type = 2;
            $transaction->transaction_account_type = $request->type;
            $transaction->amount = $request->initial_balance;
            $transaction->status = $request->status;
            $transaction->updated_by = Auth::user()->id;
            $transaction->update();

            DB::commit();
            if ($request->parameter == 'account-list') {
                return redirect()->route('admin.account.bank-account.index')->with('message', 'Update successfully.');
            } else {
                $Client = Client::findOrFail($request->client_id);
                return redirect()->route('admin.crm.client.show', $Client->id)->with('message', ' Update successfully.');
            }
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
                BankAccount::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bank Account Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $reference = BankAccount::findOrFail($request->id);

            $reference->status == 1 ? $reference->status = 0 : $reference->status = 1;

            $reference->update();
            if ($reference->status == 1) {
                return "active";
                // exit();
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }
    public function Transaction(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $data = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->where('status', 1)->orderBy('id', 'ASC')->get();

                return Datatables::of($data)

                    ->addColumn('date', function ($data) {
                        $date = Carbon::parse($data->transaction_date)->format('d F, Y');
                        return $date;
                    })

                    ->addColumn('purpose', function ($data) {
                        if ($data->transaction_purpose == 0)
                            return '<span class="text-danger text-bold"><b>Initial Balance - (Cash-In) </b></span>';
                        else if ($data->transaction_purpose == 1) {
                            return '<span class="text-danger text-bold"><b>Withdraw - </b></span>';
                        } else if ($data->transaction_purpose == 2) {
                            return '<span class="text-success text-bold"><b>Deposit -</b></span>';
                        } else if ($data->transaction_purpose == 3) {
                            return '<span class="text-success text-bold"><b>Revenue - </b></span>';
                        } else if ($data->transaction_purpose == 4) {
                            return '<span class="text-success text-bold"><b>Given Payment - </b></span>';
                        } else if ($data->transaction_purpose == 5) {
                            return '<span class="text-danger text-bold"><b>Expense -</b></span>';
                        } else if ($data->transaction_purpose == 6) {
                            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In)</span>';
                        } else if ($data->transaction_purpose == 7) {
                            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></span>';
                        } else if ($data->transaction_purpose == 8) {
                            return '<span class="text-success text-bold"><b>Cash In -</b></span>';
                        } else if ($data->transaction_purpose == 9) {
                            return '<span class="text-success text-bold"><b>Investment - </b></span>';
                        } else if ($data->transaction_purpose == 10) {
                            return '<span class="text-success text-bold"><b>Investment return - </b></span>';
                        } else if ($data->transaction_purpose == 11) {
                            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></span>';
                        } else if ($data->transaction_purpose == 12) {
                            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
                        } else if ($data->transaction_purpose == 13) {
                            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
                        } else if ($data->transaction_purpose == 14) {
                            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
                        } else if ($data->transaction_purpose == 15) {
                            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
                        } else {
                            return '...';
                        }
                    })

                    ->addColumn('debit_amount', function ($data) {
                        if ($data->transaction_type == 1) {
                            $amount = $data->amount;
                            return number_format($amount, 2);
                        } else {
                            return  '--';
                        }
                    })

                    ->addColumn('credit_amount', function ($data) {
                        if ($data->transaction_type == 2) {
                            $amount = $data->amount;
                            return number_format($amount, 2);
                        } else {
                            return  '--';
                        }
                    })

                    ->addColumn('current_balance', function ($data) use (&$current_balance) {
                        if ($data->transaction_type == 2) {
                            $credit = $data->amount;
                            $current_balance = $current_balance + $credit;
                            return (number_format($current_balance));
                        }

                        if ($data->transaction_type == 1) {
                            $debit = $data->amount;
                            $current_balance = $current_balance - $debit;
                            return (number_format($current_balance));
                        }
                    })

                    ->addIndexColumn()
                    ->rawColumns(['date', 'purpose', 'debit_amount', 'credit_amount', 'current_balance'])
                    ->toJson();
            }

            $accountInfo = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->first();

            $totalDebit = Transaction::where('account_id', $id)->where('transaction_type', 1)->sum('amount');

            $totalCredit = Transaction::where('account_id', $id)->where('transaction_type', 2)->sum('amount');

            $currentBalance = $totalCredit - $totalDebit;

            return view('admin.account.bank_account.transaction', compact('accountInfo', 'totalDebit', 'totalCredit', 'currentBalance'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function Revenue(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $data = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->where('status', 1)->where('transaction_type', 2)->orderBy('id', 'ASC')->get();

                return Datatables::of($data)

                    ->addColumn('date', function ($data) {
                        $date = Carbon::parse($data->transaction_date)->format('d F, Y');
                        return $date;
                    })

                    ->addColumn('purpose', function ($data) {
                        if ($data->transaction_purpose == 0)
                            return '<span class="text-danger text-bold"><b>Initial Balance - (Cash-In) </b></span>';
                        else if ($data->transaction_purpose == 1) {
                            return '<span class="text-danger text-bold"><b>Withdraw - </b></span>';
                        } else if ($data->transaction_purpose == 2) {
                            return '<span class="text-success text-bold"><b>Deposit -</b></span>';
                        } else if ($data->transaction_purpose == 3) {
                            return '<span class="text-success text-bold"><b>Revenue - </b></span>';
                        } else if ($data->transaction_purpose == 4) {
                            return '<span class="text-success text-bold"><b>Given Payment - </b></span>';
                        } else if ($data->transaction_purpose == 5) {
                            return '<span class="text-danger text-bold"><b>Expense -</b></span>';
                        } else if ($data->transaction_purpose == 6) {
                            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In)</span>';
                        } else if ($data->transaction_purpose == 7) {
                            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></span>';
                        } else if ($data->transaction_purpose == 8) {
                            return '<span class="text-success text-bold"><b>Cash In -</b></span>';
                        } else if ($data->transaction_purpose == 9) {
                            return '<span class="text-success text-bold"><b>Investment - </b></span>';
                        } else if ($data->transaction_purpose == 10) {
                            return '<span class="text-success text-bold"><b>Investment return - </b></span>';
                        } else if ($data->transaction_purpose == 11) {
                            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></span>';
                        } else if ($data->transaction_purpose == 12) {
                            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
                        } else if ($data->transaction_purpose == 13) {
                            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
                        } else if ($data->transaction_purpose == 14) {
                            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
                        } else if ($data->transaction_purpose == 15) {
                            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
                        } else {
                            return '...';
                        }
                    })

                    ->addColumn('credit_amount', function ($data) {
                        $amount = $data->amount;
                        return number_format($amount, 2);
                    })

                    ->addIndexColumn()
                    ->rawColumns(['date', 'purpose', 'credit_amount'])
                    ->toJson();
            }

            $accountInfo = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->first();

            $totalRevenue = Transaction::where('account_id', $id)->where('transaction_type', 2)->sum('amount');

            return view('admin.account.bank_account.revenue', compact('accountInfo', 'totalRevenue'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function Expense(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $data = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->where('transaction_type', 1)->where('status', 1)->orderBy('id', 'ASC')->get();

                return Datatables::of($data)

                    ->addColumn('date', function ($data) {
                        $date = Carbon::parse($data->transaction_date)->format('d F, Y');
                        return $date;
                    })

                    ->addColumn('purpose', function ($data) {
                        if ($data->transaction_purpose == 0)
                            return '<span class="text-danger text-bold"><b>Initial Balance - (Cash-In) </b></span>';
                        else if ($data->transaction_purpose == 1) {
                            return '<span class="text-danger text-bold"><b>Withdraw - </b></span>';
                        } else if ($data->transaction_purpose == 2) {
                            return '<span class="text-success text-bold"><b>Deposit -</b></span>';
                        } else if ($data->transaction_purpose == 3) {
                            return '<span class="text-success text-bold"><b>Revenue - </b></span>';
                        } else if ($data->transaction_purpose == 4) {
                            return '<span class="text-success text-bold"><b>Given Payment - </b></span>';
                        } else if ($data->transaction_purpose == 5) {
                            return '<span class="text-danger text-bold"><b>Expense -</b></span>';
                        } else if ($data->transaction_purpose == 6) {
                            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In)</span>';
                        } else if ($data->transaction_purpose == 7) {
                            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></span>';
                        } else if ($data->transaction_purpose == 8) {
                            return '<span class="text-success text-bold"><b>Cash In -</b></span>';
                        } else if ($data->transaction_purpose == 9) {
                            return '<span class="text-success text-bold"><b>Investment - </b></span>';
                        } else if ($data->transaction_purpose == 10) {
                            return '<span class="text-success text-bold"><b>Investment return - </b></span>';
                        } else if ($data->transaction_purpose == 11) {
                            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></span>';
                        } else if ($data->transaction_purpose == 12) {
                            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
                        } else if ($data->transaction_purpose == 13) {
                            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
                        } else if ($data->transaction_purpose == 14) {
                            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
                        } else if ($data->transaction_purpose == 15) {
                            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
                        } else {
                            return '...';
                        }
                    })

                    ->addColumn('debit_amount', function ($data) {
                        $amount = $data->amount;
                        return number_format($amount, 2);
                    })

                    ->addIndexColumn()
                    ->rawColumns(['date', 'purpose', 'debit_amount'])
                    ->toJson();
            }

            $accountInfo = Transaction::with('bankAccount', 'createdByUser')->where('account_id', $id)->first();

            $totalExpense = Transaction::where('account_id', $id)->where('transaction_type', 1)->sum('amount');

            return view('admin.account.bank_account.expense', compact('accountInfo', 'totalExpense'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
