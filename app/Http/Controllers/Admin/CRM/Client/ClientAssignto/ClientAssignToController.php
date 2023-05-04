<?php

namespace App\Http\Controllers\Admin\CRM\Client\ClientAssignto;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientAssign;
use App\Models\CRM\Client\ClientComment;
use App\Models\Employee\Employee;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientAssignToController extends Controller
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
                $ClientComments = ClientComment::with('createdBy', 'clients')->latest()->get();
                return DataTables::of($ClientComments)
                    ->addIndexColumn()
                    ->addColumn('comment', function ($ClientComments) {
                        if ($ClientComments->comment) {
                            return $ClientComments->comment;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('client', function ($ClientComments) {
                        return $ClientComments->clients->name;
                    })
                    ->addColumn('employee', function ($ClientComments) {
                        if ($ClientComments->createdBy) {
                            return $ClientComments->createdBy->name;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('created_at', function ($ClientComments) {
                        if ($ClientComments->created_at) {
                            return Carbon::parse($ClientComments->created_at)->format('M d Y');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('status', function ($ClientComments) {
                        if ($ClientComments->status == 1) {
                            return '<button
                             onclick="showStatusChangeAlert(' . $ClientComments->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $ClientComments->id . ')"
                            class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('action', function ($ClientComments) {
                        $url = asset('/img/client/comment/' . $ClientComments->comment_document);
                        if ($ClientComments->comment_document) {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $ClientComments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a href="' . $url . '" class="btn btn-sm btn-success text-white disabled" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $ClientComments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                </div>';
                        }
                    })
                    ->rawColumns(['comment', 'client', 'employee', 'created_at', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.crm.comment.index');
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
            $clients = Client::where('status', 1)->get();
            $employee = Employee::where('status', 1)->get();
            return view('admin.crm.comment.create', compact('clients', 'employee'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function clientAssignTo(Request $request, $id)
    {
        try {
            $Client = Client::findOrFail($id);
            return view('admin.crm.client.assignto.assignto-show', compact('Client'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
        DB::beginTransaction();
        try {
            //getEmployee auth id
            $data = User::where('user_type', 1)
                ->where('user_id', $request->employee)
                ->first();

            $Client = Client::where('id', $request->client_id)->first();
            $assign_to = json_decode($Client->assign_to);
            $Client->is_assign = 1;
            $assign_to[] = $data->id;
            $Client->assign_to = json_encode($assign_to);

            $own_assign = json_decode($Client->own_assign);
            $array = array_diff($own_assign, [$data->id]);
            $array = [1];
            $Client->own_assign = json_encode($array);
            $Client->update();

            DB::commit();
            return redirect()->route('admin.crm.client.client.assignto',$request->client_id);
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            //$Client = Client::findOrFail($id);
            if ($request->ajax()) {
                $Client = Client::where('id', $id)
                    ->first('assign_to'); //assign to ==  Employee(Auth Id)
                $userIds = User::where('user_type', 1)
                    ->whereIn('id', json_decode($Client->assign_to))
                    ->pluck('user_id'); //here id == employee(Auth id) . because assign to using auth id
                //user_id' ==  employee Table Id

                $employees = Employee::whereIn('id', $userIds)
                    ->get();
                return DataTables::of($employees)
                    ->addIndexColumn()
                    ->addColumn('assignTo', function ($employees) {

                        return $employees->name;
                    })
                    ->addColumn('action', function ($employees) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="assignDeleteConfirm(' . $employees->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['assignTo', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.assignto.assignto-show', compact('Client'));
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
    public function edit(Request $request, $id)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $user_id = User::where('user_type', 1)->where('user_id', $id)->first();

                $Client = Client::where('id', $request->clientId)->first();

                $assign_to = json_decode($Client->assign_to);

                return response()->json([
                    'success' => true,
                    'message' => 'Assign Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    public function AssignToDelete(Request $request, $id)
    {

        if ($request->ajax()) {
            try {
                $user_id = User::where('user_type', 1)
                    ->where('user_id', $id)
                    ->first();

                $Client = Client::where('id', $request->clientId)->first();

                $assign_to = json_decode($Client->assign_to);

                $array = array_diff($assign_to, [$user_id->id]);

                $Client->assign_to = json_encode($array);

                $Client->is_assign = json_encode([0]);

                $Client->own_assign = json_encode([$Client->created_by]);

                $Client->update();

                return response()->json([
                    'success' => true,
                    'message' => 'Assign Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }
    }

    public function AllEmployeeSearch(Request $request)
    {
        if ($request->ajax()) {
            try {
                $Client = Client::where('id', $request->client)
                    ->first('assign_to'); //assign to ==  Employee(Auth Id)

                $userIds = User::where('user_type', 1)
                    ->whereNotIn('id', json_decode($Client->assign_to))
                    ->pluck('user_id'); //here id == employee(Auth id) . because assign to using auth id
                //user_id ==  employee Table Id
                $result = Employee::whereIn('id', $userIds)
                    ->get();

                return $result;
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
