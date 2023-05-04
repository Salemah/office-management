<?php

namespace App\Http\Controllers\Admin\Settings\Address;

use App\Http\Controllers\Controller;
use App\Models\CRM\Address\City;
use App\Models\CRM\Address\Country;
use App\Models\CRM\Address\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
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
                $city = DB::table('cities')
                        ->select('id', 'name')->get();

                return DataTables::of($city)
                    ->addIndexColumn()
                    ->addColumn('action', function ($city) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a class="btn btn-sm btn-success text-white edit-city" style="cursor:pointer" onclick="getSelectedUserData(' . $city->id . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#edit_City_Modal"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $city->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.settings.address.city.index');
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
            return view('admin.settings.address.country.create');
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


        if ($request->ajax()) {
            // Validation Start
            $request->validate([
                'city_name' => 'required',
                'state_name' => 'required',
            ]);

            try {
                $data = new City();
                $data->name = $request->city_name;
                $data->state_id = $request->state_name;
                $data->created_by = Auth::user()->id;
                $data->save();

                return response()->json([
                    'success' => true,
                    'message' => 'City Created Successfully.',
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
            $city = City::findOrFail($id);
            $state = State::findOrFail($city->state_id);
            $country = Country::findOrFail($state->country_id);
            return response()->json(['state' => $state, 'city' => $city, 'country' => $country]);
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
            'city_name' => 'required',
        ]);

        // Validation End
        try {
            $data = City::where('id', $id)->first();
            $data->name = $request->city_name;
            $data->updated_by = Auth::user()->id;

            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'City Updated Successfully.',
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
                City::findOrFail($id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'City Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
