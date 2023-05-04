@push('css')
    <style>
        #certificate_table_filter,
        #certificate_table_paginate {
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
<form action="{{ route('admin.employee-certificate.store') }}" enctype="multipart/form-data" method="POST">
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
                <label for="certificate"><b>Certification Name</b><span class="text-danger">*</span></label>
                <input type="text" name="certificate" id="certificate" value="{{ old('certificate') }}"
                       class="form-control @error('certificate') is-invalid @enderror"
                       placeholder="Enter Certificate Name ">
                @error('certificate')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-6 ">
            <div class="form-group">
                <label for="duration"><b>Duration</b><span class="text-danger">*</span></label>
                <input type="text" name="duration" id="duration" value="{{ old('duration') }}"
                       class="form-control @error('duration') is-invalid @enderror" placeholder="Enter Duration">
                @error('duration')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-6 mb-2 ">
            <div class="form-group">
                <label for="certificate_year"><b>Year</b><span class="text-danger">*</span></label>
                <select name="certificate_year" id="certificate_year"
                        class="form-control @error('certificate_year') is-invalid @enderror">
                    @forelse($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @empty
                        <option value="">--Select--</option>
                    @endforelse
                </select>
                @error('certificate_year')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-12 mb-2">
            <div class="form-group">
                <label for="certificate_note"><b>Note</b></label>
                <textarea type="text" name="certificate_note" id="certificate_note"
                          value="{{ old('certificate_note') }}" class="form-control " rows="4" cols="90"
                          placeholder="Enter Note....."></textarea>
                @error('certificate_note')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <input type="hidden" name="employee_id" id="employee_id" value="{{$employee->id}}">
        @can('employee_certification_update')
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-sm btn-primary">Create</button>
            </div>
        @endcan

    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="certificate_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Organization Name</th>
                <th>Certificate Name</th>
                <th>Duration</th>
                <th>Year</th>
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
        CKEDITOR.replace('certificate_note', {
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
            var dTable = $('#certificate_table').DataTable({
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
                    url: "{{ route('admin.employee-certificate.show', $employee->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'organization_name', name: 'organization_name', orderable: true, searchable: true},
                    {data: 'certificate', name: 'certificate', orderable: true, searchable: true},
                    {data: 'duration', name: 'duration', orderable: true, searchable: true},
                    {data: 'certificate_year', name: 'certificate_year', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function certificateDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    certificateDelete(id);
                }
            });
        };

        // Delete Button
        function certificateDelete(id) {
            var url = '{{ route('admin.employee-certificate.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    // // Reloade DataTable
                    $('#certificate_table').DataTable().ajax.reload();
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
