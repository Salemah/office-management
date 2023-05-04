<?php

namespace App\Http\Controllers\Admin\Inventory\Products;

use DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Products\InventoryProductCategory;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Hamcrest\Arrays\IsArray;

class DamageProductController extends Controller
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
                $productCategory = InventoryProductCount::where('product_type','!=',1)->get();
                return DataTables::of($productCategory)
                ->addIndexColumn()
                ->addColumn('name', function ($product) {
                    if($product->variant){
                        return $product->variant->value.$product->variant->name.'-'.$product->products->name;
                    }
                    else{
                        return $product->products->name;
                    }
                })
                ->addColumn('code', function ($product) {
                    return $product->products->code;
                })
                ->addColumn('product_type', function ($product) {
                    if($product->product_type == 2){
                        return '<span class="badge bg-primary">Broken</span>';
                    }
                    else if($product->product_type == 3){
                        return '<span class="badge bg-success text-dark">Expired</span>';
                    }
                    else if($product->product_type == 4){
                        return '<span class="badge bg-info text-dark">Sample</span>';
                    }

                })
                ->addColumn('stock_in', function ($product) {
                    if($product->variant){
                        $stockIn = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_in');
                    }
                    else{
                        $stockIn = InventoryProductCount::where('product_id',$product->products->id)->sum('stock_in');
                    }
                        return $stockIn;
                })
                ->addColumn('stock_out', function ($product) {
                    if($product->variant){
                        $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->where('product_type','!=',$product->product_type)->sum('stock_out');
                    }
                    else{
                        $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('product_type','!=',$product->product_type)->sum('stock_out');
                    }
                    return $stockOut;
                })
                ->addColumn('adjust_qty', function ($product) {
                    if($product->variant){
                        $adjustQty = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->where('product_type',$product->product_type)->sum('stock_out');
                    }
                    else{
                        $adjustQty = InventoryProductCount::where('product_id',$product->products->id)->where('product_type',$product->product_type)->sum('stock_out');
                    }
                    return $adjustQty;
                })
                ->addColumn('present_qty', function ($product) {
                    if($product->variant){
                        $stockIn = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_in');
                        $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_out');
                        }
                        else{
                            $stockIn = InventoryProductCount::where('product_id',$product->products->id)->sum('stock_in');
                            $stockOut = InventoryProductCount::where('product_id',$product->products->id)->sum('stock_out');
                        }
                        return $stockIn-$stockOut;
                })
                ->addColumn('action', function ($product) {
                    return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a href="' . route('admin.inventory.damage-product.edit',$product->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                        </div>';
                })
                ->rawColumns(['adjust_qty','name','product_type','stock_out','present_qty','stock_in','code','action',])
                ->make(true);
            }
            return view('admin.inventory.damage-product.index');
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
            $products = Products::with('productVarients')->get();
            $warehouses = InventoryWarehouse::get();
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $mystore = '';
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
            }
            return view('admin.inventory.damage-product.create', compact('products','warehouses','mystore'));
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
            'warehouse_id'  => 'required',
            'type' => 'required',
            'product_id'=>'required',
            'quantity' => 'required',
        ]);

        try {
            $Ids =explode(",",$request->product_id);
           // dd($Ids);
            $warehouse = new InventoryProductCount();
                $warehouse->reference_no='pr-' . date("Ymd") . '-'. date("his");
                $warehouse->date=$request->date;
                $warehouse->stock_out = $request->quantity;
                if(is_array($Ids)){
                    if(isset($Ids[1])){
                        $warehouse->product_id=$Ids[1];
                        $warehouse->variant_id=$Ids[0];
                    }
                    else{
                        $warehouse->product_id=$request->product_id;
                    }
                }


                $warehouse->net_unit_cost=$request->price;
                $warehouse->total=$request->subtotal;
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_type=$request->type;
                $warehouse->note=$request->description;
                $warehouse->created_by=Auth::user()->id;
                $warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));
                //dd($warehouse);
                $warehouse->save();
            return redirect()->route('admin.inventory.damage-product.index')
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
        // \dd($id);
        try {
            $prodduct=InventoryProductCount::with('products','variant')->findOrFail($id);
            $products = Products::with('productVarients')->get();
            $warehouses = InventoryWarehouse::get();
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $mystore = '';
            }
            else{
                $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                $employee=Employee::where('id',$user->user_id)->first();
                $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
            }
            return view('admin.inventory.damage-product.edit', compact('prodduct','products','warehouses','mystore'));

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
            'warehouse_id'  => 'required',
            'type' => 'required',
            'product_id'=>'required',
            'quantity' => 'required',
        ]);

        try {
            $Ids =explode(",",$request->product_id);
            $warehouse = InventoryProductCount::findOrFail($id);

                $warehouse->date=$request->date;
                $warehouse->stock_out = $request->quantity;

                if($Ids[1]){
                    $warehouse->product_id=$Ids[1];
                    $warehouse->variant_id=$Ids[0];
                }
                else{
                    $warehouse->product_id = $request->product_id;
                }
                $warehouse->net_unit_cost=$request->price;
                $warehouse->total=$request->subtotal;
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_type=$request->type;
                $warehouse->note=$request->description;
                $warehouse->updated_by=Auth::user()->id;
                //$warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));
                //dd($warehouse);
                $warehouse->update();

            return redirect()->route('admin.inventory.damage-product.index')
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

    public function productSearch(Request $request)
    {
        $inventories = InventoryProductCount::with('products','variant')
                                        ->where('warehouse_id',$request->warehouse_id)
                                        ->where('purchase_id', '<>', '', 'and')
                                        ->where('purchase_return_id',null)
                                        ->get();

        foreach( $inventories as  $key=>$inventory){
                if($inventory->variant_id){
                    $variants = Variant::findOrFail($inventories[$key]->variant->id);
                    $final = array_merge(array( $inventory),array($variants));
                    $result[] = $final;
                }
                else{
                    $result[] = $inventory;
                }
        }

        return $result;
    }
}




