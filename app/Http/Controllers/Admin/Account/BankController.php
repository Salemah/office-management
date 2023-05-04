<?php

namespace App\Http\Controllers\Admin\Account;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\Bank;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;

class BankController extends Controller
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
                $banks = Bank::latest()->get();
                return DataTables::of($banks)
                    ->addIndexColumn()
                    ->addColumn('status', function ($banks) {
                        if ($banks->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $banks->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $banks->id . ')"
                            class="btn btn-sm btn-warning">In-active</button>';
                        }
                    })
                    ->addColumn('action', function ($banks) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.account.bank.edit', $banks->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>

                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $banks->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.account.bank.index');
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
            return view('admin.account.bank.create');
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
            'bank_name' => 'required|string|unique:banks,bank_name',
            'bank_code' => 'required|string|unique:banks,bank_code',
            'status' => 'required'
        ]);

        try {
            $data = new Bank();
            $data->bank_name = $request->bank_name;
            $data->bank_code = $request->bank_code;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            return redirect()->route('admin.account.bank.index')->with('message', 'Add successfully.');
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
            $bank = Bank::where('id', $id)->with('createdByUser')->first();
            return view('administrator.account.bank.bank_info.show', compact('bank'));
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
            $bank = Bank::findOrFail($id);
            return view('admin.account.bank.edit', compact('bank'));
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
            'bank_name' => 'required|string',
            'status' => 'required|numeric'
        ]);

        try {
            $data = Bank::find($id);
            $data->bank_name = $request->bank_name;
            $data->bank_code = $request->bank_code;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->update();

            return redirect()->route('admin.account.bank.index')->with('message', 'Update successfully.');
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
                Bank::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bank Deleted Successfully.',
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
            $reference=Bank::findOrFail($request->id);

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
