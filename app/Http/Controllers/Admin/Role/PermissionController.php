<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
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
                $data = Permission::orderBy('id', 'desc');
                return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
            }
            return view('admin.settings.permission.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function role(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Role::orderBy('id', 'desc');
                return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('permissions', function ($data) {
                    $roles = $data->permissions()->get();
                    $badges = '';
                    foreach ($roles as $key => $role) {
                        $badges .= '<span class="badge me-1 bg-info m-1">' . $role->name . '</span>';
                    }
                    if ($data->name == 'Super Admin') {
                        return '<span class="badge me-1 bg-success m-1">All permissions</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($data) {
                        if ($data->name == 'Super Admin') {
                            return ''; }
                            return '<div class = "btn-group">
                            <a href="' . route('admin.settings.role.edit', $data->id) . '" class="btn btn-sm btn-info"><i class="bx bxs-edit" title="Edit"></i></a><a class="btn btn-sm btn-danger text-white" onclick="showDeleteConfirm(' . $data->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['permissions', 'action'])
                    ->make(true);
            }
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
            $permissionGroups = Permission::select('name')
            ->orderBy('id','ASC')
            ->get();
            return view('admin.settings.role.create',compact('permissionGroups'));
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
        try {

            $role = new Role();
            $role->name = $request->name;
            $role->guard_name  ="web";
            $role->save();
            $role->syncPermissions($request->permissions);

            return redirect()->route('admin.settings.role.index')
            ->with('message', 'Role created successfully');
        } catch (\Exception $exception) {
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
            $role = Role::findOrFail($id);
            $permissions = Permission::all();

            return view('admin.settings.role.edit',compact('role', 'permissions'));
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

        try {
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->guard_name  ="web";
            $role->update();
            $role->syncPermissions($request->permissions);

            return redirect()->route('admin.settings.role.index')
            ->with('message', 'Role Update successfully');
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
        try {
            $role = Role::findOrfail($id);

            $role->delete();
            $role->permissions()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role Deleted Successfully.',
            ]);

        }catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
