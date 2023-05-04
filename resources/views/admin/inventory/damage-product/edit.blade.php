@extends('layouts.dashboard.app')

@section('title', 'Edit Damage Product')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
        integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;

        }
        .select2-container--default .select2-selection--single {
            padding: 6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
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
                 <a href="{{route('admin.inventory.damage-product.index')}}">Damage Product </a>
            </li>
            <li class="breadcrumb-item">
                 Edit
            </li>
        </ol>
        <a href="{{ route('admin.inventory.damage-product.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.damage-product.update',$prodduct->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="date"><b>Date</b><span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('datee') is-invalid @enderror"value="{{$prodduct->date}}" name="date" id="datepicker">
                                @error('date')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                                @php
                                    $auth = Auth::user();
                                     $user_role = $auth->roles->first();
                                @endphp
                                @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                                    <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror">
                                        <option value="" selected >--Select warehouse_id--</option>
                                        @forelse ($warehouses as $item)
                                            <option value="{{ $item->id }} " {{$prodduct->warehouse_id == $item->id ?'selected':''}}>
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Warehouse</option>
                                        @endforelse
                                    </select>

                                @else
                                <input type="text" name="warehouse_id_name" id="warehouse_id_name"  class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                                <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
                                @endif
                                @error('warehouse_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="type"><b>Type</b><span class="text-danger">*</span></label>
                                <select name="type" id="type"class="form-select @error('type') is-invalid @enderror">
                                    <option value="" selected >--Select Type--</option>
                                    <option value="2"{{$prodduct->product_type == 2 ? 'selected' : ''}}>Broken</option>
                                    <option value="3" {{$prodduct->product_type == 3 ? 'selected' : ''}}> Expired</option>
                                    <option value="4" {{$prodduct->product_type == 4 ? 'selected' : ''}}>Samply</option>
                                </select>
                                @error('type')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="name"><b>Product</b><span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id"class="form-select @error('product_id') is-invalid @enderror">
                                    <option value="" selected >--Select Product--</option>
                                    @foreach ($products as $key=>$productt)
                                        @if($productt->is_variant)
                                            @foreach ($productt->productVarients as $varient )
                                                <option value="{{$varient->id}},{{$productt->id}}"@if($productt->id == $prodduct->product_id && $varient->id == $prodduct->variant_id ) selected  @endif >
                                                    {{$varient->value}} {{$varient->varients->name}} | {{$productt->code }} | {{$productt->name}}
                                                </option>
                                            @endforeach
                                        @else
                                                <option value="{{$productt->id}}">
                                                    {{$productt->name}}
                                                  </option>
                                         @endif

                                    @endforeach
                                </select>
                                @error('name')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="quantity"><b>Quantity</b><span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="qty" class="form-control" value="{{$prodduct->stock_out}}" oninput="qtyCheck()" placeholder="0.00" step="any">
                                <input type="hidden" name="product_qty" id="product_qty" class="form-control" value="" placeholder="0.00" step="any">
                                <input type="hidden" name="customer_id" id="customer_id" class="form-control" value="" placeholder="0.00" step="any">
                                <input type="hidden" name="price" id="price" class="form-control" value="{{$prodduct->net_unit_cost}}" placeholder="0.00" step="any">
                                <input type="hidden" name="subtotal" id="subtotal" class="form-control" value="{{$prodduct->total}}" placeholder="0.00" step="any">
                                    @error('quantity')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="quantity"><b>Note</b></label>
                                <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                placeholder="Description...">{{$prodduct->note}}</textarea>
                                    @error('note')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="mb-5"></div>


@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
            $("#variant-section").hide();
            $("#diffPrice-section").hide();
        });
        var x = 0;
        $('#product_id').select2({
            ajax: {
                url: '{{route('admin.inventory.damage-product.product.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    console.log(data);
                    return {
                        results: $.map(data, function (item,i) {
                               if(Array.isArray(item)){
                                if (item[0].variant_id) {
                                    var p =  item[0].variant.value +' '+item[0].variant.name+'-'+ item[0].products.name;
                                    var v =  item[0].variant.id +','+item[0].products.id;
                                    return {
                                            text: p,
                                            value: v,
                                            id: v,
                                            }
                                    }
                                    else{
                                        return {
                                            text:  item.products.name,
                                            value: item.products.id,
                                            id: item.products.id,
                                        }
                                }

                            }
                            else{
                                    return {
                                        text:  item.products.name,
                                        value: item.products.id,
                                        id: item.products.id,
                                    }

                                }
                        })
                    };
                }
            }
        });
        $(document).on('change', '#product_id', function () {
            var id = $("#product_id").val();
            var customerId = $("#customer_id").val();
            var vairants = id.split(",");
            var vairant = '';
            if(vairants[1]){
                vairant = vairants[0];
                id = vairants[1];
            }
            else{
                 vairant = '';
            }
            var url = '{{ route('admin.inventory.sale.product.data',':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                data: {
                            data: vairant,
                            customerId:customerId
                        },
                dataType: "json",
                success: function ( data) {
                    console.log(data)
                    x++;
                    var read = 'readonly';
                    var variantData = '';
                    var netUnitCost = '';
                    if(data[0].is_batch){
                        read = '';
                    }
                    if(data[2].value){
                        variantData =  data[2].value+ data[2].name+'-';
                    }
                    if(data[4]){
                        netUnitCost = data[4].net_unit_cost;
                    }
                    else{
                        netUnitCost = data[2].net_unit_cost;
                    }
                    $('#product_data').append('<tr id="responsive-table-tr">'+
                        '<input type="hidden" class="purchase-unit" name="purchase_unit[]" value="' +data[0].purchase_unit_id   + '">'+
                        '<td ><input type="hidden" name="product[]" id="product_id-' + x + '" value="' +data[0].id+'" class="form-control" >' +variantData+data[0].name+'</td>'+
                        '<td>'+data[0].code+'</td>'+
                        '<td><input type="number" id= "qty-'+x+'" oninput="qtyCheck('+x+')" name="qty[]" class="form-control" value="0"></td>'+
                        '<td class="recieved-product-qty d-none">Recieved</td>'+

                            '<td><input type="text" name="batch_no[]" class="form-control"   '+read+ ' value="" ></td>'+
                            '<td><input type="date" name="expired_date[]" class="form-control"  '+read+ '  ></td>'+

                        '<td><input type="text" name="price[]" value="'+data[1].price+'" id="price-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="text" name="discount[]" value="" id="discount-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="text" name="tax[]" value="" id="price-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="text" name="tax[]" value="'+netUnitCost+'" id="price-' + x + '" class="form-control"   readonly ></td>'+
                        '<td><input type="number" name="subtotal[]" id="subtotal-' + x + '" class="form-control" value="" readonly></td>'+
                        '<td><input type="hidden" name="variant[]"  id="variant-' + x + '" value="'+vairant+'" class="form-control"   readonly ><button type="button"  class="jDeleteRow form-control btn btn-danger btn-icon waves-effect waves-light text-white" onclick="expensesRemove(' + x + ')">' +
                        '&times;' +
                        '</button></td>'+
                        '<input type="hidden" name="product_qty[]"  id="product_qty-' + x + '" value="" class="form-control"   readonly >'+
                        '</tr>');

                        $('#price').val(data[1].price);
                        // $.ajax({
                        //     type: "GET",
                        //     url: '{{route('admin.inventory.sale.quantity.search')}}',
                        //     data: {
                        //             productId: id,
                        //             varId :vairant,
                        //         },
                        //     success: function ( data) {
                        //         $('#product_qty-'+x).val(data);
                        //     },
                        //     error: function () {
                        //     // location.reload();
                        //     }
                        // });
                },
                error: function () {
                   // location.reload();
                }
            });
        });
        function qtyCheck(){
            var totalQty = 0;
            var subtotal = 0;
             var qty = $('#qty').val();
             var productId = $('#product_id').val();
            // alert(productId);
                var productQty = $('#product_qty').val();

                var vairants = productId.split(",");

                var vairant = '';
                if(vairants[1]){
                    vairant = vairants[0];
                    id = vairants[1];
                }
                else{
                    vairant = '';
                }
            if(qty>0){
                $.ajax({
                        type: "GET",
                        url: '{{route('admin.inventory.sale.quantity.search')}}',
                        data: {
                                productId: id,
                                varId :vairant,
                            },
                            success: function ( data) {
                                console.log(data);
                            $('#product_qty').val(data);
                        },
                        error: function () {
                         // location.reload();
                        }
                        });
                if(parseFloat(qty)>parseFloat(productQty)){
                    alert('quantity exceeded maximam limit');
                    $('#qty').val(0)
                }
                checkAmount();
            }
            else{
                $('#qty').val(0);
                alert("Quantity Can't Negetive")
            }
        }
        function checkAmount(){
               var totalQty = 0;
                var subtotal = 0;
                    var quantity = $('#qty').val() ;
                    var totalprice = $('#price').val();
                    if(!quantity){
                        quantity = 0;
                    }
                    if(!totalprice){
                        totalprice = 0;
                    }
                    var subtotalprice = parseFloat(quantity)* parseFloat(totalprice);
                    subtotal =  subtotal + subtotalprice;

                    $('#subtotal').val(subtotalprice);
                    totalQty = parseFloat(totalQty)  + parseFloat(quantity);
                    $('#totalQty').val(totalQty);
                    $('#total-val').val(subtotal);
        }
    </script>
@endpush
