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

    <form action="{{route('admin.salary.list.show')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-2">
                            <div class="col-sm-6 mb-2">
                                <div class="form-group">
                                    <label for="warehouse"><b>Warehouse</b><span class="text-danger">*</span></label>
                                    <select class="form-control warehouse " id="warehouse" name="warehouse" onchange="getEmp()" required>
                                        <option value=" " >Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse')
                                    <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="form-group">
                                    <label for="employee_id"><b>Employee</b><span class="text-danger">*</span></label>
                                    <select class="form-control employee_id " id="employee_id" name="employee_id" required>

                                    </select>
                                    @error('employee_id')
                                    <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label for="month_from"><b>Month From</b> <span class="text-danger">*</span> </label>
                                <input type="month" id="month_from" name="month_from" class="form-control"  onkeyup="check_data()" required>
                                @error('month_from')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label for="month_to"><b>Month To</b> <span class="text-danger">*</span> </label>
                                <input type="month" id="month_to" name="month_to" class="form-control"  onkeyup="check_data()" required>
                                @error('month_to')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Show Salary</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        function getEmp(){
            var url = '{{ route('admin.employee.salary.report.by.warehouse', ':id') }}';
            var warehouseId = $('#warehouse').val();
            $.ajax({
                type: "GET",
                url: url.replace(':id',warehouseId),
                success: function (employees) {
                    $('#employee_id').empty();
                    $('#employee_id').append('<option value="0" >All</option>')
                    $.each(employees, function(key, value){
                        $('#employee_id').append('<option value="'+value.employee.id+'">'+value.employee.name+'</option>')
                    });
                }
            })
        }
    </script>
@endpush


