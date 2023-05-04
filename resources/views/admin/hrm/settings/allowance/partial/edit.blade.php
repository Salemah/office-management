<form action="{{route('admin.hrm.allowance-update')}}" id="" method="POST">
    @csrf
    {{-- @method('put') --}}
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <p id="#upmsgcontainer"></p>
                <input type="hidden" name="allowance_id" id="all_id" value="{{$allowance->id}}">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="is_home_allowance_percentage"><b>Home Allowance Type</b></label>
                        <select class="form-select" name="is_home_allowance_percentage"
                                id="is_home_allowance_percentage">
                            <option>Select Type</option>
                            <option @if ($allowance->is_home_allowance_percentage == 1)
                                selected
                            @endif  value="1">Percentage</option>
                            <option @if ($allowance->is_home_allowance_percentage == 0)
                                selected
                            @endif value="0">Amount</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="houserent"><b>Home Allowance</b> <span class="text-danger">*</span></label>
                        <input type="number" id="home_allowance" name="home_allowance"
                               class="form-control" value="{{$allowance->home_allowance}}">
                    </div>
                    @error('name')
                    <span class="alert text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                    @enderror
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="medical"><b>Medical Allowance</b></label>
                        <input type="number" id="medical_allowance" name="medical_allowance"
                               class="form-control" value="{{$allowance->medical_allowance}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="transport_allowance"><b>Transport Allowance</b></label>
                        <input type="number" id="transport_allowance" name="transport_allowance"
                               class="form-control" value="{{$allowance->transport_allowance}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="mobile_allowance"><b>Mobile Allowance</b></label>
                        <input type="number" id="mobile_allowance" name="mobile_allowance"
                               class="form-control" value="{{$allowance->mobile_allowance}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-12 mb-2">
                    <label for="description"><b>Description </b></label>
                    <textarea name="description" id="descriptionEdit" rows="3"
                              class="form-control"
                              placeholder="Description...">
                        {{ $allowance->description }}
                    </textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-primary ">Submit</button>
            </div>
        </div>
    </div>
</form>
