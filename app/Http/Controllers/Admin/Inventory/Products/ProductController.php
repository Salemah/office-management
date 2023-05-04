<?php

namespace App\Http\Controllers\Admin\Inventory\Products;

use Keygen\Keygen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Settings\Taxes;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Settings\InventoryUnit;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\Inventory\Settings\InventoryBrand;
use App\Models\Inventory\Products\ProductWarehouse;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Products\InventoryProductCategory;

class ProductController extends Controller
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
                $product = Products::with('category','brands','units')->get();
                return DataTables::of($product)
                    ->addIndexColumn()
                    ->addColumn('image', function ($products) {
                        $url = asset('img/inventory/products/' . $products->image);
                        $url2 = asset('img/no-image/demo.png');
                        if ($products->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';

                    })
                    ->addColumn('description', function ($product) {
                        return $product->description;
                    })
                    ->addColumn('category', function ($product) {
                        return $product->category->name;
                    })
                    ->addColumn('brand', function ($product) {
                        return $product->brands->name;
                    })
                    ->addColumn('unit', function ($product) {
                        return $product->units->name;
                    })
                    ->addColumn('action', function ($product) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                                <a class="btn btn-sm btn-info text-white" title="History"style="cursor:pointer"href="' . route('admin.inventory.product.transaction', $product->id) . '"><i class="bx bx-file"> </i> </a>&nbsp;

                                  <a href="' . route('admin.inventory.product.edit', $product->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>&nbsp;

                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $product->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['image','action','brand','unit',  'description','category'])
                    ->make(true);
            }
            return view('admin.inventory.products.product.index');
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
            $brands = InventoryBrand::get();
            $units = InventoryUnit::where('base_unit',null)->get();
            $productCategories = InventoryProductCategory::get();
            $taxs = Taxes::where('status',1)->get();
            $warehouse_list = InventoryWarehouse::where('status',1)->get();
            return view('admin.inventory.products.product.create', compact('productCategories','brands','units','taxs','warehouse_list'));
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
            'name'            =>      'required ',
            'product_code'    =>      'required | unique:products,code',
            'category_id'     =>      'required',
            'image'     =>      'required',
        ]);
        try {
            $data                       =       new Products();
            $data->name                 =       $request->name;
            $data->category_id  =       $request->category_id ;
            if ($request->has('image')) {
                $imageUploade   =     $request->file('image');
                $imageName      =     time() . '.' . $imageUploade->getClientOriginalExtension();
                $imagePath      =     public_path('img/inventory/products/');
                $imageUploade->move($imagePath, $imageName);
                $data->image = $imageName;
            }

            $data->description      =       $request->description;
            $data->brand_id         =       $request->brand_id;
            $data->code             =       $request->product_code;
            $data->unit_id          =       $request->unit_id ;
            $data->sale_unit_id     =       $request->sale_unit_id ;
            $data->purchase_unit_id =       $request->purchase_unit_id ;
            $data->tax_id           =       $request->tax_id;
            $data->tax_method       =       $request->tax_method;
            $data->is_variant       =       $request->is_variant;
            $data->is_diffPrice      =       $request->is_diffPrice;
            if(!isset($request->is_batch))
            {
                $data->is_batch= null;
            }
            else{
                $data->is_batch= 1;
            }
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            if(isset($request->is_variant)) {
                foreach ($request->option as $key => $variant_name) {

                    $variantData = Variant::where('name',$variant_name)->first();

                    if(isset($variantData)){
                        $variant_data = $variantData;
                    }
                    else{
                        $variant_data =  new Variant();
                        $variant_data->name = $variant_name;
                        $variant_data->save();
                    }

                    $product_variant_data = new ProductVariant();
                    $product_variant_data->product_id = $data->id;
                    $product_variant_data->variant_id = $variant_data->id;
                    $product_variant_data->name = $variant_name;
                    $product_variant_data->value= $request->value[$key];
                    $product_variant_data->additional_price =$request->additional_price[$key];
                    $product_variant_data->additional_cost =$request->additional_cost[$key];
                    $product_variant_data->created_by=Auth::user()->id;
                    $product_variant_data->access_id=json_encode(UserRepository::accessId(Auth::id()));
                    $product_variant_data->save();
                }
            }
            if(isset($request->is_diffPrice)) {

                foreach ($request->diff_price as $key => $diff_price) {
                    if($diff_price) {
                        $differentPrice =  new ProductWarehouse();
                        $differentPrice->product_id = $data->id;
                        $differentPrice->warehouse_id =$request->warehouse_id[$key];
                        $differentPrice->price =$diff_price;
                        $differentPrice->save();
                    }
                }
            }

            return redirect()->route('admin.inventory.product.index')
                                ->with('toastr-success', 'Created Successfully');
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
            $product = Products::findOrFail($id);
            $brands = InventoryBrand::get();
            $units = InventoryUnit::get();
            $productCategories = InventoryProductCategory::get();
            $taxs = Taxes::where('status',1)->get();
            $warehouse_list = InventoryWarehouse::where('status',1)->get();
            $productVarient = ProductVariant::where('product_id',$id)->get();
            $productWarehouse =  ProductWarehouse::where('product_id',$id)->get();

            return view('admin.inventory.products.product.edit', compact('productCategories','brands','units','taxs','product','warehouse_list','productVarient','productWarehouse'));
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
            'name'            =>      'required ',
            'product_code'    =>      'required | unique:products,code,'.$id.',id',
            'category_id'     =>      'required',
        ]);
        try {
            $data = Products::findOrFail($id);
            $data->name = $request->name;
            $data->category_id  = $request->category_id ;
            if ($request->has('image')) {
                $imageUploade   =     $request->file('image');
                $imageName      =     time() . '.' . $imageUploade->getClientOriginalExtension();
                $imagePath      =     public_path('img/inventory/products/');
                $imageUploade->move($imagePath, $imageName);
                $data->image = $imageName;
            }

            $data->description      =       $request->description;
            $data->brand_id         =       $request->brand_id;
            $data->code             =       $request->product_code;
            $data->unit_id          =       $request->unit_id ;
            $data->sale_unit_id     =       $request->sale_unit_id ;
            $data->purchase_unit_id =       $request->purchase_unit_id ;
            $data->tax_id           =       $request->tax_id;
            $data->tax_method       =       $request->tax_method;
            $data->is_variant       =       $request->is_variant;
            $data->is_diffPrice      =       $request->is_diffPrice;
            if(!isset($request->is_batch))
            {
                $data->is_batch= null;
            }
            else{
                $data->is_batch= 1;
            }
            $data->updated_by       =       Auth::user()->id;
            $data->update();

            if(isset($request->is_variant)) {
                foreach ($request->option as $key => $variant_name) {
                    $variantData = Variant::where('name',$variant_name)->first();
                    if(isset($variantData)){
                        $variant_data = $variantData;
                    }
                    else{
                        $variant_data =  new Variant();
                        $variant_data->name = $variant_name;
                        $variant_data->save();
                    }

                    $product_variant_data = ProductVariant::where('variant_id',$variantData->id)->first();
                    if(!$product_variant_data)
                    {
                        $product_variant_data = new ProductVariant();
                    }
                    $product_variant_data->product_id = $data->id;
                    $product_variant_data->variant_id = $variant_data->id;
                    $product_variant_data->name = $variant_name;
                    $product_variant_data->value= $request->value[$key];
                    $product_variant_data->additional_price =$request->additional_price[$key];
                    $product_variant_data->additional_cost =$request->additional_cost[$key];
                    $product_variant_data->created_by=Auth::user()->id;
                    $product_variant_data->access_id=json_encode(UserRepository::accessId(Auth::id()));

                    if(!$product_variant_data)
                    {
                        $product_variant_data->save();
                    }
                    else{
                        $product_variant_data->update();
                    }
                }
            }
            if(isset($request->is_diffPrice)) {

                foreach ($request->diff_price as $key => $diff_price) {
                    if($diff_price) {
                        $differentPrice =  new ProductWarehouse();
                        $differentPrice->product_id = $data->id;
                        $differentPrice->warehouse_id =$request->warehouse_id[$key];
                        $differentPrice->price =$diff_price;
                        $differentPrice->save();
                    }
                }
            }

            return redirect()->route('admin.inventory.product.index')
                                ->with('toastr-success', 'Update Successfully');
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
                Products::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product  Deleted Successfully.',
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
    public function generateCode()
    {
        $id = random_int ( 00000000,99999999);
        $productCode = Products::where('code',$id)->first();
        if(!$productCode){
            return $id;
        }
    }

    public function unitSearch(Request $request,$id)
    {
        $unit = InventoryUnit::where("base_unit", $id)->orWhere('id', $id)->pluck('name','id');
        return json_encode($unit);
    }
    public function brandSearch(Request $request)
    {
        $result = InventoryBrand::query()
                        ->limit(10)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);

        return $result;
    }
    public function ProductTransaction(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $product = DB::table('inventory_product_counts')
                        ->where('product_id', $id)
                        ->whereNull('deleted_at')
                        ->get();

                    return Datatables::of($product)

                    ->addColumn('purchaseQuantity', function ($product) {
                        $total = $product->stock_in ?? '0';
                        return $total;
                    })

                    ->addColumn('saleQuantity', function ($product) {
                        $total = $product->stock_out ?? '0';
                        return $total;
                    })

                    ->addColumn('totalPurchase', function ($product) {
                        if ($product->purchase_id) {
                            $total = $product->total ?? '--';
                        }else{
                            $total = '0';
                        }
                        return $total;
                    })

                    ->addColumn('totalSale', function ($product) {
                        if ($product->sale_id) {
                            $total = $product->total ?? '--';
                        }else{
                            $total = '0';
                        }
                        return $total;
                    })

                    ->addColumn('currentStock', function ($product) use(&$currentStock){
                        $stockIn = $product->stock_in;
                        $stockOut = $product->stock_out;
                        $currentStock += $stockIn - $stockOut;
                        return $currentStock;
                    })

                    ->addColumn('Balance', function ($product)  use(&$currentBalance){
                        if ($product->purchase_id) {
                            $purchaseAmount = $product->total ?? '--';
                            $currentBalance += $purchaseAmount;
                        }

                        if ($product->sale_id) {
                            $saleAmount = $product->total ?? '--';
                            $currentBalance -= $saleAmount;
                        }

                        return $currentBalance;
                    })

                    ->addIndexColumn()
                    ->rawColumns(['purchaseQuantity','saleQuantity','currentStock','totalPurchase','totalSale','Balance'])
                    ->toJson();
            }

            return view('admin.inventory.products.product.transaction');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
