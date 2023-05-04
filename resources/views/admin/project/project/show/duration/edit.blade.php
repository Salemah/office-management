@extends('layouts.dashboard.app')

@section('title', 'Edit Duration')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}">Duration</a>
            </li>
            <li class="breadcrumb-item">
                Edit
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.project.duration.update',$project->id)}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="project_end_date_initial" id="project_end_date_initial" @if ($project_duration > 0) value="{{$projectInitial->end_date}}" @endif>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="start_date"> <b>Project Start Date </b><span class="text-danger">*</span></label>
                                <input type="date"  id="start_date" value="{{ $project->start_date,old('start_date')}}" class="form-control " name="start_date" placeholder="Enter Start Date" @if ($project_duration >0)@if ($projectInitial->id != $project->id)  onchange="chechExtendDate()" @else onchange="calculateDay()" @endif   @endif>
                                @error('start_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                 </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="end_date"><b>Project End Date</b><span class="text-danger">*</span></label>
                                <input type="date"  id="end_date" value="{{ $project->end_date,old('start_date')}}" class="form-control " name="end_date"   placeholder="Enter End Date" onchange="calculateDay()">
                                @error('end_date')
                                   <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="total_day"><b>Estimated Day</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="total_day" value="{{ $project->estimate_day,old('total_day')}}" id="total_day" placeholder="Enter Total Day"  min="1">
                                @error('total_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="estimated_hour_day"><b>Estimated Hour Per DAY</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('estimated_hour_day') is-invalid @enderror" name="estimated_hour_day" value="{{$project->estimate_hour_per_day}}" id="estimated_hour_day" placeholder="0.0" onkeyup="getTotalHour()">
                                @error('estimated_hour_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                                <label for="vacation_day"><b>Vacation</b><span class="text-danger">*</span></label>
                                <input type="num" value="{{$project->vacation_day}}"class="form-control @error('vacation_day') is-invalid @enderror" name="vacation_day" id="vacation_day" placeholder="0.0" onkeyup="calculateDay()">
                                @error('vacation_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                                <label for="final_day"><b>Total Day</b><span class="text-danger">*</span></label>
                                <input type="num" value="{{$project->final_day}}" class="form-control @error('final_day') is-invalid @enderror" name="final_day" id="final_day" placeholder="0.0" readonly>
                                @error('final_day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="estimated_hour"><b>Estimated Hour</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('estimated_hour') is-invalid @enderror" name="estimated_hour" value="{{$project->estimate_hour}}" id="estimated_hour" placeholder="0.0" readonly>
                                @error('estimated_hour')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- Adjustment --}}

                                <div class="col-12 mb-2">
                                    <div class="form-group">
                                        <label><b>Adjustment</b></label>
                                        <input class="form-check-input " type="checkbox" id="adjustment-btn" {{$project->adjustment_hour ? 'checked' : ''}}>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group adjustment" @if (!$project->adjustment_hour) style="display: none" @endif >
                                        <label><b>Adjustment Type</b></label>
                                        <select class="form-control" id="adjustment_type" name="adjustment_type"
                                                onchange="adjustmentHourCount()">
                                            <option value="" >--Select--</option>
                                            <option value="1" {{$project->adjustment_type == 1 ? 'selected' : ''}}>Addition</option>
                                            <option value="2" {{$project->adjustment_type == 2 ? 'selected' : ''}}>Subtraction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group adjustment" @if (!$project->adjustment_hour) style="display: none" @endif >
                                        <label><b>Adjustment hour</b></label>
                                        <input type="number" name="adjustment_hour" id="adjustment_hour"
                                               class="form-control " value="{{$project->adjustment_hour}}" placeholder="0"
                                               onkeyup="adjustmentHourCount()">
                                        @error('adjustment_hour')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="final_hour"><b>Final Hour</b><span class="text-danger">*</span></label>
                                        <input type="num" class="form-control @error('final_hour') is-invalid @enderror" name="final_hour" value="{{$project->final_hour}}" id="final_hour" placeholder="0.0" readonly>
                                        @error('final_hour')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="10" cols="40" class="form-control description" value="{{ old('description') }}" placeholder="Description..."></textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            {{--  --}}
                            <input type="hidden" name="project_id" id="project_id" value="{{ $project->duration_type_id}}">
                            <div class="form-group my-3">
                                <button type="submit" class="btn btn-sm btn-primary mb-3">Update</button>
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    CKEDITOR.replace(description,{
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
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
    })
    function chechExtendDate(){
            var date1 = new Date($('#start_date').val());
            var projectEndDate = new Date($('#project_end_date_initial').val());
            if( date1< projectEndDate){
                $('#start_date').val('');
                swal({
                        title: `Please Select Correct Date`,
                        text: date1 + "  Less Than "+ projectEndDate,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#start_date').val('');
                        }
                    });
            }
            calculateDay();
        }


    function calculateDay(){
            var date1 = new Date($('#start_date').val());
            var date2 = new Date($('#end_date').val());
            if(date2 < date1){
                swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "  Less Than "+ date1,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#end_date').val('');
                        }
                    });
            }
            else{
                var vacationDay = $('#vacation_day').val();
                var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
                $('#total_day').val(diffDays);
                if(diffDays<vacationDay){
                    $('#vacation_day').val('');
                    $('#final_day').val(diffDays);

                    swal({
                        title: `Please Write Valid Vacation`,
                        text: `Vacation Extend Total Project Day`,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#vacation_day').val('');
                        }
                    });
                    getTotalHour();
                }
                else{
                    $('#final_day').val(diffDays - vacationDay);
                    getTotalHour();
                }

            }
    }
    $(document).on("click", "#adjustment-btn", function () {
        if ($('#adjustment-btn').is(":checked"))
            $(".adjustment").show();
        else
            $(".adjustment").hide();
            $('#adjustment_hour').val(0);
            getTotalHour() ;
    });
    function getTotalHour() {
        var totalDay = $("#final_day").val();
        var dayHour = $("#estimated_hour_day").val();

        $('#estimated_hour').val(totalDay*dayHour);
        $('#final_hour').val(totalDay*dayHour);
        $('#adjustment_hour').val();
        adjustmentHourCount();
    };
    function adjustmentHourCount() {
        var adjustmentType = $('#adjustment_type').val();
        var adjustmentHour = $('#adjustment_hour').val();
        var dayHour = $("#estimated_hour_day").val();
        var totaltHour = $("#final_day").val();
        if (adjustmentType == 1) {
            if (adjustmentHour) {
                var finalHour = (parseFloat(totaltHour)*dayHour) + parseFloat(adjustmentHour);
                $("#final_hour").val(finalHour);
            }
        } else if (adjustmentType == 2) {
            if (adjustmentHour) {
                var finalBalance = (parseFloat(totaltHour)*dayHour) - parseFloat(adjustmentHour)
                $("#final_hour").val(finalBalance);
            }
        }
    }
</script>
@endpush
