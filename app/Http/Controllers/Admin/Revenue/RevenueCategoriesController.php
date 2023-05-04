<?php

namespace App\Http\Controllers\Admin\Revenue;

use App\Http\Controllers\Controller;
use App\Models\Revenue\RevenueCategory;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RevenueCategoriesController extends Controller
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
                $revenueCategorys = RevenueCategory::latest()->get();
                return DataTables::of($revenueCategorys)
                    ->addIndexColumn()
                    ->addColumn('status', function ($revenueCategorys) {
                        if ($revenueCategorys->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $revenueCategorys->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $revenueCategorys->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($revenueCategorys) {
                        return Str::limit($revenueCategorys->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($revenueCategorys) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.revenue-category.edit', $revenueCategorys->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $revenueCategorys->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.revenue.revenue-category.index');
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
            return view('admin.revenue.revenue-category.create');
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
            $data = new RevenueCategory();
            $data->name = $request->name;
            $data->description = strip_tags($request->description);
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.revenue-category.index')->with('toastr-success', 'Category Created Successfully');
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
            $revenueCategory = RevenueCategory::findOrFail($id);
            return view('admin.revenue.revenue-category.edit', compact('revenueCategory'));
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
            $data = RevenueCategory::where('id', $id)->first();
            $data->name = $request->name;
            $data->description = strip_tags($request->description);;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.revenue-category.index')->with('toastr-success', 'Expense-Category Updated Successfully');
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
                RevenueCategory::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Category Deleted Successfully.',
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
            $revenueCategory = RevenueCategory::findOrFail($id);
            // Check Item Current Status
            if ($revenueCategory->status == 1) {
                $revenueCategory->status = 0;
            } else {
                $revenueCategory->status = 1;
            }
            $revenueCategory->save();
            return response()->json([
                'success' => true,
                'message' => 'Category Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




