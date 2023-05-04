<?php

namespace App\Http\Controllers\Admin\Inventory\Purchase;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\Inventory\Products\ProductWarehouse;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Purchase\PriceManagement;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Services\InventoryServiceCategory;
use App\Models\Inventory\Settings\Taxes;
use Carbon\Carbon;

class PriceManagementController extends Controller
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
                $serviceCategory = [];
                $serviceCategories = PriceManagement::with('products','variants')->groupBy('product_id','variant_id')->latest()->get();
                foreach($serviceCategories as $price){
                    $stock_count = PriceManagement::with('products','variants')->where('product_id',$price->product_id)->where('variant_id',$price->variant_id)->latest()->first();
                    $serviceCategory[] = $stock_count;
                }
                return DataTables::of($serviceCategory)
                    ->addIndexColumn()
                    ->addColumn('product', function ($serviceCategory) {
                        if($serviceCategory->variants){
                            return $serviceCategory->variants->value.$serviceCategory->variants->name.'-'.$serviceCategory->products->name;
                        }
                        else{
                            return $serviceCategory->products->name;
                        }

                    })
                    ->addColumn('code', function ($serviceCategory) {
                        return $serviceCategory->products->code;
                    })
                    ->addColumn('date', function ($serviceCategory) {
                        return  Carbon::parse($serviceCategory->created_at)->format('d/m/Y')  ;
                    })
                    ->addColumn('action', function ($serviceCategory) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $serviceCategory->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })

                    ->rawColumns(['product','code','date','action'])
                    ->make(true);
            }
            return view('admin.inventory.purchases.price_management.index');
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
            $taxs =  Taxes::where('status',1)->get();
            $warehouses = ProductWarehouse::get();
            return view('admin.inventory.purchases.price_management.create',
                compact('warehouses','taxs'));
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
            'product_id'=>'required',
            'cost'=>'required',
            'price'=>'string',
            'wholesale_price'=>'string',
        ]);

        try {
            $varid = explode(',', $request->product_id);

            $priceData = new  PriceManagement();
            if(isset($varid[1])){
                $priceData->product_id = $varid[1];
                $priceData->variant_id = $varid[0];
            }
            else{
                $priceData->product_id = $varid[0];
            }
            $priceData->cost = $request->cost;
            $priceData->price =$request->price;
            $priceData->wholesale_price =$request->wholesale_price;
            $priceData->save();

            return redirect()->route('admin.inventory.price-management.index')
                    ->with('toastr-success', ' Created Successfully');
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
            $serviceCategory     =   InventoryServiceCategory::findOrFail($id);
            $category            =   InventoryServiceCategory::where('id', $serviceCategory->parent_category)->first();
            $serviceCategories   =   InventoryServiceCategory::get();
            return view('admin.inventory.services.service-category.edit',
                compact('serviceCategory', 'serviceCategories', 'category'));
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
            $data                       =       InventoryServiceCategory::where('id', $id)->first();
            $data->name                 =       $request->name;
            $data->category_code        =       $request->category_code;
            $data->parent_category      =       $request->parent_category;
            $data->description          =       strip_tags($request->description);;
            $data->status               =       $request->status;
            $data->updated_by           =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.inventory.services.category.index')
                    ->with('toastr-success', 'Service Category Updated Successfully');
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
                PriceManagement::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => ' Deleted Successfully.',
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


     public function productSearch(Request $request)
     {

        $product = Products::with('productVarients')->get();

         foreach( $product as  $key=>$inventory){
                 if(count($inventory->productVarients)>0){
                    foreach($inventory->productVarients as $variant){

                       $result[] = array_merge(array( $inventory),array($variant));
                    }
                    //  $variants = ProductVariant::where('product_id',$inventory->id)->first();
                    //  $final = array_merge(array( $inventory),array($variants));
                    //  $result[] = $final;
                 }
                 else{
                     $result[] = $inventory;
                 }
         }
        //$result[] =$product;
        //dd($result);
         return $result;
     }
    public function productPriceSearch(Request $request,$id)
    {
         $result = Products::findOrFail($id);
        return response()->json($result);
    }


}




