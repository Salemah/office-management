@extends('layouts.dashboard.app')

@section('title', 'employee salary create')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
        </ol>

    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <p class="m-0">Employee Salary Report</p>
            <a href="{{route('admin.salary.list')}}" class="btn btn-sm btn-info">Back</a>
        </div>
        <div class="card-body">
            <table id="table" class="table table-bordered data-table" style="width: 100%">
                <thead>
                <tr style="text-align: center">
                    <th scope="col">Employee Name</th>
                    <th scope="col">Month</th>
                    <th scope="col">Gross Salary</th>
                    <th scope="col">Paying Date</th>
                </tr>
                </thead>

                <tbody style="text-align: center">
                @foreach($salaries as $key=> $salary)
                    <tr>
                        <td>{{ $salary->employee->name }}</td>
                        <td>{{ $salary->month }}</td>
                        <td>{{$salary->employee->gross_salary}}</td>
                        <td>{{$salary->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                 <a href="javascript:window.print()" class="btn btn-sm btn-primary" style="float:right" >Print</a>
            </div>
        </div>
    </div>
@endsection


