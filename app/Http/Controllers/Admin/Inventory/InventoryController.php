<?php

namespace App\Http\Controllers\Admin\Inventory;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Employee\Employee;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Purchase\PriceManagement;
use App\Models\Inventory\Return\SaleReturn;
use App\Models\Inventory\Sales\Sales;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Settings\Taxes;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
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
                    $product = InventoryProductCount::with('products','variant','warehouse')->groupBy('product_id','variant_id')->latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $product = InventoryProductCount::with('products','variant','warehouse')->where('warehouse_id',$mystore->id)->groupBy('product_id','variant_id')->latest()->get();
                }

                return DataTables::of($product)
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
                    ->addColumn('stock_in', function ($product) {
                        if($product->variant){
                            $stockIn = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id',null)->where('variant_id',$product->variant->id)->sum('stock_in');
                            $returnIn = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id','!=',null)->where('variant_id',$product->variant->id)->sum('stock_in');
                        }

                        else{
                            $stockIn = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id',null)->sum('stock_in');
                            $returnIn = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id','!=',null)->sum('stock_in');
                        }
                        if($product->variant){
                            $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id','!=',null)->where('sale_return_qty','=',null)->where('variant_id',$product->variant->id)->sum('stock_out');
                        }
                        else{
                            $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id','!=',null)->where('sale_return_qty','=',null)->sum('stock_out');
                        }
                        // ->where('sale_return_id',null)
                        $total =$stockIn;

                            return $total ;
                    })
                    ->addColumn('stock_out', function ($product) {
                        // if($product->variant){
                        //     $stockOut = InventoryProductCount::where('sale_return_qty',null)->where('sale_return_id',null)->where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_out');
                        // }
                        // else{
                        //     $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_qty',null)->sum('stock_out');
                        // }
                        if($product->variant){
                            $stockIn1 = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id',null)->where('variant_id',$product->variant->id)->sum('stock_in');
                            $stockIn = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_in');

                            $stockOut = InventoryProductCount::where('product_id',$product->products->id)->where('variant_id',$product->variant->id)->sum('stock_out');
                            }
                            else{
                                $stockIn1 = InventoryProductCount::where('product_id',$product->products->id)->where('sale_return_id',null)->sum('stock_in');
                                $stockIn = InventoryProductCount::where('product_id',$product->products->id)->sum('stock_in');
                                $stockOut = InventoryProductCount::where('product_id',$product->products->id)->sum('stock_out');
                            }
                            $prese = $stockIn-$stockOut;
                            return $stockIn1- $prese;

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
                        if($product->variant){
                            $var =  $product->variant->id;
                        }
                        else{
                             $var = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.product-count.inventory.details',['id'=>$product->id,'variant'=>$var]) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bx-show-alt"></i></a>
                            </div>';
                    })
                    ->rawColumns(['name','stock_out','present_qty','stock_in','code','action',])
                    ->make(true);
            }

            return view('admin.inventory.inventory.index');
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
            $variantProducts = ProductVariant::with('products','varients')->get();
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
            $invoices = Sales::get(['id','invoice_no']);
            return view('admin.inventory.sale-return.create',compact('taxs','variantProducts','invoices','mystore'));
        }
        catch (\Exception $exception) {
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
            'date' => 'required',
        ]);
         //file
        try {
            $sale = Sales::findOrFail($request->invoice_id);
            $data = new SaleReturn();

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/sale-return/documents/'), $filename);
                $data->document = $filename;
            }

            $data->reference_no='pr-' . date("Ymd") . '-'. date("his");
            $data->warehouse_id=$sale->warehouse_id;

            $data->user_id=Auth::user()->id;
            $data->supplier_id=$request->supplier_id;
            $data->total_qty=$request->total_qty;
            $data->total_discount=$request->order_discount;
            $data->total_tax=$request->order_tax_rate;
            $data->invoice_no=$sale->invoice_no;
            $data->date=$request->date;
            $data->sale_return_no= 1;
            $data->shipping_cost=$request->shipping_cost;
            $data->grand_total=$request->total;
            $data->paid_amount=0.00;
            $data->payment_status=1;
            $data->status=$request->status;
            $data->note=$request->note;
            $data->created_by=Auth::user()->id;
            $data->access_id=json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            $product_id = $request->product;
            foreach ($product_id as $i => $id) {
                // $products = Products::findOrFail($id);
                // $products->qty = $products->qty - $request->qty[$i];
                // $products->update();

                $warehouse = new InventoryProductCount();
                $warehouse->stock_in = $request->qty[$i];
                $warehouse->reference_no='pr-' . date("Ymd") . '-'. date("his");
                $warehouse->date=$request->date;
                $warehouse->warehouse_id=$sale->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->sale_return_id =$data->id;
                $warehouse->sale_return_qty=$request->qty[$i];
                $warehouse->supplier_id=$request->supplier_id;
                $warehouse->product_batch_id=$request->batch_no[$i];
                $warehouse->expired_date=$request->expired_date[$i];
                $warehouse->variant_id=$request->variant[$i];
                $warehouse->sale_unit_id=$request->purchase_unit[$i];

                if($request->order_tax_rate != null){
                    $warehouse->tax_rate=$request->order_tax_rate;
                }
                    $warehouse->discount=$request->discount[$i];
                $warehouse->net_unit_cost=$request->price[$i];
                $warehouse->sale_price=$request->price[$i];
                $warehouse->total=$request->subtotal[$i];

                $warehouse->status=$request->status;
                $warehouse->created_by=Auth::user()->id;
                $warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));
                $warehouse->save();

            }

            return redirect()->route('admin.inventory.sale-return.index')
                    ->with('toastr-success', 'Sale Return Successfully');
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
            $return = SaleReturn::findOrFail($id);
            $inventories = InventoryProductCount::with('products')->where('sale_return_id',$id)->get();

            $taxs =  Taxes::where('status',1)->get();
            $warehouses = InventoryWarehouse::get();
            $variantProducts = ProductVariant::with('products','varients')->get();
            $products = Products::with('productVarients')->get();

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
            $invoices = Sales::get(['id','invoice_no']);
            $purchase = Sales::where('invoice_no',$return->invoice_no)->first();

            $purchaseProduct = InventoryProductCount::with('warehouse','products','variant')
                                                ->where('sale_id',$purchase->id)
                                                ->where('sale_return_id',null)
                                                ->get();

            return view('admin.inventory.sale-return.edit',compact('warehouses','taxs','variantProducts','products','mystore','return','inventories','invoices','purchaseProduct'));
        }
        catch (\Exception $exception) {
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
        //dd($request->all());
        $request->validate([
            'date' => 'required',
        ]);
         //file
        try {
            $sale = Sales::findOrFail($request->invoice_id);
            $data =SaleReturn::findOrFail($id);

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/sale-return/documents/'), $filename);
                $data->document = $filename;
            }

            $data->reference_no='pr-' . date("Ymd") . '-'. date("his");
            $data->warehouse_id=$sale->warehouse_id;

            $data->user_id=Auth::user()->id;

            $data->total_qty=$request->total_qty;
            $data->total_discount=$request->order_discount;
            $data->total_tax=$request->order_tax_rate;
            $data->date=$request->date;
            $data->sale_return_no= 1;
            $data->invoice_no=$sale->invoice_no;
            $data->shipping_cost=$request->shipping_cost;
            $data->grand_total=$request->total;
            $data->paid_amount=0.00;
            $data->payment_status=1;
            $data->status=$request->status;
            $data->note=$request->note;
            $data->created_by=Auth::user()->id;
            $data->access_id=json_encode(UserRepository::accessId(Auth::id()));
            $data->update();

            $product_id = $request->product;
            $warehouses = InventoryProductCount::where('sale_return_id',$id)->get();

            foreach ($warehouses as $i => $productCount) {
                $productCount->delete();
            }
            foreach ($product_id as $i => $id) {
                $warehouse = new InventoryProductCount();
                $warehouse->stock_in = $request->qty[$i];
                $warehouse->reference_no='pr-' . date("Ymd") . '-'. date("his");
                $warehouse->date=$request->date;
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->sale_return_id =$data->id;
                $warehouse->sale_return_qty=$request->qty[$i];
                $warehouse->supplier_id=$request->supplier_id;
                $warehouse->product_batch_id=$request->batch_no[$i];
                $warehouse->expired_date=$request->expired_date[$i];
                $warehouse->variant_id=$request->variant[$i];
                $warehouse->sale_unit_id=$request->purchase_unit[$i];

                if($request->order_tax_rate != null){
                    $warehouse->tax_rate=$request->order_tax_rate;
                }
                    $warehouse->discount=$request->discount[$i];
                $warehouse->net_unit_cost=$request->price[$i];
                $warehouse->sale_price=$request->price[$i];
                $warehouse->total=$request->subtotal[$i];

                $warehouse->status=$request->status;
                $warehouse->created_by=Auth::user()->id;
                $warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));
                $warehouse->save();

            }

            return redirect()->route('admin.inventory.sale-return.index')
                    ->with('toastr-success', 'Sale Return Update Successfully');
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
                $sale =  SaleReturn::findOrFail($id);
                $inventory = InventoryProductCount::where('sale_return_id', $sale->id)->first();
                //$product = Products::findOrFail($inventory->product_id);
                $inventries = InventoryProductCount::where('sale_return_id', $sale->id)->get();
                foreach($inventries as $inventry){
                    $inventry->delete();
                }
                // $transactions = Transaction::where('sale_id', $sale->id)->get();
                // foreach($transactions as $transaction){
                //     $transaction->delete();
                // }

                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Sales Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function inevntoryDetails(Request $request, $id)
    {
        //\dd($request->variant);
            try {
                $price = [];
                $inventoryProduct = InventoryProductCount::with('products','variant')->where('id',$id)->first();

                $productVariant = '';
                if(isset($inventoryProduct->variant)){
                    $productVariant=Variant::findOrFail($inventoryProduct->variant->variant_id);
                }

                $inventories = InventoryProductCount::with('products','variant')->where('product_id',$inventoryProduct->product_id)->where('variant_id',$request->variant)->get();

                foreach($inventories as $inventoryPrice ){
                    if($inventoryPrice->variant)
                    {
                        $price [] = PriceManagement::where('product_id',$inventoryPrice->products->id)->where('variant_id',$inventoryPrice->variant->id)->latest()->first();
                    }
                    else{
                        $price [] = PriceManagement::where('product_id',$inventoryPrice->products->id)->latest()->first();
                    }
            }

                return view('admin.inventory.inventory.details',compact('inventoryProduct','productVariant','inventories','price'));
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
}




