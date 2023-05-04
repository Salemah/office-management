<?php
namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectLink;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\JsonDecoder;

class ProjectLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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
        ]);

        try {
                $data = new ProjectLink();
                $data->project_id = $request->project_id;
                $data->cpanel_link = $request->cpanel_link;
                $data->cpanel_password = $request->cpanel_password;
                $data->web_link = $request->website;
                $data->git_link = $request->git_link;
                $data->role = json_encode($request->user_role);
                $data->user_email = json_encode($request->email);
                $data->	user_password = json_encode($request->password);
                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $data->save();

            return redirect()->route('admin.projects.show',$request->project_id)->with('message', 'Link Add Successfully');
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
                $projectlinks = ProjectLink::where('project_id',$id)->latest()->get();
                return DataTables::of($projectlinks)
                    ->addIndexColumn()
                    ->addColumn('action', function ($projectlinks) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a href="' . route('admin.project.link.details', $projectlinks->id) . '" class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show"><i class="bx bx-show"></i></a><a href="' . route('admin.project.link.edit', $projectlinks->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="documentDeleteConfirm(' . $projectlinks->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
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
      try {
         $project_link = ProjectLink::findOrFail($id);
         $user_roles = json_decode($project_link->role);
         $user_emails = json_decode($project_link->user_email);
         $user_passwords = json_decode($project_link->user_password);
         return view('admin.project.project.show.partial.edit-link',compact('project_link','user_emails','user_roles','user_passwords'));
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
        $request->validate([
            'project_id' => 'required',
        ]);

        try {
                $data = ProjectLink::findOrFail($id);
                $data->project_id = $request->project_id;
                $data->cpanel_link = $request->cpanel_link;
                $data->cpanel_password = $request->cpanel_password;
                $data->web_link = $request->website;
                $data->git_link = $request->git_link;
                $data->role = json_encode($request->user_role);
                $data->user_email = json_encode($request->email);
                $data->	user_password = json_encode($request->password);
                $data->updated_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

                $data->update();

            return redirect()->route('admin.projects.show',$request->project_id)->with('message', 'Link Update Successfully');
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
                ProjectLink::where('id',$id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Project Link Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function linkDetails(Request $request, $id)
    {
        try {
            $projectLink = ProjectLink::where('id',$id)->with('project')->first();
            $roles = json_decode($projectLink->role);
            $user_emails = json_decode($projectLink->user_email);
            $user_password = json_decode($projectLink->user_password);
            return view('admin.project.project.show.partial.link-view',compact('projectLink','roles','user_emails','user_password'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */


}
