@extends('layouts.dashboard.app')

@section('title', 'employee salary create')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .text-danger strong {
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title {
            width: 50%;
            font-weight: 600;
            display: none;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }

        @media (min-width: 200px ) and (max-width: 1130px ) {
            .responsive-table {
                width: 100%;
            }
            .responsive-table th {
                display: none;
            }
            .responsive-table .responsive-table-tr {
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            .responsive-table tr td {
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title {
                display: block;
            }
        }
@media print {
    .emrow{
        width:25%;
    }


}
    </style>

@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
    </script>
@endpush
@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.salaryReport.index')}}">Expense </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.salaryReport.create')}}">Create</a>
            </li>
        </ol>
        <a href="{{route('admin.salaryReport.index')}}" class="btn btn-sm btn-dark">Back to list</a>
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

    <form action="{{route('admin.salaryGenerate.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="month"><b>Select Month</b> <span class="text-danger">*</span> </label>
                                <input type="month" id="month" name="month" class="form-control"  onkeyup="check_data()">
                                @error('month')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="form-group">
                                    <label for="warehouse"><b>Warehouse</b><span class="text-danger">*</span></label>
                                    <select class="form-control warehouse " id="warehouse" name="warehouse" required onchange="getEmployees()">

                                        @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                                    <option value="" selected>--Select Warehouse--</option>
                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                    @endforeach
                                        @elseif($user_role->name == 'Employee')
                                                {{$auth = Auth::user()}}
                                                {{$emp_ware=\App\Models\Employee\Employee::where('id',$auth->user_id)->with('ware_house')->first()}}
                                                <option value="{{$emp_ware->warehouse}} readonly">{{$emp_ware->ware_house->name}}</option>
                                        @endif
                                    </select>
                                    @error('warehouse')
                                        <span class="text-danger" role="alert">
                                              <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                       <div style="display: none;" id="emrow_grid">
                        <div   class="row mt-4">
                            <div class="col-md-3 emrow">
                                <div class="form-group">
                                <label for="employee_id"><b>Employee Name</b></label>
                                </div>
                            </div>
                            <div class="col-md-3 emrow">
                                <div class="form-group">
                                    <label for="amount"><b>Gross Salary</b></label>
                                </div>
                            </div>
                            <div class="col-md-3 emrow">
                                <div class="form-group">
                                    <label for="amount"><b>Month</b></label>
                                </div>
                            </div>
                            <div class="col-md-3 emrow">
                                <div class="form-group">
                                    <label for="amount"><b>Generating Date</b></label>
                                </div>
                            </div>
                            <div class="mt-4" id="employee_rows">

                            </div>
                        </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Generate Monthly Salary</button>
                                <button onclick="printEmployee_rows()" class="btn btn-sm btn-info mr-2">Print</button>
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
        function getEmployees(){
            var url = '{{ route('admin.employee.by.warehouse', ':id') }}';
            var warehouseId = $('#warehouse').val();
            console.log(warehouseId);
            $.ajax({
                type: "GET",
                url: url.replace(':id',warehouseId),
                success: function (data) {
                    var mnth = $('#month').val()
                    $('#emrow_grid').show();
                    $('#employee_rows').empty();
                    $.each(data, function(key, value){
                        $('#employee_rows').append(`<div class="row">
                                                               <div class="col-md-3 emrow">
                                                                    <input type="text" id="employee_id" class="form-control" value="${value.name}" readonly>
                                                                    <input type="hidden" class="form-control" name="employee[]" value="${value.id}" readonly>
                                                                </div>
                                                                <div class="col-md-3 emrow">
                                                                    <div class="form-group">
                                                                        <input class="form-control amount" id="amount" type="number"  name="amount[]" value="${value.gross_salary}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 emrow">
                                                                    <div class="form-group">
                                                                        <input class="form-control" id="month" type="month" name="" value="${mnth}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 emrow">
                                                                    <div class="form-group">
                                                                        <input class="form-control" id="date" type="date"  name="date" value="{{ now()->format('Y-m-d') }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                                </div>
                                                            </div>`);
                    });
                }
            })
        }
    </script>

    <script>
        function printEmployee_rows() {
            const printContents = document.getElementById('emrow_grid').innerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endpush

