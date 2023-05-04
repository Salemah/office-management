<?php

namespace App\Http\Controllers\Admin\Inventory\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class InventoryWarehouseController extends Controller
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

                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $Warehouse = InventoryWarehouse::get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $Warehouse = InventoryWarehouse::where('id',$mystore->id)->get();
                }

                //$Warehouse = InventoryWarehouse::get();
                return DataTables::of($Warehouse)
                    ->addIndexColumn()
                    ->addColumn('status', function ($Warehouse) {
                        if ($Warehouse->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $Warehouse->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $Warehouse->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($Warehouse) {
                        return Str::limit($Warehouse->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($Warehouse) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.settings.warehouse.edit', $Warehouse->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Warehouse->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.inventory.settings.warehouse.index');
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
            return view('admin.inventory.settings.warehouse.create');
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
            'name'            =>      'required|string',
            'description'     =>      'string|nullable',
            'status'          =>      'required|numeric'
        ]);

        try {
            $data                   =       new InventoryWarehouse();
            $data->name             =       $request->name;
            $data->phone            =       $request->phone;
            $data->email            =       $request->email;
            $data->address          =       $request->address;
            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.settings.warehouse.index')
                    ->with('toastr-success', 'Warehouse Created Successfully');
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
            $warehouse = InventoryWarehouse::findOrFail($id);
            return view('admin.inventory.settings.warehouse.edit', compact('warehouse'));
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
            'name'            =>      'required|string',
            'description'     =>      'string|nullable',
            'status'          =>      'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data                   =       InventoryWarehouse::where('id', $id)->first();
            $data->name             =       $request->name;
            $data->description      =       strip_tags($request->description);;
            $data->status           =       $request->status;
            $data->updated_by       =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.settings.warehouse.index')
                    ->with('toastr-success', 'Waregouse Updated Successfully');
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
                InventoryWarehouse::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouse Deleted Successfully.',
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
            $warehouse = InventoryWarehouse::findOrFail($id);
            // Check Item Current Status
            if ($warehouse->status == 1) {
                $warehouse->status = 0;
            } else {
                $warehouse->status = 1;
            }

            $warehouse->save();
            return response()->json([
                'success' => true,
                'message' => 'Warehouse Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function warehouseSearch(Request $request)
    {
        try {
            $result = InventoryWarehouse::query()
                        ->limit(10)
                        ->get(['name', 'id']);
        return $result;
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
