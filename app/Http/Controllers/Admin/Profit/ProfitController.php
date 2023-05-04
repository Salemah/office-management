<?php

namespace App\Http\Controllers\Admin\Profit;

use App\Http\Controllers\Controller;
use App\Models\Employee\Salary\SalaryGenerate;
use App\Models\Expense\Expense;
use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Sales\Sales;
use App\Models\Inventory\Settings\InventoryWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    public function netProfit()
    {
        $warehouses =InventoryWarehouse::all();
        $auth = Auth::user();
        $user_role = $auth->roles->first();
        return view('admin.profit.netProfit',compact('warehouses','auth','user_role'));
    }

    public function netProfitGet(Request $request)
    {
        $date = explode("-", $request->month);
        $year = $date[0];
        $month = $date[1];

        $sell =new Sales;
        $sell_data = $sell->getDataForMonth($month, $year)->where('warehouse_id', $request->warehouse);
        $total_sell = $sell_data->sum('paid_amount');

        $purchase = new Purchase;
        $purchase_data = $purchase->getDataForMonth($month, $year)->where('warehouse_id', $request->warehouse);
        $total_purchase = $purchase_data->sum('paid_amount');

        $emp_salary = new SalaryGenerate;
        $salary_data = $emp_salary->getDataForMonth($month, $year)->where('warehouse', $request->warehouse)->where('status', 1);
        $total_salary = $salary_data->sum('gross_salary');

        $expense = new Expense;
        $expense_data = $expense->getDataForMonth($month, $year)->where('warehouse_id', $request->warehouse)->where('status', 2);
        $tot_exp = $expense_data->sum('total');
        $total_expense = $tot_exp + $total_salary;


        $net_profit = $total_sell - ($total_expense + $total_purchase);

        return response()->json([
            "sell" => $total_sell,
            "purchase" => $total_purchase,
            "expense" => $total_expense,
            "profit" => $net_profit,
        ]);
    }

    public function grossProfit()
    {
        $warehouses =InventoryWarehouse::all();
        $auth = Auth::user();
        $user_role = $auth->roles->first();
        return view('admin.profit.grossProfit',compact('warehouses','auth','user_role'));
    }
    public function grossProfitGet(Request $request)
    {
        $date = explode("-", $request->month);
        $year = $date[0];
        $month = $date[1];

        $sell =new Sales;
        $sell_data = $sell->getDataForMonth($month, $year)->where('warehouse_id', $request->warehouse);
        $total_sell = $sell_data->sum('paid_amount');

        $purchase = new Purchase;
        $purchase_data = $purchase->getDataForMonth($month, $year)->where('warehouse_id', $request->warehouse);
        $total_purchase = $purchase_data->sum('paid_amount');


        $gross_profit = $total_sell - $total_purchase;

        return response()->json([
            "sell" => $total_sell,
            "purchase" => $total_purchase,
            "profit" => $gross_profit,
        ]);
    }



    public function create(Request $request)
    {

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
