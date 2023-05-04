<?php

namespace App\Http\Controllers\Admin\Employee\Salary;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Employee\Salary\SalaryGenerate;
use App\Models\Employee\Salary\SalaryReport;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class SalaryReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $salaries = SalaryGenerate::with('employee','warehouse_relation')->get();
                return DataTables::of($salaries)
                    ->addIndexColumn()
                    ->addColumn('status', function ($salaries) {
                        if ($salaries->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $salaries->id . ')"
                             class="btn btn-sm btn-success">Paid</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $salaries->id . ')"
                            class="btn btn-sm btn-warning">Unpaid</button>';
                        }
                    })
                    ->addColumn('action', function ($salaries) {
                        if ($salaries->status == 0){
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a class="btn btn-sm btn-info text-white " style="cursor:pointer" href="' . route('admin.salary.confirm', $salaries->id) . '" title="confirm salary"><i class="bx bx-dollar-circle"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $salaries->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                    </div>';
                        }else{
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer" href="' . route('admin.salary.confirm.edit', $salaries->id) . '" title="edit"><i class="bx bx-edit"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $salaries->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                    </div>';
                        }
                    })
                    ->rawColumns(['employee_id','warehouse','status', 'action'])
                    ->make(true);
            }
            return view('admin.employee.salary.salary-report.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function create()
    {
        $employees = Employee::all();
        $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
        $warehouses =InventoryWarehouse::all();
        $auth = Auth::user();
        $user_role = $auth->roles->first();
        return view('admin.employee.salary.salary-report.create',compact('bankAccounts','cash_account','employees','warehouses','user_role'));
    }

    public function store(Request $request, $id)
    {
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                    'transaction_way' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }
        DB::beginTransaction();
        try{
            $account_id = 0;
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $salary = SalaryGenerate::where('id', $request->id)->first();
            $salary->status = 1;
            $salary->update();

            $transaction = new Transaction();
            $transaction->transaction_title = "Employee Salary";

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->transaction_account_type = 2;
            }
            else if($request->transaction_way == 1){
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
            $transaction->warehouse_id = $request->warehouse;
            $transaction->transaction_purpose = 21;
            $transaction->transaction_type = 1;
            $transaction->amount = $request->gross_salary;
            $transaction->emp_salaryId = $salary->id;

            $transaction->status = 1;

            if($transaction->created_by){
                $transaction->updated_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            }else{
                $transaction->created_by= Auth::user()->id;
            }
            $transaction->save();

            DB::commit();
            return redirect()->route('admin.salaryReport.index')->with('success', 'Salary Paid  Successfully');
        }catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $employees = Employee::all();
        $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
        $data = SalaryReport::where('id', $id)->with('employee','account')->first();

        return view('admin.employee.salary.salary-report.edit',compact('data','employees','cash_account','bankAccounts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required',
            'amount' => 'required',
            'month' => 'required'
        ]);
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                    'transaction_way' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }
        DB::beginTransaction();
        try{
            $account_id = 0;
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }
            $salary= SalaryReport::findOrFail($id);
            $salary->employee_id = $request->employee_id;
            if ($request->transaction_way == 2) {
                $salary->account_id = $request->account_id;
                $salary->cheque_number = $request->cheque_number;
                $salary->transaction_way = 2;
            }
            else if($request->transaction_way == 1){
                $salary->account_id = $request->cash_account_id;
                $salary->cheque_number = null;
                $salary->transaction_way = 1;
            }
            $salary->amount = $request->amount;
            $salary->month = $request->month;
            $salary->update();


            $transaction =  Transaction::where('emp_salaryId', $id)->first();
            $transaction->transaction_title = "Employee Salary";

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->transaction_account_type = 2;
            }
            else if($request->transaction_way == 1){
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }
            $transaction->transaction_purpose = 21;
            $transaction->transaction_type = 1;
            $transaction->amount = $request->amount;
            $transaction->emp_salaryId = $salary->id;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->status = 1;

            if($transaction->created_by){
                $transaction->updated_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            }else{
                $transaction->created_by= Auth::user()->id;
            }
            $transaction->update();

            DB::commit();
            return redirect()->route('admin.salaryReport.index')->with('message', 'Employee salary updated  Successfully');
        }catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $salarie = SalaryGenerate::where('id', $id)->first();
                $transaction = Transaction::where('emp_salaryId', $salarie->id)->first();
                $salarie->delete();
                $transaction->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Generated Salary Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    public function reportList()
    {
        $warehouses = InventoryWarehouse::all();
        $employees = Employee::all();
        return view('admin.employee.salary.salary-report.report-list',compact('employees','warehouses'));
    }

    public function reportListShow(Request $request)
    {

        $warehouse = $request->warehouse;
        $employee_id = $request->employee_id;
        $date_form = $request->month_from;
        $date_to = $request->month_to;
        if ($employee_id == 0){
            $salaries = SalaryGenerate::whereBetween('month',[$date_form, $date_to])->where('warehouse',$warehouse)->with('employee','warehouse_relation')->get();
        }else {
            $salaries = SalaryGenerate::whereBetween('month', [$date_form, $date_to])->where('warehouse',$warehouse)->where('employee_id', $employee_id)->with('employee', 'warehouse_relation')->get();
        }

        return view('admin.employee.salary.salary-report.salary-list-view',compact('salaries'));
    }

    public function getEmployee(Request $request, $id)

    {
        $data = Employee::where('warehouse', $request->id)->get();
        return response()->json($data);
    }

    public function salaryGenerate(Request $request)
    {
        $query = SalaryGenerate::where('month',$request->month)->where('warehouse',$request->warehouse)->first();
        if ($query == null){
            foreach ($request->employee as $key=>$employee)
            {
                $salaryGenerate= new SalaryGenerate();

                $salaryGenerate->month = $request->month;
                $salaryGenerate->warehouse = $request->warehouse;
                $salaryGenerate->employee_id = $employee;
                $salaryGenerate->gross_salary = $request->amount[$key];
                $salaryGenerate->status = 0;

                $salaryGenerate->save();
            }
            return redirect()->route('admin.salaryReport.index')->with('success', 'Employee Salary Generated  Successfully');
        }else{
            return redirect()->back()->with('error', 'This month salary already generated');
        }

    }

    public function salaryConfirm(Request $request, $id)
    {
        $data = SalaryGenerate::where('id', $request->id)->with('employee','warehouse_relation')->first();

        $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
        return view('admin.employee.salary.salary-report.salary-confirm',compact('data','cash_account','bankAccounts'));
    }

    public function salaryConfirmEdit(Request $request, $id)
    {
        $data          = SalaryGenerate::where('id', $request->id)->with('employee','warehouse_relation')->first();
        $transaction   = Transaction::where('emp_salaryId', $request->id)->with('bankAccount')->first();
        $cash_accounts = BankAccount::where('status', 1)->where('type',1)->get();
        $bankAccounts  = BankAccount::where('status', 1)->where('type',2)->get();
        return view('admin.employee.salary.salary-report.salary-confirm-edit',compact('data','transaction','cash_accounts','bankAccounts'));
    }

    public function salaryConfirmUpdate(Request $request, $id)
    {
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                    'transaction_way' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }

        try{
            $account_id = 0;
            if ($request->transaction_way) {
                $request->transaction_way == 2 ? $account_id = $request->account_id : $account_id = $request->cash_account_id ;
                $balance = AccountsRepository::postBalance($account_id);

                $balance = $balance - $request->total_balance;

                if ($balance < 0) {
                    return redirect()->back()->with('error', 'Transaction failed for insufficient balance! ');
                }
            }

            $transaction = Transaction::where('emp_salaryId', $request->id)->first();

            if ($request->transaction_way == 2) {
                $transaction->account_id = $request->account_id;
                $transaction->cheque_number = $request->cheque_number;
                $transaction->transaction_account_type = 2;
            }
            else if($request->transaction_way == 1){
                $transaction->account_id = $request->cash_account_id;
                $transaction->transaction_account_type = 1;
            }

            $transaction->updated_by= Auth::user()->id;
            $transaction->update();

            return redirect()->route('admin.salaryReport.index')->with('success', 'Updated Successfully');
        }catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function empByWarehouse(Request $request, $id)
    {
        if ($request->id == 0){
            $employees = SalaryGenerate::with('employee')->get();
        }else{
            $employees = SalaryGenerate::where('warehouse', $request->id)->with('employee')->get();
        }
        return response()->json($employees);

    }
}
