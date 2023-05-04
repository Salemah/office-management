@extends('layouts.dashboard.app')

@section('title', 'Create Product')

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
                 <a href="{{route('admin.inventory.product.index')}}">Product </a>
            </li>
            <li class="breadcrumb-item">
                 <a href="{{route('admin.inventory.product.index')}}">Product </a>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.product.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.product.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="name"><b>Name</b><span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"value="{{ old('name') }}"placeholder="Enter Product  Name">
                                    @error('name')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="product_code"><b>Product Code</b><span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-text">
                                            <button id="genbutton" type="button" class="btn btn-sm btn-default" title="Generate"><i class="fas fa-sync-alt" ></i></button>
                                        </div>
                                        <input type="text" name="product_code" id="product_code"class="form-control @error('product_code') is-invalid @enderror"value="{{ old('product_code') }}"placeholder="Enter Product Category Code">
                                      </div>
                                    @error('product_code')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="brand_id"><b>Brand</b></label>
                                    <select name="brand_id" id="brand_id"class="form-select @error('brand_id') is-invalid @enderror">
                                        <option value="" selected >--Select brand_id--</option>
                                        {{-- @forelse ($brands as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Brand</option>
                                        @endforelse --}}
                                    </select>
                                    @error('brand_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="category_id"><b>Select  Category</b><span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="" selected>--Select Category--</option>
                                        @forelse ($productCategories as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Prodcut Category</option>
                                        @endforelse
                                    </select>
                                        @error('category_id')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="unit_id"><b>Select Product Unit</b> <span class="text-danger">*</span></label>
                                    <select name="unit_id" id="unit_id"class="form-select @error('unit_id') is-invalid @enderror">
                                            <option value="" selected>--Select Unit--</option>
                                            @forelse ($units as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @empty
                                                <option value="">No Unit</option>
                                            @endforelse
                                    </select>
                                        @error('unit_id')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="sale_unit_id"><b>Select  Sale Unit </b></label>
                                    <select name="sale_unit_id" id="sale_unit_id"class="form-select @error('sale_unit_id') is-invalid @enderror">
                                            <option value="" selected>--Select Category--</option>
                                    </select>
                                        @error('sale_unit_id')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="purchase_unit_id"><b>Select Purchase Unit</b></label>
                                    <select name="purchase_unit_id" id="purchase_unit_id"class="form-select @error('purchase_unit_id') is-invalid @enderror">
                                            <option value="" selected>--Select Category--</option>
                                            @forelse ($units as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @empty
                                                <option value="">No Unit</option>
                                            @endforelse
                                    </select>
                                        @error('purchase_unit_id')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="alert_quantity"><b>Alert Quantity</b><span class="text-danger">*</span></label>
                                    <input type="number" name="alert_quantity" class="form-control" step="any">
                                        @error('alert_quantity')
                                            <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><b>Product Tax</b> </label>
                                        <select name="tax_id" id="tax_id" class="form-control selectpicker">
                                            <option value="" selected>No Tax</option>
                                            @foreach($taxs as $tax)
                                                <option value="{{$tax->id}}">{{$tax->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><b> Tax Method</b> </label> <i class="dripicons-question" data-toggle="tooltip" title=""></i>
                                        <select name="tax_method" class="form-control selectpicker">
                                            <option value="" selected>Select</option>
                                            <option value="1">Exclusive</option>
                                            <option value="2">Inclusive</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="image"><b>Upload  Image</b> <span class="text-danger">*</span></label>
                            <input type="file" id="image" data-height="100" name="image"class="dropify form-control @error('image') is-invalid @enderror" >
                            @error('image')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                    placeholder="Description..."></textarea>
                                @error('description')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-12 mt-3" id="variant-option">
                                <h5><input name="is_variant" type="checkbox" id="is-variant" value="1">This product has variant</h5>
                            </div>
                            <div class="row">
                                <div id="variant-section">
                                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Variant Name</b></label>
                                        <input type="text" name="option[]" class="form-control" placeholder="Enter Vairant Nasme ">
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Value</b></label>
                                        <input type="text" name="value[]" class="form-control" placeholder="Enter variant Value ">
                                    </div>
                                    {{-- <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Additional Cost</b></label>
                                        <input type="number" name="additional_cost[]" class="form-control" placeholder="Enter Additional Cost " step="any">
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Additional Cost</b></label>
                                        <input type="number" name="additional_price[]" class="form-control" placeholder="Enter Additional Price " step="any">
                                    </div> --}}
                                    {{-- append Here --}}
                                    <div class="variant-append">
                                    </div>
                                    {{-- end --}}
                                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <button id="addRow" type="button" class="btn btn-sm btn-primary">Add</button>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3" id="batch-option">
                                    <h5><input name="is_batch" type="checkbox" id="is-batch" value="1">This product has batch and expired date</h5>
                                </div>
                                {{-- <div class="col-md-12 mt-2" id="diffPrice-option">
                                    <h5><input name="is_diffPrice" type="checkbox" id="is-diffPrice" value="1">This product has different price for different warehouse</h5>
                                </div>
                                <div class="col-md-6" id="diffPrice-section">
                                    <div class="table-responsive ml-2">
                                        <table id="diffPrice-table" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Warehouse</th>
                                                    <th>Price</th>
                                                </tr>
                                                @foreach($warehouse_list as $warehouse)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="warehouse_id[]" value="{{$warehouse->id}}">
                                                        {{$warehouse->name}}
                                                    </td>
                                                    <td><input type="number" name="diff_price[]" class="form-control"></td>
                                                </tr>
                                                @endforeach
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> --}}
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
        $('#genbutton').on("click", function() {
            var url = '{{ route('admin.inventory.products.gencode')}}';
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(data) {
                        console.log(data);
                        $("input[name='product_code']").val(data);
                    }
                });
        });
         // client_typeData
         $(document).on('change', '#unit_id', function () {
            var id = $("#unit_id").val();
            var url = '{{ route('admin.inventory.products.unit.search', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                dataType: "json",
                success: function ( data) {
                    $('select[name="sale_unit_id"]').empty();
                    $('select[name="purchase_unit_id"]').empty();
                  $.each(data, function(key, value) {
                      $('select[name="sale_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                      $('select[name="purchase_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                  });

                },
                error: function () {
                    location.reload();
                }
            });
        });
        $('#tax_id').select2();
        $('#brand_id').select2({
            ajax: {
                url: '{{route('admin.inventory.products.brand.search')}}',
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
        $("input[name='is_variant']").on("change", function () {
                if ($(this).is(':checked')) {
                    $("#variant-section").show(300);
                    $("#batch-option").hide(300);
                }
                else {
                    $("#variant-section").hide(300);
                    $("#batch-option").show(300);
                }
             });
             // Row Append


    var max_field = 5;
    var wrapper = $(".variant-append");
    var x = 0;
    $("#addRow").click(function () {
        if (x < max_field) {
            x++;
            $(wrapper).append(`<div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Variant Name</b></label>
                                        <input type="text" name="option[]" class="form-control" placeholder="Enter Vairant Nasme ">
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                        <label for="option"><b>Value</b></label>
                                        <input type="text" name="value[]" class="form-control" placeholder="Enter variant Value ">
                                    </div>`);
        } else {
            alert('you can not add more than 5 field');
        }
    });
    $("input[name='is_diffPrice']").on("change", function () {
        if ($(this).is(':checked')) {
            $("#diffPrice-section").show(300);
        }
        else
            $("#diffPrice-section").hide(300);
    });
    </script>
@endpush
