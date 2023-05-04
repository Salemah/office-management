<?php

namespace App\Http\Controllers\Admin\Inventory\Products;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Products\InventoryProductCategory;
use App\Models\Inventory\Settings\InventoryBrand;

class InventoryProductCategoriesController extends Controller
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
                $productCategory = InventoryProductCategory::get();
                return DataTables::of($productCategory)
                    ->addIndexColumn()

                    ->addColumn('image', function ($productCategory) {
                        $url = asset('img/inventory/products/category/' . $productCategory->image);
                        $url2 = asset('img/no-image/demo.png');
                        if ($productCategory->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';

                    })

                    ->addColumn('status', function ($productCategory) {
                        if ($productCategory->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $productCategory->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $productCategory->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('description', function ($productCategory) {
                        return Str::limit($productCategory->description, 20, $end = '.....');
                    })

                    ->addColumn('parent_category', function ($productCategory) {
                        $Categories  = InventoryProductCategory::get();
                        foreach($Categories as $category){
                            if( $category->id == $productCategory->parent_category){
                                return $category->name;
                            }
                        }
                        return "---";
                    })

                    ->addColumn('action', function ($productCategory) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.products.category.edit', $productCategory->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $productCategory->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['image','action', 'status', 'description','parent_category'])
                    ->make(true);
            }
            return view('admin.inventory.products.product-category.index');
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
            $productCategories = InventoryProductCategory::get();
            return view('admin.inventory.products.product-category.create', compact('productCategories'));
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
            'name'            =>      'required|unique:inventory_product_categories,name',
            'status'          =>      'required|numeric',
            'category_code'  =>      'required|unique:inventory_product_categories,category_code'
        ]);

        try {
            $data                       =       new InventoryProductCategory();
            $data->name                 =       $request->name;
            if($request->parent_category){
                $data->parent_category      =       $request->parent_category;
            }else{
                $data->parent_category = 0;
            }

            $data->category_code        =       $request->category_code;

            if ($request->has('image')) {
                $imageUploade   =     $request->file('image');
                $imageName      =     time() . '.' . $imageUploade->getClientOriginalExtension();
                $imagePath      =     public_path('img/inventory/products/category');
                $imageUploade->move($imagePath, $imageName);
                $data->image = $imageName;
            }

            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.products.category.index')
                    ->with('toastr-success', 'Product Category Created Successfully');
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
            $productCategory     =   InventoryProductCategory::findOrFail($id);
            $category            =   InventoryProductCategory::where('id', $productCategory->parent_category)->first();
            $productCategories   =   InventoryProductCategory::get();
            return view('admin.inventory.products.product-category.edit',
                compact('productCategory', 'productCategories', 'category'));
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
        // dd($request->all());
        // Validation Start
        $request->validate([
            'name'            =>      'required|unique:inventory_product_categories,name,'. $id,
            'status'          =>      'required|numeric',
            'category_code'  =>      'required|unique:inventory_product_categories,category_code,'. $id,
        ]);
        // Validation End

        // Store Data
        try {
            $data                   =    InventoryProductCategory::where('id', $id)->first();
            $data->name             =    $request->name;
            $data->category_code    =    $request->category_code;
            $data->parent_category  =    $request->parent_category;

            if ($request->has('image')) {
                $imagePath = public_path('img/inventory/products/category');
                $old_image = $imagePath . $data->image;
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
                $imageUpdate = $request->file('image');
                $imageName   = time() . '.' . $imageUpdate->getClientOriginalExtension();
                $imageUpdate->move($imagePath, $imageName);
                $data->image = $imageName;
            } else {
                $imageName = 'category.jpg';
            }

            $data->description      =       strip_tags($request->description);;
            $data->status           =       $request->status;
            $data->updated_by       =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.products.category.index')
                    ->with('toastr-success', 'Product Category Updated Successfully');
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
                InventoryProductCategory::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product Category Deleted Successfully.',
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
            $productCategory = InventoryProductCategory::findOrFail($id);
            // Check Item Current Status
            if ($productCategory->status == 1) {
                $productCategory->status = 0;
            } else {
                $productCategory->status = 1;
            }

            $productCategory->save();
            return response()->json([
                'success' => true,
                'message' => 'Category of Peroduct Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




