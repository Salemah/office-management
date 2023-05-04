<?php

namespace App\Http\Controllers\Admin\Settings\Address;

use App\Http\Controllers\Controller;
use App\Models\CRM\Address\Country;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
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
                $country = Country::latest()->get();
                return DataTables::of($country)
                    ->addIndexColumn()
                    ->addColumn('action', function ($country) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a class="btn btn-sm btn-success text-white edit-country" style="cursor:pointer" onclick="getSelectedUserData(' . $country->id . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#editCountryModal"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $country->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.settings.address.country.index');
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
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->ajax()) {
            // Validation Start
            $data = Validator::make($request->all(), [
                'country_name' => 'required|unique:countries,name,NULL,id,deleted_at,NULL',
            ]);

            if ($data->fails()) {
                return response()->json(['errors' => $data->errors()->all()]);
            }
            // Validation End

            // Store Data
            try {
                $data = new Country();
                $data->name = $request->country_name;
                $data->created_by = Auth::user()->id;
                $data->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Country Created Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
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
            $country = Country::where('id', $id)->first();
            return response()->json($country);
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
        $data = Validator::make($request->all(), [
            'country_name' => 'required|unique:countries,name,NULL,id,deleted_at,NULL'.$id,
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()->all()]);
        }
        // Validation End

        try {
            $data = Country::where('id', $id)->first();
            $data->name = $request->country_name;
            $data->updated_by = Auth::user()->id;
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Country Updated Successfully.',
            ]);
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
                Country::findOrFail($id)->delete();
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
