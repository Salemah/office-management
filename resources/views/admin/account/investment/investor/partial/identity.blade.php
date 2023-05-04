@push('css')
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
<form class="add-client-identity" enctype="multipart/form-data" action="{{route('admin.investor.identity.store')}}" method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="id_type_id"><b>Identity Type</b><span class="text-danger">*</span></label>
            <select name="id_type_id" id="id_type_id" class="form-control" required>
                <option value="">--Select Identity Type--</option>
                @foreach ($identities as $identity)
                    <option value="{{$identity->id}}"
                        @foreach ($investorIdentity as $Identity)
                        {{$Identity->id_type_id == $identity->id  ? 'disabled' : '' }}
                        @endforeach>
                        {{$identity->name}}
                    </option>
                @endforeach
            </select>
            @error('id_type_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="id_no"><b>Id Number</b><span class="text-danger">*</span></label>
            <input type="number" name="id_no" id="id_no"class="form-control @error('id_no') is-invalid @enderror"value="{{ old('id_no') }}" placeholder="Enter Identity Number" required>
            @error('id_no')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <input type="hidden" name="investor_id" id="investor_id" value="{{ $investor->id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>
        <div class="table-responsive">
            <div class="table-responsive">
                <table class="table border mb-0" id="table">
                    <thead class="table-light fw-semibold dataTableHeader">
                        <tr class="align-middle table">
                            <th>#</th>
                            <th>Identity Type</th>
                            <th>Identity No</th>
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
                        url: "{{route('admin.investor.identity.show', $investor->id)}}",
                        type: "get"
                    },
                    columns: [
                        {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                        {data: 'identityType', name: 'identityType', orderable: true, searchable: true},
                        {data: 'id_no', name: 'id_no', orderable: true, searchable: true},
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
                var url = '{{ route("admin.investor.identity.destroy",":id") }}';
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
                            location.reload();
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
