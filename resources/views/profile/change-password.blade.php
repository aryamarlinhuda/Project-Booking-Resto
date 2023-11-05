@extends('sidebar')
@section('title','Change Password | Booking Resto App')
@section('change-password')
<div class="container col-md-6">
    <div class="card" style="margin-top: 22%">
        <div class="card-body">
            <a href="{{ url('profile') }}" class="btn btn-info mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z"></path>
                    <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"></path>
                </svg>
                Back
            </a>
            <h2 class="card-title mb-3">Change Password</h2>
            <form action="{{ url('change-password/process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="old_password" class="form-label"><strong>Old Password</strong></label>
                    <input type="password" class="form-control" name="old_password" placeholder="Enter your old password">
                    @if ($errors->has('old_password'))
                        <p class="text-danger fst-italic">{{ $errors->first('old_password') }}</p>
                    @endif
                    @if(session('wrong'))
                        <p class="text-danger fst-italic">{{ session('wrong') }}</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label"><strong>New Password</strong></label>
                    <input type="password" class="form-control" name="new_password" placeholder="Enter your new password">
                    @if ($errors->has('new_password'))
                        <p class="text-danger fst-italic">{{ $errors->first('new_password') }}</p>
                    @endif
                    @if(session('same'))
                        <p class="text-danger fst-italic">{{ session('same') }}</p>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="confirm_new_password" class="form-label"><strong>Confirm New Password</strong></label>
                    <input type="password" class="form-control" name="confirm_new_password" placeholder="Enter your confirm new password">
                    @if ($errors->has('confirm_new_password'))
                        <p class="text-danger fst-italic">{{ $errors->first('confirm_new_password') }}</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-success">Change</button>
            </form>
        </div>
    </div>
</div>
@endsection