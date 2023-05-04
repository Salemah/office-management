@extends('layouts.dashboard.app')

@section('title', 'Paid Leave')

@push('css')

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <style>
        #table_filter, #table_paginate {
            float: right;
        }

        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }

        .table-responsive {
            overflow-x: hidden !important;
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
                <a href="#">HRM</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Holiday</a>
            </li>

        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.hrm.paid-leave.create') }}">
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
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table border mb-0" id="table">
                            <thead class="table-header fw-semibold dataTableHeader">
                            <tr class="align-middle table">
                                <th>#</th>
                                <th>Days Per Year</th>
                                <th>Paid Per Leave</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="leaveSingleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Paid Leave</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-show">
    
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="leaveEditSingleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Paid Leave Edit Form</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-edit">

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

    <script>
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#table').DataTable({
                order: [],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                processing: true,
                responsive: false,
                serverSide: true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('admin.hrm.paid-leave.index')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'days',   name: 'days', orderable: true, searchable: true},
                    {data: 'paid_per_day',   name: 'paid_per_day', orderable: true, searchable: true},
                    {data: 'total_amount',   name: 'total_amount', orderable: true, searchable: true},
                    // {data: 'description',   name: 'description', orderable: true, searchable: true},
                    {data: 'status', name: 'status'},
                    //only those have manage_user permission will get access

                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    deleteItem(id);
                }
            });
        };

        // Delete Button
        function deleteItem(id) {
            var url = '{{ route("admin.hrm.paid-leave.destroy",":id") }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        // show toast message
                        toastr.success(resp.message);
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
            var url = '{{ route("admin.hrm.paid-leave.status",":id") }}';
            $.ajax({
                type: "Post",
                url: url.replace(':id', id),
                success: function (resp) {
                   // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
                    if(resp == "active"){
                        toastr.success('This status has been changed to Publish.');
                        return false;
                    }else{
                        toastr.error('This status has been changed to Un Publish.');
                        return false;
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }
        function getSelectedLeaveData(id, type) {
            event.preventDefault();
            $.ajax({
                url: '{{ route('admin.hrm.paid-leave-show') }}',
                type: "POST",
                data:{
                    id: id,
                    type: type
                },
                success: function (resp) {
                    console.log(resp)
                    if(resp.type == 1){
                        $('.modal-edit').html(resp.data);

                    }else{
                        $('.modal-show').html(resp.data);
                    }

                },
                error: function () {
                    location.reload();
                }
            });
        }
    </script>

@endpush
