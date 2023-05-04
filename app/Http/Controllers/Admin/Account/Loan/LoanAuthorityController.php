<?php

namespace App\Http\Controllers\Admin\Account\Loan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\Investment\InvestorIdentity;
use App\Models\Account\Loan\Loan_Authority;
use App\Models\Employee\Employee;
use App\Models\Identity;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;

class LoanAuthorityController extends Controller
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
                    $authorities = Loan_Authority::latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $authorities = Loan_Authority::where('warehouse_id',$mystore->id)->latest()->get();
                }

                return DataTables::of($authorities)
                    ->addIndexColumn()
                    ->addColumn('name', function ($authorities) {
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                                 href="' . route('admin.loan.show', $authorities->id) . '"> ' . $authorities->name . ' </a>';
                    })
                    ->addColumn('action', function ($authorities) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-info text-white " title="Loan" style="cursor:pointer"href="' . route('admin.loan.show', $authorities->id) . '"><i class="bx bx-comment-check"></i></a>
                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.loan-authority.show', $authorities->id) . '"><i class="bx bx-show"> </i> </a>
                                    <a class="btn btn-sm btn-success text-white " style="cursor:pointer"href="' . route('admin.loan-authority.edit', $authorities->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="authorityDeleteConfirm(' . $authorities->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['name','action'])
                    ->make(true);
            }
            return view('admin.account.loan.authority.index');
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
            return view('admin.account.loan.authority.create',\compact('warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:loan__authorities,phone',

        ]);
        try {
             $authority = new Loan_Authority();
             $auth = Auth::user();
             $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $authority->warehouse_id =  $request->warehouse_id;
                }
            else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $authority->warehouse_id = $mystore->id;
                }
             $authority->name=$request->name;
             $authority->email=$request->email;
             $authority->phone=$request->phone;
             $authority->note=strip_tags($request->note);
             $authority->created_by = Auth::user()->id;
             $authority->access_id = json_encode(UserRepository::accessId(Auth::id()));
             $authority->save();

           return redirect()->route('admin.loan-authority.index')->with('message', 'Authority Add successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
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
            $authority = Loan_Authority::findOrFail($id);
            $authorityIdentity = InvestorIdentity::where('investor_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.account.loan.authority.show',compact('authority','authorityIdentity','identities','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
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
            $authority = Loan_Authority::findOrFail($id);
           return view('admin.account.loan.authority.edit',compact('authority','warehouses','mystore'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);
        try {
             $authority = Loan_Authority::findOrFail($id);
             $auth = Auth::user();
             $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $authority->warehouse_id =  $request->warehouse_id;
                }
            else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $authority->warehouse_id = $mystore->id;
                }
             $authority->name=$request->name;
             $authority->email=$request->email;
             $authority->phone=$request->phone;
             $authority->note=strip_tags($request->note);
             $authority->updated_by = Auth::user()->id;
             $authority->update();

           return redirect()->route('admin.loan-authority.show',$id)->with('message', 'Authority Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            try {
               Loan_Authority::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Authority Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function AuthorAddressUpdate(Request $request, $id)
   {
       $request->validate([
           'present_address' => 'required',
           'permanent_address' => 'required',
           'country' => 'required|numeric',
           'states' => 'required|numeric',
           'cities' => 'required|numeric',
           'zip' => 'required',
       ]);
       try {
           $data = Loan_Authority::findOrFail($id);
           $data->present_address = $request->present_address;
           $data->permanent_address = $request->permanent_address;
           $data->country_id  = $request->country;
           $data->state_id = $request->states;
           $data->city_id = $request->cities;
           $data->zip = $request->zip;
           $data->update();

           return redirect()->route('admin.loan-authority.show', $id)->with('message', 'Address Update successfully.');
       } catch (\Exception $exception) {
           return redirect()->back()->with('error', $exception->getMessage());
       }
   }


}
