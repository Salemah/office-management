<?php

namespace App\Http\Controllers\Admin\Inventory\Sale;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Sales\Sales;
use App\Models\Inventory\Settings\Taxes;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Return\SaleReturn;
use App\Models\Inventory\Settings\InventoryUnit;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\Inventory\Purchase\PriceManagement;
use App\Models\Inventory\Products\ProductWarehouse;
use App\Models\Inventory\Customers\InventoryCustomer;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Suppliers\InventorySupplier;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Services\InventoryServiceCategory;

class SaleController extends Controller
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
                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $products = Sales::get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $products = Sales::where('warehouse_id',$mystore->id)->get();
                }
                return DataTables::of( $products)
                    ->addIndexColumn()
                    ->addColumn('action', function ($products) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                                    <a class="btn btn-sm btn-secondary text-white" title="History"style="cursor:pointer"href="' . route('admin.inventory.sale.history', $products->id) . '"><i class="bx bx-file"> </i> </a>&nbsp;

                                  <a class="btn btn-sm btn-info text-white" title="Show"style="cursor:pointer"href="' . route('admin.inventory.sale.show', $products->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                                  <a href="' . route('admin.inventory.sale.edit', $products->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>&nbsp;
                                  <button class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Add Payment" data-coreui-toggle="modal" data-coreui-target="#exampleModal" onclick="getSelectedUserData(' . $products->id . ')" ><i class="bx bx-money" style="color: #FC3207;"></i></button>&nbsp;
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' .$products->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                               </div>';
                    })
                    ->addColumn('customer', function ($products) {
                        return $products->customers->name;
                    })
                    ->addColumn('paid', function ($products) {
                        $paidAmount = Transaction::where('sale_id',$products->id)->sum('amount');
                        return number_format($paidAmount,2);
                    })
                    ->addColumn('grand_total', function ($products) {
                        return number_format($products->grand_total,2);
                    })
                    ->addColumn('due', function ($products) {
                        $paidAmount = Transaction::where('sale_id',$products->id)->sum('amount');
                        return  number_format($products->grand_total - $paidAmount,2);
                    })
                    ->rawColumns(['grand_total','due','paid','action','customer'])
                    ->make(true);
            }
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            return view('admin.inventory.sale.index',compact('cash_account','bankAccounts'));
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

            return view('admin.inventory.sale.create',compact('warehouses','taxs','variantProducts','products','mystore'));
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
            'cash_memo' => 'required|unique:purchases,cash_memo',
            'date' => 'required',
            'invoice_no' => 'required|unique:purchases,invoice_no',
            'warehouse_id' => 'required',
            'customer_id' => 'required',
        ]);
        try {
            $data = new Sales();

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/sale/documents/'), $filename);
                $data->document = $filename;
            }

            $data->reference_no='pr-' . date("Ymd") . '-'. date("his");
            $data->warehouse_id=$request->warehouse_id;

            $data->customer_id=$request->customer_id;
            $data->total_qty=$request->total_qty;
            $data->cash_memo=$request->cash_memo;
            $data->date=$request->date;
            $data->invoice_no=$request->invoice_no;
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
                $warehouse = new InventoryProductCount();

                $warehouse->reference_no='pr-' . date("Ymd") . '-'. date("his");
                $warehouse->date=$request->date;
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->sale_id=$data->id;
                $warehouse->sale_qty=$request->qty[$i];
                $warehouse->stock_out = $request->qty[$i];
                $warehouse->customer_id=$request->customer_id;
                $warehouse->product_batch_id=$request->batch_no[$i];
                $warehouse->expired_date=$request->expired_date[$i];
                $warehouse->variant_id=$request->variant[$i];
                $warehouse->purchase_unit_id=$request->purchase_unit[$i];

                if($request->order_tax_rate != null){
                    $warehouse->tax_rate=$request->order_tax_rate;
                }
                    $warehouse->discount=$request->discount[$i];
                $warehouse->net_unit_cost=$request->price[$i];
                $warehouse->selling_price=$request->price[$i];
                $warehouse->total=$request->subtotal[$i];

                $warehouse->status=$request->status;
                $warehouse->created_by=Auth::user()->id;
                $warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));
                $warehouse->save();

            }

            return redirect()->route('admin.inventory.sale.index')
                    ->with('toastr-success', 'Sale Successfully');
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
        $sale = Sales::with('customers','warehouses')->findOrFail($id);
        $saleDetails = InventoryProductCount::with('products', 'variant')->where('sale_id', $sale->id)->get();
        return view('admin.inventory.sale.show',compact('sale','saleDetails'));
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
            $taxs =  Taxes::where('status',1)->get();
            $warehouses = InventoryWarehouse::get();
            // $variantProducts = ProductVariant::with('products','varients')->get();
            //$products = Products::with('productVarients')->get();

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

            $sale = Sales::with('customers')->findOrFail($id);
            $inventories = InventoryProductCount::with('products','variant')->where('sale_id',$id)->get();
            //dd($inventories);
            return view('admin.inventory.sale.edit',compact('warehouses','taxs','mystore','sale','inventories'));
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
         $request->validate([
            'cash_memo' => 'required|unique:purchases,cash_memo,'.$id,
            'date' => 'required',
            'invoice_no' => 'required|unique:purchases,invoice_no,'.$id,
            'warehouse_id' => 'required',
            'customer_id' => 'required',
        ]);
         //file

        try {
            $data =Sales::findOrFail($id);

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/sale/documents/'), $filename);
                $data->document = $filename;
            }

            $data->reference_no='pr-' . date("Ymd") . '-'. date("his");
            $data->warehouse_id=$request->warehouse_id;

            $data->customer_id=$request->customer_id;
            $data->total_qty=$request->total_qty;
            $data->cash_memo=$request->cash_memo;
            $data->date=$request->date;
            $data->invoice_no=$request->invoice_no;
            $data->shipping_cost=$request->shipping_cost;
            $data->grand_total=$request->total;

            $data->payment_status=1;
            $data->status=$request->status;
            $data->note=$request->note;
            $data->created_by=Auth::user()->id;
            $data->access_id=json_encode(UserRepository::accessId(Auth::id()));
            $data->update();

            $product_id = $request->product;
            $warehouses = InventoryProductCount::where('sale_id',$id)->get();
             //$result = array_diff($product_id,$warehouses);
           // dd($warehouses);
        //    if(in_array($warehouses->product_id, $product_id))
        //     {
        //         dd($warehouses);
        //     }
            foreach ($warehouses as $i => $productCount) {
                $productCount->delete();
            }
            foreach ($product_id as $i => $id) {
                // if(isset($warehouses[$i])){
                //     if($warehouses[$i]->product_id == $id ){
                //         $warehouse =InventoryProductCount::findOrFail($warehouses[$i]->id);
                //     }
                //     else{
                        // $warehouses[$i]->delete();
                        $warehouse =new InventoryProductCount();
                //     }
                // }

                $warehouse->reference_no='pr-' . date("Ymd") . '-'. date("his");
                $warehouse->date=$request->date;
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->sale_id=$data->id;
                $warehouse->sale_qty=$request->qty[$i];
                $warehouse->stock_out = $request->qty[$i];
                $warehouse->customer_id=$request->customer_id;
                $warehouse->product_batch_id=$request->batch_no[$i];
                $warehouse->expired_date=$request->expired_date[$i];
                $warehouse->variant_id=$request->variant[$i];
                $warehouse->purchase_unit_id=$request->purchase_unit[$i];

                if($request->order_tax_rate != null){
                    $warehouse->tax_rate=$request->order_tax_rate;
                }
                $warehouse->discount=$request->discount[$i];
                $warehouse->net_unit_cost=$request->price[$i];
                $warehouse->selling_price=$request->price[$i];
                $warehouse->total=$request->subtotal[$i];

                $warehouse->status=$request->status;
                $warehouse->created_by=Auth::user()->id;
                $warehouse->access_id=json_encode(UserRepository::accessId(Auth::id()));

                // if($warehouses[$i]->product_id == $id ){
                    $warehouse->save();
                // }
                // else{
                //     $warehouse->update();
                // }
            }

            return redirect()->route('admin.inventory.sale.index')
                    ->with('toastr-success', 'Sale Update Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function receivedPayment(Request $request)
    {
        $request->validate([
            'date' => 'required',
        ]);
        try{
         $sale_data = Sales::findOrFail($request->sale_id);
         if( $sale_data->paid_amount){
            $sale_data->paid_amount =$sale_data->paid_amount+ $request->paying_amount;
         }
         else{
            $sale_data->paid_amount = $request->paying_amount;
         }
         $sale_data->update();

         $transaction = new Transaction();
         $transaction->transaction_title = "Sale Payment Receive";
         $transaction->transaction_date = $request->date;
         $transaction->warehouse_id = $sale_data->warehouse_id;
         if ($request->transaction_way == 2) {
              $transaction->account_id = $request->account_id;
             $transaction->transaction_account_type = 2;
         }
         else if($request->transaction_way == 1){
             $transaction->account_id = $request->cash_account_id;
             $transaction->transaction_account_type = 1;
         }
         $transaction->transaction_purpose = 18;
         $transaction->transaction_type = 2;
         $transaction->amount = $request->paying_amount;
        //  $transaction->supplier_id  = $sale_data->supplier_id;
         $transaction->customer_id  = $sale_data->customer_id;
         $transaction->sale_id  = $sale_data->id;
         $transaction->cheque_number = $request->cheque_number;
         $transaction->description = $request->note;
         $transaction->status = 1;

        $transaction->created_by= Auth::user()->id;
        $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

         $transaction->save();
         return redirect()->route('admin.inventory.sale.index')
         ->with('toastr-success', 'Payment Receive Successfully');
        }
        catch (\Exception $exception) {
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
                $sale =  Sales::findOrFail($id);
                $inventory = InventoryProductCount::where('sale_id',$sale->id)->first();
                $inventories = InventoryProductCount::where('sale_id',$sale->id)->get();
                foreach($inventories as $inventry){
                    $inventry->delete();
                }
                $transactions = Transaction::where('sale_id',$sale->id)->get();
                foreach($transactions as $transaction){
                    $transaction->delete();
                }
                $saleReturns =  SaleReturn::where('invoice_no',$sale->invoice_no)->get();
                foreach($saleReturns as $saleReturn){
                    $saleReturn->delete();
                }
                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Sale Deleted Successfully.',
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
            $serviceCategory = InventoryServiceCategory::findOrFail($id);
            // Check Item Current Status
            if ($serviceCategory->status == 1) {
                $serviceCategory->status = 0;
            } else {
                $serviceCategory->status = 1;
            }

            $serviceCategory->save();
            return response()->json([
                'success' => true,
                'message' => 'Category of Service Status Updated Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
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
    public function customerSearch(Request $request)
    {
        $result = InventoryCustomer::query()
                        ->limit(10)
                        ->where('warehouse_id',$request->warehouse_id)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);

        return $result;
    }
    public function supplierSearch(Request $request)
    {
        $result = InventorySupplier::query()
                        ->limit(10)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);
        return $result;
    }
    public function productData(Request $request,$id)
    {
        $price = [];
        $productVariants = [];
        $variants = [];
        $lastSalePrice = [];
        $result[] = Products::with('taxs')->findOrFail($id);
        $priceData = PriceManagement::where('product_id',$id)->where('variant_id',$request->data)->latest()->first();

        $SalePrice = InventoryProductCount::where('product_id',$id)->where('customer_id',$request->customerId)->latest()->first('net_unit_cost');

        if($SalePrice == null){
            $net_unit_cost= 0 ;
            $SalePrice = array("net_unit_cost"=>"0");
        }

        $lastSalePrice[]= $SalePrice ;
        //\dd($SalePrice);
        if(isset($request->data))
       {
            $productVariant = ProductVariant::findOrFail($request->data);
            $variant = Variant::findOrFail($productVariant->variant_id);
            $productVariants[] = $productVariant;
            $variants[] = $variant;
       }
       $variantData=array_merge($productVariants,$variants);

        if($priceData){
            $price[] =  $priceData;
        }
        else{
            $price[] = [
                "id" => 0,
                "product_id" => 0,
                "cost" => 0,
                "price" => 0,
                ];
        }
        $obj_merged =  array_merge($result,$price);
        $Data =  array_merge($obj_merged,$variantData);
        $allData = array_merge($Data,$lastSalePrice);

        return json_encode( $allData);
    }
    public function saleProductSearch(Request $request, $id)
    {
        try {
            $sale = Sales::findOrFail($id);
            return $sale;
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    // public function warehouseSearch(Request $request, $id)
    // {
    //     if ($request->ajax()) {
    //         $warehouse = InventoryWarehouse::find($id);
    //         return response()->json($warehouse);
    //     }
    // }
    // public function saleSearch(Request $request, $id)
    // {
    //     try {
    //         $sale = Sales::findOrFail($id);
    //         return response()->json(['data' => $sale]);
    //     } catch (\Exception $exception) {
    //         return redirect()->back()->with('error', $exception->getMessage());
    //     }
    // }
    public function quantitySearch(Request $request)
    {
        try {
            $stockIn = 0;
            $stockOut = 0;
            if($request->varId){
                $stockIn = InventoryProductCount::where('product_id',$request->productId)->where('variant_id',$request->varId)->sum('stock_in');
                $stockOut = InventoryProductCount::where('product_id',$request->productId)->where('variant_id',$request->varId)->sum('stock_out');
            }
            else{
                $stockIn = InventoryProductCount::where('product_id',$request->productId)->sum('stock_in');
                $stockOut = InventoryProductCount::where('product_id',$request->productId)->sum('stock_out');
            }
            $avaialable = $stockIn-$stockOut;
            return $avaialable;
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function saleHistory(Request $request, $id)
    {
        try {
            if ($request->ajax()) {

                $sale = Transaction::with('sales')->where('sale_id', $id)->get();

                return DataTables::of( $sale)
                    ->addIndexColumn()

                    ->addColumn('paid', function ($sale) {
                        return number_format($sale->amount,2);
                    })
                    ->addColumn('payableAmount', function ($sale) {
                        return number_format($sale->sales->grand_total,2);
                    })
                    ->addColumn('transactionType', function ($sale) {
                        if($sale->transaction_account_type == 1){
                            $data = 'Cash';
                        }elseif($sale->transaction_account_type == 2){
                            $data = 'Bank Account';
                        }
                        return $data;
                    })
                    ->addColumn('due', function ($sale) {
                        return  number_format($sale->sales->grand_total - $sale->sales->paid_amount,2);
                    })

                    ->addColumn('action', function ($sale) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <button class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Update Payment" data-coreui-toggle="modal" data-coreui-target="#exampleModal" onclick="getSelectedUserData(' . $sale->id . ')" ><i class="bx bx-money" style="color: #FC3207;"></i></button>&nbsp;

                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $sale->sale_id . ')" title="Delete"><i class="bx bxs-trash"></i></a>

                            </div>';
                    })
                    ->rawColumns(['payableAmount','due','paid','transactionType','action'])
                    ->make(true);
            }
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();

            return view('admin.inventory.sale.paymenthistory',compact('cash_account','bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function saleHistoryDelete(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $sale = Transaction::where('sale_id', $id)->first();
                $sale->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Data Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    public function saleData(Request $request, $id)
    {
        try {
            $sale = Transaction::with('sales')->findOrFail($id);
            return response()->json($sale);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function updateSale(Request $request, $id)
    {
        try {
        $transaction = Transaction::findOrFail($id);
        $transaction->transaction_date = $request->date;

        if ($request->transaction_way == 2) {
            $transaction->account_id = $request->account_id;
           $transaction->transaction_account_type = 2;
       }
       else if($request->transaction_way == 1){
           $transaction->account_id = $request->cash_account_id;
           $transaction->transaction_account_type = 1;
       }

        $transaction->description = $request->note;
        $transaction->amount = $request->paying_amount;
        $transaction->cheque_number = $request->cheque_number;
        $transaction->update();

        $sum = Transaction::where('sale_id', $transaction->sale_id)->sum('amount');
        $sale = Sales::where('id', $transaction->sale_id)->first();
        $sale->paid_amount = $sum;
        $sale->update();

        return redirect()->route('admin.inventory.sale.index')
         ->with('toastr-success', 'Payment Receive Successfully');

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




