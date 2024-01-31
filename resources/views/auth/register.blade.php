@extends('layouts.no-nav')
@section('page_title', __('Register'))

@if(getSetting('security.recaptcha_enabled') && !Auth::check())
    @section('meta')
        {!! NoCaptcha::renderJs() !!}
    @stop
@endif

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7 col-xl-6 mx-auto">
                                <a href="{{action('HomeController@index')}}" style="width: 100%; display: flex; justify-content: center;">
                                    <img class="brand-logo pb-4" style="width: 450px;" src="https://freeonlytest.s3.us-west-1.amazonaws.com/Fetishonly%20SVG%20Logo.svg">
                                </a>
                                @include('auth.register-form')
                                @include('auth.social-login-box')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 bg-primary w-100 h-100" style="background-color: #C0262C; position: relative;">
                    <img src="https://freeonlytest.s3.us-west-1.amazonaws.com/BA_Black_Yellow-removebg-preview.png" style="position: absolute; z-index: 999; width: 70%; height: auto; bottom: 0; left: 15%;" alt="">
                    <img src="{{asset('/img/pattern-lines.svg')}}" alt="pattern-lines" class="img-fluid opacity-10">
                </div>
            </div>
        </div>
    </div>
@endsection
