<?php

namespace App\Http\Controllers\Admin\Account\BalanceSheet;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Investment\Investment;
use App\Models\Account\Loan\Loan;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseDetails;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Project\Projects;
use App\Models\Revenue\Revenue;
use App\Models\Revenue\RevenueDetails;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\Admin\Account\TransactionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use App\Repositories\Admin\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return view('admin.account.balance_sheet.bank_balance_sheet');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function balanceSheetData(Request $request)
    {
        try {
            if ($request->ajax()) {
                $debit = 0;
                $credit = 0;

                $auth = Auth::user();
                $user_role = $auth->roles->first();

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $transaction = Transaction::where('transaction_account_type',2)->with('bankAccount')->groupBy('account_id');
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $transaction = Transaction::where('warehouse_id',$mystore->id)->where('transaction_account_type',2)->with('bankAccount')->groupBy('account_id');
                }
                //dd($transaction);

                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transaction) {
                        return $transaction->bankAccount->account_number . ' | ' . $transaction->bankAccount->name;
                    })
                    ->addColumn('debit', function ($transaction) use (&$debit) {

                        $debit = AccountsRepository::debitBalance($transaction->account_id);
                        return $debit;
                    })
                    ->addColumn('credit', function ($transaction) use (&$credit) {
                        $credit = AccountsRepository::creditBalance($transaction->account_id);
                        return $credit;
                    })
                    ->addColumn('balance', function ($transaction) use (&$debit, &$credit) {
                        return $credit - $debit;
                    })
                    ->rawColumns(['bankinfo', 'debit', 'credit', 'balance'])
                    ->make(true);
            }

        }

        catch (\Exception $exception) {
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
            // Get All Bank
            $bank_accounts = BankAccount::where('status', 1)->get();
            return view('admin.account.cash_in.create', compact('bank_accounts'));
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
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = 0;
            $transaction->transaction_purpose = 8;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->description = strip_tags($request->description);
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

            return redirect()->route('admin.account.cash-in.index')->with('message', 'Cash In Successfully.');
        } catch (\Exception $exception) {
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
            $cashIn = Transaction::where('id', $id)->first();
            return view('admin.account.cash_in.edit', compact('cashIn'));
        } catch (\Exception $exception) {
            dd($exception->getMessage());
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
        $request->validate([
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);

            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = 0;
            $transaction->transaction_purpose = 8;
            $transaction->transaction_type = 2;
            $transaction->amount = $request->amount;
            $transaction->description = strip_tags($request->description);
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

            return redirect()->route('admin.account.cash-in.index')->with('message', 'Cash In Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
                Transaction::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Cash In Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function bankAccountBalance(Request $request, $id)
    {
        if ($request->ajax()) {
            $balance = AccountsRepository::postBalance($id);
            return response()->json($balance);
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cashBalanceSheet(Request $request)
    {
        try {
            if ($request->ajax()) {
                // $postBalance = 0;
                // $transactions = Transaction::where('transaction_account_type',1)->where('status',1)->with('createdByUser')->get();
                // return DataTables::of($transactions)
                //     ->addIndexColumn()
                //     ->addColumn('transaction_purpose', function ($transactions) {
                //         return TransactionRepository::type($transactions);
                //     })
                //     ->addColumn('debit', function ($transactions) {
                //         if ($transactions->transaction_type == 1) {
                //             return $transactions->amount;
                //         } else {
                //             return ' ';
                //         }
                //     })
                //     ->addColumn('credit', function ($transactions) {
                //         if ($transactions->transaction_type == 2) {
                //             return $transactions->amount;
                //         } else {
                //             return '';
                //         }
                //     })
                //     ->addColumn('balance', function ($data) use (&$postBalance) {
                //         if ($data->transaction_type == 1) {
                //             $postBalance = $postBalance - $data->amount;
                //             return $postBalance;
                //         } else {
                //             $postBalance = $postBalance + $data->amount;
                //             return $postBalance;
                //         }
                //     })
                //     ->rawColumns(['transaction_purpose', 'debit', 'credit', 'balance'])
                //     ->make(true);
                $debit = 0;
                $credit = 0;

                $auth = Auth::user();
                $user_role = $auth->roles->first();

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $transaction = Transaction::where('transaction_account_type',1)->with('bankAccount')->groupBy('account_id');
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $transaction = Transaction::where('transaction_account_type',1)->where('warehouse_id',$mystore->id)->with('bankAccount')->groupBy('account_id');
                }
                return DataTables::of($transaction)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transaction) {
                        // return $transaction->bankAccount->account_number . ' | ' . $transaction->bankAccount->name;
                    return $transaction->bankAccount->name ?? '-';
                    })
                    ->addColumn('debit', function ($transaction) use (&$debit) {

                        $debit = AccountsRepository::debitBalance($transaction->account_id);
                        return $debit;
                    })
                    ->addColumn('credit', function ($transaction) use (&$credit) {
                        $credit = AccountsRepository::creditBalance($transaction->account_id);
                        return $credit;
                    })
                    ->addColumn('balance', function ($transaction) use (&$debit, &$credit) {
                        return $credit - $debit;
                    })
                    ->rawColumns(['bankinfo', 'debit', 'credit', 'balance'])
                    ->make(true);
            }
            return view('admin.account.balance_sheet.cash_balance_sheet');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
     public function bankAccountStatement()
    {
        try {
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
            $bank_accounts = BankAccount::where('status',1)->get();
            return view('admin.account.account_statement.account_statement',compact('bank_accounts','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function accountStatementData(Request $request)
    {
        if ($request->ajax()) {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
            $postBalance =AccountsRepository::previousPostBalance($request->account_id, $start_date);
            $data = Transaction::with('bankAccount')
                ->where('status',1)
                ->where('account_id', $request->account_id)
                ->where('transaction_date', '>=', $start_date)
                ->where('transaction_date', '<=', $end_date)
                ->orderBy('transaction_date', 'asc');
                // \dd($data);

            return DataTables::of($data, $postBalance)
                ->addIndexColumn()
                // ->addColumn('purpose', function ($transactions) {
                //     return TransactionRepository::type($transactions);
                // })
                ->addColumn('debit', function ($data) {
                    if ($data->transaction_type == 1) {
                        return number_format($data->amount,2) ;
                    } else {
                        return '0';
                    }
                })
                ->addColumn('details', function ($data) {
                    if ($data->revenue_id !=null) {
                        $revenueDetails= RevenueDetails::with('revenueCategory')->where('revenue_id',$data->revenue_id)->first();
                        $cat= $revenueDetails->revenueCategory->name;
                        $revenue = Revenue::with('clientId')->findOrFail($data->revenue_id);
                        $client = '';
                         if($revenue->clientId){
                            $client= $revenue->clientId->name;
                         }
                        return $cat.' | '.$revenueDetails->description .' | '.$client . ' || '. $revenue->revenue_invoice_no. '<span class="badge bg-primary">Revenue</span>';
                    }
                    else if ($data->expense_id !=null) {
                        $expenseDetails= ExpenseDetails::with('expenseCategory')->where('expense_id',$data->expense_id)->first();

                       // $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                         $cat= $expenseDetails->expenseCategory->name;
                         $expense = '';
                         $expenseby = Expense::with('updatedBy')->findOrFail($data->expense_id);
                         if($expenseby->status ==2){
                            $expense= $expenseby->updatedBy->name;
                         }
                        return $cat.' | '.$expenseDetails->description . ' | '.$expense . ' || '. $expenseby->expense_invoice_no. '<span class="badge bg-primary">Expense</span>';
                    }
                    else if ($data->loan_id  !=null) {
                        $expenseDetails= Loan::with('author')->where('id',$data->loan_id )->first();
                        return $expenseDetails->loan_title .' | ' .TransactionRepository::type($data).' || ' .$expenseDetails->author->name ;
                    }
                    else if ($data->project_id  !=null) {
                        $project= Projects::with('projectCategory')->where('id',$data->project_id)->first();
                        $client = '';
                        if($project->client_id){
                            $client = DB::table('clients')->where('id',$project->client_id)->first();
                        }
                        return $project->projectCategory->name . ' | ' .$project->project_title .' | ' . $client->name . ' ||' . $project->project_code ;
                    }
                    else if ($data->investment_id  !=null) {
                        $investment= Investment::with('investor')->where('id',$data->investment_id)->first();

                        $investor = DB::table('investors')->where('id',$investment->investor_id)->first();

                        return $investment->transaction_title . ' | '.$investor->name ;
                        // return $data->transaction_title;
                    }
                    else {
                        return $data->transaction_title;
                    }
                })
                ->addColumn('credit', function ($data) {
                    if ($data->transaction_type == 2) {
                        return number_format($data->amount,2);
                    } else {
                        return '0';
                    }
                })
                ->addColumn('transaction_date', function ($transactions) {
                    return Carbon::parse($transactions->transaction_date)->format('d/m/Y');

                })
                ->addColumn('balance', function ($data) use (&$postBalance) {
                    if ($data->transaction_type == 1) {
                        $postBalance = $postBalance - $data->amount;
                        return number_format($postBalance,2);
                    } else {
                        $postBalance = $postBalance + $data->amount;
                        return number_format($postBalance,2);
                    }
                })
                ->with('prevBalance',$postBalance)
                ->rawColumns(['transaction_date','details', 'debit', 'credit', 'balance'])
                ->make(true);
        }
    }
    public function monthlyBalanceSheet(Request $request)
    {
        try {
            $month = DateTime::allMonths();
            $year = DateTime::getYear();
            $warehouses = InventoryWarehouse::get();
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $mystore= '';
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
            }
            return view('admin.account.balance_sheet.monthly_account_statement',compact('month','year','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function monthlyBalanceSheetData(Request $request)
    {
        if ($request->ajax()) {
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

            $auth = Auth::user();
            $user_role = $auth->roles->first();

                if($request->month_id !=null ){
                    $year = $request->year ? $request->year : 2023 ;
                    $monthNumber  = $request->month_id;
                    $start_date= Carbon::parse("01-$monthNumber-$year")->format('Y-m-d');
                    $end_date = date("Y-m-t", strtotime($start_date));

                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc');
                        if($request->warehouse_id){
                            $data= $data->where('warehouse_id',$request->warehouse_id);
                        }
                    }
                    else{
                        $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                        $employee=Employee::where('id',$user->user_id)->first();
                        $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$mystore->id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc');
                    }


                    $postBalance = AccountsRepository::previousBalance($start_date,$request->warehouse_id);
                }
                else if ($request->month_id ==null && $request->start_date &&  $request->end_date ){
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc');
                        if($request->warehouse_id){
                            $data= $data->where('warehouse_id',$request->warehouse_id);
                        }
                    }
                    else{
                        $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                        $employee=Employee::where('id',$user->user_id)->first();
                        $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$mystore->id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc');
                    }
                    $postBalance = AccountsRepository::previousBalance($start_date,$request->warehouse_id);
                }
                else{
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->orderBy('transaction_date', 'asc');
                                if($request->warehouse_id){
                                    $data= $data->where('warehouse_id',$request->warehouse_id);
                                }
                    }
                    else{
                        $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                        $employee=Employee::where('id',$user->user_id)->first();
                        $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$mystore->id)
                                ->orderBy('transaction_date', 'asc');
                    }
                    $postBalance = 0;
                }

            return DataTables::of($data,$postBalance)
                ->addIndexColumn()
                ->addColumn('purpose', function ($transactions) {
                    return TransactionRepository::type($transactions);
                })
                ->addColumn('transaction_date', function ($transactions) {
                    return Carbon::parse($transactions->transaction_date)->format('d/m/Y');

                })
                ->addColumn('debit', function ($data) {
                    if ($data->transaction_type == 1) {
                        return number_format($data->amount,2);
                    } else {
                        return '0';
                    }
                })
                ->addColumn('details', function ($data) {
                    if ($data->revenue_id !=null) {
                        $revenueDetails= RevenueDetails::with('revenueCategory')->where('revenue_id',$data->revenue_id)->first();
                        $cat= $revenueDetails->revenueCategory->name;
                        $revenue = Revenue::with('clientId')->findOrFail($data->revenue_id);
                        $client = '';
                         if(isset($revenue->clientId)){
                            $client= $revenue->clientId->name;
                         }
                        return $cat.' | '.$revenueDetails->description .' | '.$client . ' | '. $revenue->revenue_invoice_no. '<span class="badge bg-primary">Revenue</span>';
                    }
                    else if ($data->expense_id !=null) {
                        $expenseDetails= ExpenseDetails::with('expenseCategory')->where('expense_id',$data->expense_id)->first();

                       // $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                         $cat= $expenseDetails->expenseCategory->name;
                         $expense = '';
                         $expenseby = Expense::with('updatedBy')->findOrFail($data->expense_id);
                         if($expenseby->status ==2){
                            $expense= $expenseby->updatedBy->name;
                         }
                        return $cat.' | '.$expenseDetails->description . ' | '.$expense . ' | '. $expenseby->expense_invoice_no. '<span class="badge bg-primary">Expense</span>';
                    }
                    else if ($data->loan_id !=null) {
                        $expenseDetails= Loan::with('author')->where('id',$data->loan_id )->first();
                        return $expenseDetails->loan_title .' | ' .TransactionRepository::type($data) . ' | ' .$expenseDetails->author->name;
                    }
                    else if ($data->project_id !=null) {
                        $project= Projects::with('projectCategory')->where('id',$data->project_id)->first();
                        $client = '';
                        if($project->client_id){
                            $client = DB::table('clients')->where('id',$project->client_id)->first();
                        }
                        return $project->projectCategory->name . ' | ' .$project->project_title .' | '.$client->name . ' ||' . $project->project_code  ;
                    }
                    else if ($data->investment_id !=null) {
                        $investment= Investment::with('investor')->where('id',$data->investment_id)->first();

                        $investor = DB::table('investors')->where('id',$investment->investor_id)->first();

                        return $investment->transaction_title . ' | '.$investor->name . ' || ' .TransactionRepository::type($data);
                        // return $data->transaction_title;
                    }
                    else {
                        return $data->transaction_title;
                    }
                })
                ->addColumn('credit', function ($data) {
                    if ($data->transaction_type == 2) {
                        return number_format($data->amount,2);
                    } else {
                        return '0';
                    }
                })
                ->addColumn('balance', function ($data) use (&$postBalance) {
                    if ($data->transaction_type == 1) {
                        $postBalance = $postBalance - $data->amount;
                        return number_format($postBalance,2);
                    } else {
                        $postBalance = $postBalance + $data->amount;
                        return number_format($postBalance,2);
                    }
                })
                ->with('prevBalance',$postBalance)
                ->rawColumns(['transaction_date','details','purpose', 'debit', 'credit', 'balance'])
                ->make(true);
        }
    }
    public function monthlyBalanceSheetDataPrint(Request $request)
    {
        try {
            $monthName = '';
            $postBalance = '';
            $data = ' ';
            $purpose = [];
            $debit = [];
            $credit = [];
            $balance = [];
            $details = [];
            $totalDebit = '';
            $totalCredit = '';
            $totalBalance = '';
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if($request->month != null){

                $monthName = date('F', mktime(0, 0, 0, $request->month, 10));
                $year = $request->year ? $request->year : 2023 ;
                $monthNumber  = $request->month;
                $start_date= Carbon::parse("01-$monthNumber-$year")->format('Y-m-d');
                $end_date = date("Y-m-t", strtotime($start_date));

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                    if($request->warehouse_id){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$request->warehouse_id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                        }
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$mystore->id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                }
                $previousBalance = AccountsRepository::previousBalance($start_date,$request->warehouse_id);
                $postBalance = $previousBalance;
                foreach($data as $key=>$transaction )
                {
                    if ($transaction->revenue_id !=null) {
                        $revenueDetails= RevenueDetails::with('revenueCategory')->where('revenue_id',$transaction->revenue_id)->first();
                        $cat= $revenueDetails->revenueCategory->name;
                        $revenue = Revenue::with('clientId')->findOrFail($transaction->revenue_id);
                        $client = '';
                         if($revenue->clientId){
                            $client= $revenue->clientId->name;
                         }
                         $details[] = $cat.' | '.$revenueDetails->description .' | '.$client . ' | '. $revenue->revenue_invoice_no;
                    }
                    else if ($transaction->expense_id !=null) {
                        $expenseDetails= ExpenseDetails::with('expenseCategory')->where('expense_id',$transaction->expense_id)->first();

                       // $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                         $cat= $expenseDetails->expenseCategory->name;
                         $expense = '';
                         $expenseby = Expense::with('updatedBy')->findOrFail($transaction->expense_id);
                         if($expenseby->status ==2){
                            $expense= $expenseby->updatedBy->name;
                         }
                         $details[] = $cat.' | '.$expenseDetails->description . ' | '.$expense . ' | '. $expenseby->expense_invoice_no;
                    }
                    else if ($transaction->loan_id  !=null) {
                        $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                        $details[] = $expenseDetails->loan_title .' | ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name;
                    }
                    else if ($transaction->project_id !=null) {
                        // $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                        // $details[] = $expenseDetails->loan_title .' | ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name;
                        $project= Projects::with('projectCategory')->where('id',$transaction->project_id)->first();
                        $client = '';
                        if($project->client_id){
                            $client = DB::table('clients')->where('id',$project->client_id)->first();
                        }
                        $details[] =  $project->projectCategory->name . ' | ' .$project->project_title .' | '.$client->name . ' ||' . $project->project_code ;
                    }
                    else if ($transaction->investment_id !=null) {
                        $investment= Investment::with('investor')->where('id',$transaction->investment_id)->first();
                        $investor = DB::table('investors')->where('id',$investment->investor_id)->first();
                        $details[] = $investment->transaction_title . ' | '.$investor->name . ' || ' .TransactionRepository::type($transaction);
                    }
                    else {
                        $details[] = $transaction->transaction_title;
                    }
                }
                foreach($data as $key=>$item )
                {
                    if ($item->transaction_type == 1) {
                        $debit[] = $item->amount;
                    } else {
                        $debit[] ='0';
                    }
                }
                foreach($data as $key=>$item )
                {
                    if ($item->transaction_type == 2) {
                        $credit[] = $item->amount;
                    } else {
                        $credit[] ='0';
                    }
                }
                foreach($data as $key=>$item ){
                    if ($item->transaction_type == 1) {
                        $previousBalance =$previousBalance - $item->amount;
                        $balance[] =  $previousBalance;
                    } else {
                        $previousBalance = $previousBalance + $item->amount;
                        $balance[] =  $previousBalance;
                    }
                }
            }
            else if ($request->month_id == null && $request->start_date &&  $request->end_date ){

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                    if($request->warehouse_id){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$request->warehouse_id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                    }
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();

                    $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$mystore->id)
                                ->where('transaction_date', '>=', $start_date)
                                ->where('transaction_date', '<=', $end_date)
                                ->orderBy('transaction_date', 'asc')->get();
                }
                        $monthName = Carbon::parse($start_date)->format('d/M/Y')  . '  To  ' .Carbon::parse($end_date)->format('d/M/Y');
                        $year = '';
                        $previousBalance = AccountsRepository::previousBalance($start_date,$request->warehouse_id);
                        $postBalance = $previousBalance;
                       foreach($data as $key=>$transaction )
                        {
                            if ($transaction->revenue_id !=null) {
                                $revenueDetails= RevenueDetails::with('revenueCategory')->where('revenue_id',$transaction->revenue_id)->first();
                                $cat= $revenueDetails->revenueCategory->name;
                                $revenue = Revenue::with('clientId')->findOrFail($transaction->revenue_id);
                                $client = '';
                                if($revenue->clientId){
                                    $client= $revenue->clientId->name;
                                }
                                $details[] = $cat.' | '.$revenueDetails->description .' | '.$client . ' | '. $revenue->revenue_invoice_no;
                            }
                            else if ($transaction->expense_id !=null) {
                                $expenseDetails= ExpenseDetails::with('expenseCategory')->where('expense_id',$transaction->expense_id)->first();

                            // $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                                $cat= $expenseDetails->expenseCategory->name;
                                $expense = '';
                                $expenseby = Expense::with('updatedBy')->findOrFail($transaction->expense_id);
                                if($expenseby->status ==2){
                                    $expense= $expenseby->updatedBy->name;
                                }
                                $details[] = $cat.' | '.$expenseDetails->description . ' | '.$expense . ' | '. $expenseby->expense_invoice_no;
                            }
                            else if ($transaction->loan_id  !=null) {
                                $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                                $details[] = $expenseDetails->loan_title .' | ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name;
                            }
                            else if ($transaction->project_id !=null) {
                                // $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                                // $details[] = $expenseDetails->loan_title .' | ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name;
                                $project= Projects::with('projectCategory')->where('id',$transaction->project_id)->first();
                                $client = '';
                                if($project->client_id){
                                    $client = DB::table('clients')->where('id',$project->client_id)->first();
                                }

                                $details[] =  $project->projectCategory->name . ' | ' .$project->project_title .' | '.$client->name . ' ||' . $project->project_code ;
                            }

                            else if ($transaction->investment_id !=null) {
                                //\
                                $investment= Investment::with('investor')->where('id',$transaction->investment_id)->first();

                                $investor = DB::table('investors')->where('id',$investment->investor_id)->first();

                                $details[] = $investment->transaction_title . ' | '.$investor->name . ' || ' .TransactionRepository::type($transaction);

                            }
                            else {
                                $details[] = $transaction->transaction_title;
                            }
                        }
                        foreach($data as $key=>$item )
                        {
                            if ($item->transaction_type == 1) {
                                $debit[] = $item->amount;
                            } else {
                                $debit[] ='0';
                            }
                        }
                        foreach($data as $key=>$item )
                        {
                            if ($item->transaction_type == 2) {
                                $credit[] = $item->amount;
                            } else {
                                $credit[] ='0';
                            }
                        }
                        foreach($data as $key=>$item ){
                            if ($item->transaction_type == 1) {
                                $previousBalance = $previousBalance - $item->amount;
                                $balance[] =  $previousBalance;
                            } else {
                                $previousBalance = $previousBalance + $item->amount;
                                $balance[] =  $previousBalance;
                            }
                        }
            }
            else{
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $data = Transaction::with('bankAccount')
                            ->where('status',1)
                            ->orderBy('transaction_date', 'asc')
                            ->get();
                    if($request->warehouse_id){
                        $data = Transaction::with('bankAccount')
                                ->where('status',1)
                                ->where('warehouse_id',$request->warehouse_id)
                                ->orderBy('transaction_date', 'asc')
                                ->get();
                    }
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();

                    $data = Transaction::with('bankAccount')
                            ->where('status',1)
                            ->where('warehouse_id',$mystore->id)
                            ->orderBy('transaction_date', 'asc')
                            ->get();
                }

                $year = Carbon::now()->format('Y');
                $previousBalance = 0;
                $postBalance = 0;
                foreach($data as $key=>$transaction )
                {
                    if ($transaction->revenue_id !=null) {
                        $revenueDetails= RevenueDetails::with('revenueCategory')->where('revenue_id',$transaction->revenue_id)->first();
                        $cat= $revenueDetails->revenueCategory->name;
                        $revenue = Revenue::with('clientId')->findOrFail($transaction->revenue_id);
                        $client = '';
                         if($revenue->clientId){
                            $client= $revenue->clientId->name;
                         }
                         $details[] = $cat.' | '.$revenueDetails->description .' | '.$client . ' | '. $revenue->revenue_invoice_no;
                    }
                    else if ($transaction->expense_id !=null) {
                        $expenseDetails= ExpenseDetails::with('expenseCategory')->where('expense_id',$transaction->expense_id)->first();

                       // $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                         $cat= $expenseDetails->expenseCategory->name;
                         $expense = '';
                         $expenseby = Expense::with('updatedBy')->findOrFail($transaction->expense_id);
                         if($expenseby->status ==2){
                            $expense= $expenseby->updatedBy->name;
                         }
                         $details[] = $cat.' | '.$expenseDetails->description . ' | '.$expense . ' | '. $expenseby->expense_invoice_no;
                    }
                    else if ($transaction->loan_id  !=null) {
                        $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                        $details[] = $expenseDetails->loan_title . ' || ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name ;

                    }
                    else if ($transaction->project_id !=null) {
                        // $expenseDetails= Loan::with('author')->where('id',$transaction->loan_id )->first();
                        // $details[] = $expenseDetails->loan_title .' | ' .TransactionRepository::type($transaction) .' || ' .$expenseDetails->author->name;
                        $project= Projects::with('projectCategory')->where('id',$transaction->project_id)->first();

                        $client = '';
                        if($project->client_id){
                            $client = DB::table('clients')->where('id',$project->client_id)->first();
                        }

                        $details[] =  $project->projectCategory->name . ' | ' .$project->project_title .' | '.$client->name . ' ||' . $project->project_code ;
                    }
                    else if ($transaction->investment_id !=null) {
                        $investment= Investment::with('investor')->where('id',$transaction->investment_id)->first();

                        $investor = DB::table('investors')->where('id',$investment->investor_id)->first();

                        $details[] = $investment->transaction_title . ' | '.$investor->name . ' || ' .TransactionRepository::type($transaction);
                        // return $data->transaction_title;
                    }
                    else {
                        $details[] = $transaction->transaction_title;
                    }
                }
                //
                foreach($data as $key=>$item )
                {
                    if ($item->transaction_type == 1) {
                        $debit[] = $item->amount;
                    } else {
                        $debit[] ='0';
                    }
                }
                foreach($data as $key=>$item )
                {
                    if ($item->transaction_type == 2) {
                        $credit[] = $item->amount;
                    } else {
                        $credit[] ='0';
                    }
                }


                foreach($data as $key=>$item ){
                    if ($item->transaction_type == 1) {
                        $previousBalance = $previousBalance - $item->amount;
                        $balance[] =  $previousBalance;
                    } else {
                        $previousBalance = $previousBalance + $item->amount;
                        $balance[] =  $previousBalance;
                    }
                }

            }
            $totalDebit =array_sum($debit);
            $totalCredit =array_sum($credit);
            $totalBalance =$totalCredit-$totalDebit+$postBalance;

            return view('admin.account.balance_sheet.print_statement',compact('data','postBalance','monthName','purpose','debit','credit','balance','previousBalance','details','year','totalBalance','totalDebit','totalCredit','start_date'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function statementBankAccountSearch(Request $request)
   {
       $result = BankAccount::query()
                ->where('warehouse', $request->warehouse_id)
                ->where('name', 'LIKE', "%{$request->search}%")
                ->get();
       return $result;
   }
}
