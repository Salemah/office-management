<div class="card-body">
    <table class="table" id="table">
        <thead class="table-header fw-semibold dataTableHeader">
        <tr class="align-middle table">
            <th class="table-th">#SL</th>
            <th class="table-th">Title</th>
            <th class="table-th">Type</th>
            <th class="table-th">Status</th>
            <th class="table-th">Added By</th>
            {{-- <th class="table-th">Document</th> --}}
            <th class="table-th">Action</th>
        </tr>
        </thead>
    </table>
</div>
<input type="hidden" id="project_id" name="project_id" value="{{$project->id}}">

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                        url: "{{route('admin.project.task.show',$project->id)}}",
                        type: "get"
                    },
                    columns: [
                        {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                        {data: 'name', name: 'name', orderable: true, searchable: true},
                        {data: 'type', name: 'type', orderable: true, searchable: true},
                        {data: 'status', name: 'status'},
                        {data: 'createdBy', name: 'createdBy', orderable: true, searchable: true},
                        // {data: 'document', name: 'document', orderable: true, searchable: true},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                });
            });
        </script>
@endpush
