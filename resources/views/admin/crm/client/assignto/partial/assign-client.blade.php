<form  action="{{ route('admin.crm.client-assignto.store') }}"
    method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="employee"><b>Assign</b></label>
                <select name="employee" id="employee"class="form-control select2" >
                    <option>--Select Employee--</option>
                </select>
                @error('employee')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
        </div>
        <input type="hidden" name="client_id" id="client_id" value="{{ $Client->id }}">
        <div class="form-group">
            <button type="submit" id="submit-btn" class="btn btn-sm btn-primary mb-3">Assign</button>
        </div>
    </div>
</form>

@push('script')
    <script>
            // $('#submit-btn').on('click', function (){
            //     function setTab(params){
            //         localStorage.setItem('activeTabClients', params);
            //     }
            // });

            $('#employee').select2({
                ajax: {
                    url: '{{route('admin.crm.allemplyee.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            client: $('#client_id').val(),
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

    </script>
@endpush
