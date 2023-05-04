<?php

namespace App\Http\Controllers\Admin\Inventory\Customers;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CRM\Address\City;
use Yajra\DataTables\DataTables;
use App\Models\CRM\Address\State;
use App\Models\Employee\Employee;
use App\Models\CRM\Address\Country;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\CRM\Client\ClientType;
use App\Models\Inventory\Sales\Sales;
use App\Models\CRM\Client\InterestedOn;
use App\Models\Inventory\Area\InventoryArea;
use App\Models\Inventory\Customers\InventoryCustomer;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Suppliers\InventorySupplier;

class InventoryCustomerController extends Controller
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
                    $customers = InventoryCustomer::with('warehouse_rel')->get();
                }
                else{
                    $user = User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee = Employee::where('id',$user->user_id)->first();
                    $mystore = InventoryWarehouse::where('id', $employee->warehouse )->first();
                    $customers = InventoryCustomer::with('warehouse_rel')->where('warehouse_id', $mystore->id)->get();
                }

                return DataTables::of($customers)
                    ->addIndexColumn()

                    ->addColumn('status', function ($customers) {
                        if ($customers->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $customers->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $customers->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('customer_type_priority', function ($customers) {
                        if ($customers->customer_type_priority == 1) {
                            return '<span style="color:#536DE7 ">First </span>';
                        } else if ($customers->customer_type_priority == 2) {
                            return '<span style="color:#536DE7 ">Second</span>';
                        } else if ($customers->customer_type_priority == 3) {
                            return '<span style="color:#536DE7 ">Third</span>';
                        } else {
                            return '--';
                        }
                    })

                    ->addColumn('action', function ($customers) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-info text-white " title="Due List"style="cursor:pointer"href="' . route('admin.inventory.customers.customer.due', $customers->id) . '"><i class="bx bx-file"> </i> </a>&nbsp;

                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.customers.customer.view', $customers->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                                    <a href="' . route('admin.inventory.customers.customer.edit', $customers->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>&nbsp;

                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $customers->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['action', 'status','customer_type_priority'])
                    ->make(true);
            }
            return view('admin.inventory.customers.customer.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function CountryWiseState(Request $request){
        try{
            $states = State::where('country_id', $request->country_id)->get();
            return response()->json($states);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function StateWiseCity(Request $request){
        try{
            $cities = City::where('state_id', $request->state_id)->get();
            return response()->json($cities);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function create()
    {
        try {
            $countries   = Country::all();
            $areas       = InventoryArea::where('status', 1)->get();
            $warehouses  = InventoryWarehouse::all();
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            return view('admin.inventory.customers.customer.create',compact('countries','areas','warehouses','user_role'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function getCustomerTypePriority(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $ClientType = ClientType::findOrFail($id);
                return response()->json($ClientType);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
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
        $messages = array(
            'warehouse.required' => 'Select warehouse',
            'name.required'      => 'Enter customer name',
            'phone.required'     => 'Enter phone number',
            'email.required'     => 'Enter email address',
            'address.required'   => 'Write your address',
        );

        $this->validate($request, array(
            'warehouse' => 'required',
            'name'      => 'required|string',
            'phone'     => 'required||min:8|max:17|regex:/(01)[0-9]{9}/|unique:inventory_customers,phone,NULL,id,deleted_at,NULL',
            'email'     => 'required|string|unique:inventory_customers,email,NULL,id,deleted_at,NULL',
            'address'   => 'required|string',
        ), $messages);

        try {
            $data = [
                    'warehouse_id'              =>$request->warehouse,
                    'country_id'                => $request->country_id,
                    'state_id'                  => $request->state_id,
                    'city_id'                   => $request->city_id,
                    'area_id'                   => $request->area_id,
                    'postal_code'               => $request->postal_code,
                    'name'                      => $request->name,
                    'company_name'              => $request->company_name,
                    'phone'                     => $request->phone,
                    'email'                     => $request->email,
                    'tax_number'                => $request->tax_number,
                    'contact_person'            => $request->contact_person,
                    'address'                   => $request->address,
                    'description'               => $request->description,
                    'status'                    => $request->status,
                    'customer_type_priority'    => $request->customer_type_priority,
                    'created_by'                => Auth::user()->id,
                    'access_id'                 => json_encode(UserRepository::accessId(Auth::id())),
                ];

            if($request->both == 1) {
                InventoryCustomer::insert($data);
                InventorySupplier::insert($data);
                return redirect()->route('admin.inventory.customers.customer.index')
                    ->with('toastr-success', 'Customer & Supplier Created Successfully');
            }else{
                InventoryCustomer::insert($data);
                return redirect()->route('admin.inventory.customers.customer.index')
                    ->with('toastr-success', 'Customer created successfully');
            }

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
        $customer           =       InventoryCustomer::findOrFail($id);
        $ClientTypes        =       ClientType::where('status', 1)->get();
        $InterestedsOn      =       InterestedOn::where('status', 1)->get();
        return view('admin.inventory.customers.customer.show',
            compact('customer', 'ClientTypes', 'InterestedsOn'));
    }

    public function view($id)
    {
        $customer = InventoryCustomer::findOrFail($id);
        return view('admin.inventory.customers.customer.view', compact('customer'));
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
            $customer  = InventoryCustomer::with('warehouse_rel')->findOrFail($id);
            $countries = Country::all();
            $states = State::where('country_id', $customer->country_id)->get();
            $cities = City::where('state_id', $customer->state_id)->get();
            $areas     = InventoryArea::where('status', 1)->get();
            $warehouses = InventoryWarehouse::all();
            return view('admin.inventory.customers.customer.edit',
                compact('customer','countries','states','cities','areas','warehouses'));
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
        $messages = array(
            'warehouse.required' => 'Select warehouse',
            'name.required'  => 'Enter customer name',
            'phone.required'  => 'Enter phone number',
            'email.required'  => 'Enter email address',
            'address.required'  => 'Write your address',
        );

        $this->validate($request, array(
            'warehouse' => 'required',
            'name' => 'required|string',
            'phone' => 'required||min:11|max:11|regex:/(01)[0-9]{9}/|unique:inventory_customers,phone,' . $id . ',id,deleted_at,NULL',
            'email' => 'required|unique:inventory_customers,email,' . $id . ',id,deleted_at,NULL',
            'address' => 'required|string',
        ), $messages);

        try {
           $data = InventoryCustomer::findOrfail($id);
           $data->warehouse_id           = $request->warehouse;
           $data->country_id             = $request->country_id;
           $data->state_id               = $request->state_id;
           $data->city_id                = $request->city_id;
           $data->area_id                = $request->area_id;
           $data->postal_code            = $request->postal_code;
           $data->phone                  = $request->phone;
           $data->company_name           = $request->company_name;
           $data->name                   = $request->name;
           $data->email                  = $request->email;
           $data->tax_number             = $request->tax_number;
           $data->address                = $request->address;
           $data->description            = $request->description;
           $data->customer_type_priority = $request->customer_type_priority;
           $data->country_id             = $request->country_id;
           $data->updated_by             = Auth::user()->id;
           $data->access_id              = json_encode(UserRepository::accessId(Auth::id()));
           $data->update();

            return redirect()->route('admin.inventory.customers.customer.index')
                    ->with('toastr-success','Customer updated successfully');

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
                InventoryCustomer::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Customer Deleted Successfully.',
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
            $customer = InventoryCustomer::findOrFail($id);
            // Check Item Current Status
            if ($customer->status == 1) {
                $customer->status = 0;
            } else {
                $customer->status = 1;
            }

            $customer->update();
            return response()->json([
                'success' => true,
                'message' => 'Customer Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function DueList(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $sales = Sales::where('customer_id', $id)->get();
                return DataTables::of( $sales)
                    ->addIndexColumn()
                    ->addColumn('action', function ($sales) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">

                        <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.inventory.sale.show', $sales->id) . '"><i class="bx bx-show"> </i> </a>&nbsp;

                        </div>';
                    })
                    ->addColumn('customer', function ($sales) {
                        return $sales->customers->name;
                    })
                    ->addColumn('paid', function ($sales) {
                        return number_format($sales->paid_amount,2);
                    })
                    ->addColumn('grand_total', function ($sales) {
                        return number_format($sales->grand_total,2);
                    })
                    ->addColumn('due', function ($sales) {

                        return  number_format($sales->grand_total - $sales->paid_amount,2);
                    })

                    ->rawColumns(['grand_total','due','paid','action','customer'])
                    ->make(true);
            }

            return view('admin.inventory.customers.customer.duelist');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




