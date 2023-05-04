@extends('layouts.dashboard.app')

@section('title', 'Edit Bank Account')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
             <a href="{{route('admin.account.bank-account.index')}}">Bank Account </a>
            </li>
            <li class="breadcrumb-item">
             <a href="{{route('admin.account.bank-account.index')}}">Edit</a>
            </li>
        </ol>
        <a href="{{ route('admin.account.bank-account.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.crm.client.bank.account.update',['id' => $bankAccount->id, 'parameter' => $parameter] )}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                     <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="warehouse"> <b>Warehouse</b><span class="text-danger">*</span></label>
                            <select name="warehouse" id="warehouse" class="form-select @error('warehouse') is-invalid @enderror" onchange="checkType()">
                                <option value="" selected >--Select Warehouse--</option>
                                @foreach($warehouses as $warehouse)
                                   <option value="{{$warehouse->id}}" {{$bankAccount->warehouse == $warehouse->id  ?'selected' : '' }} >{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                            @error('warehouse')
                            <span class="invalid-feedback" role="alert">
                                   <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="type"> <b>Account Type</b><span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" onchange="checkType()">
                                <option value="" >--Select Account Type--</option>
                                <option value="1" {{$bankAccount->type == 1 ? 'selected' : ''}}  >Cash</option>
                                <option value="2"  {{$bankAccount->type == 2 ? 'selected' : ''}} > Bank</option>
                            </select>
                            @error('bank_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank" @if ($bankAccount->type == 1) style="display:none" @endif>
                            <label for="bank_id"><b>Bank Name</b><span class="text-danger">*</span></label>
                            <select name="bank_id" id="bank_id"
                                    class="form-select @error('bank_id') is-invalid @enderror">
                                <option >--Select Bank--</option>
                                @foreach ($banks as $bank)
                                <option value="{{$bank->id}}" {{$bankAccount->bank_id == $bank->id ?'selected' : ' '}} >{{$bank->bank_name}}</option>
                                @endforeach
                            </select>
                            @error('bank_id')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank" @if ($bankAccount->type == 1) style="display:none" @endif>
                                <label for="branch_name"><b>Bank Branch</b><span class="text-danger">*</span></label>
                                <input type="text" name="branch_name" id="branch_name"
                                    class="form-control @error('branch_name') is-invalid @enderror" value="{{$bankAccount->branch_name }}"
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
                                    class="form-control @error('account_name') is-invalid @enderror" value="{{$bankAccount->name}}"
                                    placeholder="Enter Account Name">
                                @error('account_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="initial_balance"><b>Initial Balance</b><span class="text-danger">*</span></label>
                                <input type="text" name="initial_balance" id="initial_balance"
                                    class="form-control @error('initial_balance') is-invalid @enderror" value="{{$bankAccount->initial_balance }}"
                                    placeholder="Enter Initial Balance">
                                @error('initial_balance')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="account_number"><b>Account Number</b><span class="text-danger">*</span></label>
                                <input type="text" name="account_number" id="account_number"
                                    class="form-control @error('account_number') is-invalid @enderror" value="{{$bankAccount->account_number}}"
                                    placeholder="Enter Account Number">
                                @error('account_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="routing_no"><b>Routing Number</b></label>
                                <input type="text" name="routing_no" id="routing_no"
                                    class="form-control @error('routing_no') is-invalid @enderror" value="{{$bankAccount->routing_no}}"
                                    placeholder="Enter Routing Number">
                                @error('routing_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                             <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option >--Select Status--</option>
                                    <option value="1" {{$bankAccount->status == 1 ?'selected' : ' '}}>Active</option>
                                    <option value="0" {{$bankAccount->status == 0 ?'selected' : ' '}}>In-active</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Description...">{{$bankAccount->note}}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @if($Client != null)
                                <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
                            @endif
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        CKEDITOR.replace('description', {
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
        function checkType(){
            var type = $("#type").val();
            if(type == 2){
                $(".bank").show();
            }
            else if(type == 1){
                $(".bank").hide();
            }
        }
    </script>
@endpush
