@extends('sidebar')
@section('title','Detail Destination | Booking Resto App')
@section('destination','active')
@section('detail-destination')
<a href="{{ url('destination/list') }}" class="btn btn-info my-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"></path>
</svg>
Back
</a>
<h1>{{ $data->name }}</h1>
<span class="badge text-bg-success">{{ $data->category_name->category }}</span>
<span class="badge text-bg-success">{{ $data->province_name->name }}</span>
<span class="badge text-bg-success">{{ $data->city_name->name }}</span>
<hr>
<div id="carouselExample" class="carousel slide mb-3">
    <div class="carousel-inner">
        @foreach($images as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/'.$image->image )}}" class="d-block w-100" alt="image destination">
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<h5 class="text-success">Budget : {{ $data->budget }}</h5>
<div class="mb-2 fs-5">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
    </svg>
    <span>{{ $data->rating }}</span>
</div>
<Address><i>{{ $data->address }}</i></Address>
<p>{{ $data->description }}</p>
<hr>
<h3>Reviews</h3>
@foreach ($reviews as $review)
    <div class="card mb-4" style="width: 95%">
        <div class="card-header">
            @if ($review->maker_name->photo)
                <img src="{{ asset('storage/'.$review->maker_name->photo) }}" alt="userphoto" width="45" height="45" class="rounded-circle">
            @else
                <img src="{{ asset('default.jpg') }}" alt="userphoto" width="45" height="45" class="rounded-circle me-2">
            @endif
            {{ $review->maker_name->name }}
        </div>
        <div class="card-body">
            <blockquote class="blockquote mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
                </svg>
                <span>{{ $review->rating }}</span>
                <br>
                <p>{{ $review->description }}</p>
            </blockquote>
        </div>
    </div>
@endforeach
@endsection