<?php

namespace App\Http\Controllers\Admin\Inventory\Products\RawMaterial;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Settings\InventoryUnit;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Products\RawMeterial\Rawmaterial;

class RawMaterialController extends Controller
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
                $rawMaterial = Rawmaterial::get();
                return DataTables::of($rawMaterial)
                        ->addColumn('name', function ($rawMaterial) {
                            return $rawMaterial->name;
                    })
                    ->addColumn('wirehouse_name', function ($rawMaterial) {
                        $wirehouses  = InventoryWarehouse::get();
                        foreach($wirehouses as $wirehouse){
                            if( $wirehouse->id == $rawMaterial->wirehouse_id){
                                return $wirehouse->name;
                            }
                        }
                        return "---";
                    })
                    ->addColumn('unit_id', function ($rawMaterial) {
                        $units  = InventoryUnit::get();
                        foreach($units as $unit){
                            if( $unit->id == $rawMaterial->unit_id){
                                return $unit->name;
                            }
                        }
                        return "---";
                    })
                    ->addColumn('status', function ($rawMaterial) {
                        if ($rawMaterial->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $rawMaterial->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $rawMaterial->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('base_price', function ($rawMaterial) {
                         return $rawMaterial->base_price;
                    })
                    ->addColumn('action', function ($rawMaterial) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.raw-material.edit', $rawMaterial->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a> &nbsp;&nbsp;
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $rawMaterial->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['wirehouse_name', 'base_price','unit_id', 'action', 'status'])
                    ->make(true);
            }
            return view('admin.inventory.products.raw-material.index');
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
        $warehouses = InventoryWarehouse::all();
        $units = InventoryUnit::all();
       return view('admin.inventory.products.raw-material.create', compact('warehouses','units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {				
        $request->validate([
            'name' => 'required',
            'wirehouse_id' => 'required|numeric',
            'unit_id'=> 'required|numeric',
            'description' => 'string|nullable',
            'status' => 'required|numeric',
        ]);

        try{
            $data = new Rawmaterial();
            $data->name = $request->name;
            $data->wirehouse_id = $request->wirehouse_id;
            $data->unit_id = $request->unit_id;
            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            return redirect()->route('admin.raw-material.index')
            ->with('toastr-success', 'Product Category Created Successfully');
        }catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $warehouses = InventoryWarehouse::all();
        $units = InventoryUnit::all();
        $rawMaterial = Rawmaterial::find($id);
        return view('admin.inventory.products.raw-material.edit', compact('warehouses','units', 'rawMaterial')); 
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
        $request->validate([
            'name' => 'required',
            'wirehouse_id' => 'required|numeric',
            'unit_id'=> 'required|numeric',
            'description' => 'string|nullable',
            'status' => 'required|numeric',
        ]);
        try{
            $data =Rawmaterial::where('id', $id)->first();
            $data->name = $request->name;
            $data->wirehouse_id = $request->wirehouse_id;
            $data->unit_id = $request->unit_id;
            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            return redirect()->route('admin.raw-material.index')
            ->with('toastr-success', 'Product Category Created Successfully');
        }catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                Rawmaterial::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product Category Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    
    public function statusUpdate(Request $request, $id)
    {
        try {
            $data = Rawmaterial::findOrFail($id);
            // Check Item Current Status
            if ($data->status == 1) {
                $data->status = 0;
            } else {
                $data->status = 1;
            }

            $data->save();
            return response()->json([
                'success' => true,
                'message' => 'Category of Peroduct Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
