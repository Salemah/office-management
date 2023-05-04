@push('css')
    <style>
        #document_table_filter,
        #document_table_paginate {
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
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.crm.client-document.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="document_name"> <b>Document Name </b> <span class="text-danger">*</span></label>
            <input type="text" required id="document_name" class="form-control " name="document_name"
                   placeholder="Enter Document Name">
            @error('document_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="document"><b> File</b> <span class="text-danger">*</span></label>
            <input type="file" required id="document" class="form-control " name="document">
            @error('document')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="document_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Document Name</th>
                <th>Document</th>
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
            var dTable = $('#document_table').DataTable({
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
                    url: "{{ route('admin.crm.client-document.show', $Client->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'document_name', name: 'document_name', orderable: true, searchable: true},
                    {data: 'document_file', name: 'document_file', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

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
            var url = '{{ route('admin.crm.client-document.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // // Reloade DataTable
                    $('#document_table').DataTable().ajax.reload();
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
