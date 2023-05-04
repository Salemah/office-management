@push('css')
    <style>
        #work_experience_table_filter,
        #work_experience_table_paginate {
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
<form action="{{ route('admin.employee-work-experience.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class=" col-sm-6 ">
            <div class="form-group">
                <label for="organization_name"><b>Organization Name</b><span class="text-danger">*</span></label>
                <input type="text" name="organization_name" id="organization_name"
                       value="{{ old('organization_name') }}"
                       class="form-control @error('organization_name') is-invalid @enderror"
                       placeholder="Enter Organization Name">
                @error('organization_name')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-6 ">
            <div class="form-group">
                <label for="designation"><b>Designation </b><span class="text-danger">*</span></label>
                <input type="text" name="designation" id="designation" value="{{ old('designation') }}"
                       class="form-control " placeholder="Enter Designation">
                @error('designation')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-6 ">
            <div class="form-group">
                <label for="start_date"><b>Staring Date</b><span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                       class="form-control @error('start_date') is-invalid @enderror" placeholder="Enter Start Date">
                @error('start_date')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-6 ">
            <div class="form-group">
                <label for="end_date"><b>End Date </b><span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                       class="form-control @error('end_date') is-invalid @enderror" placeholder="Enter Identity No">
                @error('end_date')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <input type="hidden" name="employee_id" id="employee_id" value="{{$employee->id}}">
        <div class=" col-sm-12 mb-2">
            <div class="form-group">
                <label for="note"><b>Note</b></label>
                <textarea name="note" id="note" rows="4" cols="90" class="form-control "
                          placeholder="Enter Note..."></textarea>
            </div>
        </div>
        @can('employee_workexperience_update')
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-sm btn-primary">Create</button>
            </div>
        @endcan
    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="work_experience_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Organization Name</th>
                <th>Designation</th>
                <th>Staring Date</th>
                <th>End Date</th>
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
        CKEDITOR.replace('note', {
            height: '80px',
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#work_experience_table').DataTable({
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
                    url: "{{ route('admin.employee-work-experience.show', $employee->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'organization_name', name: 'organization_name', orderable: true, searchable: true},
                    {data: 'designation', name: 'designation', orderable: true, searchable: true},
                    {data: 'start_date', name: 'start_date', orderable: true, searchable: true},
                    {data: 'end_date', name: 'end_date', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function experienceDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    experienceDelete(id);
                }
            });
        };

        // Delete Button
        function experienceDelete(id) {
            var url = '{{ route('admin.employee-work-experience.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reload DataTable
                    $('#work_experience_table').DataTable().ajax.reload();
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
