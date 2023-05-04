<form  action="{{ route('admin.project.reporting.assign') }}"
    method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="reporting_person_id"><b>Assign</b></label>
                <select name="reporting_person_id[]" id="reporting_person_id"class="form-control select2" multiple="multiple" >
                    <option>--Select Employee--</option>
                </select>
                @error('reporting_person_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
        </div>
        <input type="hidden" name="project_id" id="project_id" value="{{$project->id}}">
        <div class="form-group">
            <button type="submit" id="submit-btn" class="btn btn-sm btn-primary mb-3">Assign</button>
        </div>
    </div>
</form>

@push('script')
    <script>
            $('#reporting_person_id').select2({
               placeholder:"--Select Reporting Person--",
                ajax: {
                    url: '{{route('admin.project.reporting.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            project: $('#project_id').val(),
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

    </script>
@endpush

