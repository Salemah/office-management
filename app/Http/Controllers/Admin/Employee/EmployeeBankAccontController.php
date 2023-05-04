<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class EmployeeBankAccontController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Start
        $request->validate([
            'employee_id' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'status' => 'required',
            'bank_id' => 'required',
        ]);
        try {
            $data = new BankAccount();
            $data->user_id = $request->employee_id;
            $data->account_type = 2; //employee bank account type == 3
            $data->account_number = $request->account_number;
            $data->name = $request->account_name;
            $data->note = $request->descriptions;
            $data->bank_id = $request->bank_id;
            $data->branch_name = $request->branch_name;
            $data->routing_no = $request->routing_no;
            $data->status = $request->status;

            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $data->save();

            return redirect()->route('admin.employee.show', $request->employee_id)->with('message', 'Bank Account Add successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $BankAccounts = BankAccount::where('user_id', $id)->where('account_type', 2)->with('bank')->latest()->get();
                return DataTables::of($BankAccounts)
                    ->addIndexColumn()
                    ->addColumn('status', function ($BankAccounts) {
                        if ($BankAccounts->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $BankAccounts->id . ')" class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $BankAccounts->id . ')" class="btn btn-sm btn-warning">In-Active</button>';
                        }
                    })
                    ->addColumn('bank', function ($BankAccounts) {
                        return $BankAccounts->bank->bank_name;
                    })
                    ->addColumn('action', function ($BankAccounts) {
                        if (Auth::user()->can('employee_bank_accounts_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="clientBankAccountDeleteConfirm(' . $BankAccounts->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'bank', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.show');
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
    public function edit(Request $request, $id)
    {
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
                BankAccount::where('id', $id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bank Account Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    public function statusUpdate(Request $request)
    {
        try {
            $clientBankAccount = BankAccount::findOrFail($request->id);
            $clientBankAccount->status == 1 ? $clientBankAccount->status = 0 : $clientBankAccount->status = 1;

            $clientBankAccount->update();

            if ($clientBankAccount->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
