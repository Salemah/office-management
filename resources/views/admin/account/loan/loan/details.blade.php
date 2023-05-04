@extends('layouts.dashboard.app')

@section('title', 'Loan Details')

@push('css')
    <style>
        .card-box {
            padding: 20px;
            border-radius: 3px;
            margin-bottom: 30px;
            background-color: #fff;
            height:700px;
        }

        .thumb-sm {
            height: 36px;
            width: 36px;
        }

        .task-detail .task-dates li {
            width: 50%;
            float: left
        }

        .task-detail .task-tags .bootstrap-tagsinput {
            padding: 0;
            border: none
        }

        .task-detail .assign-team a {
            display: inline-block;
            margin: 5px 5px 5px 0
        }

        .task-detail .files-list .file-box {
            display: inline-block;
            vertical-align: middle;
            width: 80px;
            padding: 2px;
            border-radius: 3px;
            -moz-border-radius: 3px;
            background-clip: padding-box
        }

        .task-detail .files-list .file-box img {
            line-height: 70px
        }

        .task-detail .files-list .file-box p {
            width: 100%;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .add-new-plus {
            height: 32px;
            text-align: center;
            width: 32px;
            display: block;
            line-height: 32px;
            color: #98a6ad;
            font-weight: 700;
            background-color: #e3eaef;
            border-radius: 50%
        }

        .project-sort-item .form-group {
            margin-right: 30px
        }

        .project-sort-item .form-group:last-of-type {
            margin-right: 0
        }

        .project-box {
            position: relative
        }

        .project-box .badge {
            position: absolute;
            right: 20px
        }

        .project-box h4 {
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            width: 100%;
            overflow: hidden
        }

        .project-box ul li {
            padding-right: 30px
        }

        .project-box .project-members a {
            margin: 0 0 10px -12px;
            display: inline-block;
            border: 3px solid #fff;
            border-radius: 50%;
            -webkit-box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02);
            box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02)
        }

        .project-box .project-members a:first-of-type {
            margin-left: 0
        }

        .company-card .company-logo {
            float: left;
            height: 60px;
            width: 60px;
            border-radius: 3px
        }

        .company-card .company-detail {
            margin: 0 0 50px 75px
        }

        .text-muted {
            color: #98a6ad !important;
        }

        p {
            line-height: 1.6;
            font-size: 14px;
        }

        .bootstrap-tagsinput .label-info {
            background-color: #02c0ce;
            display: inline-block;
            padding: 4px 8px;
            font-size: 13px;
            margin: 3px 1px;
            border-radius: 3px;
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
                <a href="admin.loan.list.status">Loan Details</a>
            </li>
        </ol>
        <a href="{{ route('admin.loan.list.status') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-box task-detail">
                        <div class="media mt-0 m-b-30"><img class="d-flex mr-3 rounded-circle" alt="64x64"
                                src="https://bootdey.com/img/Content/avatar/avatar2.png" style="width: 48px; height: 48px;">
                            <div class="media-body">
                                <h5 class="media-heading mb-0 mt-0">{{$loan->author->name}}</h5><span
                                    class="badge badge-danger">Urgent</span>
                            </div>
                        </div>
                        <h4 class="m-b-20">{{$loan->loan_title}}</h4>
                        <p class="text-muted">{!!$transaction->description!!}</p>

                        <ul class="list-inline task-dates m-b-0 mt-5">
                            <li>
                                <h5 class="m-b-5">Loan Date</h5>
                                <p>{{Carbon\Carbon::parse($loan->loan_date)->format('M d Y')}} <small class="text-muted">{{Carbon\Carbon::parse($loan->created_at)->format('h:i a')}}</small></p>
                            </li>
                            <li class="mb-5">
                                <h5 class="m-b-5">Duration</h5>
                                <h6>{{$loan->duration}} Years</h6>

                            </li>
                        </ul>
                        <ul class="list-inline task-dates m-b-0 mt-5">
                            <li>
                                <h5 class="m-b-5">Loan Amount</h5>
                                <p><strong>{{number_format($loan->loan_amount,2)}} </strong></p>
                            </li>
                            <li>
                                <h5 class="m-b-5">Loan Type</h5>
                                <p><strong> @if ($loan->loan_type == 2)<span class="badge bg-primary">Giving</span> @else <span class="badge bg-warning text-dark">Taking</span> @endif   </strong></p>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                        <div class="task-tags mt-4">
                            <h5 class="">Transaction Details</h5>
                            <div class="bootstrap-tagsinput"><span class="tag label label-info">{{$loan->transaction_way == 1 ? 'Cash' : 'Bank'}}<span
                                        data-role="remove"></span></span> <span class="tag label label-info">{{$transaction->bankAccount->name}}<span
                                        data-role="remove"></span></span> <span class="tag label label-info">{{$transaction->bankAccount->account_number}}<span
                                        data-role="remove"></span></span>

                            </div>

                        </div>
                        <div class="assign-team mt-4">
                            <h5 class="m-b-5">Interest Details</h5>
                            <p>Interest Rate : {{$loan->interest_rate}}</p>
                       

                        </div>
                        
                        <div class="assign-team mt-4">
                        <ul class="list-inline task-dates ">
                            <li>
                                <h5 class="m-b-5">Return Amount</h5>
                                <p><strong>{{number_format($returnAmount,2)}} </strong></p>
                            </li>
                            <li>
                                <h5 class="m-b-5">Due Amount</h5>
                                <p><strong> {{number_format($due,2)}}</strong></p>
                            </li>
                        </ul>
                       

                        </div>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-lg-4">
                    <div class="card-box">
                        <h4 class="header-title m-b-30">Return Details</h4>
                        <div>
                            @foreach($returnList as $key=>$return)     
                            <div class="media my-3">
                                <div class="media-body">
                                    <h5 class="mt-0">{{$return->transaction_title}}</h5>
                                    <p>Return Date : {{$return->transaction_date}}</p>
                                    <p class="font-13 text-dark mb-0">Loan Author : {{$loan->author->name}}
                                  
                                    {!!$return->description!!}</p>
                                    <p class="my-1">Return Amount : <strong> {{number_format ($return->amount,2)}}</strong></p>
                                    <p>Transaction Details : @if($return->transaction_account_type ==1)  <span class="badge bg-primary">Cash</span> @else <span class="badge bg-primary">Bank</span> @endif <span class="badge bg-primary"> {{$return->bankAccount->name}}</span><span class="badge bg-primary"> {{$return->bankAccount->account_number}}</span>
                                </p>
                                </div>
                            </div>
                        
                           @endforeach
                           
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- container -->
    </div>

@endsection

@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script></script>
@endpush
