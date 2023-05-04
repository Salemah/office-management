<?php
namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ProjectDocumentController extends Controller
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
                $projects = Projects::with('projectCategory')->latest()->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('project_category', function ($projects) {
                        if ($projects->projectCategory) {
                            return $projects->projectCategory->name;
                        } else {
                            return '';
                        }

                    })
                    ->addColumn('status', function ($projects) {
                        if ($projects->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white">Up Coming</button>';
                        } else if ($projects->status == 2) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" >On Going</button>';

                        } else if ($projects->status == 3) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" >Complete</button>';

                        } else if ($projects->status == 4) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white">Cancel</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($projects) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.projects.show', $projects->id) . '" class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show"><i class="bx bx-show"></i></a>
                                  <a href="' . route('admin.projects.edit', $projects->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['project_category','status','action'])
                    ->make(true);
            }
            return view('admin.project.project.index');
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'project_id' => 'required',
            'document_name' => 'required',
            'document' => 'required',
        ]);

        try {
            $data = new Documents();
                $data->document_id = $request->project_id;
                $data->document_name = $request->document_name;
                $data->document_type =5; // project userType = 5

                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/project/documents/'), $filename);
                $data->document_file = $filename;

                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->save();

            return redirect()->route('admin.projects.show',$request->project_id)->with('message', 'Document Add Successfully');
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
    public function show(Request $request, $id)
    {
        try{
            if ($request->ajax()) {
                $projectDocuments = Documents::where('document_id',$id)->where('document_type',5)->latest()->get();
                return DataTables::of($projectDocuments)
                    ->addIndexColumn()
                    ->addColumn('action', function ($projectDocuments) {
                        $url= asset('img/project/documents/'.$projectDocuments->document_file);
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a href="'. $url .'" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="documentDeleteConfirm(' . $projectDocuments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.project.project.show.assignto.assignto-show',compact('project'));
        }
        catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
        if ($request->ajax()) {
            try {
                Documents::where('id',$id)->where('document_type',5)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Project Document Deleted Successfully.',
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


}
