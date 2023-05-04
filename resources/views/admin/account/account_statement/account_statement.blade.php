@extends('layouts.dashboard.app')

@section('title', 'Account Statement')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
    integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .text-danger strong {
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title {
            width: 50%;
            font-weight: 600;
            display: none;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
        .dropify-wrapper .dropify-message p {
            font-size: initial;

        }
        .dropify-wrapper {
            border-radius: 6px;
        }


        @media (min-width: 200px ) and (max-width: 1130px ) {
            .responsive-table {
                width: 100%;
            }
            .responsive-table th {
                display: none;
            }
            .responsive-table .responsive-table-tr {
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            .responsive-table tr td {
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title {
                display: block;
            }
        }

    </style>

@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
    </script>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.account.cash.balance.sheet.index') }}">Account Statement</a>
            </li>

        </ol>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                        <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                        @php
                            $auth = Auth::user();
                             $user_role = $auth->roles->first();
                        @endphp
                        @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                            <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror" >
                                <option value="" selected >--Select warehouse_id--</option>
                                @forelse ($warehouses as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @empty
                                    <option value="">No Warehouse</option>
                                @endforelse
                            </select>

                        @else
                            <input type="text" name="warehouse_id_name"  class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                            <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
                        @endif
                        @error('warehouse_id')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="account_id"><b>Account</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('account_id') ? ' has-error' : '' }} has-feedback">
                            <select name="account_id" id="account_id" class="form-control">
                                <option value="" selected>-- Select Bank Account --</option>
                                @foreach ($bank_accounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                        | {{ $bankAccount->account_number }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('account_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('account_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="date_form"><b>Form</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ old('start_date') }}" placeholder="d/m/yy">
                            @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="date_to"><b>To</b><span class="text-danger">*</span></label>
                        <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }} has-feedback">
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ old('end_date') }}" placeholder="d/m/yy" required>
                            @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2 col-12">
                        <label for="date_to"><span class="text-danger"></span></label>
                        <div class="form-group has-feedback">
                            <button type="submit" class="btn btn-info btn-flat  search" onclick="search()" style="color: white"><i class='bx bx-search-alt'></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    {{-- <th width="5%">SL#</th> --}}
                                    <th width="10%"> Transaction Date</th>
                                    <th width="66%"> Details</th>
                                    <th width="8%"> Debit</th>
                                    <th width="8%"> Credit</th>
                                    <th width="8%"> Balance</th>
                                </tr>
                                <tr id="previous_tr" class="bg-success" style="display: none">
                                    <td colspan="4">Previous Balance</td>
                                    <td id="prevBalance"></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script>
        function search() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var account_id = $("#account_id").val();
            var x = 1;
            if (start_date !== '' && end_date !== '' && account_id !== '') {
                var searchable = [];
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    }
                });
                var dTable = $('#table').DataTable({
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    processing: true,
                    serverSide: true,
                    "bDestroy": true,
                    language: {
                        processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                    },
                    "bDestroy": true,
                    ajax: {
                        url: "{{route('admin.account.statement.data')}}",
                        type: "POST",
                        data: {
                            'start_date': start_date,
                            'end_date': end_date,
                            'account_id': account_id,
                        },
                    },
                    columns: [
                        // { "render": function () {return x++; } },
                        {data: 'transaction_date', name:'transaction_date', orderable: true, searchable: true},
                        { data: 'details',name: 'details', orderable: true, searchable: true},
                        { data: 'debit',name: 'debit', orderable: true, searchable: true,class: "text-end"},
                        { data: 'credit',name: 'credit', orderable: true, searchable: true,class: "text-end"},
                        { data: 'balance', name: 'balance', orderable: true, searchable: true,class: "text-end"},
                    ],
                    order: [[0, 'asc']],
                    initComplete: function (data) {
                        var prevBalance = data.json.prevBalance;
                        $('#previous_tr').show()
                        document.getElementById('prevBalance').innerHTML = prevBalance;
                    },
                });
            }
            else {
                alert('Enter All Value')
            }
        }
        $('#account_id').select2({
            ajax: {
                url: '{{route('admin.account.statement.account.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            var type = '';
                            if(item.type == 1){
                                 type = 'Cash Account'
                            }
                            else{
                                type = 'Bank Account'
                            }
                            return {
                                text: item.name+'-'+type+ ' - ' + item.account_number ,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

    </script>
@endpush
