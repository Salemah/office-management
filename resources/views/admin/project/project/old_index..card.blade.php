@extends('layouts.dashboard.app')

@section('title', 'Project')

@push('css')
    <style>
        /* .card{
            border-radius: 6px;
            border: none;
        } */
        .ongoing-column{
            background: rgba(209, 205, 205, 0.349);
            border-radius: 6px;
            padding: 10px;
        }
        .ongoing-header{
            border-radius: 8px 8px 0 0;
            padding: 4px;
            background: #008cea;
            color: white;
            text-align: center;
        }

        #ongoing-project{
            color: #000000;
            text-align: center;
        }
        .ongoing-card{
            border-radius: 8px;
            border: none;
            margin-bottom: 12px;
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
                <a href="#">Project</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.projects.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card ">
            <div class="card-body">
                    <div class="row align-items-start">
                      <div class="col-12 col-sm-12 col-md-3 ">
                        <div class="ongoing-column">
                            <h5 id="ongoing-project">Not Started</h5>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div >
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 ">
                        <div class="ongoing-column">
                            <h5 id="ongoing-project">On Going</h5>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div >
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 ">
                        <div class="ongoing-column">
                            <h5 id="ongoing-project">Complete</h5>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div >
                      </div>
                      <div class="col-12 col-sm-12 col-md-3 ">
                        <div class="ongoing-column">
                            <h5 id="ongoing-project">Cancel</h5>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card ongoing-card" >
                                    <div class="ongoing-header">
                                        Project Priority
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Project title</h6>
                                        <p class="card-text">Project Category</p>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="row justify-content-start">
                                                        <div class="col-6">
                                                        Start Date
                                                        </div>
                                                        <div class="col-6">
                                                        End Date
                                                        </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="progress my-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="btn-group-vertical">
                                                    <a type="button" class="btn btn-sm  btn-primary my-1"><i  class="bx bxs-edit "></i></a>
                                                    <a type="button" class="btn btn-sm btn-danger"><i class="bx bxs-trash "></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div >
                      </div>

                    </div>
                </div>
            </div>
    </div>

@endsection

@push('script')
    <script>

    </script>
@endpush
