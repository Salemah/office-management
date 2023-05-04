<style>
    .text-right {
        text-align: right;
    }
</style>
<div class="body flex-grow-1 px-3">
    <div class="container-lg">
        <!-- Alert -->

        <form action="{{route('admin.salary.store')}}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        <div class="col-sm-4">
                            <div class="form-group">
                                @if (count($allowances)>0) 
                                    <label for="showallck"><b>Add Allowance</b><input id="showallck" type="checkbox"
                                   ></label>
                                @else
                                    If you want add allowance please <a class="link" href="{{route('admin.hrm.allowance.create')}}">create allowance</a> 
                                @endif                          
                                <select style="display: none;" class="form-select show-allawance"  
                                        id="allowanceAttr">
                                        @if(isset($allowances))
                                            @foreach ($allowances as $allowance)
                                                <option 
                                                    @if ($allowance->status == 1)
                                                        selected
                                                    @endif
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
                        <div class="col-md-4">
                            <label for="salary_month"><b>Select Month</b> <span class="text-danger">*</span> </label>
                            <input type="month" id="salary_month" name="salary_month" class="form-control"  onkeyup="check_data()">
                            @error('salary_month')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-sm-4 ">
                            <label for="salary_basic"><b>Basic Salary</b> <span class="text-danger">*</span> </label>
                            <?php
                                if ($employee->current_salary == null){
                                    $salary = $employee->joining_salary;
                                }else{
                                    $salary = $employee->current_salary;
                                }
                            ?>
                            <input type="number" id="salary_basic" name="basic_salary" class="form-control" value="{{$salary}}" onkeyup="check_data()">
                            @error('salary_basic')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            <div style="display: none;" class="show-allawance">
                                <input id="percentage" type="hidden" value="" class="form-control">
                                <div class="form-group mb-2 mt-2">
                                    <label for="">Home Allowance <span class="text-danger" id="percentage_show"></span></label>
                                    <input id="home-allowance" type="number" name="home_allowance"  class="form-control" onkeyup="check_data()">
                                </div>
                                <div class="form-group  mb-2">
                                    <label for="">Transport Allowance</label>
                                    <input id="transport_allowance" type="number" name="transport_allowance" value="" class="form-control" onkeyup="check_data()">
                                </div>
                                <div class="form-group  mb-2">
                                    <label for="">Medical Allowance</label>
                                    <input id="medical_allowance" type="number" value="" name="medical_allowance" class="form-control" onkeyup="check_data()">
                                </div>
                                <div class="form-group  mb-2">
                                    <label for="">Mobile Allowance</label>
                                    <input id="mobile_allowance" type="number" value="" name="mobile_allowance" class="form-control" onkeyup="check_data()">
                                </div>
                            </div>
                            <div class="form-group">

                                <hr>
                                <label for="gross_salary"><b>Gross Salary</b> <span class="text-danger"></span></label>
                                <input type="text" id="gross_salary" name="gross_salary" class="form-control">
                                @error('gross_salary')
                                <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12 mb-2">
                            <label for="description"><b>Description </b></label>
                            <textarea name="description" id="description5" rows="3"
                                      class="form-control description"
                                      placeholder="Description..."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </div>
            </div>

        </form>
        <div class="mb-5"></div>
        <!-- Button trigger modal -->

        <!-- Modal -->

    </div>
</div>
@push('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace('description5', {
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


        function allowance_data() {
            var home_allowance = parseFloat($('#allowanceAttr').find('option:selected').attr('home_allowance'));
            var is_home_allowance_percentage = parseInt($('#allowanceAttr').find('option:selected').attr('is_home_allowance_percentage'));
            var transport_allowance = parseFloat($('#allowanceAttr').find('option:selected').attr('transport_allowance'));
            var medical_allowance = parseFloat($('#allowanceAttr').find('option:selected').attr('medical_allowance'));
            var mobile_allowance = parseFloat($('#allowanceAttr').find('option:selected').attr('mobile_allowance'));
            $("#percentage").val(is_home_allowance_percentage);
            $("#home-allowance").val(home_allowance);
            $("#transport_allowance").val(transport_allowance);
            $("#medical_allowance").val(medical_allowance);
            $("#mobile_allowance").val(mobile_allowance);
            let basic_salary =  parseFloat($('#salary_basic').val());
            if(is_home_allowance_percentage == 1){
                $("#percentage_show").text("%");
            }
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
            check_data();

        }
        function check_data() {
            let basic_salary =  parseFloat($('#salary_basic').val());
            let is_home_allowance_percentage =  parseFloat($("#percentage").val());
            let transport_allowance = parseFloat($("#transport_allowance").val());
            let medical_allowance  = parseFloat($("#medical_allowance").val());
            let mobile_allowance = parseFloat($("#mobile_allowance").val());
            let homeAllowance = parseFloat($("#home-allowance").val());
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
                    $('#gross_salary').val(total_allowance);
                }
                else{
                    $('#gross_salary').val(basic_salary);
                }
            // }
            // else if(homeAllowance = null){
            //     $('#gross_salary').val(homeAllowance);
            // }
            // else{
            //     $('#gross_salary').val(0);
            // }
        }


        $('#showallck').on('click', function () {
            if ($(this).is(":checked")) {
                $('.show-allawance').show();
                $("#allowanceAttr").attr("name", "allowance_id");
                allowance_data();


            } else if ($(this).is(":not(:checked)")) {
                $("#allowanceAttr").attr("name", "");
                let basic_salary =  parseFloat($('#salary_basic').val());
                $('#gross_salary').val(basic_salary);
                $('.show-allawance').hide();

                //
                // $('#salary_basic').val(0);
                $("#percentage").val(0);
                $("#transport_allowance").val(0);
                $("#medical_allowance").val(0);
                $("#mobile_allowance").val(0);
                $("#home-allowance").val(0);
            }
        })

        $('#allowanceAttr').on('change', function (){
            check_data();
            allowance_data();
        })

        // $('#salary_basic').on('input', function(){
        //     allowance_data()
        // })

    </script>
@endpush
