<?php

namespace App\Http\Controllers\Admin\Account\Withdraw;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
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
                    $transactions = Transaction::whereIn('transaction_purpose', [1])->latest()->with(['bankAccount','createdByUser'])->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $transactions = Transaction::whereIn('transaction_purpose', [1])->where('warehouse_id',$mystore->id)->latest()->with(['bankAccount','createdByUser'])->get();
                    //$transactions = Transaction::whereNotIn('transaction_purpose', [0])->where('status',1)->latest()->where('warehouse_id',$mystore->id)->with(['bankAccount','createdByUser'])->get();
                }

                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return $transactions->bankAccount->account_number . ' | ' . $transactions->bankAccount->name;
                        }
                        else{
                            return $transactions->bankAccount->account_number . ' | ' . $transactions->bankAccount->name;
                        }
                    })
                    ->addColumn('transaction_way', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return '<span class="badge rounded-pill bg-primary">Bank</span>';
                        }
                        else{
                            return '<span class="badge rounded-pill bg-info ">Cash</span>';
                        }
                    })
                    ->addColumn('createdByUser', function ($transactions) {
                        return $transactions->createdByUser->name;
                    })
                    ->addColumn('action', function ($transactions) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.account.withdraw.edit', $transactions->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showTransactionDeleteConfirm(' . $transactions->id . ')" title="Delete"><i class="bx bxs-trash"></i></a> </div>';
                    })
                    ->rawColumns(['action','transaction_way','createdByUser', 'bankinfo'])
                    ->make(true);
            }
            return view('admin.account.withdraw.index');
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
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bank_accounts = BankAccount::where('status', 1)->where('type',2)->get();

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
            return view('admin.account.withdraw.create',compact('bank_accounts','cash_account','warehouses','mystore'));
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
            'transaction_way' => 'required',
            'amount' => 'required',
        ]);
        if($request->transaction_way == 2)//account 1 == cash ,2 == bank
        {
            $request->validate([
            'account_id' => 'required',
             ]);
        }
        else if($request->transaction_way == 1)
        {
            $request->validate([
            'cash_account_id' => 'required',
             ]);
        }
        try {
            if ($request->transaction_way){
                if ($request->transaction_way == 2) {
                    $balance = AccountsRepository::postBalance($request->account_id);
                }else{
                    $balance = AccountsRepository::postBalance($request->cash_account_id);
                }
                $balance = $balance - $request->amount;
                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;
            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
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
            $transaction->transaction_purpose = 1;
            $transaction->transaction_type = 1;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->description;
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

           return redirect()->route('admin.account.withdraw.index')->with('message', 'Withdraw Successfull.');
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
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bank_accounts = BankAccount::where('status', 1)->where('type',2)->get();
            $transaction = Transaction::where('id', $id)->first();
            $accountBalance = AccountsRepository::postBalance($transaction->account_id);
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
            return view('admin.account.withdraw.edit',compact('transaction','bank_accounts','accountBalance','cash_account','warehouses','mystore'));
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
        $request->validate([
            'transaction_title' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_way' => 'required',
            'amount' => 'required',
        ]);
        if($request->transaction_way == 2)//account 1 == cash ,2 == bank
        {
            $request->validate([
            'account_id' => 'required',
             ]);
        }
        else if($request->transaction_way == 1)
        {
            $request->validate([
            'cash_account_id' => 'required',
             ]);
        }
        try {
            if ($request->transaction_way){
                if ($request->transaction_way == 2) {
                    $balance = AccountsRepository::postBalance($request->account_id);
                }else{
                    $balance = AccountsRepository::postBalance($request->cash_account_id);
                }
                $balance = $balance - $request->amount;
                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;
            } else {
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
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
            $transaction->transaction_purpose = 1;
            $transaction->transaction_type = 1;
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description = $request->description;
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

           return redirect()->route('admin.account.withdraw.index')->with('message', 'Update successfully.');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
                Transaction::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Withdraw Deleted Successfully.',
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
}
