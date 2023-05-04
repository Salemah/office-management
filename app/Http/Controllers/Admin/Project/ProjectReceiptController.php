<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Documents;
use App\Models\Employee\Employee;
use App\Models\Project\ProjectBudget;
use App\Models\Project\ProjectDuration;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectReceiptController extends Controller
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
    public function create(Request $request,$id)
    {
        try {
            $project = Projects::findOrFail($id);
            return view('admin.project.project.show.duration.module.module-show', compact('project'));
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
        $request->validate([
            'receipt_amount' => 'required',
            'transaction_way' => 'required',
            'amount_type' => 'required',
            'receipt_date' => 'required',
            'receive_by_id' => 'required',
        ]);
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }
        try {
                $transaction = new Transaction();
                $transaction->project_id = $request->project_id;
                $transaction->amount = $request->receipt_amount;
                $transaction->amount_type = $request->amount_type;
                // $transaction->transaction_way = $request->transaction_way;
                $transaction->transaction_date = $request->receipt_date;

                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                    $transaction->transaction_account_type = 2;
                }
                else if($request->transaction_way == 1){
                    $transaction->account_id = $request->cash_account_id;
                    $transaction->transaction_account_type = 1;
                }

                $transaction->transaction_title =" Project Budget Receipt";
                $transaction->transaction_purpose = 16 ; // 16 == project receipt budget
                $transaction->cheque_number = $request->cheque_number; // 16 == project receipt budget
                $transaction->transaction_type = 2 ;
                $transaction->description = $request->description;
                $transaction->status = 1;
                $transaction->received_by = $request->receive_by_id;

               $transaction->created_by = Auth::user()->id;
                $transaction->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $transaction->save();

                if($request->file('documents')){
                    foreach ($request->file('documents') as $key => $image) {

                        $name = $image->getClientOriginalName();
                        $image->move(public_path() . '/img/budget/documents', $name);

                        $documents = new Documents();
                        $documents->document_id = $transaction->id;
                        $documents->document_file = $name;
                        $documents->document_name = $request->document_title[$key];
                        $documents->document_type = 7; //document_type 7 == budget receive

                        $documents->created_by = Auth::user()->id;
                        $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                        $documents->save();
                    }
                }

            return redirect()-> route('admin.project.account-budget.view',$request->project_id)->with('message', 'Project Budget Add Successfully');

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
        try {
            if ($request->ajax()) {
                $projects = Transaction::with('createdByUser')->where('project_id',$id)->latest()->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('createdBy', function ($projects) {
                        $employee = Employee::findOrFail($projects->received_by);
                        return $employee->name;
                     })
                    ->addColumn('time', function ($projects) {
                        return Carbon::parse($projects->transaction_date)->format('d/m/Y');
                     })
                    ->addColumn('details', function ($projects) {
                        $project= Projects::with('projectCategory')->where('id',$projects->project_id)->first();
                        //$budget= DB::table('project_budgets')->where('project_id',$projects->project_id)->sum('amount');

                        return $project->projectCategory->name . ' | ' .$project->project_title  . ' || ' . $project->project_code ;
                     })
                    ->addColumn('amount', function ($projects) {
                        return number_format($projects->amount,2) ;
                     })
                    // ->addColumn('client', function ($projects) {

                    //     $project = Projects::findOrFail($projects->project_id);
                    //     if(json_decode($project->client_id)!=null){
                    //         $client_ids= json_decode($project->client_id);
                    //         foreach($client_ids as $key=>$client){
                    //             $clients[]= Client::findOrFail($client);
                    //         }
                    //     }
                    //  })
                    ->addColumn('action', function ($projects) {
                                return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                            <a href="' . route('admin.project.budget-receipt.edit', $projects->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Show"><i class="bx bxs-edit"></i></a>
                                            <a class="btn btn-sm btn-primary text-white edit-city" style="cursor:pointer" onclick="getSelectedAllowanceData(' . $projects->id . ',1' . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#allowanceEditSingleModal"><i class="bx bx-download"></i></a>
                                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showReceiveDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                        </div>';
                    })
                    ->rawColumns(['amount','details','createdBy','time','action'])
                    ->make(true);
            }
            return view('admin.project.project.show.duration.duration-show');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $projectReceive = Transaction::findOrFail($id);
            $project = Projects::find($projectReceive->project_id);
            $total_budget =ProjectBudget::where('project_id',$projectReceive->project_id)->sum('amount');
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            $documents = Documents::where('document_id', $id)->where('document_type', 7)->get();

            $projectpayment = ProjectBudget::where('project_id',$project->id)->sum('amount');
            $due = Transaction::where('project_id',$project->id)->where('status',1)
                 ->where('transaction_purpose',16) ->sum('amount');

            $total_due = $projectpayment -$due ;
            $employee = Employee::findOrFail($projectReceive->received_by);
            return view('admin.project.project.show.account.partial.receipt-edit',compact('project','projectReceive','total_budget','bankAccounts','documents','cash_account','total_due','employee'));
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
        $request->validate([
            'receipt_amount' => 'required',
            'transaction_way' => 'required',
            'amount_type' => 'required',
            'receipt_date' => 'required',
        ]);
        if ($request->transaction_way) {
            if ($request->transaction_way == 2) {
                $request->validate([
                    'account_id' => 'required',
                ]);
            }
            else if ($request->transaction_way == 1) {
                $request->validate([
                    'cash_account_id' => 'required',
                ]);
            }
        }
        try {
                $transaction =Transaction::findOrFail($id);
                $transaction->project_id = $request->project_id;
                $transaction->amount = $request->receipt_amount;
                $transaction->amount_type = $request->amount_type;
                $transaction->transaction_date = $request->receipt_date;
                if ($request->transaction_way == 2) {
                    $transaction->account_id = $request->account_id;
                    $transaction->transaction_account_type = 2;
                }
                else if($request->transaction_way == 1){
                    $transaction->account_id = $request->cash_account_id;
                    $transaction->transaction_account_type = 1;
                }
                $transaction->transaction_title =" Project Budget Receipt";
                $transaction->transaction_purpose = 16 ; // 16 == project receipt budget
                $transaction->cheque_number = $request->cheque_number; // 16 == project receipt budget
                $transaction->transaction_type = 2 ;
                $transaction->description = $request->description;
                $transaction->status = 1;
                $transaction->received_by = $request->receive_by_id;
                $transaction->updated_by = Auth::user()->id;
                $transaction->update();

                // revenue document Update
                    $old_documents = Documents::where('document_type',7)
                    ->where('document_id',$id)
                    ->pluck('id')->toArray();


                if ($request->revenue_document_id) {
                    $result=array_diff($old_documents,$request->revenue_document_id);
                    if($result){
                        Documents::whereIn('id', $result)->delete();
                    }
                    foreach ($request->revenue_document_id as $key=>$document_id) {
                        $doc = Documents::findOrFail($document_id);
                        $doc->document_name = $request->document_title[$key];
                        $doc->update();

                    }
                }else{
                    Documents::whereIn('id', $old_documents)->delete();
                }

                if ($request->hasfile('documents')) {
                    foreach ($request->file('documents') as $key => $image) {

                        $name = $image->getClientOriginalName();
                        $image->move(public_path() . '/img/budget/documents', $name);
                        $documents = new Documents();
                        $documents->document_id = $id;
                        $documents->document_file = $name;
                        $documents->document_name = $request->document_title[$key];
                        $documents->document_type = 7; //document_type 7 == receive
                        $documents->updated_by = Auth::user()->id;
                        $documents->save();
                    }
                }

                return redirect()-> route('admin.project.account-budget.view',$request->project_id)->with('message', 'Project Receive Update Successfully');

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function projectUpdate(Request $request)
    {
        $request->validate([
            'module_name' => 'required',
            'project_id' => 'required',
            'project_duration_id' => 'required',
            'module_start_date' => 'required',
            'module_end_date' => 'required',
            'estimated_hour_day' => 'required',
            'module_total_day' => 'required',
            'module_total_hour' => 'required',
            'final_hour' => 'required',
            'status' => 'required',
        ]);

        try {
            $data = ProjectDuration::findOrFail($request->edit_project_module_id);
            $data->duration_type_id = $request->project_id;
            $data->duration_type  = 2; // Project Module Type = 1
            $data->name  = $request->module_name;
            $data->start_date = $request->module_start_date;
            $data->end_date = $request->module_end_date;
            $data->estimate_day = $request->module_estimate_day;
            $data->estimate_hour_per_day = $request->estimated_hour_day;
            $data->estimate_hour = $request->module_total_hour;
            $data->	final_hour = $request->final_hour;
            $data->adjustment_type = $request->adjustment_type;
            $data->adjustment_hour = $request->adjustment_hour;
            $data->vacation_day = $request->edit_module_vacation_day;
            $data->final_day = $request->edit_module_final_day;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.project.duration', $request->project_id)
                ->with('message', 'Project Module Update Successfully');
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
                Transaction::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Project Budget Receive Deleted Successfully.',
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
    public function projectAccounts(Request $request, $id)
    {
        try {
            // $projectDuration = ProjectDuration::findOrFail($id);
            $project = Projects::findOrFail($id);
            $total_budget = ProjectBudget::where('project_id',$id)   ->sum('amount');
            $bankAccounts = BankAccount::where('status', 1)->get();
            return view('admin.project.project.show.account.account-show',compact('project','total_budget','bankAccounts'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function receiveDocument(Request $request)
    {

        try {
            $documents = Documents::where('document_id',$request->id)
                                    ->where('document_type',7)
                                    ->get();

            $html = view('admin.project.project.show.account.partial.receive-document-download', compact('documents'))->render();

            return response()->json([
                'data' =>  $html,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

}
