<?php

namespace App\Http\Controllers\Admin\Inventory\Settings;

use App\Http\Controllers\Controller;

use App\Models\Inventory\Settings\InventoryUnit;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InventoryUnitController extends Controller
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
                // $Warehouse = Warehouse::latest()->get();
                $unit = InventoryUnit::get();
                return DataTables::of($unit)
                    ->addIndexColumn()

                    ->addColumn('status', function ($unit) {
                        if ($unit->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $unit->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $unit->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('description', function ($unit) {
                        return Str::limit($unit->description, 20, $end = '.....');
                    })

                    ->addColumn('base_unit', function ($unit) {
                        $Categories  =  InventoryUnit::get();
                        foreach($Categories as $category){
                            if( $category->id == $unit->base_unit){
                                return $category->name;
                            }
                        }
                        return "N/A";
                    })
                    ->addColumn('operation_value', function ($unit) {
                            if($unit->base_unit){
                                return $unit->operation_value;
                            }
                        return "N/A";
                    })

                    ->addColumn('action', function ($unit) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.settings.unit.edit', $unit->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $unit->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['image','action', 'status', 'description','base_unit'])
                    ->make(true);
            }
            return view('admin.inventory.settings.unit.index');
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
            $units = InventoryUnit::get();
            return view('admin.inventory.settings.unit.create', compact('units'));
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
            'name'            =>      'required|string|unique:inventory_brands',
            'description'     =>      'string|nullable',
            'unit_code'     =>      'required|unique:inventory_units,unit_code',
            'status'          =>      'required|numeric'
        ]);
        if($request->base_unit !=null )
        {
            $request->validate([
                'operation_value'            =>      'required',
                'operator'            =>      'required',
            ]);
        }

        try {
            $data                       =       new InventoryUnit();
            $data->name                 =       $request->name;
            $data->unit_code            =       $request->unit_code;
            $data->base_unit            =       $request->base_unit;
            $data->operation_value      =       $request->operation_value;
            $data->operator             =       $request->operator;
            $data->description          =       $request->description;
            $data->status               =       $request->status;
            $data->created_by           =       Auth::user()->id;
            $data->access_id            =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.settings.unit.index')
                    ->with('toastr-success', 'Unit Added Successfully');
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
            $unit      =   InventoryUnit::findOrFail($id);
            $baseUnit  =   InventoryUnit::where('id', $unit->base_unit)->first();
            $units     =   InventoryUnit::get();
            return view('admin.inventory.settings.unit.edit',
                compact('unit', 'units', 'baseUnit'));
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
            'name'            =>      'required|string|unique:inventory_brands,'.$id,
            'description'     =>      'string|nullable',
            'unit_code'     =>      'required|unique:inventory_units,unit_code,'.$id,
            'status'          =>      'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data                       =       InventoryUnit::where('id', $id)->first();
            $data->name                 =       $request->name;
            $data->unit_code            =       $request->unit_code;
            $data->base_unit            =       $request->base_unit;
            $data->operation_value      =       $request->operation_value;
            $data->operator             =       $request->operator;
            $data->description          =       $request->description;
            $data->status               =       $request->status;
            $data->updated_by           =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.settings.unit.index')
                    ->with('toastr-success', 'Unit Updated Successfully');
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
                InventoryUnit::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Unit Deleted Successfully.',
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
            $unit = InventoryUnit::findOrFail($id);
            // Check Item Current Status
            if ($unit->status == 1) {
                $unit->status = 0;
            } else {
                $unit->status = 1;
            }

            $unit->save();
            return response()->json([
                'success' => true,
                'message' => 'Unit Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




