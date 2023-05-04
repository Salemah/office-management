@extends('layouts.dashboard.app')

@section('title', 'Investment')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
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
    </style>

@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                <a href="{{ route('admin.investment.index') }}">Investment </a>
            </li>
        </ol>
        <a href="{{ route('admin.investment.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <form action="{{ route('admin.investment.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
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
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="investor_id"><b>Select Investor</b><span class="text-danger">*</span></label>
                                    <select name="investor_id" id="investor_id" class="form-control">
                                        <option value="" selected>-- Select --</option>
                                        @foreach($investors as $investor)
                                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('investor_id'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('investor_id') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_title"><b>Investment Title</b><span class="text-danger">*</span></label>
                                    <input type="text" name="transaction_title" id="transaction_title" class="form-control"value="{{ old('transaction_title') }}" placeholder="Enter Transaction_title...">
                                    @if ($errors->has('transaction_title'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_title') }}
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <div class="">
                                    <label for="negotiator"><b>Negotiator</b><span class="text-danger">*</span></label>
                                    <select name="negotiator_id" id="negotiator_id"class="form-control select2" style="min-height:30px" >
                                        <option>--Select Negotiator--</option>
                                    </select>
                                    @error('negotiator_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
                                <select name="transaction_way" id="transaction_way" class="form-control"
                                onchange="expenseTransactionWay()">
                                    <option value="" selected>-- Select --</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Bank</option>
                                </select>
                                    @if ($errors->has('transaction_way'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_way') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash" style="display: none">
                                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                                    style="display: none"></span></label>
                                <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                    <option  value=""  selected>--Select Cash Account--</option>
                                    @foreach ($cash_account as $account)
                                         <option value="{{$account->id}}" >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('cash_account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way  "
                            style="display: none">
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="balance"
                                    style="display: none"></span></label>
                                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                            | {{ $bankAccount->account_number }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('account_id'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('account_id') }}
                                    </span>
                                @endif
                            </div>
                       <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way "
                            style="display: none">
                           <label for="cheque_number"><b>Cheque Number / Transaction</b><span class="text-danger">*</span></label>
                           <input type="text" name="cheque_number" id="cheque_number"
                                  class="form-control" value="{{ old('cheque_number') }}" placeholder=" ...">
                           @if ($errors->has('cheque_number'))
                               <span class="alert text-danger">
                                   {{ $errors->first('cheque_number') }}
                               </span>
                           @endif
                       </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{ old('transaction_date') }}">
                                    @if ($errors->has('transaction_date'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_date') }}
                                        </span>
                                    @endif
                            </div>


                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="amount_balance"><b>Amount</b><span class="text-danger">*</span></label>
                                <input type="hidden" value="" id="amount_balance">
                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} has-feedback">
                                    <input type="number" name="amount" id="amount" class="form-control"
                                           value="{{ old('amount') }}" placeholder="1200 ...">
                                    @if ($errors->has('amount'))
                                        <span class="alert text-danger">
                                           {{ $errors->first('amount') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="note"><b>Note</b></label>
                                <textarea name="note" id="note" rows="3" class="form-control " value="{{ old('note') }}"placeholder="Enter Note..."></textarea>
                                    @if ($errors->has('note'))
                                        <span class="alert text-danger">
                                           {{ $errors->first('note') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>


@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    CKEDITOR.replace('note', {
            toolbarGroups: [
                {"name": "styles","groups": ["styles"]},
                {"name": "basicstyles","groups": ["basicstyles"]},
                {"name": "paragraph","groups": ["list", "blocks"]},
                {"name": "document","groups": ["mode"]},
                {"name": "links","groups": ["links"]},
                {"name": "insert","groups": ["insert"]},
                {"name": "undo","groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
    });
        $('#negotiator_id').select2({
                    height:30,
                    ajax: {
                        url: '{{route('admin.investment.employee.search')}}',
                        dataType: 'json',
                        type: "POST",
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public',
                                warehouse_id: $('#warehouse_id').val(),
                            }
                            return query;
                        },
                        processResults: function (data) {
                            console.log();
                            // Transforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.name,
                                        value: item.id,
                                        id: item.id,
                                    }
                                })
                            };
                        }
                    }
        });
        $('#investor_id').select2({
            ajax: {
                url: '{{route('admin.investment.investor.search')}}',
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
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        function expenseTransactionWay() {
            var transaction_way = $("#transaction_way").val();
            if (transaction_way == 2) {
                $('.bank-way').show();
                $('#cash_account_id').val('');
                $('.cash').hide();
            } else if(transaction_way == 1) {
                 $('.bank-way').hide();
                 $('#account_id').val('');
                 $('.cash').show();
            }
             else{
                $('.bank-way').hide();
                $('.cash').hide();
                $('#account_id').val('');
             }
        };
        $('#account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                        transaction_way : $("#transaction_way").val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });
        $('#cash_account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                       transaction_way : $("#transaction_way").val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
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
