@push('css')
    <style>
        #qualifications_table_filter,
        #qualifications_table_paginate {
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
<form action="{{ route('admin.employee-qualification.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class=" col-sm-5 ">
            <div class="form-group">
                <label for="institute_name"><b>Institute Name</b><span class="text-danger">*</span></label>
                <input type="text" name="institute_name" id="institute_name" value="{{ old('institute_name') }}"
                       class="form-control @error('institute_name') is-invalid @enderror"
                       placeholder="Enter Institute Name">
                @error('institute_name')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-4 ">
            <div class="form-group">
                <label for="degree"><b>Degree</b><span class="text-danger">*</span></label>
                <input type="text" name="degree" id="degree" value="{{ old('degree') }}"
                       class="form-control @error('degree') is-invalid @enderror" placeholder="Enter Degree">
                @error('degree')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class=" col-sm-3 mb-2">
            <div class="form-group">
                <label for="passing_year"><b>Passing Year </b><span class="text-danger">*</span></label>
                <select name="passing_year" id="passing_year"
                        class="form-control @error('passing_year') is-invalid @enderror">
                    @forelse($years as $year)
                        <option value="{{$year}}">{{$year}}</option>
                    @empty
                        <option value="">--Select--</option>
                    @endforelse
                </select>
                @error('passing_year')
                <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror

            </div>
        </div>
        <input type="hidden" name="employee_id" id="employee_id" value="{{$employee->id}}">
        @can('employee_qualifications_update')
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-sm btn-primary">Create</button>
            </div>
        @endcan

    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="qualifications_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Institute Name</th>
                <th>Degree</th>
                <th>Passing Year</th>
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
            var dTable = $('#qualifications_table').DataTable({
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
                    url: "{{ route('admin.employee-qualification.show', $employee->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'institute_name', name: 'institute_name', orderable: true, searchable: true},
                    {data: 'degree', name: 'degree', orderable: true, searchable: true},
                    {data: 'passing_year', name: 'passing_year', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function qualificationDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    qualificationDelete(id);
                }
            });
        };

        // Delete Button
        function qualificationDelete(id) {
            var url = '{{ route('admin.employee-qualification.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // // Reloade DataTable
                    $('#qualifications_table').DataTable().ajax.reload();
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
