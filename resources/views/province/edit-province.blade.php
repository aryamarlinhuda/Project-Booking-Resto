@extends('sidebar')
@section('title','Edit Province | Booking Resto App')
@section('province','active')
@section('edit-province')
<a href="{{ url('province/list') }}" class="btn btn-info my-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"></path>
</svg>
Back
</a>
<h1>Edit Province</h1>
<hr>
<form action="{{ url('province/edit/'.$data->id.'/process') }}" method="POST" class="col-md-8" enctype="multipart/form-data">
    @csrf
        <div class="mt-3 mb-4">
            <label for="name" class="form-label">Province Name</label>
            <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            value="{{ $data->province }}"
            />
            @if ($errors->has('name'))
                <p class="text-danger fst-italic">{{ $errors->first('name') }}</p>
            @endif
            @if(session('unique'))
                <p class="text-danger fst-italic">{{session('unique')}}</p>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Edit province</button>
</form>
@endsection