@extends('layouts.dashboard.app')

@section('title', 'State')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <style>
        #table_filter,
        #table_paginate {
            float: right;
        }

        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }

        .table-responsive {
            overflow-x: hidden !important;
        }

        .select2-close-mask {
            z-index: 2099;
        }

        .select2-dropdown {
            z-index: 3051;
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
                <span>State</span>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" data-coreui-toggle="modal"
           data-coreui-target="#addStateModal">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table border mb-0" id="table">
                                <thead class="table-light fw-semibold">
                                <tr class="align-middle table">
                                    <th>#</th>
                                    <th>State Name</th>
                                    <th>Action</th>
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

    </div>


    <!-- Add Country Modal -->
    <div class="modal fade" id="addStateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add State</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="add-state" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="country_name"><b>Country Name</b><span class="text-danger">*</span></label>
                                <select name="country_name" id="country_name" class="form-control select2">
                                    <option>--Select Country--</option>
                                </select>
                                @error('country_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="state_name"><b>State Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="state_name" id="state_name"
                                       class="form-control @error('state_name') is-invalid @enderror"
                                       value="{{ old('state_name') }}" placeholder="State Name">
                                @error('state_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                <button type="button" class="btn btn-sm btn-secondary"
                                        data-coreui-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit State Modal -->
    <div class="modal fade" id="edit_State_Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit State</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="edit-city" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="country_name"><b>Country Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="country_name" id="edit_country_name"
                                       class="form-control @error('country_name') is-invalid @enderror"
                                       value="{{ old('country_name') }}" placeholder="Country Name" readonly>
                                @error('country_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <input type="hidden" id="state_id" value="">
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="state_name"><b>State Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="state_name" id="edit_state_name"
                                       class="form-control @error('state_name') is-invalid @enderror"
                                       value="{{ old('state_name') }}" placeholder="State Name">
                                @error('state_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <input type="hidden" id="state_id" value="">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                <button type="button" class="btn btn-sm btn-secondary"
                                        data-coreui-dismiss="modal">Close
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
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>


    <script>
        $(document).ready(function () {
            var dTable = $('#table').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
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
                    url: "{{ route('admin.address.state.index') }}",
                    type: "get"
                },
                columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true
                    },
                    //only those have manage_user permission will get access
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });

        // Add Country
        $('#country_name').select2({
            ajax: {
                url: '{{ route('admin.employee.details.address.country.search') }}',
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
        $(document).ready(function ($) {
            $(".add-state").submit(function (event) {
                event.preventDefault(); //prevent default action
                var post_url = '{{ route('admin.address.state.store') }}'; //get form action url
                var State_Name = $('.add-state #state_name').val(); //Encode form elements for submission
                var Country_Name = $('.add-state #country_name').val(); //Encode form elements for submission
                console.log(Country_Name);
                $.ajax({
                    url: post_url,
                    type: "POST",
                    data: {
                        state_name: State_Name,
                        Country_Name: Country_Name

                    },
                    success: function (resp) {
                        if (resp.success === true) {
                            // clear form input field data
                            $('.add-state').trigger('clear');
                            // Reload DataTable
                            $('#table').DataTable().ajax.reload();

                            // show toast message
                            toastr.success(resp.message);
                        } else if (resp.errors) {
                            toastr.error(resp.errors[0]);
                        } else {
                            toastr.error(resp.message);
                        }
                    },
                    error: function () {
                        location.reload();
                    }
                });
                // Close Modal After Process Complete
                jQuery.noConflict();
                $('#addStateModal').modal('hide');
            });
        });

        // Edit Country Data
        function getSelectedUserData(id) {
            var url = '{{ route('admin.address.state.edit', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#edit_State_Modal #edit_state_name').val(resp.state.name);
                    $('#edit_State_Modal #edit_country_name').val(resp.country.name);
                    $('#edit_State_Modal #state_id').val(resp.state.id);
                },
                error: function () {
                    location.reload();
                }
            });
        }

        // Update Country Name
        $(document).ready(function ($) {
            $(".edit-city").submit(function (event) {
                event.preventDefault(); //prevent default action
                var url = '{{ route('admin.address.state.update', ':id') }}'; //get form action url
                var id = $('#state_id').val(); // Encode form elements for submission
                var StateName = $('#edit_state_name').val(); //Encode form elements for submission
                console.log(StateName);
                $.ajax({
                    url: url.replace(':id', id),
                    type: "PUT",
                    data: {
                        state_name: StateName
                    },
                    success: function (resp) {
                        if (resp.success === true) {
                            // clear form input field data
                            $('.edit-city').trigger('reset');
                            // Reload DataTable
                            $('#table').DataTable().ajax.reload();

                            // show toast message
                            toastr.success(resp.message);
                        } else if (resp.errors) {
                            toastr.error(resp.errors[0]);
                        } else {
                            toastr.error(resp.message);
                        }
                    },
                    error: function () {
                        //location.reload();
                    }
                });
                // Close Modal After Process Complete
                $.noConflict();
                $('#edit_State_Modal').modal('hide');
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
            var url = '{{ route('admin.address.state.destroy', ':id') }}';
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
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }
    </script>
@endpush
