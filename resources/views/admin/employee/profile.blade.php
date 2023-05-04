@extends('layouts.dashboard.app')

@section('title', 'Employee')

@push('css')
    <style>
        .text-right {
            text-align: end;
        }

        .invoice-container {
            padding: 1rem;
        }

        .invoice-container .invoice-header .invoice-type {
            width: 158px;
            text-align: center;
            margin: auto;
            border: 1px solid gray;
            display: block;
            padding: 2px 4px;
            text-decoration: none;
            margin-top: 8px;
        }

        .invoice-container .invoice-header .invoice-logo {
            margin: 0.8rem 0 0 0;
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 700;
            color: #2e323c;
        }

        .invoice-container .invoice-header .invoice-logo img {
            max-width: 130px;
        }

        .invoice-container .invoice-header address {
            font-size: 0.8rem;
            color: #9fa8b9;
            margin: 0;
        }

        .invoice-container .invoice-details {
            margin: 1rem 0 0 0;
            padding: 1rem;
            line-height: 180%;
            background: #f5f6fa;
        }

        .invoice-container .invoice-details .invoice-num {
            text-align: right;
            font-size: 0.8rem;
        }

        .invoice-container .invoice-body {
            padding: 1rem 0 0 0;
        }

        .invoice-container .invoice-footer {
            text-align: center;
            font-size: 0.7rem;
            margin: 5px 0 0 0;
        }

        .invoice-status {
            text-align: center;
            padding: 1rem;
            background: #ffffff;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .invoice-status h2.status {
            margin: 0 0 0.8rem 0;
        }

        .invoice-status h5.status-title {
            margin: 0 0 0.8rem 0;
            color: #9fa8b9;
        }

        .invoice-status p.status-type {
            margin: 0.5rem 0 0 0;
            padding: 0;
            line-height: 150%;
        }

        .invoice-status i {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            display: inline-block;
            padding: 1rem;
            background: #f5f6fa;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
        }

        .invoice-status .badge {
            text-transform: uppercase;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 1rem;
            }
        }


        .custom-table {
            border: 1px solid #e0e3ec;
        }

        .custom-table thead {
            background: #007ae1;
        }

        .custom-table thead th {
            border: 0;
            color: #ffffff;
        }

        .custom-table > tbody tr:hover {
            background: #fafafa;
        }

        .custom-table > tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .custom-table > tbody td {
            border: 1px solid #e6e9f0;
        }


        .card {
            background: #ffffff;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }

        .text-success {
            color: #00bb42 !important;
        }

        .text-muted {
            color: #9fa8b9 !important;
        }

        .custom-actions-btns {
            margin: auto;
            display: flex;
            justify-content: flex-end;
        }

        .custom-actions-btns .btn {
            margin: .3rem 0 .3rem .3rem;
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
                <span>Employee</span>
            </li>
        </ol>
        <a href="{{ route('admin.employee.show',$employee->id) }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <h3 class="text-primary text-center mt-2">Employee Details</h3>
                <div class="card-body p-0">
                    <div class="invoice-container">
                        <div class="ms-2 my-4">
                            <a href="{{route('admin.employee-profile.pdf')}}" class="btn btn-sm btn-success text-white" ><i style="font-size: 30px" class='pdf bx bxs-file-pdf'></i></a>
                            <a href="{{route('admin.crm.import.export')}}" class="btn btn-sm btn-primary text-white"><i style="font-size: 30px" class='bx bx-printer'></i></a>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead class="table-success">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Primary Phone</th>
                                        <th scope="col">Secondary Phone</th>
                                        <th scope="col">Email</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>{{$employee->name}}</th>
                                        <td>{{$employee->phone_primary}}</td>
                                        <td>{{$employee->phone_secondary}}</td>
                                        <td>{{$employee->email}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead class="table-success">
                                    <tr>
                                        <th scope="col">Description</th>
                                        <th scope="col">Details</th>
                                        <th scope="col">Designation</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{!! $employee->description !!}</td>
                                        <td>{{$employee->departments->name}}</td>
                                        <td>{{$employee->designations->name}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($employeeQualifications)
                            <h4 class="text-primary">Qualifications : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-warning">
                                        <tr>
                                            <th scope="col">Institute Name</th>
                                            <th scope="col">Degree</th>
                                            <th scope="col">Year</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($employeeQualifications as $qualification)
                                            <tr>
                                                <td>{{$qualification->institute_name}}</td>
                                                <td>{{$qualification->degree}}</td>
                                                <td>{{$qualification->passing_year}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($employeeCertifications)
                            <h4 class="text-primary">Certification : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-danger">
                                        <tr>
                                            <th scope="col">Organization Name</th>
                                            <th scope="col">Certificate</th>
                                            <th scope="col">Duration</th>
                                            <th scope="col">Certificate Year</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($employeeCertifications as $certificate)
                                            <tr>
                                                <td>{{$certificate->organization_name}}</td>
                                                <td>{{$certificate->certificate}}</td>
                                                <td>{{$certificate->duration}}</td>
                                                <td>{{$certificate->certificate_year}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($employeeWorkExperiences)
                            <h4 class="text-primary">Work Experience : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Organization Name</th>
                                            <th scope="col">Designation</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($employeeWorkExperiences as $Experience)
                                            <tr>
                                                <td>{{$Experience->organization_name}}</td>
                                                <td>{{$Experience->designation}}</td>
                                                <td>{{$Experience->start_date}}</td>
                                                <td>{{$Experience->start_date}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($employeeIdentity)
                            <h4 class="text-primary">Identity Details : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-success">
                                        <tr>
                                            <th scope="col">Identity Type</th>
                                            <th scope="col">Identity No</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($employeeIdentity as $employeeIdentity)
                                            <tr>
                                                <td>{{$employeeIdentity->identity->name}}</td>
                                                <td>{{$employeeIdentity->id_no	}}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($documents)
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
                                                <th>{{$document->document_name}}</th>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary "><a
                                                            href="{{ asset('/img/employee/documents/'.$document->document) }}"
                                                            style="text-decoration: none;color:black"><i
                                                                class='bx bx-down-arrow-alt'></i>Download</a></button>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($employeeReferences)
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
                                        @foreach ($employeeReferences as $key => $Reference)
                                            <tr>
                                                <th>{{$key+1}}</th>
                                                <td>{{$Reference->reference->name}}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($employee->present_address)
                            <h4 class="text-primary">Address : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-info">
                                        <tr>
                                            <th scope="col">Present Address</th>
                                            <th scope="col">Permanent Address</th>
                                            <th scope="col">Permanent Address</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>{{$employee->present_address}}</th>
                                            <td>{{$employee->permanent_address	}}</td>
                                            <td>{{$employee->zip }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($bankAccounts)
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
                                        @foreach ($bankAccounts as $key => $account)
                                            <tr>
                                                <td>{{$account->bank->bank_name}}</td>
                                                <td>{{$account->branch_name}}</td>
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


