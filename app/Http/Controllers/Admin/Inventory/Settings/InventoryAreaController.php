<?php

namespace App\Http\Controllers\Admin\Inventory\Settings;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\Area\InventoryArea;

class InventoryAreaController extends Controller
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
                $data = InventoryArea::all();
                return DataTables::of($data)
                    ->addIndexColumn()

                    ->addColumn('status', function ($data) {
                        if ($data->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $data->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $data->id . ')">Inactive</button>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($data) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.inventory.settings.area.edit', $data->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer; margin-left:5px" type="submit" onclick="showDeleteConfirm(' . $data->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['status','action',])
                    ->make(true);
            }
            return view('admin.inventory.settings.area.index');
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
            return view('admin.inventory.settings.area.create');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
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
            'name.required' => 'Enter area name',
            'area_code.required' => 'Enter area code',
        );

        $this->validate($request, array(
            'name' => 'required|string|unique:inventory_areas,name,NULL,id,deleted_at,NULL',
            'area_code' => 'required|string|unique:inventory_areas,area_code,NULL,id,deleted_at,NULL',
        ), $messages);

        try {
            $data                 =  new InventoryArea();
            $data->name           =  $request->name;
            $data->area_code      =  $request->area_code;
            $data->status         =  $request->status;
            $data->created_by     =  Auth::user()->id;
            $data->access_id      =  json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.inventory.settings.area.index')
                    ->with('toastr-success', 'Area added successfully');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data = InventoryArea::findOrFail($id);
            return view('admin.inventory.settings.area.edit', compact('data'));
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
            'name.required' => 'Enter area name',
            'area_code.required' => 'Enter area code',
        );

        $this->validate($request, array(
            'name' => 'required|unique:inventory_areas,name,' . $id . ',id,deleted_at,NULL',
            'area_code' => 'required|unique:inventory_areas,area_code,' . $id . ',id,deleted_at,NULL',
        ), $messages);

        // update Data
        try {
            $data                 =  InventoryArea::findOrFail($id);
            $data->name           =  $request->name;
            $data->area_code      =  $request->area_code;
            $data->status         =  $request->status;
            $data->updated_by     =  Auth::user()->id;
            $data->access_id      =  json_encode(UserRepository::accessId(Auth::id()));
            $data->update();

            return redirect()->route('admin.inventory.settings.area.index')
                    ->with('toastr-success', 'Area updated sccessfully');
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
                InventoryArea::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Area deleted successfully.',
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
            $data = InventoryArea::findOrFail($id);
            $data->status = $data->status == 1 ? 0 : 1;
            $data->update();

            if ($data->status == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Area status activated successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Area status inactivated successfully',
                ]);
            }
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
    }
}
