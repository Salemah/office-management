<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\CRM\Client\Client;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientBankAccontController extends Controller
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
            'client_id' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required|unique:bank_accounts,account_number',
            'account_name' => 'required|unique:bank_accounts,name',
            'status' => 'required',
            'bank_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = new BankAccount();
            $data->user_id = $request->client_id;
            $data->account_type = 3; //type 3 == client bank account
            $data->account_number = $request->account_number;
            $data->name = $request->account_name;
            $data->initial_balance = $request->initial_balance;
            $data->note = $request->descriptions;
            $data->bank_id = $request->bank_id;
            $data->branch_name = $request->branch_name;
            $data->routing_no = $request->routing_no;
            $data->status = $request->status;

            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $data->save();

            $transaction = new Transaction();
            $transaction->transaction_title = 'Initial Balance Deposit';
            $transaction->account_id = $data->id;
            $transaction->transaction_date = Carbon::now();
            $transaction->transaction_purpose = 0;
            $transaction->transaction_type = 2;
            $transaction->cheque_number = $request->cheque_number;
            $transaction->amount = $request->initial_balance;
            $transaction->status = $request->status;

            $transaction->created_by = Auth::user()->id;
            $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $transaction->save();

            $client = Client::findOrFail($request->client_id);
                if ($client->is_assign == false) {
                    $client->is_assign = 1;
                    $own_assign = json_decode($client->own_assign);
                    $own_assign [] = Auth::id();
                    $client->own_assign = json_encode($own_assign);
                    $client->update();
                }

            DB::commit();

            return redirect()->route('admin.crm.client.show', $request->client_id)->with('message', 'Client Bank Account Add successfully.');
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
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $BankAccounts = BankAccount::where('user_id', $id)->where('account_type', 3)->with('bank')->latest()->get();
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
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.crm.client.bank.account.edit', ['id' => $BankAccounts->id, 'parameter' => 'show']) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="clientBankAccountDeleteConfirm(' . $BankAccounts->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
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
    public function bankAccountEdit(Request $request, $id)
    {
        try {
            $parameter = $request->parameter;
            $banks = Bank::where('status', 1)->get();
            $bankAccount = BankAccount::where('id', $id)->first();
            $Client = Client::where('id', $bankAccount->user_id)->first();
            return view('admin.account.bank_account.edit',compact('bankAccount','banks','parameter','Client'));
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
