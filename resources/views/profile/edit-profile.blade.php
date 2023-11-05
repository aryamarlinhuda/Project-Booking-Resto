@extends('sidebar')
@section('title','Edit Profile | Booking Resto App')
@section('edit-profile')
<div class="container col-md-6">
    <div class="card" style="margin-top: 20%">
        <div class="card-body">
            <a href="{{ url('profile') }}" class="btn btn-info mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z"></path>
                    <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"></path>
                </svg>
                Back
            </a>
            <h2 class="card-title mb-3">Edit Profile Admin</h2>
            <form action="{{ url('edit-profile/process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label"><strong>Name</strong></label>
                    <input type="text" class="form-control" name="name" placeholder="Enter your name" value="{{ $data->name }}">
                    @if ($errors->has('name'))
                        <p class="text-danger fst-italic">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label"><strong>Photo</strong></label>
                    <input type="file" class="form-control" name="photo" id="photo"/>
                    @if ($errors->has('photo'))
                        <p class="text-danger fst-italic">{{ $errors->first('photo') }}</p>
                    @endif
                    @if(session('format'))
                        <p class="text-danger fst-italic">{{ session('format') }}</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="phone_or_email" class="form-label"><strong>Phone Number or Email</strong></label>
                    <input class="form-control" name="phone_or_email" placeholder="Enter your Phone Number or Email" value="{{ $data->phone_or_email }}">
                    @if ($errors->has('phone_or_email'))
                        <p class="text-danger fst-italic">{{ $errors->first('phone_or_email') }}</p>
                    @endif
                    @if(session('credentials'))
                        <p class="text-danger fst-italic">{{session('credentials')}}</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-success">Edit</button>
            </form>
        </div>
    </div>
</div>
@endsection