@extends('sidebar')
@section('title','Create City | Booking Resto App')
@section('city','active')
@section('create-city')
<a href="{{ url('city/list') }}" class="btn btn-info my-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"></path>
</svg>
Back
</a>
<h1>Create City</h1>
<hr>
<form action="create/process" method="POST" class="col-md-8" enctype="multipart/form-data">
    @csrf
        <div class="mt-3 mb-4">
            <label for="name" class="form-label">City Name</label>
            <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            />
            @if ($errors->has('name'))
                <p class="text-danger fst-italic">{{ $errors->first('name') }}</p>
            @endif
            @if(session('unique'))
                <p class="text-danger fst-italic">{{session('unique')}}</p>
            @endif
        </div>
        <div class="mt-3 mb-4">
            <label for="province" class="form-label">Province</label>
            <input
            type="text"
            class="form-control"
            id="province"
            name="province"
            />
        </div>
        @if(session('wrong'))
            <p class="text-danger fst-italic">{{session('wrong')}}</p>
        @endif
        <button type="submit" class="btn btn-primary">Create City</button>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript">
    var route = "{{ url('province/autocomplete') }}";
    $('#province').typeahead({
        source: function (query, process) {
            return $.get(route, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });
</script>
@endsection