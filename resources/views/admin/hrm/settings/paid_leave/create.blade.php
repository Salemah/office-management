@extends('layouts.dashboard.app')

@section('title', 'Create Holiday')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
             <a href="{{route('admin.hrm.paid-leave.index')}}">Paid Leave</a>
            </li>
        </ol>
        <a href="{{ route('admin.hrm.paid-leave.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.hrm.paid-leave.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                     <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="days"><b>Days</b><span class="text-danger">*</span></label>
                                <input type="text" name="days" id="days"
                                    class="form-control @error('days') is-invalid @enderror"
                                    value="{{ old('days') }}" placeholder="Enter Days">
                                @error('days')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="paid_per_day"><b>Paid Per Day</b><span class="text-danger">*</span></label>
                                <input type="text" name="paid_per_day" id="paid_per_day"
                                    class="form-control @error('paid_per_day') is-invalid @enderror"
                                    value="{{ old('paid_per_day') }}" placeholder="Paid Per Day">
                                @error('paid_per_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="total_amount"><b>Total Amount</b><span class="text-danger">*</span></label>
                                <input type="text" name="total_amount" id="total_amount"
                                    class="form-control @error('total_amount') is-invalid @enderror"
                                    value="{{ old('total_amount') }}" placeholder="Total Amount">
                                @error('total_amount')
                                    <span class="invalid-feedback" role="alert">
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
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>

        $('.single_date').hide();
        $('.start_date').hide();
        $('.end_date').hide();

        $('#type').on('change', function() {
            let type = $(this).val();
            if(type == 1){
                $('.start_date').hide();
                $('.end_date').hide();
                $('.single_date').show();
            }

            if(type == 2){
                $('.single_date').hide();
                $('.start_date').show();
                $('.end_date').show();

                var startDate = new Date($('.start_date').val());
                var endDate   = new Date($('.end_date').val());

                if (startDate < endDate){
                    console.log('Start must be less than the end date!!!');
                }
            }

        });


        CKEDITOR.replace('description', {
            toolbarGroups: [{
                    "name": "styles",
                    "groups": ["styles"]
                },
                {
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                },

                {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                },
                {
                    "name": "document",
                    "groups": ["mode"]
                },
                {
                    "name": "links",
                    "groups": ["links"]
                },
                {
                    "name": "insert",
                    "groups": ["insert"]
                },

                {
                    "name": "undo",
                    "groups": ["undo"]
                },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>
<script>
    $(document).ready(function(){
      $("#paid_per_day").keyup(function(){
       var paid_per_day = $("#paid_per_day").val();
       var days = $("#days").val();
       var totalAmount = parseFloat(paid_per_day) * parseFloat(days)
       $("#total_amount").val(totalAmount);
      });
    });
    </script>
@endpush
