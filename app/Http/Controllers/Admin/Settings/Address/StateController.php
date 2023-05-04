<?php

namespace App\Http\Controllers\Admin\Settings\Address;

use App\Http\Controllers\Controller;
use App\Models\CRM\Address\Country;
use App\Models\CRM\Address\District;
use App\Models\CRM\Address\State;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $countries = Country::latest()->get();
            if ($request->ajax()) {
                $state = State::latest()->get();
                return DataTables::of($state)
                    ->addIndexColumn()
                    ->addColumn('action', function ($state) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a class="btn btn-sm btn-success text-white edit-city" style="cursor:pointer" onclick="getSelectedUserData(' . $state->id . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#edit_State_Modal"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $state->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.settings.address.state.index', compact('countries'));
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
                'state_name' => 'required',
                'Country_Name' => 'required',
            ]);

            try {
                $data = new State();
                $data->name = $request->state_name;
                $data->country_id = $request->Country_Name;
                $data->created_by = Auth::user()->id;
                $data->save();

                return response()->json([
                    'success' => true,
                    'message' => 'State Created Successfully.',
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
            $state = State::where('id', $id)->first();
            $country = Country::findOrFail($state->country_id);

            return response()->json(['state' => $state, 'country' => $country]);
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
            'state_name' => 'required',
        ]);

        // Validation End

        try {
            $data = State::where('id', $id)->first();
            $data->name = $request->state_name;
            $data->updated_by = Auth::user()->id;
            $data->update();

            return response()->json([
                'success' => true,
                'message' => 'State Updated Successfully.',
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
                State::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'State Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
