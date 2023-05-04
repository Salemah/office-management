<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense\ExpenseCategory;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExpenseCategoriesController extends Controller
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
                $expenseCategorys = ExpenseCategory::latest()->get();
                return DataTables::of($expenseCategorys)
                    ->addIndexColumn()
                    ->addColumn('status', function ($expenseCategorys) {
                        if ($expenseCategorys->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $expenseCategorys->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $expenseCategorys->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($expenseCategorys) {
                        return Str::limit($expenseCategorys->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($expenseCategorys) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.expense.category.edit', $expenseCategorys->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $expenseCategorys->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.expense.expense-category.index');
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
            return view('admin.expense.expense-category.create');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'string|nullable',
            'status' => 'required|numeric'
        ]);

        try {
            $data = new ExpenseCategory();
            $data->name = $request->name;
            $data->description =$request->description;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.expense.category.index')->with('toastr-success', 'Expense-Category Created Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $expenseCategory = ExpenseCategory::findOrFail($id);
            return view('admin.expense.expense-category.edit', compact('expenseCategory'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'name' => 'required|string',
            'description' => 'string|nullable',
            'status' => 'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data = ExpenseCategory::where('id', $id)->first();
            $data->name = $request->name;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.expense.category.index')->with('toastr-success', 'Expense-Category Updated Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                ExpenseCategory::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'ExpenseCategory Deleted Successfully.',
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

    public function statusUpdate(Request $request, $id)
    {
        try {
            $expenseCategory = ExpenseCategory::findOrFail($id);
            // Check Item Current Status
            if ($expenseCategory->status == 1) {
                $expenseCategory->status = 0;
            } else {
                $expenseCategory->status = 1;
            }

            $expenseCategory->save();
            return response()->json([
                'success' => true,
                'message' => 'Expense Category Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




