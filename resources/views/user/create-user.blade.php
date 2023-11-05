@extends('sidebar')
@section('title','Create User | Booking Resto App')
@section('user','active')
@section('create-user')
<a href="{{ url('user/list') }}" class="btn btn-info my-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"></path>
</svg>
Back
</a>
<h1>Create User</h1>
<hr>
<form action="create/process" method="POST" class="col-md-8" enctype="multipart/form-data">
    @csrf
        <div class="mt-3 mb-4">
            <label for="name" class="form-label">Name</label>
            <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            />
            @if ($errors->has('name'))
                <p class="text-danger fst-italic">{{ $errors->first('name') }}</p>
            @endif
        </div>
        <div class="mb-4">
            <label for="photo" class="form-label">Photo</label>
            <input
            type="file"
            class="form-control"
            id="photo"
            name="photo"
            />
            @if ($errors->has('photo'))
                <p class="text-danger fst-italic">{{ $errors->first('photo') }}</p>
            @endif
            @if(session('format'))
                <p class="text-danger fst-italic">{{ session('format') }}</p>
            @endif
            <p class="text-secondary"><i>*optional</i></p>
        </div>
        <div class="mb-4">
            <label for="phone_or_email" class="form-label">Phone Number or Email</label>
            <input
            type="text"
            class="form-control"
            id="phone_or_email"
            name="phone_or_email"
            />
            @if ($errors->has('phone_or_email'))
                <p class="text-danger fst-italic">{{ $errors->first('phone_or_email') }}</p>
            @endif
            @if(session('credentials'))
                <p class="text-danger fst-italic">{{session('credentials')}}</p>
            @endif
            <p class="text-secondary"><i>*if you enter a phone number, enter the country code of your phone number</i></p>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            />
            @if ($errors->has('password'))
                <p class="text-danger fst-italic">{{ $errors->first('password') }}</p>
            @endif
        </div>
        <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input
            type="password"
            class="form-control"
            id="confirm_password"
            name="confirm_password"
            />
            @if ($errors->has('confirm_password'))
                <p class="text-danger fst-italic">{{ $errors->first('confirm_password') }}</p>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
</form>
@endsection