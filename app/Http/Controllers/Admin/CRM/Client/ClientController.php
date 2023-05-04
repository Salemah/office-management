<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Exports\ClientExport;
use App\Http\Controllers\Controller;
use App\Imports\ClientImport;
use App\Models\Account\Bank;
use App\Models\Account\BankAccount;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientBusinessCategory;
use App\Models\CRM\Client\ClientType;
use App\Models\CRM\Client\ContactThrough;
use App\Models\CRM\Client\InterestedOn;
use App\Models\Employee\EmployeeDocuments;
use App\Models\Employee\EmployeeIdentity;
use App\Models\Employee\EmployeeReference;
use App\Models\Identity;
use App\Models\Settings\Priority;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
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
                if(Auth::id() != 1){
                    $Client = DB::table('clients')
                                    ->orderBy('clients.ID','DESC')
                                    ->join('users', 'clients.created_by', '=', 'users.id')
                                    ->whereJsonContains('clients.own_assign', Auth::user()->id)
                                    ->orwhereJsonContains('clients.assign_to',Auth::id())
                                    ->orwhere('clients.is_assign',false)
                                    ->where('clients.created_by',1)
                                    ->select('clients.*', 'users.name as added_by')
                                    ->where('clients.deleted_at',null)
                                    ->get();
                }else{
                    $Client = DB::table('clients')
                                ->orderBy('clients.ID','DESC')
                                ->join('users', 'clients.created_by', '=', 'users.id')
                                //->Join('client_business_categories', 'clients.client_business_category_id', '=', 'client_business_categories.id')
                                ->select('clients.*', 'users.name as added_by')
                                ->where('clients.deleted_at',null)
                                ->get();

                }
                return DataTables::of($Client)
                    ->addIndexColumn()
                    ->addColumn('image', function ($Client) {
                        $url = asset('img/client/' . $Client->image);
                        $url2 = asset('img/no-image/noman.jpg');
                        if ($Client->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';
                    })
                    ->addColumn('name', function ($Client) {
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                                 href="' . route('admin.crm.client.show', $Client->id) . '"> ' . $Client->name . ' </a>';
                    })

                    ->addColumn('client_type_priority', function ($Client) {
                        if($Client->client_business_category_id != null){
                           $category = ClientBusinessCategory::findOrFail($Client->client_business_category_id );
                           return $category->name;
                        }
                        else{
                            return '--';
                        }
                    })
                    ->addColumn('creation_date', function ($Client) {
                        if ($Client->created_at ) {
                            return Carbon::parse($Client->created_at)->format('d M, Y');
                        }
                            return '--';

                    })
                    ->addColumn('added_by', function ($Client) {
                        if ($Client->added_by) {
                            return $Client->added_by;
                        }else{
                            return '--';
                        }
                    })
                    ->addColumn('status', function ($Client) {
                        if ($Client->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('action', function ($Client) {
                        return '<div class="btn-group" role="group"aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Client->id) . '"><i class="bx bx-comment-check"></i></a>
                                    <a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Client->id) . '"><i class="bx bx-stopwatch"></i> </a>
                                    <a class="btn btn-sm btn-info text-white " title="Profile"style="cursor:pointer"href="' . route('admin.crm.client.profile', $Client->id) . '"><i class="bx bx-show"></i> </a>
                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.crm.client.show', $Client->id) . '"><i class="bx bx-user "> </i> </a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Client->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['image','added_by','creation_date', 'name', 'status', 'client_type_priority', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ClientList(Request $request)
    {
        try {
            if ($request->ajax()) {
                if(Auth::id() != 1){
                    $Client = DB::table('clients')

                                    ->orderBy('clients.ID','DESC')
                                    ->join('users', 'clients.created_by', '=', 'users.id')
                                    ->whereJsonContains('clients.own_assign', Auth::user()->id)
                                    ->orwhereJsonContains('clients.assign_to',Auth::id())
                                    ->orwhere('clients.is_assign',false)
                                    ->where('clients.created_by',1)
                                    ->select('clients.*', 'users.name as added_by')
                                    ->where('clients.deleted_at',null)
                                    ->get();
                                    if($request->category != null && $request->client_type == null){
                                        $Client = $Client->where('client_business_category_id',$request->category);
                                    }
                                    else if($request->category==null && $request->client_type != null){
                                        $Client = $Client->where('client_type',$request->client_type);
                                    }
                                    else if($request->category != null && $request->client_type != null){
                                        $Client = $Client->where('client_business_category_id',$request->category)->where('client_type',$request->client_type);
                                    }
                }else{
                    $Client = DB::table('clients')
                                ->orderBy('clients.ID','DESC')
                                ->join('users', 'clients.created_by', '=', 'users.id')
                                //->Join('client_business_categories', 'clients.client_business_category_id', '=', 'client_business_categories.id')
                                ->select('clients.*', 'users.name as added_by')
                                ->where('clients.deleted_at',null)
                                ->get();
                    if($request->category != null && $request->client_type == null){
                        $Client = $Client->where('client_business_category_id',$request->category);
                    }
                    else if($request->category==null && $request->client_type != null){
                        $Client = $Client->where('client_type',$request->client_type);
                    }
                    else if($request->category != null && $request->client_type != null){
                        $Client = $Client->where('client_business_category_id',$request->category)->where('client_type',$request->client_type);
                    }
                }
                return DataTables::of($Client)
                    ->addIndexColumn()
                    ->addColumn('image', function ($Client) {
                        $url = asset('img/client/' . $Client->image);
                        $url2 = asset('img/no-image/noman.jpg');
                        if ($Client->image) {
                            return '<img src="' . $url . '" border="0" width="40"  align="center" />';
                        }
                        return '<img src="' . $url2 . '" border="0" width="40"  align="center" />';
                    })
                    ->addColumn('name', function ($Client) {
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                                 href="' . route('admin.crm.client.show', $Client->id) . '"> ' . $Client->name . ' </a>';
                    })

                    ->addColumn('client_type_priority', function ($Client) {
                        if($Client->client_business_category_id != null){
                           $category = ClientBusinessCategory::findOrFail($Client->client_business_category_id );
                           return $category->name;
                        }
                        else{
                            return '--';
                        }
                    })
                    ->addColumn('creation_date', function ($Client) {
                        if ($Client->created_at ) {
                            return Carbon::parse($Client->created_at)->format('d M, Y');
                        }
                            return '--';

                    })
                    ->addColumn('added_by', function ($Client) {
                        if ($Client->added_by) {
                            return $Client->added_by;
                        }else{
                            return '--';
                        }
                    })
                    ->addColumn('status', function ($Client) {
                        if ($Client->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $Client->id . ')"class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('action', function ($Client) {
                        return '<div class="btn-group" role="group"aria-label="Basic mixed styles example">
                                    <a class="btn btn-sm btn-info text-white " title="Comment" style="cursor:pointer"href="' . route('admin.crm.client.comment', $Client->id) . '"><i class="bx bx-comment-check"></i></a>
                                    <a class="btn btn-sm btn-success text-white " title="Reminder" style="cursor:pointer"href="' . route('admin.crm.client.reminder', $Client->id) . '"><i class="bx bx-stopwatch"></i> </a>
                                    <a class="btn btn-sm btn-info text-white " title="Profile"style="cursor:pointer"href="' . route('admin.crm.client.profile', $Client->id) . '"><i class="bx bx-show"></i> </a>
                                    <a class="btn btn-sm btn-primary text-white " title="Show"style="cursor:pointer"href="' . route('admin.crm.client.show', $Client->id) . '"><i class="bx bx-user "> </i> </a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Client->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['image','added_by','creation_date', 'name', 'status', 'client_type_priority', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.index');
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
        try{
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $priorities = Priority::where('status', 1)->get();
            $businessCategories = ClientBusinessCategory::where('status', 1)->get();
            return view('admin.crm.client.create', compact('ClientTypes', 'ContactThrough', 'InterestedsOn','priorities','businessCategories'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Start
        $request->validate([
            'client_name' => 'required|unique:clients,name',
            'client_type' => 'required',
            'client_business_category_id' => 'required',
            'contact_through' => 'required',
            'email' => 'nullable|unique:clients,email',
            'primary_phone' => 'required|regex:/(01)[0-9]{9}/|min:11|unique:clients,phone_primary',
        ]);
        // Validation End
        // Store Data
        DB::beginTransaction();
        try {
            $data = new Client();
            $data->name = $request->client_name;
            $data->email = $request->email;
            $data->phone_primary = $request->primary_phone;
            $data->client_type = $request->client_type;
            $data->client_type_priority = $request->client_type_priority;
            $data->contact_through = $request->contact_through;
            $data->client_business_category_id = $request->client_business_category_id;
            $data->interested_on = $request->interested_on;
            $data->status = $request->status;
            $data->description = $request->description;

            $data->present_address = $request->present_address;
            $data->country_id  = $request->country;
            $data->state_id = $request->states;
            $data->city_id = $request->cities;
            $data->zip = $request->zip;

            if($request->permanent_address)
            {
                $data->permanent_address = $request->permanent_address;
                $data->country_id_others  = $request->country_other;
                $data->state_id_others  = $request->states_other;
                $data->city_id_others  = $request->cities_other;
                $data->zip_others  = $request->zip_other;

            }
            $data->created_by = Auth::user()->id;
            $data->assign_to = json_encode([]);
            $data->own_assign = json_encode([Auth::id()]);
            $data->is_assign = 0 ;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            // $user = new User();
            // $user->name = $request->client_name;
            // $user->email = $request->email;
            // $user->mobile = $request->primary_phone;
            // $user->password = Hash::make('client');
            // $user->user_id = $data->id;
            // $user->user_type = 2; // client userType = 2
            // $user->record_access = 1;
            // $user->role_id = $request->role;
            // $user->access_id = json_encode(UserRepository::accessId(Auth::id()));
            // $user->created_by = Auth::id();
            // $user->save();

            // DB::table('model_has_roles')->insert([
            //     'role_id' => 2,
            //     'model_type' => 'App\\Models\\User',
            //     'model_id' => $user->id,
            // ]);

            DB::commit();

            return redirect()->route('admin.crm.client.index')->with('message', 'Create successfully.');
        } catch (\Exception $exception) {

            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            $banks = Bank::where('status', 1)->get();
            $priorities = Priority::where('status', 1)->get();
            $businessCategories = ClientBusinessCategory::where('status', 1)->get();
            return view('admin.crm.client.show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity', 'banks','priorities','businessCategories'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'client_name' => 'required|unique:clients,name,'.$id.',id',
            'email' => 'nullable|unique:clients,email,'.$id.',id',
            'primary_phone' => 'required|regex:/(01)[0-9]{9}/|min:11|unique:clients,phone_primary,'.$id.',id',
            'client_type' => 'required',
            'contact_through' => 'required',
            'client_business_category_id' => 'required',

        ]);

        // Validation End

        try {
            $data = Client::findOrFail($id);
            $data->name = $request->client_name;
            $data->email = $request->email;
            $data->phone_primary = $request->primary_phone;
            $data->phone_secondary = $request->phone_secondary;
            $data->client_type = $request->client_type;
            $data->client_type_priority = $request->client_type_priority;
            $data->contact_through = $request->contact_through;
            $data->interested_on = $request->interested_on;
            $data->client_business_category_id = $request->client_business_category_id;
            $data->present_address = $request->present_address;
            $data->website = $request->website;

            $data->country_id  = $request->country;
            $data->state_id = $request->states;
            $data->city_id = $request->cities;
            $data->zip = $request->zip;
            // if($request->permanent_address)
            // {
                $data->permanent_address = $request->permanent_address;
                $data->country_id_others  = $request->country_other;
                $data->state_id_others  = $request->states_other;
                $data->city_id_others  = $request->cities_other;
                $data->zip_others  = $request->zip_other;

            // }
            $data->description = $request->description;
            $data->updated_by = Auth::user()->id;

            if ($request->file()) {
                $file = $request->file('image');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/'), $filename);
                $data->image = $filename;
            }

            $data->update();

            $client = Client::findOrFail($id);
            if ($client->is_assign == false) {
                $client->is_assign = 1;
                $own_assign = json_decode($client->own_assign);
                $own_assign [] = Auth::id();
                $client->own_assign = json_encode($own_assign);
                $client->update();
            }
            //store data user table
            // $user =User::where('user_id',$id)->where('user_type',2)->first();
            // $user->name = $request->name;
            // $user->email = $request->email;
            // $user->mobile = $request->primary_phone;
            // $user->user_id =$data->id;
            // $user->update();

            return redirect()->route('admin.crm.client.show', $id)->with('message', 'Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $client = Client::findOrFail($id);
                if ($client) {
                    $client->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Client Deleted Successfully.',
                    ]);
                }
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function getClientTypePriority(Request $request, $id)
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
    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $client = Client::findOrFail($request->id);

            $client->status == 1 ? $client->status = 0 : $client->status = 1;

            $client->update();

            if ($client->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
    public function ClientAddressUpdate(Request $request, $id)
    {
        $request->validate([
            'present_address' => 'required',
        ]);
        try {
            $data = Client::findOrFail($id);
            $data->present_address = $request->present_address;
            $data->country_id  = $request->country;
            $data->state_id = $request->states;
            $data->city_id = $request->cities;
            $data->zip = $request->zip;
            if($request->permanent_address)
            {
                $data->permanent_address = $request->permanent_address;
                $data->country_id_others  = $request->country_other;
                $data->state_id_others  = $request->states_other;
                $data->city_id_others  = $request->cities_other;
                $data->zip_others  = $request->zip_other;

            }
            $data->update();

            $client = Client::findOrFail($id);
            if ($client->is_assign == false) {
                $client->is_assign = 1;
                $own_assign = json_decode($client->own_assign);
                $own_assign [] = Auth::id();
                $client->own_assign = json_encode($own_assign);
                $client->update();
            }

            return redirect()->route('admin.crm.client.show', $id)->with('message', 'Address Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientComment($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.crm.client.comment.comment-show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientReminder($id)
    {
        try{
            $Client = Client::findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $ContactThrough = ContactThrough::where('status', 1)->get();
            $InterestedsOn = InterestedOn::where('status', 1)->get();
            $ClientIdentity = EmployeeIdentity::where('employee_id', $id)->where('user_type', 2)->get();
            $identities = Identity::where('status', 1)->get();
            return view('admin.crm.client.reminder.reminder-show', compact('Client', 'ClientTypes', 'ContactThrough', 'InterestedsOn', 'identities', 'ClientIdentity'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function clientProfile($id)
    {
        try{
            $Client = Client::with('interestedOn','contactThrough','comments','reminders')->findOrFail($id);
            $ClientTypes = ClientType::where('status', 1)->get();
            $documents = EmployeeDocuments::where('employee_id',$id)->where('user_type',2)->get();
            $EmployeeIdentity = EmployeeIdentity::where('employee_id',$id)
                            ->where('user_type',2)
                            ->with('employee','identity')
                            ->get();
            $ClientReferences = EmployeeReference::where('employee_id',$id) ->where('user_type',2)
            ->with('reference')
            ->get();
            $BankAccounts = BankAccount::where('user_id', $id)->where('account_type', 3)->with('bank')->get();
            return view('admin.crm.client.client-details',compact('Client','documents','EmployeeIdentity','ClientReferences','BankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ClientSearch(Request $request)
    {
        //  if(Auth::id() != 1){
        //     $result = Client::query()
        //         ->where('name', 'LIKE', "%{$request->search}%")
        //         ->whereJsonContains('assign_to',Auth::id())
        //         ->orwhere('is_assign',false)
        //         ->limit(10)
        //         ->get(['name', 'id']);
        //  }else{
            $result = Client::query()
                        ->limit(10)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);
        //  }
        return $result;
    }
    public function BusinesCategory(Request $request)
    {
        $result = ClientBusinessCategory::query()
                        ->limit(10)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);

        return $result;
    }
    public function ClientType(Request $request)
    {
        $result = ClientType::query()
                        ->limit(10)
                        ->where('name', 'LIKE', "%{$request->search}%")
                        ->get(['name', 'id']);

        return $result;
    }

    public function import()
    {
        try{
            Excel::import(new ClientImport,request()->file('file'));
            return redirect()->back()->with('message', 'Client Import successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }
    public function export()
    {
        try{
            return Excel::download(new ClientExport, 'users.xlsx');
            //return (new ClientExport)->download('users.xlsx',Excel::XLSX);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

}
