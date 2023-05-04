@extends('layouts.dashboard.app')

@section('title', 'Revenue Report')

@push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            padding: 6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
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
                <a href="#">Revenue Report</a>
            </li>
        </ol>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <h6>Total Revenue : <b>{{ number_format($totalRevenue, 2) ?? '--'}}</b></h6>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                        <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>

                        @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                            <select name="warehouse_id" id="warehouse_id"class="form-select mt-1 @error('warehouse_id') is-invalid @enderror">
                                <option value="" selected >--Select Warehouse--</option>
                                @forelse ($warehouses as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @empty
                                    <option value="">No Warehouse</option>
                                @endforelse
                            </select>

                        @else
                        <input type="text" name="warehouse_id_name" id="warehouse_id_name"  class="form-control mt-1" placeholder="Enter Invoice No" value="{{$warehouses->name}}" readonly >
                        <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control mt-1" placeholder="Enter Invoice No" value="{{$warehouses->id}}" >
                        @endif
                        @error('warehouse_id')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="start_date"><b>Start Date</b>  <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                class="form-control mt-1 @error('start_date') is-invalid @enderror"
                                placeholder="Enter start date" required>

                            @error('start_date')
                                <span class="text-danger" role="alert">
                                    <p>{{ $message }}</p>
                                </span>
                            @enderror

                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="end_date"><b>End Date</b>  <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                class="form-control mt-1 @error('end_date') is-invalid @enderror"
                                placeholder="Enter end date" required>

                            @error('end_date')
                                <span class="text-danger" role="alert">
                                    <p>{{ $message }}</p>
                                </span>
                            @enderror

                        </div>
                    </div>

                </div>
                <div class="form-group mt-3">
                    <button title="Submit Button" type="submit" id="search" class="btn btn-sm btn-primary float-left search"> <i class="bx bxs-eye"></i>Search</button>
                </div>

            </div>
        </div>

        <!-- Main Content -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="table" class="table table-hover table-bordered ">
                                <thead>
                                    <tr class="align-middle table">
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>

@endsection

@push('script')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

$('#start_date, #end_date').on('change',function(event){
    event.preventDefault();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();

    var currentDate = new Date().toISOString().slice(0, 10);

    if(start_date > currentDate){
        swal({
            title: 'Error!!!',
            text: "Start date must be less than current date",
            dangerMode: true,
        });
        $('#start_date').val('null');
    }
    if(end_date > currentDate) {
        swal({
            title: 'Error!!!',
            text: "End date must be less than or equal current date",
            dangerMode: true,
        });
        $('#end_date').val('null');
    }
});

    $('#search').on('click',function(event){
        event.preventDefault();

        var warehouse_id = $("#warehouse_id").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();

        if (warehouse_id !== '') {

        var table =  $('#table').DataTable({
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            serverSide: true,
            "bDestroy": true,

            ajax: {
                url: "{{route('admin.reports.revenue.report')}}",
                type: "get",
                data:{
                    'warehouse_id':warehouse_id,
                    'start_date':start_date,
                    'end_date':end_date,
                },
            },
            columns: [
            {data: "DT_RowIndex",name: "DT_RowIndex", orderable: false,  searchable: false},
            {data: 'date',name: 'date', orderable: true,   searchable: true},
            {data: 'transaction_title', name: 'transaction_title', orderable: true,searchable: true},
            {data: 'total',name: 'total', orderable: true,   searchable: true},
        ],
        })
    } else if(start_date !== '' && end_date !== '') {
        swal({
            title: 'Error!!!',
            text: "Select Warehouse",
            dangerMode: true,
        });
    }else{
        swal({
            title: 'Error!!!',
            text: "Enter All Data",
            dangerMode: true,
        });
    }
});

</script>
@endpush
