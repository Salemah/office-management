<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Documents;
use App\Models\Employee\Employee;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseDetails;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Settings\DashboardSetting;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\Admin\Expense\ExpenseRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
                    $expenses = Expense::with('expenseBy','warehouse')->orderBy('expense_invoice_date','desc');
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $expenses = Expense::with('expenseBy','warehouse')->where('warehouse_id',$mystore->id)->orderBy('expense_invoice_date','desc');
                }

                $postBalance = number_format(AccountsRepository::expenseBalance(5),2);
                return DataTables::of($expenses,$postBalance)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($expenses) {
                        if ($expenses->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }
                    })
                    ->addColumn('expenseBy', function ($expenses) {
                        if($expenses->expenseBy)
                       {
                        return $expenses->expenseBy->name;
                       }
                       else{
                         return 'Admin';
                       }
                    })
                    ->addColumn('expense_invoice_date', function ($expenses) {
                        return Carbon::parse($expenses->expense_invoice_date)->format('d/m/Y');
                    })
                    ->addColumn('amount', function ($expenses) {
                        return number_format($expenses->total,2);
                    })
                    ->addColumn('warehouse', function ($expenses) {
                        return $expenses->warehouse->name ?? '--';
                    })
                    ->addColumn('status', function ($expenses) {
                        if ($expenses->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $expenses->id . ')"
                             class="btn btn-sm btn-warning">Pending</button>';
                        } else  {
                            return '<button
                            onclick="showStatusChangeAlert(' . $expenses->id . ')"
                            class="btn btn-sm btn-success">Approve</button>';
                        }
                    })
                    ->addColumn('description', function ($expenses) {
                        $details =  ExpenseDetails::with('expenseCategory')->where('expense_id',$expenses->id)->first();
                         $cat= $details->expenseCategory->name;
                        return $cat .' || ' . $details->description . ' || '.$expenses->expense_invoice_no;
                    })
                    ->addColumn('action', function ($expenses) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                                    href="' . route('admin.expense.expense.show', $expenses->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a href="' . route('admin.expense.expense.edit', $expenses->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                    <a href="' . route('admin.expense.print.invoice', $expenses->id) . '" class="btn btn-sm btn-dark text-white" style="cursor:pointer" title="Print Bill"><i class="bx bx-printer"></i></a>
                                    <a href="' . route('admin.expense.print.voucher', $expenses->id) . '" class="btn btn-sm btn-info text-white" style="cursor:pointer" title="Print Voucher"><i class="bx bx-printer"></i></a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $expenses->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->with('prevBalance',$postBalance)
                    ->rawColumns(['warehouse','amount','expense_invoice_date','expenseBy','status', 'transaction_way','description', 'action'])
                    ->make(true);
            }
            return view('admin.expense.expense_invoice.index');
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
            $expenseCategorys = ExpenseCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $employees = Employee::all();
            $warehouses = InventoryWarehouse::get();
            $expense =DB::table('expenses')->get();
            $serial = count($expense) + 1;

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
            return view('admin.expense.expense_invoice.create', compact('expenseCategorys', 'bankAccounts', 'employees', 'serial','cash_account','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
            'invoice_date' => 'required',
            'expense_invoice_no' => 'required|unique:expenses,expense_invoice_no',
            'expense_date.*' => 'required',
            'expense_by_id' => 'required',
            'expense_categorie_id.*' => 'required',
            'amount.*' => 'required',
            'total_balance' => 'required',
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
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $expense = new Expense();
            $expense->adjustment_balance = $request->adjustment_balance;
            $expense->expense_invoice_no = $request->expense_invoice_no;
            $expense->vat_type = $request->vat_type;
            $expense->vat_rate = $request->vat_rate;
            $expense->warehouse_id = $request->warehouse_id;
            $expense->expense_invoice_date = $request->invoice_date;
            $expense->expense_by = $request->expense_by_id;
            $expense->adjustment_type = $request->adjustment_type;
            $expense->total = $request->total_balance;
            $expense->transaction_way = $request->transaction_way;
            $expense->status = 1;
            $expense->description = $request->note;
            $expense->created_by = Auth::user()->id;
            $expense->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $expense->save();

            //Expense Details
            $con = count($request->amount);
            for ($i = 0; $i < $con; $i++) {
                $expense_details = new ExpenseDetails();
                $expense_details->expense_id = $expense->id;
                $expense_details->expense_category = $request->expense_categorie_id[$i];
                $expense_details->expense_date = $request->expense_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];
                $expense_details->created_by = Auth::user()->id;
                $expense_details->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $expense_details->save();
            }
            //file
            if (isset($request->documents)){
                foreach ($request->file('documents') as $key => $image) {
                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/expense/documents', $name);

                    $documents = new Documents();
                    $documents->document_id = $expense->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 3; //document_type 3 == expense
                    $documents->created_by = Auth::user()->id;
                    $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                    $documents->save();
                }
            }
            //Transaction
            if ($request->transaction_way) {
                $transaction = new Transaction();
                ExpenseRepository::transaction($transaction, $request, $expense->id);
            }
            DB::commit();
            return redirect()->route('admin.expense.expense.index')->with('message', 'Expense  Successfully');
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
    public function show($id)
    {
        $expenseInvoice = Expense::with('expenseBy', 'createdBy')->findOrFail($id);

        $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();

        $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
        $transaction = Transaction::where('expense_id', $id)->with('bankAccount')->first();
        $bank = '';

        if ($transaction) {
            if ($transaction->bankAccount->transaction_account_type	 == 2) {
                $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
            }
        }
        $dashboard_settings = DashboardSetting::first();
        $totalString =$this->convert_number_to_words($expenseInvoice->total) ;

        return view('admin.expense.expense_invoice.show', compact('expense_details', 'expenseInvoice', 'totalBalance', 'transaction', 'bank','dashboard_settings','totalString'));
    }
    public function printInvoice(Request $request,$id)
    {
        try {
            $expenseInvoice = Expense::with('expenseBy', 'createdBy')->findOrFail($id);

            $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();

            $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $transaction = Transaction::where('expense_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }
            $dashboard_settings = DashboardSetting::first();
            $totalString =$this->convert_number_to_words($expenseInvoice->total) ;

            return view('admin.expense.expense_invoice.invoice', compact('expense_details', 'expenseInvoice', 'totalBalance', 'transaction', 'bank','dashboard_settings','totalString'));
        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }
    public function printVoucher(Request $request,$id)
    {
        try {
            $expenseInvoice = Expense::with('expenseBy', 'createdBy')->findOrFail($id);

            $expense_details = ExpenseDetails::where('expense_id', $id)->with('expenseCategory')->get();

            $totalBalance = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $transaction = Transaction::where('expense_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }
            $dashboard_settings = DashboardSetting::first();
            $totalString =$this->convert_number_to_words($expenseInvoice->total) ;

            return view('admin.expense.expense_invoice.voucher', compact('expense_details', 'expenseInvoice', 'totalBalance', 'transaction', 'bank','dashboard_settings','totalString'));
        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
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
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $expense = Expense::where('id', $id)->with('createdBy')->first();
            $expenseCategorys = ExpenseCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $employees = Employee::all();
            $expense_details = ExpenseDetails::where('expense_id', $id)->get();
            $transaction = Transaction::where('expense_id', $id)->first();
            $total = ExpenseDetails::where('expense_id', $id)->sum('amount');
            $availableBalance = '';
            if ($transaction) {
                $availableBalance = AccountsRepository::postBalance($transaction->account_id);
            }
            $documents = Documents::where('document_id', $id)
                ->where('document_type', 3)->get();


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

            return view('admin.expense.expense_invoice.edit', compact('bankAccounts', 'expenseCategorys', 'expense', 'employees', 'expense_details', 'transaction', 'total', 'availableBalance', 'documents','cash_account','mystore','warehouses'));

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
            'invoice_date' => 'required',
            'expense_invoice_no' => 'required',
            'expense_date' => 'required',
            'expense_by_id' => 'required',
            'expense_categorie_id' => 'required',
            'amount' => 'required',
            'total_balance' => 'required',
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
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);
                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $expense = Expense::findOrFail($id);
            $expense->adjustment_balance = $request->adjustment_balance;
            $expense->expense_invoice_no = $request->expense_invoice_no;
            $expense->vat_type = $request->vat_type;
            $expense->vat_rate = $request->vat_rate;
            $expense->warehouse_id = $request->warehouse_id;
            $expense->expense_invoice_date = $request->invoice_date;
            $expense->expense_by = $request->expense_by_id;
            $expense->adjustment_type = $request->adjustment_type;
            $expense->total = $request->total_balance;
            $expense->transaction_way = $request->transaction_way;
            $expense->description = $request->note;
            $expense->updated_by = Auth::user()->id;

            $expense->update();

            $old_documents = Documents::where('document_type', 3)
                ->where('document_id', $id)
                ->pluck('id')->toArray();

            if ($request->expense_document_id) {
                $result = array_diff($old_documents, $request->expense_document_id);
                if ($result) {
                    Documents::whereIn('id', $result)->delete();
                }
                foreach ($request->expense_document_id as $key => $document_id) {
                    $doc = Documents::findOrFail($document_id);
                    $doc->document_name = $request->document_title[$key];
                    $doc->update();
                }
            } else {
                Documents::whereIn('id', $old_documents)->delete();
            }

            if ($request->hasfile('documents')) {
                foreach ($request->file('documents') as $key => $image) {
                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/expense/documents', $name);
                    $documents = new Documents();
                    $documents->document_id = $expense->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 3; //document_type 3 == expense
                    $expense->updated_by = Auth::user()->id;
                    $documents->save();
                }
            }

            if ($request->transaction_way) {
                $transaction = Transaction::where('expense_id', $id)->first();
                if ($transaction) {
                    $transaction->transaction_title = $request->description[0];
                    $transaction->transaction_date = $request->invoice_date;

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
                    }
                    else if($request->transaction_way == 1){
                        $transaction->account_id = $request->cash_account_id;
                        $transaction->transaction_account_type = 1;
                    }
                    $transaction->transaction_purpose = 5;
                    $transaction->transaction_type = 1;
                    $transaction->amount = $request->total_balance;
                    $transaction->expense_id = $expense->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    if($transaction->created_by){
                        $transaction->updated_by = Auth::user()->id;
                        $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                    }else{
                        $transaction->created_by= Auth::user()->id;
                    }

                    $transaction->update();
                } else {
                    $transaction = new Transaction();
                    $transaction->transaction_title = $request->description[0];
                    $transaction->transaction_date = $request->invoice_date;


                    if ($request->transaction_way == 2) {
                        $transaction->account_id = $request->account_id;
                        $transaction->transaction_account_type = 2;
                    }
                    else if($request->transaction_way == 1){
                        $transaction->account_id = $request->cash_account_id;
                        $transaction->transaction_account_type = 1;
                    }
                    $transaction->transaction_purpose = 5;
                    $transaction->transaction_type = 1;
                    $transaction->amount = $request->total_balance;
                    $transaction->expense_id = $expense->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    if($transaction->created_by){
                        $transaction->updated_by = Auth::user()->id;
                        $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                    }else{
                        $transaction->created_by= Auth::user()->id;
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
                    $transaction->save();
                }
            } else {
                $transaction = Transaction::where('expense_id', $id)->first();
                if ($transaction) {
                    $transaction->delete();
                }
            }

            $expense_details = ExpenseDetails::where('expense_id', $id)->get();
            foreach ($expense_details as $key => $expenseDetails) {
                $expenseDetails->delete();
            }

            $con = count($request->amount);
            $i = 0;
            for ($i = 0; $i < $con; $i++) {
                $expense_details = new ExpenseDetails();
                $expense_details->expense_id = $expense->id;
                $expense_details->document = $request->document;
                $expense_details->expense_category = $request->expense_categorie_id[$i];
                $expense_details->expense_date = $request->expense_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];
                $expense_details->updated_by = Auth::user()->id;
                $expense_details->save();

            }
            DB::commit();
            return redirect()->route('admin.expense.expense.index')->with('message', 'Expense Update Successfully');
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
                $expense = Expense::where('id', $id)->first();
                $transaction = Transaction::where('expense_id', $expense->id)
                    ->first();

                $expense_details = ExpenseDetails::where('expense_id', $expense->id)
                    ->get();

                foreach ($expense_details as $key => $expenses) {
                    $expenses->delete();
                }

                $expense->delete();
                if ($transaction) {
                    $transaction->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Expense Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function employeeSearch(Request $request)
    {
        $result = Employee::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }
    //starts status change function
   public function statusUpdate(Request $request)
   {
       try {
           $reference = Expense::findOrFail($request->id);


           if($reference->status == 1)
           {
            $reference->status = 2;
            $transaction =Transaction ::where('expense_id',$request->id)->first();
            $transaction->status = 1;
            $transaction->updated_by = Auth::user()->id;
            $transaction->update();
           }
           else{
            $reference->status = 1;
            $transaction =Transaction ::where('expense_id',$request->id)->first();
            $transaction->status = 0;
            $transaction->updated_by = Auth::user()->id;
            $transaction->update();

           }


           $reference->updated_by = Auth::user()->id;
           $reference->update();
           if ($reference->status == 2) {
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
