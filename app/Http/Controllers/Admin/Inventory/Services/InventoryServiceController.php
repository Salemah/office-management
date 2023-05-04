<?php

namespace App\Http\Controllers\Admin\Inventory\Services;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Services\InventoryService;
use App\Models\Inventory\Services\InventoryServiceCategory;

class InventoryServiceController extends Controller
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
                $serviceCategory = InventoryService::get();
                return DataTables::of($serviceCategory)
                    ->addIndexColumn()

                    ->addColumn('status', function ($serviceCategory) {
                        if ($serviceCategory->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $serviceCategory->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $serviceCategory->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('description', function ($serviceCategory) {
                        return Str::limit($serviceCategory->description, 20, $end = '.....');
                    })

                    ->addColumn('service_category', function ($serviceCategory) {
                        $Categories  = InventoryServiceCategory::get();
                        foreach($Categories as $category){
                            if( $category->id == $serviceCategory->service_category){
                                return $category->name;
                            }
                        }
                        return "---";
                    })

                    ->addColumn('action', function ($serviceCategory) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.services.service.edit', $serviceCategory->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $serviceCategory->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description', 'service_category'])
                    ->make(true);
            }
            return view('admin.inventory.services.service.index');
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
            $serviceCategories = InventoryServiceCategory::get();
            return view('admin.inventory.services.service.create',
                compact('serviceCategories'));
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
        // dd($request->all());
        $request->validate([
            'name'                =>    'required|string|unique:inventory_services',
            'service_category'    =>    'required',
            'service_code'        =>    'required',
            'base_price'          =>    'required',
            'description'         =>    'string|nullable',
            'status'              =>    'required|numeric'
        ]);

        try {
            $data                       =       new InventoryService();
            $data->name                 =       $request->name;
            $data->service_category     =       $request->service_category;
            $data->service_code         =       $request->service_code;
            $data->base_price           =       $request->base_price;
            $data->description          =       strip_tags($request->description);
            $data->status               =       $request->status;
            $data->created_by           =       Auth::user()->id;
            $data->access_id            =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.services.service.index')
                    ->with('toastr-success', 'Service Created Successfully');
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
            $service             =   InventoryService::findOrFail($id);
            // $category            =   InventoryService::where('id', $service->service_category)->first();
            $serviceCategories   =   InventoryServiceCategory::get();
            return view('admin.inventory.services.service.edit',
                compact('service', 'serviceCategories'));
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
            'name'                  =>      'required|string',
            'service_category'      =>      'required',
            'service_code'          =>      'required',
            'base_price'            =>      'required',
            'description'           =>      'string|nullable',
            'status'                =>      'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data                       =       InventoryService::where('id', $id)->first();
            $data->name                 =       $request->name;
            $data->service_code         =       $request->service_code;
            $data->service_category     =       $request->service_category;
            $data->base_price           =       $request->base_price;
            $data->description          =       strip_tags($request->description);;
            $data->status               =       $request->status;
            $data->updated_by           =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.services.service.index')
                    ->with('toastr-success', 'Service Updated Successfully');
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
                InventoryService::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Service Deleted Successfully.',
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
            $service = InventoryService::findOrFail($id);
            // Check Item Current Status
            if ($service->status == 1) {
                $service->status = 0;
            } else {
                $service->status = 1;
            }

            $service->save();
            return response()->json([
                'success' => true,
                'message' => 'Service Status Updated Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




