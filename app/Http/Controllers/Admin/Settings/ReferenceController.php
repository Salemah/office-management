<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Reference;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class ReferenceController extends Controller
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
                $Reference = Reference::latest()->get();
                return DataTables::of($Reference)
                    ->addIndexColumn()
                    ->addColumn('status', function ($Reference) {
                        if ($Reference->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Reference->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Reference->id . ')"
                            class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('description', function ($Reference) {
                        return $Reference->description;
                    })
                    ->addColumn('action', function ($Reference) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer" title="Edit"
                        href="' . route('admin.settings.reference.edit', $Reference->id) . '"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Reference->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }
            return view('admin.settings.reference.index');
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
            return view('admin.settings.reference.create');
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
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        // Validation End

        // Store Data
        try {

            $data = new Reference();

            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->website = $request->website;
            $data->gender = $request->gender;
            $data->address = $request->address;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();

            $data->save();

            return redirect()->route('admin.settings.reference.index')->with('message', 'Create successfully.');
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
            $Reference = Reference::findOrFail($id);
            return view('admin.settings.reference.edit', compact('Reference'));
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
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = Reference::findORFail($id);

            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->website = $request->website;
            $data->gender = $request->gender;
            $data->address = $request->address;
            $data->status = $request->status;
            $data->description = $request->description;
            $data->updated_by = Auth::id();

            $data->update();
            return redirect()->route('admin.settings.reference.index')->with('message', 'Update successfully.');
            $data->update();

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
                Reference::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Department Deleted Successfully.',
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
            $reference = Reference::findOrFail($request->id);
            $reference->status == 1 ? $reference->status = 0 : $reference->status = 1;

            $reference->update();

            if ($reference->status == 1) {
                return "active";
            } else {
                return "inactive";
            }


        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
