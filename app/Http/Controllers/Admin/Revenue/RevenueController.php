<?php

namespace App\Http\Controllers\Admin\Revenue;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\CRM\Client\Client;
use App\Models\Documents;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Revenue\Revenue;
use App\Models\Revenue\RevenueCategory;
use App\Models\Revenue\RevenueDetails;
use App\Models\Settings\DashboardSetting;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevenueController extends Controller
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
                    $revenue = Revenue::with('createdBy','clientId', 'revenueBy','warehouse')->orderBy('revenue_invoice_date','desc');
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $revenue = Revenue::with('createdBy','clientId', 'revenueBy','warehouse')->where('warehouse_id',$mystore->id)->orderBy('revenue_invoice_date','desc');
                }

                $postBalance = number_format(AccountsRepository::revenueBalance(3),2);
                return DataTables::of($revenue,$postBalance)
                    ->addIndexColumn()
                    ->addColumn('transaction_way', function ($revenue) {
                        if ($revenue->transaction_way == 1) {
                            return '<span class="text-success">Cash</span>';
                        } else {
                            return '<span class="text-info">Bank</span>';
                        }
                    })
                    ->addColumn('revenueBy', function ($revenue) {
                        if($revenue->revenueBy){
                            return $revenue->revenueBy->name;
                        }
                         return '--';

                    })
                    ->addColumn('warehouse', function ($revenue) {
                        if($revenue->warehouse){
                            return $revenue->warehouse->name;
                        }
                         return '--';

                    })
                    ->addColumn('amount', function ($revenue) {
                        return number_format($revenue->total,2);

                    })
                    ->addColumn('status', function ($revenue) {
                        if ($revenue->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $revenue->id . ')"
                             class="btn btn-sm btn-warning ">Pending</button>';
                        } else  {
                            return '<button
                            onclick="showStatusChangeAlert(' . $revenue->id . ')"
                            class="btn btn-sm btn-success text-light">Approve</button>';
                        }
                    })
                    ->addColumn('revenue_date', function ($revenue) {
                            return Carbon::parse($revenue->revenue_invoice_date)->format('d/m/Y');
                    })
                    ->addColumn('description', function ($revenue) {
                        $details =  RevenueDetails::with('revenueCategory')->where('revenue_id',$revenue->id)->first();
                         $cat= $details->revenueCategory->name;
                         $client = '';
                         if($revenue->clientId){
                            $client= $revenue->clientId->name;
                         }

                        return $cat .' || ' . $details->description . ' || '. $client .' || '.$revenue->revenue_invoice_no;
                    })
                    ->addColumn('action', function ($revenue) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                                href="' . route('admin.revenue.show', $revenue->id) . '"><i class="bx bx-show"> </i> </a>
                                <a href="' . route('admin.revenue.edit', $revenue->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                <a href="' . route('admin.revenue.print.invoice', $revenue->id) . '" class="btn btn-sm btn-dark text-white" style="cursor:pointer" title="Bill Print"><i class="bx bx-printer"></i></a>
                                <a href="' . route('admin.revenue.print.voucher', $revenue->id) . '" class="btn btn-sm btn-info text-white" style="cursor:pointer" title="Voucher Print"><i class="bx bx-printer"></i></a>
                                <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $revenue->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->with('prevBalance',$postBalance)
                    ->rawColumns(['warehouse','amount','revenue_date','transaction_way','status', 'action', 'revenueBy', 'description'])
                    ->make(true);
            }
            return view('admin.revenue.revenue_invoice.index');
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
            $revenueCategorys = RevenueCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $serial = DB::table('revenues')->count()+1;

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
            return view('admin.revenue.revenue_invoice.create', compact('revenueCategorys', 'bankAccounts', 'serial','cash_account','warehouses','mystore'));
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
            'revenue_invoice_no' => 'required|unique:revenues,revenue_invoice_no',
            'revenue_date' => 'required',
            'transaction_way' => 'required',
            // 'revenue_by_id' => 'required',
            'revenue_categorie_id' => 'required',
            'description' => 'required',
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

            $revenue = new Revenue();

            $revenue->adjustment_balance = $request->adjustment_balance;
            $revenue->revenue_invoice_date = $request->invoice_date;
            $revenue->vat_type = $request->vat_type;
            $revenue->vat_rate = $request->vat_rate;
            $revenue->revenue_invoice_no = $request->revenue_invoice_no;
            $revenue->revenue_by = $request->revenue_by_id;
            $revenue->client = $request->client;
            $revenue->status = 1;
            $revenue->warehouse_id = $request->warehouse_id;
            $revenue->adjustment_type = $request->adjustment_type;
            $revenue->total = $request->total_balance;
            $revenue->transaction_way = $request->transaction_way;
            $revenue->description = $request->note;
            $revenue->created_by = Auth::user()->id;
            $revenue->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $revenue->save();

            if($request->file('documents')){
                foreach ($request->file('documents') as $key => $image) {

                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/revenue/documents', $name);

                    $documents = new Documents();
                    $documents->document_id = $revenue->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 4; //document_type 4 == revenue

                    $documents->created_by = Auth::user()->id;
                    $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                    $documents->save();
                }
            }


            if ($request->transaction_way != null) {

                $transaction = new Transaction();

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

                $transaction->transaction_title = $request->description[0]."-Revenue";
                $transaction->transaction_date = $request->invoice_date;

                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                     $transaction->transaction_account_type = 2;
                }
                else {
                    $transaction->account_id = $request->cash_account_id;
                    $transaction->transaction_account_type = 1;
                }

                $transaction->transaction_purpose = 3;
                $transaction->transaction_type = 2;
                $transaction->amount = $request->total_balance;
                $transaction->revenue_id = $revenue->id;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->description = $request->note;
                $transaction->status = 0;
                $transaction->created_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transaction->save();
            }
            $con = count($request->amount);

            for ($i = 0; $i < $con; $i++) {
                $expense_details = new RevenueDetails();
                $expense_details->revenue_id = $revenue->id;
                $expense_details->revenue_category = $request->revenue_categorie_id[$i];
                $expense_details->revenue_date = $request->revenue_date[$i];
                $expense_details->description = $request->description[$i];
                $expense_details->amount = $request->amount[$i];


                $expense_details->created_by = Auth::user()->id;
                $expense_details->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $expense_details->save();
            }
            DB::commit();
            return redirect()->route('admin.revenue.index')->with('message', 'Revenue  Successfully');
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
        try{
        $revenueInvoice = Revenue::with('revenueBy', 'createdBy')->findOrFail($id);
        $revenue_details = RevenueDetails::where('revenue_id', $id)->with('revenueCategory')->get();
        $totalBalance = RevenueDetails::where('revenue_id', $id)->sum('amount');
        $transaction = Transaction::where('revenue_id', $id)->with('bankAccount')->first();
        $bank = '';
        if ($transaction) {
            if ($transaction->bankAccount->type	 == 2) {
                $bank = Bank::findOrFail($transaction->bankAccount->bank_id);

            }
        }
        $documents = json_decode($revenueInvoice->document);
        $documents_title = json_decode($revenueInvoice->document_title);

        return view('admin.revenue.revenue_invoice.show', compact('revenue_details', 'revenueInvoice', 'totalBalance', 'transaction', 'bank', 'documents', 'documents_title'));
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
            $revenue = Revenue::where('id', $id)->first();
            $revenueCategorys = RevenueCategory::where('status', 1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $employees = Employee::get(['name','id']);
            $revenue_details = RevenueDetails::where('revenue_id', $id)->get();
            $transaction = Transaction::where('revenue_id', $id)->first();
            $total = $revenue_details->sum('amount');
            $documents = Documents::where('document_id', $id)->where('document_type', 4)->get();
           // $clients = Client::get(['name','id']);
              $client = Client::where('id',$revenue->client)->first();//['name','id']

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
            return view('admin.revenue.revenue_invoice.edit', compact('bankAccounts', 'revenueCategorys', 'revenue', 'employees', 'revenue_details', 'transaction', 'total', 'documents','cash_account','client','warehouses','mystore'));
        }
        catch (\Exception $exception) {
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
            'revenue_invoice_no' => 'required',
            'revenue_date' => 'required',
            'transaction_way' => 'required',
            'revenue_categorie_id' => 'required',
            'description' => 'required',
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
            $revenue = Revenue::findOrFail($id);
            $revenue->adjustment_balance = $request->adjustment_balance;
            $revenue->revenue_invoice_date = $request->invoice_date;
            $revenue->vat_type = $request->vat_type;
            $revenue->vat_rate = $request->vat_rate;
            $revenue->warehouse_id = $request->warehouse_id;
            $revenue->revenue_invoice_no = $request->revenue_invoice_no;
            $revenue->revenue_by = $request->revenue_by_id;
            $revenue->client = $request->client;
            $revenue->adjustment_type = $request->adjustment_type;
            $revenue->total = $request->total_balance;
            $revenue->transaction_way = $request->transaction_way;
            $revenue->description = strip_tags($request->note);
            $revenue->updated_by = Auth::user()->id;
            $revenue->update();

            // revenue document Update
            $old_documents = Documents::where('document_type',4)
            ->where('document_id',$id)
            ->pluck('id')->toArray();

        if ($request->revenue_document_id) {
            $result=array_diff($old_documents,$request->revenue_document_id);
            if($result){
                Documents::whereIn('id', $result)->delete();
            }
            foreach ($request->revenue_document_id as $key=>$document_id) {
                $doc = Documents::findOrFail($document_id);
                $doc->document_name = $request->document_title[$key];
                $doc->update();
            }
        }else{
            Documents::whereIn('id', $old_documents)->delete();
        }

        if ($request->hasfile('documents')) {
            foreach ($request->file('documents') as $key => $image) {
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/img/revenue/documents', $name);
                $documents = new Documents();
                $documents->document_id = $revenue->id;
                $documents->document_file = $name;
                $documents->document_name = $request->document_title[$key];
                $documents->document_type = 4; //document_type 4 == revenue
                $revenue->updated_by = Auth::user()->id;
                $documents->save();
            }
        }

            if ($request->transaction_way) {
                $transaction = Transaction::where('revenue_id', $id)->first();

                if ($transaction) {
                    $transaction->transaction_title =$request->description[0] ." -Revenue";
                    $transaction->transaction_date = $request->invoice_date;

                    if ($request->transaction_way == 2) {
                        $transaction->account_id = $request->account_id;
                         $transaction->transaction_account_type = 2;
                    }
                    else {
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
                    $transaction->transaction_purpose = 3;
                    $transaction->transaction_type = 2;
                    $transaction->amount = $request->total_balance;
                    $transaction->revenue_id = $revenue->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    $transaction->updated_by = Auth::user()->id;
                    $transaction->update();
                } else {
                    $transaction = new Transaction();
                    $transaction->transaction_title = $request->description[0]."Revenue";
                    $transaction->transaction_date = $request->invoice_date;
                    if ($request->transaction_way == 2) {
                        $transaction->account_id = $request->account_id;
                         $transaction->transaction_account_type = 2;
                    }
                    else {
                        $transaction->account_id = $request->cash_account_id;
                        $transaction->transaction_account_type = 1;
                    }
                    $transaction->transaction_purpose = 3;
                    $transaction->transaction_type = 2;
                    $transaction->amount = $request->total_balance;
                    $transaction->revenue_id = $revenue->id;
                    $transaction->cheque_number = $request->cheque_number;
                    $transaction->description = $request->note;
                    $transaction->created_by = Auth::user()->id;
                    $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                    $transaction->save();
                }
            } else {
                $transaction = Transaction::where('revenue_id', $id)->first();
                if ($transaction) {
                    $transaction->delete();
                }
            }

            $revenue_details = RevenueDetails::where('revenue_id', $id)->get();
            foreach ($revenue_details as $key => $revenueDetails) {
                $revenueDetails->delete();
            }

            $con = count($request->amount);
            $i = 0;
            for ($i = 0; $i<$con; $i++) {

                $expense_details = new RevenueDetails();
                $expense_details->revenue_id = $revenue->id;
                $expense_details->revenue_category = $request->revenue_categorie_id[$i];
                $expense_details->revenue_date = $request->revenue_date[$i];
                $expense_details->description = strip_tags($request->description[$i]);
                $expense_details->amount = $request->amount[$i];

                $expense_details->updated_by = Auth::user()->id;

               $expense_details->save();
            }
            DB::commit();
            return redirect()->route('admin.revenue.index')->with('message', 'Revenue  Successfully');
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
                $revenue = Revenue::where('id', $id)->first();

                $transaction = Transaction::where('revenue_id', $revenue->id)->first();

                $revenue_details = RevenueDetails::where('revenue_id', $revenue->id)->get();

                foreach ($revenue_details as $key => $expenses) {
                    $expenses->delete();
                }
                $revenue->delete();
                $transaction->delete();


                return response()->json([
                    'success' => true,
                    'message' => 'Expense Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function statusUpdate(Request $request)
    {
        try {
            $reference = Revenue::findOrFail($request->id);


            if($reference->status == 1)
            {
             $reference->status = 2;
             $transaction =Transaction ::where('revenue_id',$request->id)->first();
             $transaction->status = 1;
             $transaction->updated_by = Auth::user()->id;
             $transaction->update();
            }
            else{
             $reference->status = 1;
             $transaction =Transaction ::where('revenue_id',$request->id)->first();
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

    public function printInvoice(Request $request,$id)
    {
        try {
            $revenueInvoice = Revenue::with('revenueBy', 'createdBy','clientId')->findOrFail($id);

        $revenue_details = RevenueDetails::where('revenue_id', $id)->with('revenueCategory')->get();

        $totalBalance = RevenueDetails::where('revenue_id', $id)->sum('amount');
        $transaction = Transaction::where('revenue_id', $id)->with('bankAccount')->first();
        $bank = '';

        if ($transaction) {
            if ($transaction->bankAccount->transaction_account_type	 == 2) {
                $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
            }
        }
       $totalString =$this->convert_number_to_words($revenueInvoice->total) ;

        $dashboard_settings = DashboardSetting::first();
            return view('admin.revenue.revenue_invoice.invoice',compact('revenue_details', 'revenueInvoice', 'totalBalance', 'transaction', 'bank','dashboard_settings','totalString'));

        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }
    public function printVoucher(Request $request,$id)
    {
        try {
            $revenueInvoice = Revenue::with('revenueBy', 'createdBy','clientId')->findOrFail($id);

            $revenue_details = RevenueDetails::where('revenue_id', $id)->with('revenueCategory')->get();

            $totalBalance = RevenueDetails::where('revenue_id', $id)->sum('amount');
            $transaction = Transaction::where('revenue_id', $id)->with('bankAccount')->first();
            $bank = '';

            if ($transaction) {
                if ($transaction->bankAccount->transaction_account_type	 == 2) {
                    $bank = Bank::findOrFail($transaction->bankAccount->bank_id);
                }
            }

            $totalString =$this->convert_number_to_words($revenueInvoice->total) ;

            $dashboard_settings = DashboardSetting::first();
                return view('admin.revenue.revenue_invoice.voucher',compact('revenue_details', 'revenueInvoice', 'totalBalance', 'transaction', 'bank','dashboard_settings','totalString'));

        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }




    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */


}




