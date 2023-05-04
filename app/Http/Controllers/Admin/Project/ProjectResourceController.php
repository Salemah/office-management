<?php

namespace App\Http\Controllers\Admin\Project;

use DataTables;
use Carbon\Carbon;

use App\Models\Documents;
use Illuminate\Http\Request;
use App\Models\Project\Projects;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Project\ProjectDuration;

class ProjectResourceController extends Controller
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
                $documents = Documents::latest()->get();
                return datatables()::of($documents)
                    ->addIndexColumn()

                    ->addColumn('action', function ($documents) {
                        $url= asset('img/project/resource/'.       $documents->document_file);
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"> <a href="'. $url .'" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a> <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $documents->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.project.project.show.resource.partial.resource-list');
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
        // dd($request->all());
        $request->validate([
            'document_name' => 'required',
            'document_file' => 'required',
        ]);

        try {
            $data                =  new Documents();
            $data->document_id   =  $request->project_id;
            $data->document_name =  $request->document_name;
            $data->document_type =  6;

            $file = $request->file('document_file');
            $filename = time() . $file->getClientOriginalName();
            $file->move(public_path('/img/project/resource/'), $filename);
            $data->document_file = $filename;

            $data->created_by    =  Auth::user()->id;
            $data->access_id     =  json_encode(UserRepository::accessId(Auth::id()));

            if($data->save()){
                return redirect()
                    ->route('admin.project.resource', $request->project_id)
                    ->with('message', 'Project Resource AddSuccessfully');
            }

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
    public function show(Request $request, $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
                Documents::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Project Resource Deleted Successfully.',
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
    public function projectResource(Request $request, $id)
    {
        try {
            $project = Projects::findOrFail($id);
            return view('admin.project.project.show.resource.resource-show',
                compact('project'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function durationDetails(Request $request, $id)
    {

    }

}
