@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        #own_client_table_filter,
        #own_client_table_paginate {
            float: right;
        }
        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive {
            /* overflow-x: true !important; */
        }
    </style>
@endpush
<div class="table-responsive">
    <div class="table-responsive">
        {{-- <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
            <label for="date_form"><b>Select Date</b></label>
            <div class="form-group">
                <input type="date" name="start_date" id="start_date" class="form-control"
                       value="{{ old('start_date') }}" placeholder="d/m/yy" onchange="search()">

            </div>
        </div> --}}
        <table class="table border mb-0" id="own_client_table">
            <thead class="table-light fw-semibold dataTableHeader">
                <tr class="align-middle table">
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Interested On</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<input type="hidden" value="{{$employee->id}}" name="employee_id" id="employee_id">
@push('script')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

<!-- sweetalert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        search();
    });

    function search(val) {

        var start_date = $("#start_date").val();
        var employee_id = $("#employee_id").val();
        var x = 1;
        var searchable = [];
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
        var dTable = $('#own_client_table').DataTable({
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
            pagingType: "full_numbers",
            ajax: {
                url: "{{route('admin.employee.employee-own-leeds')}}",
                type: "POST",
                data: {
                    'start_date': start_date,
                    'id': employee_id,

                },
            },
            columns: [
                {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                { data: 'name',name: 'name',orderable: true,searchable: true},
                { data: 'interestedOn', name: 'interestedOn'},
                { data: 'email', name: 'email'},
                { data: 'phone_primary', name: 'phone_primary'},
                { data: 'action',name: 'action',orderable: false,searchable: false}
            ],
        });
    }

    </script>
@endpush
