@extends('layouts.dashboard.app')

@section('title', 'Commented Client')

@push('css')
<link rel="stylesheet" href="{{ asset('backend/datatables.min.css') }}">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <style>

    </style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Commented Client</a>
            </li>

        </ol>
        {{-- <a class="btn btn-sm btn-success text-white" href="{{ route('admin.crm.client.create') }}">
            <i class='bx bx-plus'></i> Create
        </a> --}}
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
              {{--  @can('client_import')
                     <a class="btn btn-sm btn-success text-white" data-coreui-toggle="modal"
                         data-coreui-target="#clientImportModal">Import</a>
                @endcan
                @can('client_export')
                    <a href="{{route('admin.crm.import.export')}}" class="btn btn-sm btn-primary text-white"> Export</a>
                @endcan --}}


                <div class="table-responsive mt-3">
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                            <tr class="align-middle table">
                                <th>#</th>
                                <th>Date</th>
                                <th>Client</th>

                                <th>Commented By</th>

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

     <script src="{{ asset('backend/datatables.min.js') }}"></script>

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
                ajax: {
                    url: "{{route('admin.commented-client.index')}}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex",name: "DT_RowIndex",orderable: false,searchable: false},
                    { data: 'created_at', name: 'created_at'},
                    { data: 'client',name: 'client',orderable: true,searchable: true},

                    { data: 'employee',name: 'employee',orderable: true,searchable: true},

                ],
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [

                            {
                                extend: 'colvis',
                                className: 'btn-sm btn-warning',
                                title: 'Clients',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Clients',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Clients',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-dark',
                                title: 'Clients',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Clients',
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
                                title: 'Clients',
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
                    targets: 1,
                    orderable: false,
                    visible: false
                }]
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

            var url = '{{ route("admin.crm.client.destroy",":id") }}';
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
            var url = '{{ route("admin.crm.client.update.status",":id") }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
                    if (resp == "active") {
                        toastr.success('This status has been changed to Active.');
                        return false;
                    } else {
                        toastr.error('This status has been changed to Inactive.');
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
