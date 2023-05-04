@extends('layouts.dashboard.app')

@section('title', 'Balance-Sheet')

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/datatables.min.css') }}">
    <style>
        .dataTables_wrapper .dataTables_processing {
    background: rgba(255, 255, 255, 0);
    border: none !important;
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
                <a href="{{ route('admin.account.cash.balance.sheet.index') }}">Account Statement</a>
            </li>
        </ol>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
            <div class="container mt-3">
                <div class="sub2-row16">
                    <div class="l26 md-heading">
                        <form action="{{route('admin.account.bank.account.monthly.balance.sheet.data.print')}}" method="POST">
                            @csrf
                            <input type="hidden" name="year" value="" id="year-select">
                            <input type="hidden" name="month" value="" id="month-select">
                            <input type="hidden" name="start_date" value="" id="start-date-select">
                            <input type="hidden" name="end_date" value="" id="end-date-select">
                            <input type="hidden" name="warehouse_id" value="" id="warehouse_id_select">
                            <button type="submit" class="print btn btn-sm btn-info">Print</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row ">
                    <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                        <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                        @php
                            $auth = Auth::user();
                            $user_role = $auth->roles->first();
                        @endphp
                        @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                            <select name="warehouse_id"
                                id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror" onchange="search()">
                                <option value="" selected>--Select warehouse_id--</option>
                                @forelse ($warehouses as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @empty
                                    <option value="">No Warehouse</option>
                                @endforelse
                            </select>
                        @else
                            <input type="text" name="warehouse_id_name" class="form-control"
                                placeholder="Enter Invoice No" value="{{ $mystore->name }}" readonly>
                            <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control"
                                placeholder="Enter Invoice No" value="{{ $mystore->id }}">
                        @endif
                        @error('warehouse_id')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-3 col-12">
                        <label for="year"><b>Year</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }} has-feedback">
                            <select name="year" id="year" class="form-control"  >
                                <option value="" selected>-- Select Year --</option>
                                @foreach ($year as $key=>$yearr )
                                    <option value="{{ $yearr }}">{{ $yearr  }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('year'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('year') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <label for="month_id"><b>Month</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('month_id') ? ' has-error' : '' }} has-feedback">
                            <select name="month_id" id="month_id" class="form-control" onchange="search()" >
                                <option value="" selected>-- Select Month --</option>
                                @foreach ($month as $key=>$bankAccount)
                                    <option value="{{ $key }}">{{ $bankAccount }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('month_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('month_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 col-12">
                        <label for="date_form"><b>Form</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="start_date" id="start_date" class="form-control"value="{{ old('start_date') }}" placeholder="d/m/yy">
                            @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <label for="date_to"><b>To</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}"  onchange="search()">
                            @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    {{-- <th width="1%">SL#</th> --}}
                                    <th width="10%"> Date</th>
                                    <th width="74%"> Details</th>
                                    <th width="10%" > Debit</th>
                                    <th width="10%"> Credit</th>
                                    <th width="10%"> Balance</th>
                                </tr>
                                <tr id="previous_tr"  style="display: none" >
                                    <td class="text-center "></td>
                                    <td  class="text-start ">Closing Balance</td>
                                    <td  class="text-end " id="debBalance"> </td>
                                    <td  class="text-end " id="creBalance"> </td>
                                    <td id="prevBalance" class=" text-end" ></td>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> --}}
    <script src="{{ asset('backend/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            search();
        });
        function search() {

            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var month_id = $("#month_id").val();
            var year = $("#year").val();
            var wareHouse = $("#warehouse_id").val();
            $("#year-select").val(year);
            $("#month-select").val(month_id );
            $("#start-date-select").val(start_date);
            $("#end-date-select").val(end_date);
            $("#warehouse_id_select").val(wareHouse);
            $("#debBalance").val(null);
            $("#creBalance").val(null);


            var x = 1;
            // if (start_date !== '' && end_date !== '' && month_id !== '') {
                var searchable = [];
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    }
                });
                var dTable = $('#table').DataTable({
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    processing: true,
                    serverSide: true,
                    "bDestroy": true,
                    "language": {
                     processing: '<div class="spinner-border text-primary mt-5" role="status" id="spinner-pre"><span class="visually-hidden">Loading...</span></div>'},
                    "bDestroy": true,
                    ajax: {
                        url: "{{route('admin.account.bank.account.monthly.balance.sheet.data')}}",
                        type: "POST",
                        data: {
                            'start_date': start_date,
                            'end_date': end_date,
                            'month_id': month_id,
                            'year': year,
                            'warehouse_id': $("#warehouse_id").val(),
                        },
                    },
                    columns: [
                        // { data: "DT_RowIndex",name: "DT_RowIndex",orderable: false,searchable: false},
                        {data: 'transaction_date', name:'transaction_date'},
                        // { data: 'purpose',name: 'purpose',orderable: true,searchable: true},
                        { data: 'details',name: 'details',orderable: true,searchable: true},
                        { data: 'debit',name: 'debit',orderable: true,searchable: true,class: "text-end"},
                        { data: 'credit',name: 'credit',orderable: true,searchable: true,class: "text-end"},
                        { data: 'balance', name: 'balance',orderable: true,searchable: true,class: "text-end"},
                    ],
                    order: [[0, 'asc']],
                    initComplete: function (data) {
                        var prevBalance = data.json.prevBalance;
                        $('#previous_tr').show()
                        document.getElementById('prevBalance').innerHTML = prevBalance;
                        if(prevBalance > 0){
                            document.getElementById('creBalance').innerHTML =prevBalance;
                            document.getElementById('debBalance').innerHTML = 0.00;
                        }
                        else if(prevBalance <= 0){
                            document.getElementById('debBalance').innerHTML = prevBalance;
                            document.getElementById('creBalance').innerHTML = 0.00;

                        }
                    },

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'colvis',
                                className: 'btn-sm btn-warning',
                                title: 'Balance Statement',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Balance Statement',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Balance Statement',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-dark',
                                title: 'Balance Statement',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Balance Statement',
                                pageSize: 'A2',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn-sm btn-danger',
                                title: 'Balance Statement',
                                pageSize: 'A2',
                                header: true,
                                footer: true,
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: ':visible',
                                    stripHtml: false
                                }
                            },

                        ],
                            columnDefs: [{
                                // targets: 0,
                                orderable: false,
                                visible: false
                            }],

                });
            }
            // else {
            //     alert('Enter All Value')
            // }
        //}

    </script>
@endpush
