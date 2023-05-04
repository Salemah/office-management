@extends('layouts.dashboard.app')

@section('title', 'Client')

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/datatables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                <a href="{{ route('admin.dashboard') }}">Client</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.crm.client.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
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
                @can('client_import')
                    <a class="btn btn-sm btn-success text-white" data-coreui-toggle="modal"
                         data-coreui-target="#clientImportModal">Import</a>
                @endcan
                @can('client_export')
                    <a href="{{route('admin.crm.import.export')}}" class="btn btn-sm btn-primary text-white"> Export</a>
                @endcan
                <div class="row">
                    <div class="form-group col-4 col-sm-4 col-md-4 mb-2">
                        <label for="client_business_category_id"><b>Client Business Category</b></label>
                        <select name="client_business_category_id" id="client_business_category_id" class="form-select @error('client_business_category_id') is-invalid @enderror">
                            <option  value="">--Client Business Category--</option>
                        </select>
                    </div>
                    <div class="form-group col-4 col-sm-4 col-md-4 mb-2">
                        <label for="client_type_id"><b>Client Type</b></label>
                        <select name="client_type_id" id="client_type_id" class="form-select @error('client_type_id') is-invalid @enderror">
                            <option  value="">--Client Type--</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-4 col-md-2 mb-2 mt-4 ">
                        <button type="submit" class="btn btn-primary" onclick="search()"><i
                                class='bx bx-filter me-1 search-icon'></i>Filter
                        </button>
                        <button type="button" class="btn  btn-primary" onclick="clearData()"><i
                            class='bx bx-filter me-1 search-icon'></i>Clear
                    </button>
                    </div>

                </div>

                <div class="table-responsive mt-3">
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                            <tr class="align-middle table">
                                <th width="1%">#SL</th>
                                <th>Image</th>
                                <th>Creation Date</th>
                                <th>Added By</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Business Category</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="clientImportModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Client</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.crm.import.client') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="country_name"><b>File</b><span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control">
                            </div>
                            <div class="form-group col-12 my-4">
                                <label for="country_name"></label>
                                <button type="button" class="btn btn-outline-info"><a id="sample-download-btn"
                                                                                      href="{{asset('client/Sample-Client.xlsx')}}"><i
                                            class="bx bx-down-arrow-alt"></i>Sample File Download</a></button>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                <button type="button" class="btn btn-sm btn-danger" data-coreui-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
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
            search();
        });
        function clearData(){
           // alert(5435);
            $('#client_business_category_id').val(null).trigger('change');
            $('#client_type_id').val(null).trigger('change');
            search();
        };
        function search() {
            var business_category = $("#client_business_category_id").val();
            var client_type = $("#client_type_id").val();
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
                "bDestroy": true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                ajax: {
                    url: "{{route('admin.crm.client.list')}}",
                    type: "post",
                    data:{
                            'category':business_category,
                            'client_type':client_type
                         }
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'image', name: 'image', orderable: true, searchable: true},
                    {data: 'creation_date', name: 'creation_date', orderable: true, searchable: true},
                    {data: 'added_by', name: 'added_by', orderable: true, searchable: true},
                    {data: 'name', name: 'name', orderable: true, searchable: true},
                    {data: 'phone_primary', name: 'phone_primary', orderable: true, searchable: true},
                    {data: 'client_type_priority', name: 'client_type_priority', orderable: true, searchable: true},
                    //only those have manage_user permission will get access
                    // {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
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
        };
        $('#client_business_category_id').select2({
            ajax: {
                url: '{{route('admin.crm.client.busines.category')}}',
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
        $('#client_type_id').select2({
            ajax: {
                url: '{{route('admin.crm.client.type.search')}}',
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
