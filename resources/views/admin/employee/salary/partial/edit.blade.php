<form action="{{route('admin.salary-update',$data->employee_id)}}" id="" method="POST">
    @csrf

    <div class="row">
        <div class="form-group col-12 mb-2">
            @if (count($allowances)>0)
            <label  for="showallckEdit">Add Allowance<input @if ($data->allowance_id != null)
              checked
            @endif type="checkbox"
                id="showallckEdit"></label>
            @else
                If you want add allowance please create allowance
            @endif
        </div>
        <div class="form-group col-6 mb-2">
            <input type="hidden" value="{{$data->id}}" name="salary_id">
            <label for="">Basic Salary</label>
            <input id="basic_salary_edit" name="basic_salary" class="form-control forkeyup" value="{{$data->basic_salary}}" type="number">
        </div>
        <div style="display: none;" class="col-6 show-allawance">
            <label>Select Allowance</label>
            <select  class="form-select" name=""
                id="edit_allowanceAttr" >
                @if(isset($allowances))
                <option
                   selected
                    home_allowance="{{$data->home_allowance}}"
                    is_home_allowance_percentage="{{$data->is_home_allowance_percentage}}"
                    transport_allowance="{{$data->transport_allowance}}"
                    medical_allowance="{{$data->medical_allowance}}"
                    mobile_allowance="{{$data->mobile_allowance}}"
                    leave_allowance="{{$data->leave_allowance}}"
                    description="{{$data->description}}"
                    value="{{$data->id}}">
                   Custom Allowance
                </option>
                    @foreach ($allowances as $allowance)
                        <option
                            home_allowance="{{$allowance->home_allowance}}"
                            is_home_allowance_percentage="{{$allowance->is_home_allowance_percentage}}"
                            transport_allowance="{{$allowance->transport_allowance}}"
                            medical_allowance="{{$allowance->medical_allowance}}"
                            mobile_allowance="{{$allowance->mobile_allowance}}"
                            leave_allowance="{{$allowance->leave_allowance}}"
                            description="{{$allowance->description}}"
                            value="{{$allowance->id}}">
                            {{$allowance->allowance_name}}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
<div style="display: none;" class="row show-allawance">
    <div class="form-group col-6 mb-2">
        <label for="">Home allowance <span id="percentageEdit_show" class="text-danger"></span></label>
        <input id="home_allowance_edit" name="home_allowance" type="number" class="form-control forkeyup" value="{{$data->home_allowance ?? 0}}" type="number">
    </div>
    <div class="form-group col-6 mb-2">
        <label for="">Transport allowance</label>
        <input id="transport_allowance_edit" name="transport_allowance" type="number"  class="form-control forkeyup" value="{{$data->transport_allowance ?? 0}}" type="number">
    </div>
    <div class="form-group col-6 mb-2">
        <label for="">Medical allowance</label>
        <input id="medical_allowance_edit" name="medical_allowance" type="number"  class="form-control forkeyup" value="{{$data->medical_allowance ?? 0}}" type="number">
    </div>
    <div class="form-group col-6 mb-2">
        <label for="">Mobile allowance</label>
        <input id="mobile_allowance_edit" name="mobile_allowance" type="number"  class="form-control forkeyup" value="{{$data->mobile_allowance ?? 0}}" type="number">
    </div>
</div>
    <div class="row">
        <div class="form-group col-6 mb-2">
            <label for="">Gross salary</label>
            <input id="gross_salary_edit" name="gross_salary" readonly class="form-control" value="{{$data->gross_salary}}" type="number">
        </div>
        <div class="form-group col-6 mb-2">
            <label for="salary_month"><b>Select Month</b> <span class="text-danger">*</span> </label>
            <input type="month" id="salary_month" name="salary_month" class="form-control forkeyup" value="{{$data->salary_month}}">
        </div>
        <div class="form-group col-12 mb-2">
            <label for="">Description</label>
            <textarea name="descriptionEdit" id="" class="form-control" cols="30" rows="10">{!!$data->description!!}</textarea>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-6 mb-2">
       <button class="btn btn-info mt-10">Submit</button>
    </div>
</div>
</form>
{{-- <script>
    $(document).ready(function(){
      $(".forkeyup").keyup(function(){
       $salary_basic =  parseFloat($("#basic_salary_edit").val());
       $home_allowance_edit =  parseFloat($("#home_allowance_edit").val());
       $transport_allowance_edit =  parseFloat($("#transport_allowance_edit").val());
       $medical_allowance_edit =  parseFloat($("#medical_allowance_edit").val());
       $mobile_allowance_edit =  parseFloat($("#mobile_allowance_edit").val());
       $gross_salary = $salary_basic+$home_allowance_edit + $transport_allowance_edit+$medical_allowance_edit+$mobile_allowance_edit
       $("#gross_salary_edit").val($gross_salary);
      });
    });
    </script> --}}
    <script>
        CKEDITOR.replace('descriptionEdit', {
            height: '80px',
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

        //
    </script>

    <script>


        function edit_allowance_data() {
            var home_allowance = parseFloat($('#edit_allowanceAttr').find('option:selected').attr('home_allowance'));
            var is_home_allowance_percentage = parseInt($('#edit_allowanceAttr').find('option:selected').attr('is_home_allowance_percentage'));
            var transport_allowance = parseFloat($('#edit_allowanceAttr').find('option:selected').attr('transport_allowance'));
            var medical_allowance = parseFloat($('#edit_allowanceAttr').find('option:selected').attr('medical_allowance'));
            var mobile_allowance = parseFloat($('#edit_allowanceAttr').find('option:selected').attr('mobile_allowance'));
//             var basic_salary = parseFloat($('#edit_allowanceAttr').find('option:selected').attr('mobile_allowance'));
// alert(basic_salary);
            if(is_home_allowance_percentage == 1){
                $("#percentageEdit_show").text("%");
            }else{
                $("#percentageEdit_show").text("");
            }
            
            $("#percentage").val(is_home_allowance_percentage);
            
            $("#home_allowance_edit").val(home_allowance);
            $("#transport_allowance_edit").val(transport_allowance);
            $("#medical_allowance_edit").val(medical_allowance);
            $("#mobile_allowance_edit").val(mobile_allowance);
            // $("#basic_salary_edit").val(basic_salary);
          //  let basic_salary =  parseFloat($('#basic_salary_edit').val());
         // $('#gross_salary').val(basic_salary);
         //alert(basic_salary);
            // // if(basic_salary){
            //     if(isNaN(basic_salary)){
            //     basic_salary = 0;
            //     //
            // }
            //     let homeAllowance = 0;
            //     if(is_home_allowance_percentage == 0){
            //         homeAllowance = home_allowance;
            //     }else{
            //         homeAllowance = basic_salary * (home_allowance/100)
            //     }
            //     let total_allowance = basic_salary+homeAllowance + transport_allowance +medical_allowance+mobile_allowance;

            //     $('#gross_salary').val(total_allowance);

            // }else{
            //     $('#gross_salary').val(0);
            // }
            edit_edit_check_data();

        }
        function edit_edit_check_data() {
            let basic_salary =  parseFloat($('#basic_salary_edit').val());
            let is_home_allowance_percentage =  parseFloat($("#percentage").val());
            let transport_allowance = parseFloat($("#transport_allowance_edit").val());
            let medical_allowance  = parseFloat($("#medical_allowance_edit").val());
            let mobile_allowance = parseFloat($("#mobile_allowance_edit").val());
            let homeAllowance = parseFloat($("#home_allowance_edit").val());
            if(isNaN(basic_salary)){
                basic_salary = 0;
            }
            if(isNaN(homeAllowance)){
                homeAllowance = 0;
            }
            if(isNaN(transport_allowance)){
                transport_allowance = 0;
            }
            if(isNaN(medical_allowance)){
                medical_allowance = 0;
            }
            if(isNaN(mobile_allowance)){
                mobile_allowance = 0;
            }
            // if(basic_salary){
                if(is_home_allowance_percentage == 0){
                    homeAllowancee = homeAllowance;
                }else{
                    homeAllowancee = basic_salary * (homeAllowance/100)
                }
                let total_allowance = basic_salary+homeAllowancee + transport_allowance +medical_allowance+mobile_allowance;
                if(total_allowance ){
                    $('#gross_salary_edit').val(total_allowance);
                }
                else{
                    $('#gross_salary_edit').val(basic_salary);
                }
                
            // }
            // else if(homeAllowance = null){
            //     $('#gross_salary').val(homeAllowance);
            // }
            // else{
            //     $('#gross_salary').val(0);
            // }
        }
        if ($('#showallckEdit').is(":checked")) {
                $('.show-allawance').show();
                edit_allowance_data();
            }

        $('#showallckEdit').on('click', function () {
            if ($(this).is(":checked")) {
                $('.show-allawance').show();
                edit_allowance_data();
                
            } else if ($(this).is(":not(:checked)")) {
                let basic_salary =  parseFloat($('#basic_salary_edit').val());
                $('#gross_salary_edit').val(basic_salary);
                $('.show-allawance').hide();

                //
                // $('#salary_basic').val(0);
                $("#percentage").val(0);
                $("#transport_allowance_edit").val(0);
                $("#medical_allowance_edit").val(0);
                $("#mobile_allowance_edit").val(0);
                $("#home_allowance_edit_edit").val(0);
            }
        })

        $('#edit_allowanceAttr').on('change', function (){
            edit_edit_check_data();
            edit_allowance_data();
        })

        // $('#salary_basic').on('input', function(){
        //     edit_allowance_data()
        // })
       
        $("input").keyup(function(){
            var basic_salary_edit =  parseFloat($('#basic_salary_edit').val());
            var home_allowance_edit = parseFloat($("#home_allowance_edit").val());
            var transport_allowance_edit = parseFloat($("#transport_allowance_edit").val());
            var medical_allowance_edit = parseFloat($("#medical_allowance_edit").val());
            var mobile_allowance_edit = parseFloat($("#mobile_allowance_edit").val());
            let total_salary = home_allowance_edit+basic_salary_edit+transport_allowance_edit+medical_allowance_edit+mobile_allowance_edit
            $('#gross_salary_edit').val(total_salary);
        });
    </script>
