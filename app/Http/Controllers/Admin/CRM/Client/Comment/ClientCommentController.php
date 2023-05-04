<?php

namespace App\Http\Controllers\Admin\CRM\Client\Comment;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientAssign;
use App\Models\CRM\Client\ClientComment;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientCommentController extends Controller
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
                if(Auth::id()== 1)
               {
                $ClientComments = ClientComment::with('createdBy', 'clients')->latest()->get();
               }
               else{
                $ClientComments = ClientComment::with('createdBy', 'clients')->where('created_by',Auth::id())->latest()->get();
               }
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
                        return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                        href="' . route('admin.crm.client.show', $ClientComments->clients->id) . '"> ' . $ClientComments->clients->name . ' </a>';
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
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.comment.edit',['id' => $ClientComments->id, 'parameter' => 'comment-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a><a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $ClientComments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.comment.edit',['id' => $ClientComments->id, 'parameter' => 'comment-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
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
            return view('admin.crm.comment.create', compact('clients'));
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
            'client_id' => 'required',
            'comment' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = new ClientComment();
            $data->client_id = $request->client_id;
            $data->comment = $request->comment;
            $data->status = 1;

            if ($request->file('document')) {
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/comment/'), $filename);
                $data->comment_document = $filename;
            }

            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            $client = Client::findOrFail($request->client_id);
                if ($client->is_assign == false) {
                    $client->is_assign = 1;
                    $own_assign = json_decode($client->own_assign);
                    $own_assign [] = Auth::id();
                    $client->own_assign = json_encode($own_assign);
                    $client->update();
                }

            DB::commit();
            return redirect()->route('admin.crm.client.comment', $request->client_id)->with('message', ' Add successfully.');
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
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $ClientComments = ClientComment::with('createdBy', 'clients')->where('client_id', $id)->get();
                return DataTables::of($ClientComments)
                    ->addIndexColumn()
                    ->addColumn('comment', function ($ClientComments) {
                        if ($ClientComments->comment) {
                            return $ClientComments->comment;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('employee', function ($Comments) {
                        if($Comments->createdBy){
                           return $Comments->createdBy->name;
                        }
                    else{
                        return ' -- ';
                        }
                    })
                    ->addColumn('date', function ($Comments) {
                        if($Comments->createdBy){
                           return Carbon::parse($Comments->created_at)->format('d M, Y');
                        }
                    else{
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
                        <a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.comment.edit',['id' => $ClientComments->id, 'parameter' => 'show']) . '" title="Edit"> <i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $ClientComments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a href="' . $url . '" class="btn btn-sm btn-success text-white disabled" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                            <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.comment.edit',['id' => $ClientComments->id, 'parameter' => 'show']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $ClientComments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                </div>';
                        }
                    })
                    ->rawColumns(['employee','comment', 'status','date', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.show');
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
        try {
            $clients = Client::where('status', 1)->get();
            $comment = ClientComment::findOrFail($id);
            $Client = Client::where('id', $comment->client_id)->first();
            return view('admin.crm.comment.edit',compact('comment','Client','clients'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }
    public function commentEdit(Request $request, $id)
    {
        try {
            $parameter = $request->parameter;
            $clients = Client::where('status', 1)->get();
            $comment = ClientComment::findOrFail($id);
            $Client = Client::where('id', $comment->client_id)->first();
            return view('admin.crm.comment.edit',compact('comment','Client','clients','parameter'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

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
        // Validation Start
        $request->validate([
            'client_id' => 'required',
            'comment' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = ClientComment::findOrFail($id);
            $data->client_id = $request->client_id;
            $data->comment = $request->comment;
            $data->status = 1;

            if ($request->file('document')) {
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/comment/'), $filename);
                $data->comment_document = $filename;
            }

            $data->updated_by = Auth::user()->id;
            $data->update();

            $client = Client::findOrFail($request->client_id);
                if ($client->is_assign == false) {
                    $client->is_assign = 1;
                    $own_assign = json_decode($client->own_assign);
                    $own_assign [] = Auth::id();
                    $client->own_assign = json_encode($own_assign);
                    $client->update();
                }

            DB::commit();
            return redirect()->route('admin.crm.client-comment.index')->with('message', ' Updatesuccessfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function commentUpdate(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'client_id' => 'required',
            'comment' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = ClientComment::findOrFail($id);
            $data->client_id = $request->client_id;
            $data->comment = $request->comment;
            $data->status = 1;

            if ($request->file('document')) {
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/comment/'), $filename);
                $data->comment_document = $filename;
            }

            $data->updated_by = Auth::user()->id;
            $data->update();

            $Client = Client::findOrFail($request->client_id);
                if ($Client->is_assign == false) {
                    $Client->is_assign = 1;
                    $own_assign = json_decode($Client->own_assign);
                    $own_assign [] = Auth::id();
                    $Client->own_assign = json_encode($own_assign);
                    $Client->update();
                }
            DB::commit();
            if($request->parameter == 'comment-list'){
                return redirect()->route('admin.crm.client-comment.index')->with('message', ' Updatesuccessfully.');
            }
            else{
                return view('admin.crm.client.comment.comment-show',compact('Client'))->with('message', ' Updatesuccessfully.');
            }

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
                ClientComment::where('id', $id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'comment Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $comment = ClientComment::findOrFail($request->id);
            $comment->status == 1 ? $comment->status = 0 : $comment->status = 1;
            $comment->update();
            if ($comment->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }
    public function ClientSearch(Request $request)
    {
         if(Auth::id() != 1){
            $result = Client::query()
                ->where('name', 'LIKE', "%{$request->search}%")
                ->whereJsonContains('assign_to',Auth::id())
                ->orwhere('is_assign',false)
                ->limit(10)
                ->get(['name', 'id']);
         }else{
            $result = Client::query()
             ->where('name', 'LIKE', "%{$request->search}%")
             ->limit(10)
             ->get(['name', 'id']);
         }
        return $result;
    }
}
