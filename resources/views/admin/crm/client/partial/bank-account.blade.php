@push('css')
    <style>
        #bank_account_table_filter,
        #bank_account_table_paginate {
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
<form class="add-bank-account" enctype="multipart/form-data" action="{{ route('admin.crm.client-bank-account.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="bank_id"><b>Bank Name</b><span class="text-danger">*</span></label>
            <select name="bank_id" id="bank_id" class="form-select @error('bank_id') is-invalid @enderror">
                <option value="" selected>--Select Bank--</option>
                @foreach ($banks as $bank)
                    <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                @endforeach
            </select>
            @error('bank_id')
            <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                        </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="branch_name"><b>Bank Branch</b><span class="text-danger">*</span></label>
            <input type="text" name="branch_name" id="branch_name"
                   class="form-control @error('branch_name') is-invalid @enderror" value="{{ old('branch_name') }}"
                   placeholder="Enter Branch Name">
            @error('branch_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="account_name"><b>Account Name</b><span class="text-danger">*</span></label>
            <input type="text" name="account_name" id="account_name"
                   class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name') }}"
                   placeholder="Enter Account Name">
            @error('account_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="account_number"><b>Account Number</b><span class="text-danger">*</span></label>
            <input type="text" name="account_number" id="account_number"
                   class="form-control @error('account_number') is-invalid @enderror"
                   value="{{ old('account_number') }}" placeholder="Enter Account Number">
            @error('account_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="initial_balance"><b>Initial Balance</b><span class="text-danger">*</span></label>
            <input type="number" name="initial_balance" id="initial_balance"
                class="form-control @error('initial_balance') is-invalid @enderror" value="{{ old('initial_balance') }}"
                placeholder="Enter Initial Balance">
            @error('initial_balance')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="routing_no"><b>Routing Number</b></label>
            <input type="text" name="routing_no" id="routing_no"
                   class="form-control @error('routing_no') is-invalid @enderror" value="{{ old('routing_no') }}"
                   placeholder="Enter Routing Number">
            @error('routing_no')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="status"><b>Status</b><span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                <option>--Select Status--</option>
                <option value="1" selected>Active</option>
                <option value="0">In-active</option>
            </select>
            @error('status')
            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
        <div class="form-group col-12 mb-2">
            <label for="descriptions"><b>Description</b></label>
            <textarea name="descriptions" id="descriptions" rows="3" class="form-control descriptions"
                      value="{{ old('description') }}" placeholder="Description..."></textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
            @enderror
        </div>
        <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
        </div>
    </div>
</form>
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="bank_account_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Account Name</th>
                <th>Bank</th>
                <th>Account Number</th>
                <th>Branch Name</th>
                <th>Status</th>
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
        CKEDITOR.replace('descriptions', {
            height: '120px',
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#bank_account_table').DataTable({
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
                    url: "{{ route('admin.crm.client-bank-account.show', $Client->id) }}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'name', name: 'name', orderable: true, searchable: true},
                    {data: 'bank', name: 'bank', orderable: true, searchable: true},
                    {data: 'account_number', name: 'account_number', orderable: true, searchable: true},
                    {data: 'branch_name', name: 'branch_name', orderable: true, searchable: true},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function clientBankAccountDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    clientBankAccountDelete(id);
                }
            });
        };

        // Delete Button
        function clientBankAccountDelete(id) {
            var url = '{{route('admin.crm.client-bank-account.destroy', ':id')}}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // // Reloade DataTable
                    $('#bank_account_table').DataTable().ajax.reload();
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

        // Status Change Confirm Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();
            swal({
                title: `Are you sure?`,
                text: "You want to update the status?.",
                buttons: true,
                infoMode: true,
            }).then((willStatusChange) => {
                if (willStatusChange) {
                    statusChange(id);
                }
            });
        };

        // Status Change
        function statusChange(id) {
            var url = '{{ route("admin.crm.client.bank.update.status",":id") }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#bank_account_table').DataTable().ajax.reload();
                    if (resp == "active") {
                        toastr.success('This status has been changed to Active.');
                        return false;
                    } else {
                        toastr.error('This status has been changed to Inactive.');
                        return false;
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }
    </script>
@endpush
