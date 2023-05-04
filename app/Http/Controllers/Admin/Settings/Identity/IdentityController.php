<?php

namespace App\Http\Controllers\Admin\Settings\Identity;

use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentityController extends Controller
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
                $Identity = Identity::latest()->get();
                return DataTables::of($Identity)
                    ->addIndexColumn()
                    ->addColumn('status', function ($Identity) {
                        if ($Identity->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Identity->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Identity->id . ')"
                            class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('action', function ($Identity) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                        href="' . route('admin.settings.identity.edit', $Identity->id) . '" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Identity->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }
            return view('admin.settings.identity.index');
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
            return view('admin.settings.identity.create');
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
            'identity_name' => 'required|string',
        ]);

        try {
            $data = new Identity();
            $data->name = $request->identity_name;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.settings.identity.index')->with('message', 'Create successfully.');
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
            $identity = Identity::findOrFail($id);
            return view('admin.settings.identity.edit', compact('identity'));
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
            'identity_name' => 'required|string',
        ]);

        try {
            $data = Identity::findOrFail($id);
            $data->name = $request->identity_name;
            $data->status = $request->status;
            $data->created_by = Auth::user()->id;
            $data->update();

            return redirect()->route('admin.settings.identity.index')->with('message', 'Update successfully.');
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
                Identity::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Identity Deleted Successfully.',
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
            $reference = Identity::findOrFail($request->id);
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
