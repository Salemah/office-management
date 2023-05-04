<?php

namespace App\Http\Controllers\Admin\Account\Investment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account\Investment\Investor;
use App\Models\Account\Investment\InvestorIdentity;
use App\Models\Employee\Employee;
use App\Models\Identity;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use DataTables;

class InvestorController extends Controller
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
                    $investors = Investor::latest()->get();
                }
                else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $investors = Investor::where('warehouse_id',$mystore->id)->latest()->get();
                }

                return DataTables::of($investors)
                    ->addIndexColumn()
                    ->addColumn('status', function ($investors) {
                        if ($investors->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $investors->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $investors->id . ')"
                            class="btn btn-sm btn-warning">In Active</button>';
                        }
                    })
                    ->addColumn('action', function ($investors) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-info text-white " title="Investment" style="cursor:pointer"href="' . route('admin.investment.show', $investors->id) . '"><i class="bx bx-comment-check"></i></a><a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.investor.show', $investors->id) . '"><i class="bx bx-show"> </i> </a><a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.investor.edit', $investors->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="investorDeleteConfirm(' . $investors->id . ')" title="Delete"><i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.account.investment.investor.index');
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
            return view('admin.account.investment.investor.create',\compact('warehouses','mystore'));
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
            'email' => 'required|unique:investors,email',
            'phone' => 'required|unique:investors,email',

        ]);
        try {
             $investor = new Investor();
             $auth = Auth::user();
             $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $investor->warehouse_id =  $request->warehouse_id;
                }
            else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $investor->warehouse_id = $mystore->id;
                }

             $investor->name=$request->name;
             $investor->email=$request->email;
             $investor->phone=$request->phone;
             $investor->status=1;
             $investor->note=strip_tags($request->note);
             $investor->created_by = Auth::user()->id;
             $investor->access_id = json_encode(UserRepository::accessId(Auth::id()));
             $investor->save();

           return redirect()->route('admin.investor.index')->with('message', 'Investor Add successfully.');
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
            $investor = Investor::findOrFail($id);
            $investorIdentity = InvestorIdentity::where('investor_id', $id)->where('user_type', 1)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.account.investment.investor.show',compact('investor','investorIdentity','identities','warehouses','mystore'));
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
            $investor = Investor::findOrFail($id);
           return view('admin.account.investment.investor.edit',compact('investor','warehouses','mystore'));
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
            'email' => 'required',
            'phone' => 'required',
        ]);
        try {
             $investor =Investor::findOrFail($id);
             $auth = Auth::user();
             $user_role = $auth->roles->first();
            if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' ){
                $investor->warehouse_id =  $request->warehouse_id;
                }
            else{
                    $user=User::where('id',Auth::user()->id)->where('user_type',1)->first();
                    $employee=Employee::where('id',$user->user_id)->first();
                    $mystore=InventoryWarehouse::where('id',$employee->warehouse )->first();
                    $investor->warehouse_id = $mystore->id;
                }

             $investor->name=$request->name;
             $investor->email=$request->email;
             $investor->phone=$request->phone;
             $investor->status=1;
             $investor->note=$request->note;
             $investor->updated_by = Auth::user()->id;
             $investor->update();

           return redirect()->route('admin.investor.show',$investor->id);
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
               Investor::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Investor Deleted Successfully.',
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
   //starts status change function
   public function statusUpdate(Request $request)
   {
       try {
           $investor=Investor::findOrFail($request->id);

           $investor->status == 1 ? $investor->status = 0 : $investor->status = 1;

           $investor->update();
           if ($investor->status == 1) {
               return "active";
               // exit();
           } else {
               return "inactive";
           }
       }
       catch (\Exception $exception) {
           return  $exception->getMessage();
       }
   }
   public function InvestorAddressUpdate(Request $request, $id)
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
           $data = Investor::findOrFail($id);
           $data->present_address = $request->present_address;
           $data->permanent_address = $request->permanent_address;
           $data->country_id  = $request->country;
           $data->state_id = $request->states;
           $data->city_id = $request->cities;
           $data->zip = $request->zip;
           $data->update();

           return redirect()->route('admin.investor.show', $id)->with('message', 'Address Update successfully.');
       } catch (\Exception $exception) {
           return redirect()->back()->with('error', $exception->getMessage());
       }
   }

}
