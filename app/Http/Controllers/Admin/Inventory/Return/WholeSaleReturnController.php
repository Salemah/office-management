<?php

namespace App\Http\Controllers\Admin\Inventory\Return;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Products\InventoryProductCount;
use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\Inventory\Products\Variant;
use App\Models\Inventory\Purchase\PriceManagement;
use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Return\PurchaseReturn;
use App\Models\Inventory\Return\SaleReturn;
use App\Models\Inventory\Return\WholesaleReturn;
use App\Models\Inventory\Sales\Sales;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Services\InventoryServiceCategory;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Settings\Taxes;
use App\Models\Inventory\Suppliers\InventorySupplier;
use App\Models\Inventory\Wholesale\Wholesale;
use App\Models\User;

class WholeSaleReturnController extends Controller
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
                // $products = SaleReturn::get();
                $auth = Auth::user();
                $user_role = $auth->roles->first();
                if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                    $products = WholesaleReturn::get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $products = WholesaleReturn::where('warehouse_id',$mystore->id)->get();
                }
                return DataTables::of( $products)
                    ->addIndexColumn()
                    ->addColumn('paid', function ($products) {
                        return number_format($products->paid_amount,2);
                    })
                    ->addColumn('grand_total', function ($products) {
                        return number_format($products->grand_total,2);
                    })
                    ->addColumn('due', function ($products) {
                        return  number_format($products->grand_total - $products->paid_amount,2);
                    })
                    ->addColumn('action', function ($products) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.whole-sale-return.edit', $products->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' .$products->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['grand_total','due','paid','action'])
                    ->make(true);
            }
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            return view('admin.inventory.whole-sale-return.index',compact('cash_account','bankAccounts'));
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
            $invoices = Wholesale::get(['id','invoice_no']);
            return view('admin.inventory.whole-sale-return.create',compact('taxs','variantProducts','invoices','mystore'));
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
            $sale =Wholesale::findOrFail($request->invoice_id);
            $data = new WholesaleReturn();

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/whole-sale-return/documents/'), $filename);
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
                $warehouse->stock_in = $request->qty[$i];
                $warehouse->reference_no=$data->reference_no;
                $warehouse->date=$request->date;
                $warehouse->warehouse_id=$sale->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->whole_sale_return_id =$data->id;
                $warehouse->whole_sale_return_qty=$request->qty[$i];
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

            return redirect()->route('admin.inventory.whole-sale-return.index')
                    ->with('toastr-success', 'Whole Sale Return Successfully');
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
            $return = WholesaleReturn::findOrFail($id);
            $inventories = InventoryProductCount::with('products','variant')->where('whole_sale_return_id',$id)->get();

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
            $invoices = Wholesale::get(['id','invoice_no']);
            $purchase = Wholesale::where('invoice_no',$return->invoice_no)->first();

            $purchaseProduct = InventoryProductCount::with('warehouse','products','variant')
                                                ->where('whole_sale_id',$purchase->id)
                                                ->where('whole_sale_return_id',null)
                                                ->get();

            return view('admin.inventory.whole-sale-return.edit',compact('warehouses','taxs','variantProducts','products','mystore','return','inventories','invoices','purchaseProduct'));
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
            $sale = Wholesale::findOrFail($request->invoice_id);
            $data =WholesaleReturn::findOrFail($id);

            if (isset($request->document)){
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/inventory/whole-sale-return/documents/'), $filename);
                $data->document = $filename;
            }

            $data->reference_no='pr-' . date("Ymd") . '-'. date("his");
            $data->warehouse_id=$sale->warehouse_id;

            $data->user_id=Auth::user()->id;

            $data->total_qty=$request->total_qty;
            $data->total_discount=$request->order_discount;
            $data->total_tax=$request->order_tax_rate;
            $data->date=$request->date;
            // $data->sale_return_no= 1;
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
                //$warehouse->stock_in = $request->qty[$i];
                $warehouse->reference_no=$data->reference_no;
                $warehouse->date=$request->date;
                $warehouse->stock_in = $request->qty[$i];
                $warehouse->warehouse_id=$request->warehouse_id;
                $warehouse->product_id=$id;
                $warehouse->whole_sale_return_id =$data->id;
                $warehouse->whole_sale_return_qty=$request->qty[$i];
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

            return redirect()->route('admin.inventory.whole-sale-return.index')
                    ->with('toastr-success', 'Whole Sale Return Update Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function addPayment(Request $request)
    {
        $data = $request->all();
         $purchase_data = Purchase::findOrFail($request->purchase_id);
         if($purchase_data->paid_amount){
            $purchase_data->paid_amount =$purchase_data->paid_amount+ $request->paying_amount;
         }
         else{
            $purchase_data->paid_amount = $request->paying_amount;
         }
         $purchase_data->update();

         $transaction = new Transaction();
         $transaction->transaction_title = "Purchase Payment Receive";
         $transaction->transaction_date = $request->date;

         if ($request->transaction_way == 2) {
              $transaction->account_id = $request->account_id;
             $transaction->transaction_account_type = 2;
         }
         else if($request->transaction_way == 1){
             $transaction->account_id = $request->cash_account_id;
             $transaction->transaction_account_type = 1;
         }
         $transaction->transaction_purpose = 17;
         $transaction->transaction_type = 1;
         $transaction->amount = $request->paying_amount;
         $transaction->supplier_id  = $purchase_data->supplier_id;
         $transaction->purchase_id  = $purchase_data->id;
         $transaction->cheque_number = $request->cheque_number;
         $transaction->description = $request->note;
         $transaction->status = 1;

        $transaction->created_by= Auth::user()->id;
        $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));

         $transaction->save();
         return redirect()->route('admin.inventory.purchase.index')
         ->with('toastr-success', 'Payment Receive Successfully');
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
                $sale =  WholesaleReturn::findOrFail($id);
                $inventory = InventoryProductCount::where('whole_sale_return_id', $sale->id)->first();
                $inventries = InventoryProductCount::where('whole_sale_return_id', $sale->id)->get();
                foreach($inventries as $inventry){
                    $inventry->delete();
                }

                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Sales Return Deleted Successfully.',
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
        //\dd($request->all());
        // $result = Products::query()
        //                 ->limit(10)
        //                 ->where('name', 'LIKE', "%{$request->search}%")
        //                 ->get(['name', 'id']);
        $purchase = Wholesale::where('id',$request->warehouse_id)->first();

        $result = [];
        //\dd($purchase);

        $inventories = InventoryProductCount::with('products','variant')
                                        ->where('whole_sale_id',$purchase->id)
                                        ->where('whole_sale_return_id',null)
                                        ->get();
                                       //dd($inventories);
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
        //$resultt=array_diff($inventories,$result);
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
        $result[] = Products::with('taxs')->findOrFail($id);
        $priceData = PriceManagement::where('product_id',$id)->latest()->first();


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
        $allData =  array_merge($obj_merged,$variantData);

        return json_encode( $allData);
    }

    public function purchaseSearch(Request $request, $id)
    {
        try {
            $purchase = Wholesale::findOrFail($id);
            return response()->json(['data' => $purchase]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function saleProductSearch(Request $request, $id)
    {
        try {
            $inventories = InventoryProductCount::with('warehouse','products','variant','suppliers')
                                        ->where('whole_sale_id',$id)
                                        // ->where('sale_return_id',null)
                                        ->get();
            return $inventories;
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




