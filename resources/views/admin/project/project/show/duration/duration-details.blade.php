@extends('layouts.dashboard.app')

@section('title', 'Project Link Details')

@push('css')
<style>
    .card{
        box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
   }
   table tr{
    border: 1px solid rgb(0, 0, 0);
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
                <a href="{{ route('admin.projects.index') }}">Project Duration</a>
            </li>
            <li class="breadcrumb-item">
                Details
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center text-success ">Project Duration</h3>
                    <table class="table table-bordered   table-hover  ">
                        <thead class="table-primary ">
                          <tr>
                            <th scope="col">Project</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td >{{$projectDuration->project->project_title}}</td>
                            <td >{{$projectDuration->start_date}}</td>
                            <td>{{$projectDuration->end_date}}</td>
                          </tr>
                        </tbody>
                    </table>
                    @if($modules)
                        <h3 class="text-center text-primary mt-4 ">Project Module Duration</h3>
                        <table class="table table-bordered   table-hover  ">
                            <thead class="table-success ">
                            <tr>
                                <th scope="col">Module Name</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $key => $module )
                                <tr>
                                    <td >{{$module}}</td>
                                    <td>{{$modulesStare_date[$key]}}</td>
                                    <td>{{$modulesEnd_date[$key]}}</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @endif
                    <h3 class="text-center text-danger mt-4 ">Total Duration</h3>
                    <table class="table table-bordered   table-hover  ">
                        <thead class="table-info ">
                          <tr>
                            <th scope="col">Estimate Day</th>
                            <th scope="col">Estimate Hour</th>
                            <th scope="col">Total Hour</th>
                          </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$projectDuration->total_day}}</td>
                                <td>{{$projectDuration->estimate_hour}}</td>
                                <td  width="30%"> @if ($projectDuration->adjustment_type) <p> <b> Adjustment Type :  </b> {{$projectDuration->adjustment_type == 1 ? 'Addition' : 'Substraction'}} </p>
                                         <b>Adjustment Hour : </b>  {{$projectDuration->adjustment_hour}}
                                       @endif
                                       <p> <b> Total Hour    : </b> {{$projectDuration->total_hour}}</p> </td>
                              </tr>
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>



    <div class="mb-5"></div>


@endsection

