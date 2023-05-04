<?php

namespace App\Http\Controllers\Admin\CRM\Address;

use App\Http\Controllers\Controller;
use App\Models\CRM\Address\Country;
use App\Models\CRM\Address\District;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class istrictController extends Controller
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
                $district = District::latest()->with('country')->get();
                return DataTables::of($district)
                    ->addIndexColumn()
                    ->addColumn('action', function ($district) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.crm.district.edit', $district->id) . '" class="btn btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $district->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('crm.district.index');
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
            // Get All Country
            $countrys = Country::latest()->get();

            return view('crm.district.create',compact('countrys'));
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
            'districts_name' => 'required|string|unique:districts,districts_name',
            'country_id' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = new District();
            $data->districts_name = $request->districts_name;
            $data->country_id = $request->country_id;
            $data->created_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.crm.district.index')->with('toastr-success', 'District Created Successfully');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $district = District::findOrFail($id)->with('country')->first();
            // Get All Country
            $countrys = Country::latest()->get();
            return view('crm.district.edit', compact('district','countrys'));
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
            'districts_name' => 'required|string',
            'country_id' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = District::findOrFail($id);
            $data->districts_name = $request->districts_name;
            $data->country_id = $request->country_id;
            $data->updated_by = Auth::user()->id;
            $data->save();

            return redirect()->route('admin.crm.district.index')->with('toastr-success', 'District Updated Successfully');
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
                District::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Item Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
