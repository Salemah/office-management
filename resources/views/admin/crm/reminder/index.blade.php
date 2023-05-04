@extends('layouts.dashboard.app')

@section('title', 'Reminder')

@push('css')

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <style>
        #reminder_table_all_filter,
        #reminder_table_all_paginate {
            float: right;
        }

        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }

        .table-responsive {
            overflow-x: hidden !important;
        }

        .search-icon {
            margin-top: 1px
        }
        .modal-details{
            font-weight: bolder;
            color:rgb(3, 82, 186) ;
        }
        .modal-header{
            background-color:rgba(0, 110, 255, 0.11) ;
        }
        .modal-close{
            background-color: red;
            color: white;
        }
        .select2-container--default .select2-selection--single{
           padding:6px;
           height: 37px;
           width: 100%;
           font-size: 1.2em;
           position: relative;
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
                <a href="{{ route('admin.crm.client-reminder.index') }}">Reminder</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.crm.client-reminder.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-primary" onclick="search('today')">Today</button>
                <button type="button" onclick="search('tomorrow')" class="btn btn-sm btn-info">Tomorrow</button>
                <button type="button" onclick="search('sevenDays')" class="btn btn-sm btn-warning ">Next 7 Days</button>

                <div class="row my-3">
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
                        <label for="date_form"><b>Form</b></label>
                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ old('start_date') }}" placeholder="d/m/yy">
                            @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
                        <label for="date_to"><b> To</b></label>
                        <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ old('end_date') }}" placeholder="d/m/yy" required>
                            @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
                        <label for="client_id"><b>Client</b></label>
                        <select name="client_id" id="client_id"class="form-select select2">
                            <option value="">--Select Client--</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
                        <label for="employee_id"><b>Added By</b></label>
                        <select name="employee_id" id="employee_id"class="form-select select2">
                            <option value="">--Select Employee--</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2 mt-4">
                        <select name="filter_type" id="filter_type"
                                class="form-select @error('filter_type') is-invalid @enderror">
                            <option value="all" selected>--Select Type--</option>
                            <option value="1">Up Coming</option>
                            <option value="2">Expired</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2 mt-4">
                        <button type="submit" class="btn btn-primary" onclick="search()"><i
                                class='bx bx-filter me-1 search-icon'></i>Filter
                        </button>
                    </div>
                </div>
                <div class="table-responsive" id="all-reminder">
                    <div class="table-responsive">
                        <table class="table border mb-0  " id="reminder_table_all">
                            <thead class="table-light fw-semibold dataTableHeader">
                            <tr class="align-middle table">
                                <th width="1%">#</th>
                                <th width="15%">Date</th>
                                <th width="15%">Create Date</th>
                                <th width="10%">Time</th>
                                <th width="15%">Client</th>
                                <th width="15%">Note</th>
                                <th width="10%">Added By</th>
                                <th width="10%">Status</th>
                                <th width="20%">Action</th>
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
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title text-primary" id="staticBackdropLabel">Reminder Details</h5>
        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body modal-show">

        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary modal-close" data-coreui-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>


@endsection

@push('script')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            search();
        });

        function search(val) {

            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var status = $("#filter_type").val();
            var client = $("#client_id").val();
            var employee = $("#employee_id").val();
            var day = val;
            var x = 1;
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#reminder_table_all').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                processing: true,
                serverSide: true,
                "bDestroy": true,
                bFilter: false,
                bInfo: false,
                responsive: false,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                ajax: {
                    url: "{{route('admin.crm.client-reminder-today')}}",
                    type: "POST",
                    data: {
                        'start_date': start_date,
                        'end_date': end_date,
                        'status': status,
                        'client': client,
                        'employee': employee,
                        'day': day,
                    },
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'date', name: 'date', orderable: true, searchable: true},
                    {data: 'created_at', name: 'created_at', orderable: true, searchable: true},
                    {data: 'time', name: 'time', orderable: true, searchable: true},
                    {data: 'client', name: 'client', orderable: true, searchable: true},
                    {data: 'reminder_note', name: 'reminder_note', orderable: true, searchable: true},
                    {data: 'employee', name: 'employee', orderable: true, searchable: true},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        }
        function getReminder(id) {
            event.preventDefault();
            var url = '{{ route('admin.crm.client-reminder.edit', ':id') }}';
            $.ajax({
                type: "get",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp)
                    $('.modal-show').html(resp.data);
                },
                error: function () {
                    location.reload();
                }
            });
        }
        $('#client_id').select2({
                ajax: {
                    url: '{{route('admin.crm.client.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        console.log();
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    value: item.id,
                                    id: item.id,
                                }
                            })
                        };
                    }
                }
        });
        $('#employee_id').select2({
                ajax: {
                    url: '{{route('admin.crm.reminder.employee-search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        console.log();
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    value: item.id,
                                    id: item.id,
                                }
                            })
                        };
                    }
                }
        });

        // // delete Confirm
        function commentDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    commentDelete(id);
                }
            });
        };

        // Delete Button
        function commentDelete(id) {
            var url = '{{ route('admin.crm.client-reminder.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // // Reloade DataTable
                    $('#reminder_table_all').DataTable().ajax.reload();
                    if (resp.success === true) {
                        //     // show toast message
                        toastr.success(resp.message);
                        //location.reload();
                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }

        // Status Change Confirm Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();
            swal({
                title: `Are you sure?`,
                text: "You want to update the status?.",
                buttons: true,
                infoMode: true,
            }).then((willStatusChange) => {
                if (willStatusChange) {
                    statusChange(id);
                }
            });
        };

        // Status Change
        function statusChange(id) {
            var url = '{{ route("admin.crm.reminder.update.status",":id") }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#reminder_table_all').DataTable().ajax.reload();
                    if (resp == "Complete") {
                        toastr.success('This status has been changed to Complete.');
                        return false;
                    } else {
                        toastr.error('This status has been changed to Pending.');
                        return false;
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }
    </script>
@endpush
