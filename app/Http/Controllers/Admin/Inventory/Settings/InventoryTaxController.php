<?php

namespace App\Http\Controllers\Admin\Inventory\Settings;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Settings\InventoryBrand;
use App\Models\Inventory\Settings\Taxes;

class InventoryTaxController extends Controller
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
                $tax = Taxes::latest()->get();
                return DataTables::of($tax)
                    ->addIndexColumn()
                    ->addColumn('status', function ($tax) {
                        if ($tax->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $tax->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $tax->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($tax) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.settings.tax.edit', $tax->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $tax->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                               </div>';
                    })
                    ->rawColumns(['status','action', 'description'])
                    ->make(true);
            }
            return view('admin.inventory.settings.tax.index');
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
            return view('admin.inventory.settings.tax.create');
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
            'name'            =>      'required',
            'rate'            =>      'required',
            'status'          =>      'required'
        ]);
        try {
            $data=new Taxes();
            $data->name = $request->name;
            $data->rate = $request->rate;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.settings.tax.index')
                    ->with('toastr-success', 'Tax Created Successfully');
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
            $tax= Taxes::findOrFail($id);
            return view('admin.inventory.settings.tax.edit', compact('tax'));
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
        $request->validate([
            'name'            =>      'required',
            'rate'            =>      'required',
            'status'          =>      'required'
        ]);
        try {
            $data=Taxes::findOrFail($id);
            $data->name = $request->name;
            $data->rate = $request->rate;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->update();

            return redirect()->route('admin.inventory.settings.tax.index')
                    ->with('toastr-success', 'Tax Updated Successfully');
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
                Taxes::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Tax Deleted Successfully.',
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
            $brand = Taxes::findOrFail($id);
            // Check Item Current Status
            if ($brand->status == 1) {
                $brand->status = 0;
            } else {
                $brand->status = 1;
            }

            $brand->save();
            return response()->json([
                'success' => true,
                'message' => 'Tax Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
