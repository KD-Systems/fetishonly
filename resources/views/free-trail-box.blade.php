@extends('layouts.user-no-nav')

@section('page_title',  'Active your free trail')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('elements.feed.suggestion-card',['profile' => $profile ,'isListMode' => false, 'isListManageable' => false])
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">{{ $trailLink->duration }} days free trail.</h3>
        </div>
    </div>
    @if ($trailLink->expire_at <= now() || $trailLink->trailLog->count() >= $trailLink->limit)
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <button class="btn btn-success" disabled>Trail is expired!</button>
                </div>
            </div>
        </div>
    @elseif($trailLink->trailLog->where('user_id', Auth::user()->id)->first())
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <button class="btn btn-success" disabled>You have used this trail!</button>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <form action="{{ route('free-trail-active', ['slug' => $trailLink->slug]) }}" method="POST">
                        @csrf
                        <button class="btn btn-success">Click to active</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
