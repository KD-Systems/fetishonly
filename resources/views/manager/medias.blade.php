@extends('layouts.manager')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Media for your creator </h3>
    <nav aria-label="breadcrumb">
        {{-- <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Tables</a></li>
            <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
        </ol> --}}
    </nav>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            @foreach ($medias as $item)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        @if ($item->attachment->attachmentType == "image")
                            <img style="width: 100%" src="{{$item->attachment->thumbnail}}" alt="">
                        @else
                            <video style="width: 100%" src="{{$item->attachment->thumbnail}}" controls></video>
                        @endif
                        <p class="mt-4">User: {{ $item->user->name }}</p>
                        <a href="{{ route('manager.view', ['id' => $item->id]) }}">Update</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
