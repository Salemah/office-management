<?php

namespace App\Http\Controllers\Admin\Account\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
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
                // whereIn('transaction_purpose', [0,1, 2])->
                $auth = Auth::user();
                $user_role = $auth->roles->first();

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $transactions = Transaction::whereNotIn('transaction_purpose', [0])->where('status',1)->latest()->with(['bankAccount','createdByUser'])->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $transactions = Transaction::whereNotIn('transaction_purpose', [0])->where('status',1)->latest()->where('warehouse_id',$mystore->id)->with(['bankAccount','createdByUser'])->get();
                }

                return DataTables::of($transactions)
                    ->addIndexColumn()
                    ->addColumn('bankinfo', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return $transactions->bankAccount->bank->bank_name . ' | ' . $transactions->bankAccount->account_number . '<span style="color:#6B84F5">('.$transactions->bankAccount->name.') </span>';
                        }
                        else{
                            return " <b>Cash</b>";
                        }
                    })
                    ->addColumn('transaction_way', function ($transactions) {
                        if($transactions->transaction_account_type == 2){
                            return 'Bank';
                        }
                        else{
                            return "<b>Cash</b>";
                        }
                    })
                    ->addColumn('createdByUser', function ($transactions) {
                        return $transactions->createdByUser;
                    })
                    ->addColumn('transaction_date', function ($transactions) {
                        return Carbon::parse($transactions->transaction_date)->format('d M, Y');
                    })
                    ->addColumn('purpose', function ($transactions) {
                        if ($transactions->transaction_purpose == 1) {
                            return '<span class="text-danger text-bold"><b>Withdraw - </b></span>';
                        } else if ($transactions->transaction_purpose == 2) {
                            return '<span class="text-success text-bold"><b>Deposit -</b></span>';
                        } else if ($transactions->transaction_purpose == 3) {
                            return '<span class="text-success text-bold"><b>Revenue - </b></span>';
                        } else if ($transactions->transaction_purpose == 4) {
                            return '<span class="text-success text-bold"><b>Given Payment - </b></span>';
                        } else if ($transactions->transaction_purpose == 5) {
                            return '<span class="text-danger text-bold"><b>Expense -</b></span>';
                        } else if ($transactions->transaction_purpose == 6) {
                            return '<span class="text-success text-bold"><b>Fund-Transfer - </b>(Cash-In)</span>';
                        } else if ($transactions->transaction_purpose == 7) {
                            return '<span class="text-danger text-bold"><b>  Fund-Transfer - (Cash-Out) </b></span>';
                        } else if ($transactions->transaction_purpose == 8) {
                            return '<span class="text-success text-bold"><b>Cash In -</b></span>';
                        } else if ($transactions->transaction_purpose == 9) {
                            return '<span class="text-success text-bold"><b>Investment - </b></span>';
                        } else if ($transactions->transaction_purpose == 10) {
                            return '<span class="text-success text-bold"><b>Investment return - </b></span>';
                        } else if ($transactions->transaction_purpose == 11) {
                            return '<span class="text-success text-bold"><b>Investment profit retrun - </b></span>';
                        } else if ($transactions->transaction_purpose == 12) {
                            return '<span class="text-success text-bold"><b>Giving loan - </b></span>';
                        } else if ($transactions->transaction_purpose == 13) {
                            return '<span class="text-success text-bold"><b>Taking loan - </b></span>';
                        } else if ($transactions->transaction_purpose == 14) {
                            return '<span class="text-success text-bold"><b>Return loan (Giving) - </b></span>';
                        } else if ($transactions->transaction_purpose == 15) {
                            return '<span class="text-success text-bold"></b>Return loan (Taking) - </b></span>';
                        } else {
                            return '...';
                        }
                    })

                    ->addColumn('action', function ($transactions) {
                        $id = '';
                        if($transactions->expense_id && $transactions->transaction_account_type == 2){
                            $id = $transactions->expense_id;
                           $show = '<a class="btn btn-sm btn-primary text-white " href="' . route('admin.expense.expense.show', $id) . '"title="Show"><i class="bx bx-show"></i> </a>';
                        }
                        else if($transactions->revenue_id && $transactions->transaction_account_type == 2){
                            $id = $transactions->revenue_id;
                            $show = '<a class="btn btn-sm btn-primary text-white " href="' . route('admin.revenue.show', $id) . '"title="Show"><i class="bx bx-show"></i> </a>';
                        }
                        else if($transactions->expense_id ) {
                            $id = $transactions->expense_id;
                            $show = '<a class="btn btn-sm btn-primary text-white " href="' . route('admin.expense.expense.show', $id) . '"title="Show"><i class="bx bx-show"></i></a>';

                        }
                        else if($transactions->revenue_id ){
                            $id = $transactions->revenue_id;
                            $show = '<a class="btn btn-sm btn-primary text-white " href="' . route('admin.revenue.show', $id) . '"title="Show"><i class="bx bx-show"></i></a>';
                        }
                        else if($transactions->loan_id ){
                            $id = $transactions->loan_id;
                            $show = '<a class="btn btn-sm btn-primary text-white " href=""title="Show"><i class="bx bx-show"></i></a>';
                        }
                        else{
                            $show = '<a class="btn btn-sm btn-primary text-white " href=""title="Show"><i class="bx bx-show"></i></a>';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$show.'</div>';
                    })
                    ->rawColumns(['action','transaction_way','createdByUser', 'bankinfo', 'purpose','transaction_date'])
                    ->make(true);
            }
            return view('admin.account.transaction.index');
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
            // Get All Bank
            $bank_accounts = BankAccount::where('status', 1)->get();
            return view('admin.account.transaction.create',compact('bank_accounts'));
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
            'transaction_purpose' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = new Transaction();
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = $request->account_id;
            $transaction->transaction_purpose = $request->transaction_purpose;
            if ($request->transaction_purpose == 2) {
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_type = 1;
                // Balance Check
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =strip_tags($request->description);
            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $transaction->save();

           return redirect()->route('admin.account.transaction.index')->with('message', 'Transaction Successfully.');
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
        try {
            // Get All Bank
            $transaction = Transaction::where('id',$id)->with(['bankAccount','createdByUser'])->first();

            if ($transaction->transaction_purpose == 1) {
                $transaction_purpose = 'Withdraw';
            } elseif ($transaction->transaction_purpose == 2) {
                $transaction_purpose = 'Deposit';
            }
            if($transaction->account_id == 0){
                $transaction_way = "Cash";
            }
            else{
                $transaction_way = "Bank";
            }
            return view('admin.account.transaction.show',compact('transaction','transaction_purpose','transaction_way'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
            $bank_accounts = BankAccount::where('status', 1)->get();
            $transaction = Transaction::where('id', $id)->first();
           return view('admin.account.transaction.edit',compact('transaction','bank_accounts'));
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
            'transaction_purpose' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        try {
            //  Transaction Store
            $transaction = Transaction::findOrFail($id);
            $transaction->transaction_title = $request->transaction_title;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->account_id = $request->account_id;
            $transaction->transaction_purpose = $request->transaction_purpose;
            if ($request->transaction_purpose == 2) {
                $transaction->transaction_type = 2;
            } else {
                $transaction->transaction_type = 1;
                // Balance Check
            }
            $transaction->amount = $request->amount;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->description =strip_tags($request->description);
            $transaction->updated_by = Auth::user()->id;

            $transaction->update();

           return redirect()->route('admin.account.transaction.index')->with('message', 'Transaction Update successfully.');
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
                    'message' => 'Transaction Deleted Successfully.',
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

           $reference=Transaction::findOrFail($request->id);
           $reference->status == 1 ? $reference->status = 0 : $reference->status = 1;

           $reference->update();
           if ($reference->status == 1) {
               return "active";
               // exit();
           } else {
               return "inactive";
           }
       }
       catch (\Exception $exception) {
           return  $exception->getMessage();
       }
   }


}
