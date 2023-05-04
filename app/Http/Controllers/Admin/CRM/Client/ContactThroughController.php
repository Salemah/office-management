<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\ContactThrough;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactThroughController extends Controller
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
                $ContactThrough = ContactThrough::latest()->get();
                return DataTables::of($ContactThrough)
                    ->addIndexColumn()
                    ->addColumn('status', function ($ContactThrough) {
                        if ($ContactThrough->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $ContactThrough->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $ContactThrough->id . ')" class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('description', function ($ContactThrough) {
                        return $ContactThrough->description;
                    })
                    ->addColumn('action', function ($ContactThrough) {
                        if (Auth::user()->can('contact_through_edit')){
                            $editBtn = '<a class="btn btn-sm  btn-success text-white " style="cursor:pointer"
                            href="' . route('admin.crm.contact-through.edit', $ContactThrough->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('contact_through_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $ContactThrough->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }
            return view('admin.crm.client.settings.contact-through.index');
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
            return view('admin.crm.client.settings.contact-through.create');
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
        // Validation Start
        $request->validate([
            'name' => 'required|unique:contact_throughs,name',
            'status' => 'required',

        ]);
        // Validation End
        // Store Data
        try {
            $data = new ContactThrough();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();
            $data->save();

            return redirect()->route('admin.crm.contact-through.index')->with('message', 'Create Successfully.');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $ContactThrough = ContactThrough::where('id', $id)->first();
            return view('admin.crm.client.settings.contact-through.edit', \compact('ContactThrough'));
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
        // Validation Start
        $request->validate([
            'name' => 'required|unique:contact_throughs,name,'. $id .',id',
            'status' => 'required',

        ]);
        // Validation End

        try {
            $data = ContactThrough::findOrFail($id);
            $data->name = $request->name;
            $data->status = $request->status;
            $data->description = $request->description;
            $data->updated_by = Auth::id();
            $data->update();

            return redirect()->route('admin.crm.contact-through.index')->with('message', 'Update successfull.');
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
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                ContactThrough::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Contact Through  Deleted Successfully.',
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

            $ContactThrough= ContactThrough::findOrFail($request->id);

            $ContactThrough->status == 1 ? $ContactThrough->status = 0 : $ContactThrough->status = 1;
            $ContactThrough->update();

            if ($ContactThrough->status == 1) {
                return "active";
            } else {
                return "inactive";
            }

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
