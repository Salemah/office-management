@push('css')
    <style>
        #hold_table_filter,
        #hold_table_paginate {
            float: right;
        }
        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive {
            overflow-x: hidden !important;
        }
        .card{
            border: none;
        }
    </style>
@endpush
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="hold_table">
            <thead class="table-light fw-semibold dataTableHeader">
               <tr class="align-middle table">
                    <th>#</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Start Date </th>
                    <th>End Date</th>
                    <th>Estimate Day</th>
                    <th>Final Hour</th>
               </tr>
            </thead>
        </table>
    </div>
</div>

@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#hold_table').DataTable({
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
                    url: "{{route('admin.project.duration.hold.list',$project->id)}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'type', name: 'type', orderable: true, searchable: true},
                    {data: 'status', name: 'status', orderable: true, searchable: true},
                    {data: 'start_date', name: 'start_date', orderable: true, searchable: true},
                    {data: 'end_date', name: 'end_date', orderable: true, searchable: true},
                    {data: 'total_day', name: 'total_day', orderable: true, searchable: true},
                    {data: 'total_hour', name: 'total_hour', orderable: true, searchable: true},
                ],
            });
        });
    </script>
@endpush
