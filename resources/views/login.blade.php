@extends('component')
@section('title','Login Page Admin | Booking Resto App')
@section('login')
<style>
    body {
        background-color: #44613F
    }
</style>
<div class="vh-100 d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card bg-white shadow-lg">
                    <div class="card-body p-5">
                        @if(session('auth'))
                        <div class="alert alert-danger alert-block dismissible show fade">
                            <div class="alert-body">
                                {{session('auth')}}
                            </div>
                        </div>
                        @endif
                        @if(session('logout'))
                        <div class="alert alert-success alert-block dismissible show fade">
                            <div class="alert-body">
                                {{session('logout')}}
                            </div>
                        </div>
                        @endif
                        <h2 class="fw-bold mb-2 text-uppercase ">Booking Resto App</h2>
                        <p class=" mb-5">Please enter your phone number or email and password!</p>
                        <form action="{{ url('login') }}" method="POST" class="mb-3 mt-md-4">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label ">Phone Number or Email</label>
                                <input type="email" class="form-control" name="phone_or_email" placeholder="081********* or name@example.com">
                                @if ($errors->has('phone_or_email'))
                                    <p class="text-danger fst-italic">{{ $errors->first('phone_or_email') }}</p>
                                @endif
                                @if(session('not_found'))
                                    <p class="text-danger fst-italic">{{session('not_found')}}</p>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label ">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="*******">
                                @if ($errors->has('password'))
                                    <p class="text-danger fst-italic">{{ $errors->first('password') }}</p>
                                @endif
                                @if(session('error'))
                                    <p class="text-danger fst-italic">{{session('error')}}</p>
                                @endif
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection