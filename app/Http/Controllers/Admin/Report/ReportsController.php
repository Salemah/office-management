<?php

namespace App\Http\Controllers\Admin\Report;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Account\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Sales\Sales;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Customers\InventoryCustomer;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Suppliers\InventorySupplier;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Wholesale\Wholesale;

class ReportsController extends Controller
{
    public function CustomerDueList(Request $request)
    {
        try {
            if ($request->ajax()) {

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $data = Sales::groupBy('customer_id')
                    ->select('*')
                    ->selectRaw('sum(paid_amount) as receiveAmount')
                    ->selectRaw('sum(grand_total) as grandTotal')
                    ->get();
                }else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $mystore = InventoryWarehouse::where('id', $employee->warehouse )->first();

                    $data = Sales::where('warehouse_id', $mystore->id)->groupBy('customer_id')
                    ->select('*')
                    ->selectRaw('sum(paid_amount) as receiveAmount')
                    ->selectRaw('sum(grand_total) as grandTotal')
                    ->get();
                }

                $sales = [];

                foreach($data as $sale){
                    $totalDue = $sale->grandTotal - $sale->receiveAmount;
                    if($totalDue != 0){
                        $sales[] = $sale;
                    }
                }

                return DataTables::of( $sales)
                    ->addIndexColumn()
                    ->addColumn('action', function ($sales) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Report"style="cursor:pointer"href="' . route('admin.inventory.customers.customer.due', $sales->customer_id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })
                    ->addColumn('customer', function ($sales) {
                        return $sales->customers->name;
                    })

                    ->addColumn('due', function ($sales) {
                        $totalDue = $sales->grandTotal - $sales->receiveAmount;
                        return  number_format($totalDue,2);
                    })

                    ->rawColumns(['due','action','customer'])
                    ->make(true);
            }

            return view('admin.report.customer_due_list');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function SupplierPayList(Request $request)
    {
        try {
            if ($request->ajax()) {

                $auth = Auth::user();
                $user_role = $auth->roles->first();

                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){

                    $data = Purchase::groupBy('supplier_id')
                    ->select('*')
                    ->selectRaw('sum(paid_amount) as receiveAmount')
                    ->selectRaw('sum(grand_total) as grandTotal')
                    ->get();

                }else{

                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id', $user->user_id)->first();
                    $mystore = InventoryWarehouse::where('id', $employee->warehouse )->first();

                    $data = Purchase::where('warehouse_id', $mystore->id)->groupBy('supplier_id')
                    ->select('*')
                    ->selectRaw('sum(paid_amount) as receiveAmount')
                    ->selectRaw('sum(grand_total) as grandTotal')
                    ->get();
                }

                $purchase = [];

                foreach($data as $sale){
                    $totalDue = $sale->grandTotal - $sale->receiveAmount;
                    if($totalDue != 0){
                        $purchase[] = $sale;
                    }
                }

                return DataTables::of( $purchase)
                    ->addIndexColumn()
                    ->addColumn('action', function ($purchase) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Report"style="cursor:pointer"href="' . route('admin.inventory.suppliers.supplier.pay', $purchase->supplier_id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })
                    ->addColumn('supplier', function ($purchase) {
                        return $purchase->suppliers->name ?? '--';
                    })

                    ->addColumn('due', function ($purchase) {
                        $totalDue = $purchase->grandTotal - $purchase->receiveAmount;
                        return  number_format($totalDue,2);
                    })

                    ->rawColumns(['due','action','supplier'])
                    ->make(true);
            }

            return view('admin.report.supplier_pay_list');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function PurchaseReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $purchase = Purchase::where('warehouse_id', $request->warehouse_id)->get();

                }
                if($request->warehouse_id && $request->supplier_id){
                    $purchase = Purchase::where('warehouse_id', $request->warehouse_id)->where('supplier_id', $request->supplier_id)->get();

                }
                if($request->warehouse_id && $request->supplier_id && $request->product_id){

                    $purchase = DB::table('purchases')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'purchases.id')
                    ->select('purchases.*', 'inventory_product_counts.product_id')
                    ->where('purchases.warehouse_id', $request->warehouse_id)
                    ->where('purchases.supplier_id', $request->supplier_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('purchases.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->supplier_id && $request->product_id && $request->start_date && $request->end_date){

                    $purchase = DB::table('purchases')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'purchases.id')
                    ->select('purchases.*', 'inventory_product_counts.product_id')
                    ->where('purchases.warehouse_id', $request->warehouse_id)
                    ->where('purchases.supplier_id', $request->supplier_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('purchases.date', '>', $request->start_date)
                    ->where('purchases.date', '<=', $request->end_date)
                    ->whereNull('purchases.deleted_at')
                    ->get();


                }
                if($request->warehouse_id && $request->product_id){
                    $purchase = DB::table('purchases')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'purchases.id')
                    ->select('purchases.*', 'inventory_product_counts.product_id')
                    ->where('purchases.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('purchases.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->product_id && $request->start_date && $request->end_date){

                    $purchase = DB::table('purchases')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'purchases.id')
                    ->select('purchases.*', 'inventory_product_counts.product_id')
                    ->where('purchases.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('purchases.date', '>', $request->start_date)
                    ->where('purchases.date', '<=', $request->end_date)
                    ->whereNull('purchases.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->supplier_id && $request->start_date && $request->end_date){
                    $purchase = Purchase::where('warehouse_id', $request->warehouse_id)->where('supplier_id', $request->supplier_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();

                }
                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $purchase = Purchase::where('warehouse_id', $request->warehouse_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();
                }

                return DataTables::of( $purchase)
                    ->addIndexColumn()
                    ->addColumn('action', function ($purchase) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.purchase.show', $purchase->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })

                    ->addColumn('paid', function ($purchase) {
                        return number_format($purchase->paid_amount,2);
                    })

                    ->addColumn('grand_total', function ($purchase) {
                        return number_format($purchase->grand_total,2);
                    })
                    ->addColumn('due', function ($purchase) {

                        return  number_format($purchase->grand_total - $purchase->paid_amount,2);
                    })

                    ->rawColumns(['grand_total','due','paid','action'])
                    ->make(true);
                }
                $products = Products::all();

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                }

            return view('admin.report.purchase_report', compact('user_role','warehouses','products'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function SalesReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $sales = Sales::where('warehouse_id', $request->warehouse_id)->get();

                }
                if($request->warehouse_id && $request->customer_id){
                    $sales = Sales::where('warehouse_id', $request->warehouse_id)->where('customer_id', $request->customer_id)->get();

                }
                if($request->warehouse_id && $request->customer_id && $request->product_id){

                    $sales = DB::table('sales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'sales.id')
                    ->select('sales.*', 'inventory_product_counts.product_id')
                    ->where('sales.warehouse_id', $request->warehouse_id)
                    ->where('sales.customer_id', $request->customer_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('sales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->customer_id && $request->product_id && $request->start_date && $request->end_date){

                    $sales = DB::table('sales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'sales.id')
                    ->select('sales.*', 'inventory_product_counts.product_id')
                    ->where('sales.warehouse_id', $request->warehouse_id)
                    ->where('sales.customer_id', $request->customer_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('sales.date', '>', $request->start_date)
                    ->where('sales.date', '<=', $request->end_date)
                    ->whereNull('sales.deleted_at')
                    ->get();


                }
                if($request->warehouse_id && $request->product_id){
                    $sales = DB::table('sales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'sales.id')
                    ->select('sales.*', 'inventory_product_counts.product_id')
                    ->where('sales.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('sales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->product_id && $request->start_date && $request->end_date){

                    $sales = DB::table('sales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.purchase_id', '=', 'sales.id')
                    ->select('sales.*', 'inventory_product_counts.product_id')
                    ->where('sales.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('sales.date', '>', $request->start_date)
                    ->where('sales.date', '<=', $request->end_date)
                    ->whereNull('sales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->customer_id && $request->start_date && $request->end_date){
                    $sales = Sales::where('warehouse_id', $request->warehouse_id)->where('customer_id', $request->customer_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();

                }
                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $sales = Sales::where('warehouse_id', $request->warehouse_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();
                }

                return DataTables::of( $sales)
                    ->addIndexColumn()
                    ->addColumn('action', function ($sales) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.sale.show', $sales->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })

                    ->addColumn('paid', function ($sales) {
                        return number_format($sales->paid_amount,2);
                    })

                    ->addColumn('grand_total', function ($sales) {
                        return number_format($sales->grand_total,2);
                    })
                    ->addColumn('due', function ($sales) {

                        return  number_format($sales->grand_total - $sales->paid_amount,2);
                    })

                    ->rawColumns(['grand_total','due','paid','action'])
                    ->make(true);
                }

                $products = Products::all();
                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                }

            return view('admin.report.sales_report', compact('user_role','warehouses','products'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ExpenseReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $expense = Transaction::where('warehouse_id', $request->warehouse_id)
                    ->where('transaction_type', 1)
                    ->get();
                }

                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $expense = Transaction::where('transaction_type', 1)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->where('transaction_date', '>', $request->start_date)
                    ->where('transaction_date', '<=', $request->end_date)
                    ->get();
                }

                return DataTables::of( $expense)
                    ->addIndexColumn()

                    ->addColumn('date', function ($expense) {
                        $data = Carbon::parse($expense->transaction_date)->format('d F, Y');
                        return $data;
                    })

                    ->addColumn('total', function ($expense) {
                        $data = $expense->amount;
                        return number_format($data, 2);
                    })

                    ->rawColumns(['date','total'])
                    ->make(true);
                }

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                    $totalExpense = Transaction::where('transaction_type', 1)
                    ->sum('amount');
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                    $totalExpense = Transaction::where('transaction_type', 1)->where('warehouse_id', $warehouses->id)->sum('amount');
                }

            return view('admin.report.expense_report', compact('user_role','warehouses','totalExpense'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function RevenueReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $revenue = Transaction::where('warehouse_id', $request->warehouse_id)
                    ->where('transaction_type', 2)
                    ->get();
                }

                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $revenue = Transaction::where('transaction_type', 2)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->where('transaction_date', '>', $request->start_date)
                    ->where('transaction_date', '<=', $request->end_date)
                    ->get();
                }

                return DataTables::of( $revenue)
                    ->addIndexColumn()

                    ->addColumn('date', function ($revenue) {
                        $data = Carbon::parse($revenue->transaction_date)->format('d F, Y');
                        return $data;
                    })

                    ->addColumn('total', function ($revenue) {
                        $data = $revenue->amount;
                        return number_format($data, 2);
                    })

                    ->rawColumns(['date','total'])
                    ->make(true);
                }

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                    $totalRevenue = Transaction::where('transaction_type', 2)
                    ->sum('amount');
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                    $totalRevenue = Transaction::where('transaction_type', 2)->where('warehouse_id', $warehouses->id)->sum('amount');
                }

            return view('admin.report.revenue_report', compact('user_role','warehouses','totalRevenue'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function IncomeReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $income = Transaction::where('warehouse_id', $request->warehouse_id)
                    ->whereIn('transaction_type', [1,2])
                    ->get();
                }

                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $income = Transaction::whereIn('transaction_type', [1, 2])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->where('transaction_date', '>', $request->start_date)
                    ->where('transaction_date', '<=', $request->end_date)
                    ->get();

                }

                return DataTables::of( $income)
                    ->addIndexColumn()

                    ->addColumn('date', function ($income) {
                        $date = Carbon::parse($income->transaction_date)->format('d F, Y');
                        return $date;
                    })

                    ->addColumn('revenue', function ($income) {
                        if ($income->transaction_type == 2) {
                            $revenue = $income->amount;
                            return number_format($revenue, 2);
                        }else{
                            return '0.00';
                        }
                    })

                    ->addColumn('expense', function ($income) {
                        if ($income->transaction_type == 1) {
                            $revenue = $income->amount;
                            return number_format($revenue, 2);
                        }else{
                            return '0.00';
                        }
                    })

                    ->addColumn('income', function ($income) use (&$current_income) {
                        if ($income->transaction_type == 2) {
                            $current_income = $current_income + $income->amount;
                            return (number_format($current_income));
                        }

                        if ($income->transaction_type == 1) {
                            $current_income = $current_income - $income->amount;
                            return (number_format($current_income));
                        }
                    })

                    ->rawColumns(['date','revenue','expense','income'])
                    ->make(true);
                }

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                    $totalExpense = Transaction::where('transaction_type', 1)
                    ->sum('amount');
                    $totalRevenue = Transaction::where('transaction_type', 2)
                    ->sum('amount');
                    $totalIncome = $totalRevenue - $totalExpense;
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                    $totalExpense = Transaction::where('transaction_type', 1)
                    ->sum('amount');
                    $totalRevenue = Transaction::where('transaction_type', 2)->where('warehouse_id', $warehouses->id)->sum('amount');
                    $totalIncome = $totalRevenue - $totalExpense;
                }

            return view('admin.report.income_report', compact('user_role','warehouses','totalRevenue','totalIncome','totalExpense'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function WholeSaleReport(Request $request)
    {
        try {
            if ($request->ajax()) {

                if($request->warehouse_id){
                    $sales = Wholesale::where('warehouse_id', $request->warehouse_id)->get();
                }

                if($request->warehouse_id && $request->customer_id){
                    $sales = Wholesale::where('warehouse_id', $request->warehouse_id)->where('customer_id', $request->customer_id)->get();
                }

                if($request->warehouse_id && $request->customer_id && $request->product_id){

                    $sales = DB::table('wholesales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.whole_sale_id', '=', 'wholesales.id')
                    ->select('wholesales.*', 'inventory_product_counts.product_id')
                    ->where('wholesales.warehouse_id', $request->warehouse_id)
                    ->where('wholesales.customer_id', $request->customer_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('wholesales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->customer_id && $request->product_id && $request->start_date && $request->end_date){

                    $sales = DB::table('wholesales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.whole_sale_id', '=', 'wholesales.id')
                    ->select('wholesales.*', 'inventory_product_counts.product_id')
                    ->where('wholesales.warehouse_id', $request->warehouse_id)
                    ->where('wholesales.customer_id', $request->customer_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('wholesales.date', '>', $request->start_date)
                    ->where('wholesales.date', '<=', $request->end_date)
                    ->whereNull('wholesales.deleted_at')
                    ->get();


                }
                if($request->warehouse_id && $request->product_id){
                    $sales = DB::table('wholesales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.whole_sale_id', '=', 'wholesales.id')
                    ->select('wholesales.*', 'inventory_product_counts.product_id')
                    ->where('wholesales.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->whereNull('wholesales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->product_id && $request->start_date && $request->end_date){

                    $wholesales = DB::table('wholesales')
                    ->Join('inventory_product_counts', 'inventory_product_counts.whole_sale_id', '=', 'wholesales.id')
                    ->select('wholesales.*', 'inventory_product_counts.product_id')
                    ->where('wholesales.warehouse_id', $request->warehouse_id)
                    ->where('inventory_product_counts.product_id', $request->product_id)
                    ->where('wholesales.date', '>', $request->start_date)
                    ->where('wholesales.date', '<=', $request->end_date)
                    ->whereNull('wholesales.deleted_at')
                    ->get();

                }
                if($request->warehouse_id && $request->customer_id && $request->start_date && $request->end_date){
                    $sales = Wholesale::where('warehouse_id', $request->warehouse_id)->where('customer_id', $request->customer_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();

                }
                if($request->warehouse_id && $request->start_date && $request->end_date){
                    $sales = Wholesale::where('warehouse_id', $request->warehouse_id)->where('date', '>', $request->start_date)
                    ->where('date', '<=', $request->end_date)->get();
                }

                return DataTables::of( $sales)
                    ->addIndexColumn()
                    ->addColumn('action', function ($sales) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.wholesale.show', $sales->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })

                    ->addColumn('paid', function ($sales) {
                        return number_format($sales->paid_amount,2);
                    })

                    ->addColumn('grand_total', function ($sales) {
                        return number_format($sales->grand_total,2);
                    })
                    ->addColumn('due', function ($sales) {

                        return  number_format($sales->grand_total - $sales->paid_amount,2);
                    })

                    ->rawColumns(['grand_total','due','paid','action'])
                    ->make(true);
                }

                $products = Products::all();
                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $warehouses = InventoryWarehouse::all();
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $warehouses = InventoryWarehouse::where('id', $employee->warehouse )->first();
                }

            return view('admin.report.wholesale_report', compact('user_role','warehouses','products'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
