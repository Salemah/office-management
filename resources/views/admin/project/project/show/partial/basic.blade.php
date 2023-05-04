<form action="{{ route('admin.projects.update',$project->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_code"><b>Project Code</b><span class="text-danger">*</span></label>
              <input type="text" name="project_code" id="project_code"class="form-control @error('project_code') is-invalid @enderror"value="{{$project->project_code}}" placeholder="Enter Project Code" readonly>
              @error('project_code')
                  <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_title"><b>Project Title</b></label>
              <input type="text" name="project_title" id="project_title"class="form-control @error('project_title') is-invalid @enderror"value="{{$project->project_title}}" placeholder="Enter Project Title">
              @error('project_title')
                  <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_category"><b>Project Category</b><span class="text-danger">*</span></label>
              <select name="project_category" id="project_category"class="form-select @error('project_category') is-invalid @enderror">
                  <option value="" >--Select Project Category--</option>
                    @foreach($projectCategory as $category)
                        <option value="{{$category->id}}"@if($category->id == $project->project_category) {{ "selected" }}@endif>
                            {{$category->name}}
                        </option>
                    @endforeach
              </select>
              @error('project_category')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_type"><b>Project Type</b><span class="text-danger">*</span></label>
              <select name="project_type" id="project_type"class="form-select @error('project_type') is-invalid @enderror" onclick="projectType()">
                  <option value="" selected>--Select Project Type--</option>
                  <option value="1" {{$project->project_type == 1 ? 'selected' : ''}} >Own Project </option>
                  <option value="2" {{$project->project_type == 2 ? 'selected' : ''}}>Client Project</option>
                  <option value="3" {{$project->project_type == 3 ? 'selected' : ''}}>Public Project </option>
              </select>
              @error('project_type')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2 client" @if($project->project_type != 2 ) style="display:none"  @endif>
              <label for="client"><b>Client</b></label>
              <select name="client" id="client" class="form-select @error('client') is-invalid @enderror" >
                  <option value="">--Select Client--</option>
                @if ($project->project_type == 2)
                    <option value="{{$clients->id}}"@if($clients->id == $project->client_id)  selected @endif>
                        {{$clients->name}}
                    </option>
                @endif
              </select>
              @error('client')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_priority"><b>Project Priority</b><span class="text-danger">*</span></label>
              <select name="project_priority" id="project_priority" class="form-select @error('project_priority') is-invalid @enderror">
                  <option value="" >--Select Project Priority--</option>
                    @foreach ($priorities as $key => $priority )
                        <option value="{{$priority->id}}" {{$priority->id ==$project->project_priority ? 'selected' : '' }}>{{$priority->name}}</option>
                    @endforeach
              </select>
              @error('project_priority')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                <label for="project_date"><b>Project Date</b><span class="text-danger">*</span></label>
                <input type="date" name="project_date" id="project_date" class="form-control" value="{{$project->project_date}}" placeholder="Invoice Date" required>
                @error('project_date')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                <label for="amount"> <b>Project Amount </b><span class="text-danger">*</span></label>
                <input type="number"  id="amount" step="any" value="{{$budget->amount}}" class="form-control @error('amount') is-invalid @enderror" name="amount" placeholder="Enter Amount" >
                @error('amount')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                <label for="amount_type"> <b> Amount Type</b></label>
                <select name="amount_type" id="amount_type" class="form-select" onchange="expenseTransactionWay()">
                    <option value=""  selected>--Select Amount Type--</option>
                    <option value="1" {{$transaction->amount_type == 1 ? 'selected' : ''}} >Full</option>
                    <option value="2" {{$transaction->amount_type == 2 ? 'selected' : ''}}>Partial</option>
                </select>
                @error('amount_type')
                    <span class="alert text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                <label for="receipt-amount"> <b>Receipt Amount </b><span class="text-danger">*</span></label>
                <input type="number"  id="receipt-amount" value="{{$transaction->amount}}" class="form-control " name="receipt_amount" placeholder="Enter Receipt Amount" onkeyup="checkAmount()" >
                @error('receipt_amount')
                <span class="alert text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                <label for="revenue_invoice_no"><b>Recive By</b><span class="text-danger">*</span></label>
                <select name="receive_by_id" id="receive_by_id"class="form-control select2" >
                    <option>--Select Employee--</option>
                    <option value="{{ $employee->id}}"@if( $employee->id ==  $transaction->received_by)  selected @endif>
                        {{ $employee->name}}
                    </option>
                </select>
                @error('receive_by_id')
                    <span class="text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 transaction" >
                <label for="transaction_way"><b>Transaction Type</b></label>
                <select name="transaction_way" id="transaction_way" class="form-control"
                        onchange="expenseTransactionWay()">
                    <option value="" selected>-- Select --</option>
                    <option value="1" {{$transaction->transaction_account_type == 1 ? 'selected' : '' }}>Cash</option>
                    <option value="2" {{$transaction->transaction_account_type == 2 ? 'selected' : ''}}>Bank</option>
                </select>
                @if ($errors->has('transaction_way'))
                    <span class="alert text-danger">
                        {{ $errors->first('transaction_way') }}
                    </span>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash" @if($transaction->transaction_account_type == 2) style="display: none" @endif>
                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                    style="display: none"></span></label>
                <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                    <option  value=""  selected>--Select Cash Account--</option>
                    @foreach ($cash_account as $account)
                        <option value="{{$account->id}}" {{$transaction->account_id == $account->id ? 'selected' : ''}} >{{$account->name}}</option>
                    @endforeach
                </select>
                    @error('cash_account_id')
                        <span class="text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way  "
            @if($transaction->transaction_account_type == 1) style="display: none" @endif>
                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="balance"
                    style="display: none"></span></label>
                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                    <option value="" selected>-- Select Bank Account --</option>
                    @foreach ($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}" {{$transaction->account_id == $bankAccount->id ? 'selected' : ''}}>{{ $bankAccount->name }}
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
            @if($transaction->transaction_account_type == 1) style="display: none" @endif>
                <label for="cheque_number"><b>Cheque Number/Transaction</b></label>
                <input type="text" name="cheque_number" id="cheque_number"
                    class="form-control" value="{{$transaction->cheque_number}}" placeholder=" ...">
                @if ($errors->has('cheque_number'))
                    <span class="alert text-danger">
                        {{ $errors->first('cheque_number') }}
                    </span>
                @endif
            </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="status"><b>Status</b><span class="text-danger">*</span></label>
              <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                  <option>--Select Status--</option>
                  <option value="1" {{$project->status == 1 ? 'selected' : ''}} >Up Coming</option>
                  <option value="2" {{$project->status == 2 ? 'selected' : ''}}>On Going</option>
                  <option value="3" {{$project->status == 3 ? 'selected' : ''}}>Complete</option>
                  <option value="4" {{$project->status == 4 ? 'selected' : ''}}>Cancel</option>
              </select>
              @error('status')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 mb-2">
              <label for="description"><b>Description</b></label>
              <textarea name="description" id="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                         placeholder="Description...">{{$project->description}}</textarea>
              @error('description')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group">
              <button type="submit" class="btn btn-sm btn-primary">Submit</button>
          </div>
      </div>


</form>
<div class="mb-5"></div>
@push('script')
    <script>
        ckEditor('description');
        $('#project_category').select2({
            ajax: {
                url: '{{route('admin.projects.category.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
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
        $('#client').select2({
            placeholder:'Select Client',
                ajax: {
                    url: '{{route('admin.crm.client.search.all')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
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
        $('#receive_by_id').select2({
                ajax: {
                    url: '{{route('admin.expense.employee.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
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
         //document append
         $(document).ready(function () {
            var wrapper = $(".documentRow");
            var x = 0;
            $("#add_document").click(function () {
                    x++;
                    $(wrapper).append('<div class="row mt-2 document-table-tr" id="document-table-tr-' + x + '">' +
                                            '<div class="col-sm-6 mb-2 document">'+
                                                ' <div class="form-group">'+
                                                        '<input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >'+
                                                        '@error("document_title")'+
                                                            '<span class="text-danger" role="alert">'+
                                                            ' <p>{{ $message }}</p>'+
                                                            '</span>'+
                                                    ' @enderror'+
                                                    '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-5 mb-2">'+
                                                '<div class="form-group">'+
                                                    '<input type="file" id="document" data-height="25"data-default-file=""class="dropify form-control " name="documents[]">'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-1 ">' +
                                                '<button type="button"  class=" btn btn-sm btn-danger " onclick="documentRemove(' + x + ')">' +
                                                'X' +
                                                '</button>' +
                                            '</div>'+
                                        '</div>' );
                                        $('.dropify').dropify();

            });
        });

        function projectType() {
            var projectType = $("#project_type").val();
            if (projectType == 2) {
                $('.client').show();
            } else {
                $('.client').hide();
                $('#client').val('');
            }
        };

    </script>
@endpush
