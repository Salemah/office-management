@push('css')
    <style>
        #module_table_filter,
        #module_table_paginate {
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

<div class="table-responsive">

    <div class="table-responsive">
        <table class="table border mb-0" id="module_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Estimate Hour </th>
                <th>Estimate Day </th>
                <th>Final Hour</th>
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
            var dTable = $('#module_table').DataTable({
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
                    url: "{{ route('admin.project.module.show',$project->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'module_start_date', name: 'module_start_date', orderable: true, searchable: true},
                    {data: 'module_end_date', name: 'module_end_date', orderable: true, searchable: true},
                    {data: 'module_estimate_hour', name: 'module_estimate_hour', orderable: true, searchable: true},
                    {data: 'module_total_day', name: 'module_total_day', orderable: true, searchable: true},
                    {data: 'module_final_hour', name: 'module_final_hour', orderable: true, searchable: true},
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
                    durationDelete(id);
                }
            });
        };

        // Delete Button
        function durationDelete(id) {
            var url = '{{ route('admin.project.module.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#module_table').DataTable().ajax.reload();
                    if (resp.success === true) {
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
    </script>
@endpush
