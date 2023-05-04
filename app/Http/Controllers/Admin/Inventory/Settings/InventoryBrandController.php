<?php

namespace App\Http\Controllers\Admin\Inventory\Settings;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Settings\InventoryBrand;

class InventoryBrandController extends Controller
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
                $brand = InventoryBrand::get();
                return DataTables::of($brand)
                    ->addIndexColumn()
                    ->addColumn('image', function ($brand) {
                        $url = asset('img/inventory/setting/brand/' . $brand->image);
                        $url2 = asset('img/no-image/demo.png');
                        if ($brand->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';

                    })
                    ->addColumn('status', function ($brand) {
                        if ($brand->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $brand->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $brand->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($brand) {
                        return Str::limit($brand->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($brand) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.settings.brand.edit', $brand->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $brand->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['image','action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.inventory.settings.brand.index');
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
            return view('admin.inventory.settings.brand.create');
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
            $data                   =       new InventoryBrand();
            $data->name             =       $request->name;

            if ($request->has('image')) {
                $imageUploade   =     $request->file('image');
                $imageName      =     time() . '.' . $imageUploade->getClientOriginalExtension();
                $imagePath      =     public_path('img/inventory/setting/brand');
                $imageUploade->move($imagePath, $imageName);
                $data->image = $imageName;
            }


            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.settings.brand.index')
                    ->with('toastr-success', 'Brand Created Successfully');
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
            $brand = InventoryBrand::findOrFail($id);
            return view('admin.inventory.settings.brand.edit', compact('brand'));
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
            $data                   =       InventoryBrand::where('id', $id)->first();
            $data->name             =       $request->name;

            if ($request->has('image')) {
                $imagePath = public_path('img/inventory/setting/brand');
                $old_image = $imagePath . $data->image;
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
                $imageUpdate = $request->file('image');
                $imageName   = time() . '.' . $imageUpdate->getClientOriginalExtension();
                $imageUpdate->move($imagePath, $imageName);
                $data->image = $imageName;
            } else {
                $imageName = 'brand.jpg';
            }

            $data->description      =       strip_tags($request->description);;
            $data->status           =       $request->status;
            $data->updated_by       =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.settings.brand.index')
                    ->with('toastr-success', 'Brand Updated Successfully');
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
                InventoryBrand::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Brand Deleted Successfully.',
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
            $brand = InventoryBrand::findOrFail($id);
            // Check Item Current Status
            if ($brand->status == 1) {
                $brand->status = 0;
            } else {
                $brand->status = 1;
            }

            $brand->save();
            return response()->json([
                'success' => true,
                'message' => 'Brand Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
