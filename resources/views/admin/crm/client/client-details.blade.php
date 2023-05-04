@extends('layouts.dashboard.app')

@section('title', 'Client')

@push('css')
    <style>
        .reminder{
           border-right: 2px solid red;
           height: 18vh;
        }
        .reminder p{
            font-weight: bold;
            font-size: 20px;
           text-align: right;
           color: blue;
        }
        .reminder-details{
            padding-left: 15px;
            color: blue;
        }
        .reminder-details p{
            padding-left: 15px;
            color: blue;
        }
        .reminder-label{
            font-size: 15px;
            font-weight: bolder;

        }
        .reminder-head{
            color:blue;
        }
        #reminder-head-hr{
            width: 13rem;
            margin: auto;
            color:blue;
        }


    </style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Client</span>
            </li>
        </ol>
        <a href="{{ route('admin.crm.client.show',$Client->id) }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->
        <div class="row gutters ">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <h3 class="text-primary text-center mt-2">Client Details</h3>
                    <div class="card-body p-0">
                        <div class="invoice-container p-5">
                            <div class="row">
                                <div class="col-12  col-sm-12 col-md-2 mb-2">
                                    @if ($Client->image)
                                        <img src="{{asset('img/client/' . $Client->image)}}" style="border:0;width:40%;" />
                                    @else
                                       <img src="{{asset('img/no-image/noman.jpg')}}"   style="border:0;width:90%;"/>
                                     @endif
                                </div>
                                <div class="col-12 col-sm-12 col-md-10 mb-2">
                                    <table class="table table-bordered">
                                        <thead class="table-success">
                                          <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Primary Phone</th>
                                            <th scope="col">Secondary Phone</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Details</th>
                                            <th scope="col">Contact Through</th>
                                            <th scope="col">Interested On</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                           <th >{{$Client->name}}</th>
                                            <td>{{$Client->phone_primary}}</td>
                                            <td>{{$Client->phone_secondary}}</td>
                                            <td>{{$Client->email}}</td>
                                            <td>{!! $Client->description !!}</td>
                                            <td> @if (isset($Client->contactThrough))
                                                {{$Client->contactThrough->name}}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($Client->interestedOn))
                                                {{$Client->interestedOn->name}}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                </div>
                                {{-- <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead class="table-success">
                                          <tr>
                                            <th scope="col">Details</th>
                                            <th scope="col">Contact Through</th>
                                            <th scope="col">Interested On</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td>{!! $Client->description !!}</td>
                                            <td> @if (isset($Client->contactThrough))
                                                {{$Client->contactThrough->name}}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($Client->interestedOn))
                                                {{$Client->interestedOn->name}}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                </div> --}}
                            </div>
                            @if (count($Client->comments) > 0)
                                <h4 class="text-danger">Comments : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="" style="background-color: rgba(11, 38, 146, 0.496)">
                                            <tr>
                                                <th scope="col">Comment</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Added By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($Client->comments as $comment)
                                                    <tr>
                                                        <td >{!!$comment->comment!!}</td>
                                                        <td>{{Carbon\Carbon::parse($comment->created_at	)->format('M d Y')}}</td>
                                                        <td class="text-primary">{{$comment->createdBy->name}}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if (count($Client->reminders) > 0)
                                {{-- <h4 class=" text-center reminder-head">Reminder </h4> --}}
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <h4 class=" text-center reminder-head">Reminder </h4>
                                        <hr id="reminder-head-hr">
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            @foreach ($Client->reminders as $key=>$reminder)
                                                <div class="col-2 col-sm-2 col-md-1 mb-3 reminder">
                                                    <p>{{$key+1}}</p>
                                                </div>
                                                <div class="col-10 col-sm-10 col-md-5 mb-3 reminder-details">
                                                    <p><span class="reminder-label" ><i class='bx bx-timer me-1 text-danger'></i></i>Date : </span>{{Carbon\Carbon::parse($reminder->created_at)->format('M d Y')}}</p>
                                                    <p><span class="reminder-label" ><i class='bx bx-calendar me-1 text-danger' ></i>Time : </span>{{Carbon\Carbon::parse($reminder->time)->format('h:i a')}}</p>
                                                    <p><span class="reminder-label " ><i class='bx bx-info-circle me-1 text-danger'></i>Status:</span>
                                                         @if ($reminder->status == 0)
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @else
                                                            <span class="badge bg-primary">Complete</span>
                                                        @endif
                                                    </p>
                                                    <p><span class="reminder-label " ><i class='bx bx-info-circle me-1 text-danger'></i>Added By :</span> {{$reminder->createdBy->name}}</p>
                                                    <p><span class="reminder-label " ><i class='bx bx-info-circle me-1 text-danger'></i>Details :</span> {!! $reminder->reminder_note !!}</p>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="" style="background-color: rgba(228, 89, 2, 0.496)">
                                            <tr>
                                                <th scope="col">Time</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Satus</th>
                                                <th scope="col">Added By</th>
                                                <th scope="col">Details</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($Client->reminders as $reminder)
                                                    <tr>
                                                        <td ><i class='bx bxs-calendar-alt me-1'></i>{{Carbon\Carbon::parse($reminder->time)->format('h:i a')}}</td>
                                                        <td><i class='bx bxs-calendar-alt me-1'></i>{{Carbon\Carbon::parse($reminder->created_at)->format('M d Y')}}</td>
                                                        <td>
                                                            @if ($reminder->status == 0)
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                            @else
                                                            <span class="badge bg-primary">Complete</span>
                                                            @endif
                                                        </td>
                                                        <td>{!! $reminder->reminder_note !!}</td>
                                                        <td class="text-primary ">{{$reminder->createdBy->name}}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if (count($EmployeeIdentity) > 0)
                                <h4 class="text-primary">Identity Details : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-warning">
                                            <tr>
                                                <th scope="col">Identity Type</th>
                                                <th scope="col">Identity No</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($EmployeeIdentity as $employeeIdentity)
                                                    <tr>
                                                        <td >{{$employeeIdentity->identity->name}}</td>
                                                        <td>{{$employeeIdentity->id_no	}}</td>

                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if (count($documents) > 0)
                                <h4 class="text-primary">Documents : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Document Name</th>
                                                <th scope="col">Document</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($documents as $key => $document)
                                                    <tr>
                                                        <th >{{$document->document_name}}</th>
                                                        <td><button type="button" class="btn btn-outline-primary " ><a href="{{ asset('img/employee/documents/'.$document->document) }}" style="text-decoration: none;color:black"><i class='bx bx-down-arrow-alt'></i>Download</a></button></td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if (count($ClientReferences) > 0)
                                <h4 class="text-primary">References : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-danger">
                                            <tr>
                                                <th scope="col">Sl#</th>
                                                <th scope="col">Reference</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($ClientReferences as $key => $Reference)
                                                    <tr>
                                                        <th >{{$key+1}}</th>
                                                        <td>{{$Reference->reference->name}}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if ($Client->present_address)
                                <h4 class="text-primary">Address : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-info">
                                            <tr>
                                                <th scope="col">Present Address</th>
                                                <th scope="col">Permanent Address</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                    <tr>
                                                        <td>{{$Client->present_address}}</td>
                                                        <td>{{$Client->permanent_address	}}</td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @if (count($BankAccounts) > 0)
                                <h4 class="text-primary">Bank : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-info">
                                            <tr>
                                                <th scope="col">Bank Name</th>
                                                <th scope="col">Bank Branch</th>
                                                <th scope="col">Account Name</th>
                                                <th scope="col">Account No</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($BankAccounts as $key => $account)
                                                    <tr>
                                                        <td >{{$account->bank->bank_name}}</td>
                                                        <td >{{$account->branch_name}}</td>
                                                        <td>{{$account->name}}</td>
                                                        <td>{{$account->account_number}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('script')

@endpush


