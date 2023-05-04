<?php

namespace App\Http\Controllers\Admin\CRM\Client\Reminder;

use App\Http\Controllers\Controller;
use App\Models\Account\Transaction;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientReminder;
use App\Models\Employee\Employee;
use App\Models\User;
use App\Repositories\Admin\Account\AccountsRepository;
use App\Repositories\Admin\Account\TransactionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class ReminderController extends Controller
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
                if (Auth::id() == 1) {
                    $Reminders = ClientReminder::with('createdBy', 'clients')->latest()->get();
                } else {
                    $Reminders = ClientReminder::with('createdBy', 'clients')->where('created_by', Auth::id())->latest()->get();
                }
                return DataTables::of($Reminders)
                    ->addIndexColumn()
                    ->addColumn('comment', function ($Reminders) {
                        if ($Reminders->reminder_note) {
                            return $Reminders->reminder_note;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('client', function ($Reminders) {
                        return $Reminders->clients->name;
                    })
                    ->addColumn('employee', function ($Reminders) {
                        if ($Reminders->createdBy) {
                            return $Reminders->createdBy->name;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('date', function ($Reminders) {
                        if ($Reminders->created_at) {
                            return Carbon::parse($Reminders->date)->format('d M, Y');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('created_at', function ($Reminders) {
                        if ($Reminders->created_at) {
                            return Carbon::parse($Reminders->created_at)->format('d M, Y');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('time', function ($Reminders) {
                        if ($Reminders->created_at) {
                            return Carbon::parse($Reminders->time)->format('h:i a');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('status', function ($Reminders) {
                        if ($Reminders->status == 1) {
                            return '<button
                             onclick="showStatusChangeAlert(' . $Reminders->id . ')"
                             class="btn btn-sm btn-primary">Complete</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Reminders->id . ')"
                            class="btn btn-sm btn-warning">Pending</button>';
                        }
                    })
                    ->addColumn('action', function ($Reminders) {
                        $url = asset('/img/client/comment/' . $Reminders->reminder_document);
                        if ($Reminders->reminder_document) {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                                        <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.reminder.edit', ['id' => $Reminders->id, 'parameter' => 'reminder-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-warning text-white" style="cursor:pointer" type="submit" data-coreui-toggle="modal" data-coreui-target="#staticBackdrop" title="View"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $Reminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                    </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a href="' . $url . '" class="btn btn-sm btn-success text-white disabled" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                                        <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.reminder.edit', ['id' => $Reminders->id, 'parameter' => 'reminder-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-warning text-white" style="cursor:pointer" type="submit" data-coreui-toggle="modal" data-coreui-target="#staticBackdrop" title="View"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $Reminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                    </div>';
                        }
                    })
                    ->rawColumns(['created_at','date', 'time', 'comment', 'client', 'employee', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.crm.reminder.index');
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
            $clients = Client::where('status', 1)->get();
            return view('admin.crm.reminder.create', compact('clients'));
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
        // Validation Start
        $request->validate([
            'client_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = new ClientReminder();
            $data->client_id = $request->client_id;
            $data->reminder_note = $request->reminder_note;
            $data->date = $request->date;
            $data->time = $request->time;
            $data->status = 0;
            $data->email_status = 1;

            if ($request->file('document')) {
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/reminder/'), $filename);
                $data->reminder_document = $filename;
            }

            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

                $client = Client::findOrFail($request->client_id);
                if ($client->is_assign == false) {
                    $client->is_assign = 1;
                    $own_assign = json_decode($client->own_assign);
                    $own_assign [] = Auth::id();
                    $client->own_assign = json_encode($own_assign);
                    $client->update();
                }

            DB::commit();
            if($request->show == 'show'){
                return redirect()->route('admin.crm.client.reminder', $request->client_id)->with('message', "Reminder Add Successfull");
            }
            if($request->list == 'from-list'){
                return redirect()->route('admin.crm.client-reminder.index', $request->client_id)->with('message', "Reminder Add Successfull");
            }



        } catch (\Exception $exception) {
            DB::rollBack();
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
                $ClientReminders = ClientReminder::with('createdBy')->where('client_id', $id)->get();
                return DataTables::of($ClientReminders)
                    ->addIndexColumn()
                    ->addColumn('reminder_note', function ($ClientReminders) {
                        if ($ClientReminders->reminder_note) {
                            return $ClientReminders->reminder_note;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('employee', function ($Reminders) {
                        if ($Reminders->createdBy) {
                            return $Reminders->createdBy->name;
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('date', function ($Reminders) {
                        if ($Reminders->created_at) {
                            return Carbon::parse($Reminders->created_at)->format('d M, Y');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('time', function ($Reminders) {
                        if ($Reminders->created_at) {
                            return Carbon::parse($Reminders->time)->format('h:i a');
                        } else {
                            return ' -- ';
                        }
                    })
                    ->addColumn('status', function ($ClientReminders) {
                        if ($ClientReminders->status == 1) {
                            return '<button
                             onclick="showStatusChangeAlert(' . $ClientReminders->id . ')"
                             class="btn btn-sm btn-primary">Complete</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $ClientReminders->id . ')"
                            class="btn btn-sm btn-warning">Pending</button>';
                        }
                    })
                    ->addColumn('action', function ($ClientReminders) {
                        $url = asset('/img/client/reminder/' . $ClientReminders->reminder_document);
                        if ($ClientReminders->reminder_document) {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                                        <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.reminder.edit', ['id' => $ClientReminders->id, 'parameter' => 'show']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="reminderDeleteConfirm(' . $ClientReminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                    </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a href="' . $url . '" class="btn btn-sm btn-success text-white disabled" style="cursor:pointer" type="submit"  title="Download" > <i class="bx bxs-download"></i></a>
                                        <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.reminder.edit', ['id' => $ClientReminders->id, 'parameter' => 'show']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="reminderDeleteConfirm(' . $ClientReminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                    </div>';
                        }
                    })
                    ->rawColumns(['employee', 'date', 'time', 'reminder_note', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.reminder.reminder-show');
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
    public function edit(Request $request, $id)
    {
        try {
            $data = ClientReminder::findOrFail($id);

            $html = view('admin.crm.reminder.show', compact('data'))->render();

            return response()->json([
                'data' => $html,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function reminderEdit(Request $request, $id)
    {
        try {
            $parameter = $request->parameter;
            $reminder = ClientReminder::findOrFail($id);
            $clients = Client::where('status', 1)->get();
            return view('admin.crm.reminder.edit', compact('clients', 'reminder', 'parameter'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

    public function reminderUpdate(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'client_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = ClientReminder::findOrFail($id);
            $data->client_id = $request->client_id;
            $data->reminder_note = $request->reminder_note;
            $data->date = $request->date;
            $data->time = $request->time;
            $data->status = 0;

            if ($request->file('document')) {
                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/client/reminder/'), $filename);
                $data->reminder_document = $filename;
            }
            $data->updated_by = Auth::user()->id;
            $data->update();

            DB::commit();
            if ($request->parameter == 'reminder-list') {
                return redirect()->route('admin.crm.client-reminder.index')->with('message', ' Updatesuccessfully.');
            } else {
                return redirect()->route('admin.crm.client.reminder', $request->client_id)->with('message', "Reminder UpdateSuccessfull");
            }

        } catch (\Exception $exception) {
            DB::rollBack();
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
                ClientReminder::where('id', $id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Reminder Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    //starts status change function
    public function statusUpdate(Request $request)
    {

        try {
            $comment = ClientReminder::findOrFail($request->id);
            $comment->status == 1 ? $comment->status = 0 : $comment->status = 1;
            $comment->update();
            if ($comment->status == 1) {
                return "Complete";
            } else {
                return "Pending";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function todayReminder(Request $request)
    {
        if ($request->ajax()) {

            $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d');

            $reminders = ClientReminder::with('createdBy', 'clients');

            if ($request->start_date) {
                $reminders->where('date', '>=', $start_date);
            }
            if ($request->end_date) {
                $reminders->where('date', '<=', $end_date);
            }
            if ($request->status) {
                if ($request->status == 1) {
                    $reminders->where('date', '>=', Carbon::now()->format('Y-m-d'));
                }
                if ($request->status == 2) {
                    $reminders->where('date', '<', Carbon::now()->format('Y-m-d'));
                }

            }
            if($request->client != null){
                $reminders->where('client_id', '=', $request->client);
            }
            if($request->employee != null){
                $reminders->where('created_by', '=', $request->employee);
            }
            if($request->day){
                if($request->day == 'today'){
                    $reminders->where('date',  Carbon::now()->format('Y-m-d'));
                }
                if($request->day == 'tomorrow'){
                    $reminders->where('date', Carbon::tomorrow()->format('Y-m-d'));
                }
                if($request->day == 'sevenDays'){
                    $reminders->where('date','>', Carbon::now()->format('Y-m-d'))
                                ->where('date','<=', Carbon::today()->addDays(7)->format('Y-m-d'));
                }

            }

            $reminders->get();

            if (Auth::id() == 1) {
                $Reminders = $reminders;
            } else {
                $Reminders = $reminders->where('created_by', Auth::id())->get();
            }
            return DataTables::of($Reminders)
                ->addIndexColumn()
                ->addColumn('comment', function ($Reminders) {
                    if ($Reminders->reminder_note) {
                        return $Reminders->reminder_note;
                    } else {
                        return ' -- ';
                    }
                })
                ->addColumn('client', function ($Reminders) {
                    return '<a class="text-primary" style="cursor:pointer;text-decoration: none;"
                    href="' . route('admin.crm.client.show', $Reminders->clients->id) . '"> ' . $Reminders->clients->name . ' </a>';
                })
                ->addColumn('reminder_note', function ($Reminders) {
                    return $Reminders->reminder_note;
                })
                ->addColumn('employee', function ($Reminders) {
                    if ($Reminders->createdBy) {
                        return $Reminders->createdBy->name;
                    } else {
                        return ' -- ';
                    }
                })
                ->addColumn('date', function ($Reminders) {
                    if ($Reminders->date) {
                        return Carbon::parse($Reminders->date)->format('d M, Y');
                    } else {
                        return ' -- ';
                    }
                })
                ->addColumn('created_at', function ($Reminders) {
                    if ($Reminders->created_at) {
                        return Carbon::parse($Reminders->created_at)->format('d M, Y');
                    } else {
                        return ' -- ';
                    }
                })
                ->addColumn('time', function ($Reminders) {
                    if ($Reminders->time) {
                        return Carbon::parse($Reminders->time)->format('h:i a');
                    } else {
                        return ' -- ';
                    }
                })
                ->addColumn('status', function ($Reminders) {
                    if ($Reminders->status == 1) {
                        return '<button
                         onclick="showStatusChangeAlert(' . $Reminders->id . ')"
                         class="btn btn-sm btn-primary">Complete</button>';
                    } else {
                        return '<button
                        onclick="showStatusChangeAlert(' . $Reminders->id . ')"
                        class="btn btn-sm btn-warning">Pending</button>';
                    }
                })
                ->addColumn('action', function ($Reminders) {
                    $url = asset('/img/client/comment/' . $Reminders->reminder_document);
                    if ($Reminders->reminder_document) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a href="' . $url . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                                <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" onclick="getReminder(' . $Reminders->id .')" href="' . route('admin.crm.reminder.edit', ['id' => $Reminders->id, 'parameter' => 'reminder-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                <a class="btn btn-sm btn-info text-white" style="cursor:pointer" type="submit" data-coreui-toggle="modal" data-coreui-target="#staticBackdrop" title="show"> <i class="bx bx-show-alt"></i></a>
                                <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $Reminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                    } else {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a href="' . $url . '" class="btn btn-sm btn-success text-white disabled" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>
                                    <a class="btn btn-sm btn-primary text-white" style="cursor:pointer" type="submit" href="' . route('admin.crm.reminder.edit', ['id' => $Reminders->id, 'parameter' => 'reminder-list']) . '" title="Edit"> <i class="bx bxs-edit"></i></a>
                                    <a class="btn btn-sm btn-info text-white" style="cursor:pointer" type="submit" onclick="getReminder(' . $Reminders->id .')" data-coreui-toggle="modal" data-coreui-target="#staticBackdrop" title="show"> <i class="bx bx-show-alt"></i></a>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="commentDeleteConfirm(' . $Reminders->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                </div>';
                    }
                })
                ->rawColumns(['created_at','reminder_note','date', 'time', 'comment', 'client', 'employee', 'status', 'action'])
                ->make(true);
        }
    }
    public function employeeSearch(Request $request)
    {
        $result = User::query()
                    ->where('user_type', 1)
                    ->where('name', 'LIKE', "%{$request->search}%")
                    ->get(['name', 'id']);
        return $result;
    }
}
