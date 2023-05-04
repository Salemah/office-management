<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\DashboardSetting;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardSettingsController extends Controller
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
                $dashboardSettings = DashboardSetting::latest()->get();
                return DataTables::of($dashboardSettings)
                    ->addIndexColumn()
                    ->addColumn('status', function ($dashboardSettings) {
                        if ($dashboardSettings->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $dashboardSettings->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $dashboardSettings->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($dashboardSettings) {
                        return $dashboardSettings->description;
                    })
                    ->addColumn('action', function ($dashboardSettings) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.expense.category.edit', $dashboardSettings->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $dashboardSettings->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.expense.expense-category.index');
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
            $DashboardSetting = DashboardSetting::first();
            return view('admin.dashboard-setting.create', compact('DashboardSetting'));
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
        try {
            if (!$request->id) {
                $request->validate([
                    'system_title' => 'required',
                    'favicon' => 'required',
                    'logo' => 'required',

                ]);
                $data = new DashboardSetting();
            } else {
                $data = DashboardSetting::findOrFail($request->id);
            }
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/dashboard'), $filename);
                $data->logo = $filename;
            }
            if ($request->file('favicon')) {
                $file = $request->file('favicon');
                $filenamefavicon = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/dashboard'),  $filenamefavicon);
                $data->favicon =  $filenamefavicon;
            }
            $data->system_title = $request->system_title;
            $data->description = $request->description;

            if (!$request->id) {
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->created_by = Auth::id();
                $data->save();
                return redirect()->route('admin.settings.dashboard.create')->with('success', 'Create successfull.');
            } else {
                $data->updated_by = Auth::id();
                $data->update();
                return redirect()->route('admin.settings.dashboard.create')->with('success', 'Update successfull.');
            }

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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

    }

}
