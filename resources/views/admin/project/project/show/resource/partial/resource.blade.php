<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.resource.store') }}"
    method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
        <div class="col-sm-6 mb-2 col-md-6 document">
            <div class="form-group">
                <label for="document_name"><b>Resource Title <span style="color: red">*</span></b></label>
                <input class="form-control " type="text" placeholder="Document Title.." name="document_name" >
                @error('document_name')
                    <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-5 col-md-6 mb-2 document">
            <div class="form-group">
                <label for="document_file"><b>Document</b></label>
                <input data-height="25"class="dropify form-control " type="file" placeholder="Document" name="document_file" value="">
                @error('document_file')
                    <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group my-3">
        <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
    </div>
</form>

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
                $('#duration_total_day').val(diffDays);
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
                    getTotalDurationHour();
                }
                else{
                    $('#final_day').val(diffDays - vacationDay);
                    getTotalDurationHour();
                }
            }
        }
        $(document).on("click", "#adjustment-btn", function () {
            if ($('#adjustment-btn').is(":checked"))
                {
                    $(".adjustment").show();
                }
            else
                {
                    $(".adjustment").hide();
                    $('#adjustment_hour').val(0);
                    getTotalDurationHour() ;
                }
        });

        function getTotalDurationHour() {
            var totalDay = $("#final_day").val();
            var dayHour = $("#duration_estimated_hour_day").val();
            $('#duration_estimated_hour').val(totalDay*dayHour);
            $('#duration_final_hour').val(totalDay*dayHour);
           $('#adjustment_hour').val(0);
            DurationadjustmentHourCount();
        };

        function DurationadjustmentHourCount() {
            var adjustmentType = $('#adjustment_type').val();
            var adjustmentHour = $('#adjustment_hour').val();
            var dayHour = $("#duration_estimated_hour_day").val();
            var totaltHour = $("#final_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour)*dayHour) + parseFloat(adjustmentHour);
                    $("#duration_final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour)*dayHour) - parseFloat(adjustmentHour)
                    $("#duration_final_hour").val(finalBalance);
                }
            }
        }
    </script>
@endpush
