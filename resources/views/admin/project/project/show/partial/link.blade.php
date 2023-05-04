@push('css')
    <style>
        #link_table_filter,
        #link_table_paginate {
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
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.link.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="cpanel_link"> <b>Cpanel Link </b></label>
            <input type="text"  id="cpanel_link" class="form-control " name="cpanel_link"
                   placeholder="Enter Cpanel Link">
            @error('cpanel_link')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="cpanel_password"><b> Cpanel Password</b></label>
            <input type="text"  id="cpanel_password" class="form-control " name="cpanel_password"   placeholder="Enter Cpanel Password">
            @error('cpanel_password')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="website"><b> Web Link</b></label>
            <input type="text"  type="text" name="website" id="website" class="form-control @error('website') is-invalid @enderror"  placeholder="Enter Web Link">
            @error('website')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="git_link"><b> Git Link</b></label>
            <input type="text"  id="git_link" class="form-control " name="git_link"   placeholder="Enter Git Link">
            @error('git_link')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="row mt-2">
            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                <label for="user_role"><b>User Role</b></label>
                <input type="text"  id="user_role" class="form-control " name="user_role[]"   placeholder="Enter User Role">
                @error('user_role')
                   <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                <label for="email"><b>Email</b></label>
                <input type="email"  id="email" class="form-control " name="email[]"   placeholder="Enter User Email">
                @error('email')
                   <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-3 mb-2">
                <label for="password"><b>Password</b></label>
                <input type="text"  id="password" class="form-control " name="password[]"   placeholder="Enter User Password">
                @error('password')
                   <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-sm-1 ">
                <button style="margin-top:22px"class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white"type="button" disabled>&times;
                </button>
            </div>
            <div class="expense-body">

            </div>
        </div>
        {{-- append data --}}

        <div class="col-sm-4 my-3 ">
            <button type="button" id="addRow" class="btn btn-sm btn-success text-white">
                + Add User
            </button>
        </div>
        {{--  --}}
        <input type="hidden" name="project_id" id="project_id" value="{{ $project->id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="link_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Cpanel</th>
                <th>Web Site</th>
                <th>Git </th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#link_table').DataTable({
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
                ajax: {
                    url: "{{ route('admin.project.link.show',$project->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'cpanel_link', name: 'cpanel_link', orderable: true, searchable: true},
                    {data: 'web_link', name: 'web_link', orderable: true, searchable: true},
                    {data: 'git_link', name: 'git_link', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });
        $(document).ready(function () {
            var max_field = 5;
            var wrapper = $(".expense-body");
            var x = 0;
            $("#addRow").click(function () {
                    x++;
                    $(wrapper).append('<div class="row mt-2" id="link-row-' + x + '">'+
                                        '<div class="form-group col-12 col-sm-12 col-md-4 mb-2">'+
                                            '<input type="text"  id="user_role" class="form-control " name="user_role[]"   placeholder="Enter User Role">'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-4 mb-2">'+
                                            '<input type="email"  id="email" class="form-control " name="email[]"   placeholder="Enter User Email">'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-3 mb-2">'+
                                            '<input type="text"  id="password" class="form-control " name="password[]"   placeholder="Enter User Password">'+
                                        '</div>'+
                                        '<div class="form-group col-sm-1 ">'+
                                            '<button type="button"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="linkRemove(' + x + ')">' +
                                                        '&times;' +
                                            '</button>' +
                                       ' </div>'+
                                     '</div>');
            });
        });

        function linkRemove(id) {
            $('#link-row-' + id).remove();
        }
        // delete Confirm
        function documentDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    documentDelete(id);
                }
            });
        };

        // Delete Button
        function documentDelete(id) {
            var url = '{{ route('admin.project.link.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // // Reloade DataTable
                    $('#link_table').DataTable().ajax.reload();
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
    </script>
@endpush
