@extends('layouts.no-nav')
@section('page_title', __('Login'))

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7 col-xl-6 mx-auto">
                                <a href="{{action('HomeController@index')}}">
                                    <img class="brand-logo pb-4" src="https://freeonlytest.s3.us-west-1.amazonaws.com/logo-removebg-preview%20%281%29.png">
                                </a>
                                @include('auth.login-form')
                                @include('auth.social-login-box')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex bg-image p-0 m-0">
                <div class="d-flex m-0 p-0 w-100 h-100" style="background-color: #C0262C;">
                    <img src="{{asset('/img/pattern-lines.svg')}}" alt="pattern-lines" class="img-fluid opacity-10">
                </div>
            </div>
        </div>
    </div>
@endsection
