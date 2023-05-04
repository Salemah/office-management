@extends('layouts.dashboard.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                {{-- <span>{{__('message.title')}}</span> --}}
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-primary">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">@if (isset($employee))
                                    {{count($employee)}}
                                    @else
                                    0
                                @endif
                                <span class="fs-6 fw-normal"></span>
                        </div>
                        <div>Total Employee</div>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                    <canvas class="chart" id="card-chart1" height="70"></canvas>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-info">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">
                            @php
                                 $clients = \Illuminate\Support\Facades\DB::table('clients')->get();
                            @endphp
                            @if(isset($clients)) {{count($clients)}} @else 0 @endif
                            <span class="fs-6 fw-normal">
                           </span>
                        </div>
                        <div>Total Client</div>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                    <canvas class="chart" id="card-chart2" height="70"></canvas>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-warning">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">
                            @php
                                 $reminder = \Illuminate\Support\Facades\DB::table('client_reminders')->get();
                            @endphp
                                @if(isset($reminder))
                                    {{count($reminder)}}
                                @else
                                 0
                                @endif
                            <span class="fs-6 fw-normal"></span>
                        </div>
                        <div>Reminder</div>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3" style="height:70px;">
                    <canvas class="chart" id="card-chart3" height="70"></canvas>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-danger">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">
                            @php
                                 $comment = \Illuminate\Support\Facades\DB::table('client_comments')->get();
                            @endphp
                            @if(isset($comment))
                                {{count($comment)}}
                            @else
                                0
                            @endif

                        </div>
                        <div>Comments</div>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                    <canvas class="chart" id="card-chart4" height="70"></canvas>
                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
    <!-- /.row-->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">Traffic</h4>
                    <div class="small text-medium-emphasis">January - July 2022</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                    <div class="btn-group btn-group-toggle mx-3" data-coreui-toggle="buttons">
                        <input class="btn-check" id="option1" type="radio" name="options" autocomplete="off">
                        <label class="btn btn-outline-secondary"> Day</label>
                        <input class="btn-check" id="option2" type="radio" name="options" autocomplete="off" checked="">
                        <label class="btn btn-outline-secondary active"> Month</label>
                        <input class="btn-check" id="option3" type="radio" name="options" autocomplete="off">
                        <label class="btn btn-outline-secondary"> Year</label>
                    </div>
                    <button class="btn btn-primary" type="button">
                        <svg class="icon">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
                <canvas class="chart" id="main-chart" height="300"></canvas>
            </div>
        </div>
        <div class="card-footer">
            <div class="row row-cols-1 row-cols-md-5 text-center">
                <div class="col mb-sm-2 mb-0">
                    <div class="text-medium-emphasis">Visits</div>
                    <div class="fw-semibold">29.703 Users (40%)</div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col mb-sm-2 mb-0">
                    <div class="text-medium-emphasis">Unique</div>
                    <div class="fw-semibold">24.093 Users (20%)</div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col mb-sm-2 mb-0">
                    <div class="text-medium-emphasis">Pageviews</div>
                    <div class="fw-semibold">78.706 Views (60%)</div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col mb-sm-2 mb-0">
                    <div class="text-medium-emphasis">New Users</div>
                    <div class="fw-semibold">22.123 Users (80%)</div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col mb-sm-2 mb-0">
                    <div class="text-medium-emphasis">Bounce Rate</div>
                    <div class="fw-semibold">40.15%</div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card.mb-4-->
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4" style="--cui-card-cap-bg: #3b5998">
                <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <svg class="icon icon-3xl text-white my-4">
                        <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-facebook-f"></use>
                    </svg>
                    <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                        <canvas id="social-box-chart-1" height="90"></canvas>
                    </div>
                </div>
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="fs-5 fw-semibold">89k</div>
                        <div class="text-uppercase text-medium-emphasis small">friends</div>
                    </div>
                    <div class="vr"></div>
                    <div class="col">
                        <div class="fs-5 fw-semibold">459</div>
                        <div class="text-uppercase text-medium-emphasis small">feeds</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4" style="--cui-card-cap-bg: #00aced">
                <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <svg class="icon icon-3xl text-white my-4">
                        <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-twitter"></use>
                    </svg>
                    <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                        <canvas id="social-box-chart-2" height="90"></canvas>
                    </div>
                </div>
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="fs-5 fw-semibold">973k</div>
                        <div class="text-uppercase text-medium-emphasis small">followers</div>
                    </div>
                    <div class="vr"></div>
                    <div class="col">
                        <div class="fs-5 fw-semibold">1.792</div>
                        <div class="text-uppercase text-medium-emphasis small">tweets</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-4">
            <div class="card mb-4" style="--cui-card-cap-bg: #4875b4">
                <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <svg class="icon icon-3xl text-white my-4">
                        <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-linkedin"></use>
                    </svg>
                    <div class="chart-wrapper position-absolute top-0 start-0 w-100 h-100">
                        <canvas id="social-box-chart-3" height="90"></canvas>
                    </div>
                </div>
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="fs-5 fw-semibold">500+</div>
                        <div class="text-uppercase text-medium-emphasis small">contacts</div>
                    </div>
                    <div class="vr"></div>
                    <div class="col">
                        <div class="fs-5 fw-semibold">292</div>
                        <div class="text-uppercase text-medium-emphasis small">feeds</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection
