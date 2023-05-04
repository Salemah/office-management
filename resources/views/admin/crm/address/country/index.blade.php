@extends('layouts.dashboard.app')

@section('title', 'CRM')

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
                <span>Country</span>
            </li>
        </ol>
        <a href="" class="btn btn-sm btn-dark">dashboard</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Country List
                    <button class="btn btn-success text-white" data-coreui-toggle="modal"
                            data-coreui-target="#addCountryModal"><i class='bx bx-message-square-add'></i> Create
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table border mb-0" id="table">
                                <thead class="table-light fw-semibold">
                                <tr class="align-middle table">
                                    <th>#</th>
                                    <th>Country Name</th>
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
    <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Country</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="add-country" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="country_name">Country Name<span class="text-danger">*</span></label>
                                <input type="text" name="country_name" id="country_name"
                                       class="form-control @error('country_name') is-invalid @enderror"
                                       value="{{ old('country_name') }}" placeholder="Country Name">
                                @error('country_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                <button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Country Modal -->
    <div class="modal fade" id="editCountryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Country</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="edit-country" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-12 mb-2">
                                <label for="country_name">Country Name<span class="text-danger">*</span></label>
                                <input type="text" name="country_name" id="edit_country_name"
                                       class="form-control @error('country_name') is-invalid @enderror"
                                       value="{{ old('country_name') }}" placeholder="Country Name">
                                @error('country_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                <input type="hidden" id="country_id" value="" >
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                <button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Close</button>
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
            var searchable = [];
            var selectable = [];
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
                    url: "{{route('admin.crm.country.index')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'country_name', name: 'country_name', orderable: true, searchable: true},
                    //only those have manage_user permission will get access
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // Add Country
        jQuery(document).ready(function($){
            $(".add-country").submit(function(event) {
                event.preventDefault(); //prevent default action
                var post_url = '{{ route("admin.crm.country.store") }}'; //get form action url
                var countryName = $('.add-country #country_name').val(); //Encode form elements for submission
                $.ajax({
                    url: post_url,
                    type: "POST",
                    data: {
                        country_name : countryName
                    },
                    success: function(resp) {
                        if (resp.success === true) {
                            // clear form input field data
                            $('.add-country').trigger('clear');
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
                    error: function() {
                        location.reload();
                    }
                });
                // Close Modal After Process Complete
                jQuery.noConflict();
                $('#addCountryModal').modal('hide');
            });
        });

        // Edit Country Data
        function getSelectedUserData(id){
            var url = '{{ route("admin.crm.country.edit",":id") }}';
            jQuery.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function(resp) {
                    console.log(resp);
                    jQuery('#editCountryModal #edit_country_name').val(resp.country_name);
                    jQuery('#editCountryModal #country_id').val(resp.id);

                    // $('#editCountryModal').addClass('show');
                },error: function() {
                    location.reload();
                }
            });
        }







        // Update Country Name
        jQuery(document).ready(function($){
            $(".edit-country").submit(function(event) {
                event.preventDefault(); //prevent default action
                var url = '{{ route("admin.crm.country.update",":id") }}'; //get form action url
                var id = $('#country_id').val(); // Encode form elements for submission
                var countryName = $('#edit_country_name').val(); //Encode form elements for submission

                console.log(id);
                $.ajax({
                    url: url.replace(':id', id),
                    type: "PUT",
                    data: {
                        country_name : countryName
                    },
                    success: function(resp) {
                        if (resp.success === true) {
                            // clear form input field data
                            $('.edit-country').trigger('reset');
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
                    error: function() {
                        location.reload();
                    }
                });
                // Close Modal After Process Complete
                jQuery.noConflict();
                $('#editCountryModal').modal('hide');
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
            var url = '{{ route("admin.crm.country.destroy",":id") }}';
            jQuery.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    jQuery('#table').DataTable().ajax.reload();
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
