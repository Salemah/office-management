@extends('layouts.dashboard.app')

@section('title', 'Notice Show')

@push('css')
    <style>
        .text-right{
            text-align: end;
        }

        .invoice-container {
            padding: 1rem;
        }
        .invoice-container .invoice-header .invoice-type{
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
                <span>HRM</span>
            </li>
            <li class="breadcrumb-item">
                <span>Notice</span>
            </li>
            <li class="breadcrumb-item">
                <span>Show</span>
            </li>
        </ol>
        <a href="{{ route('admin.hrm.notice.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="invoice-container">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Notice Title</th>
                                            <th scope="col">Notice Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>{{$notice->notice_title}}</th>
                                            <td>{{$notice->notice_date	}}</td>
                                        </tr>
                                    </tbody>
                                    </table>
                            </div>
                        </div>

                        {{-- Department --}}
                        @if ($departmentNames)
                            <h4 class="text-primary">Department : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Department Names</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>{{ $departmentNames }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Employee --}}
                        @if ($employeeNames)
                            <h4 class="text-primary">Notice To : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Employee Names</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>{{ $employeeNames }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Meeting --}}
                        @if($meetings)
                        <h4 class="text-primary">Meetings : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Meeting Date</th>
                                            <th scope="col">Meeting Time</th>
                                            <th scope="col">Meeting Link</th>
                                            <th scope="col">Meeting Purpose</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($meetings['date'] as $key => $meeting)
                                                <tr>
                                                    <th >{{ $meetings['date'][$key] }}</th>
                                                    <th >{{ $meetings['time'][$key] }}</th>
                                                    <th >{{ $meetings['purpose'][$key] }}</th>
                                                    <th >{{ $meetings['link'][$key] }}</th>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Document --}}
                        @if ($document_file)
                            <h4 class="text-primary">Documents : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Document Title</th>
                                            <th scope="col">Document File</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($document_file as $key => $document)
                                                <tr>
                                                    <th >{{$documents_title[$key]}}</th>
                                                    <td><button type="button" class="btn btn-outline-primary " ><a href="{{ asset('img/hrm/notice/documents/'.$document) }}" style="text-decoration: none;color:black"><i class='bx bx-down-arrow-alt'></i>Download</a></button></td>
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

