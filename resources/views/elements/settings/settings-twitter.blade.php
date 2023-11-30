
@if ($twitter)
<button class="btn btn-info rounded mr-0 d-flex align-items-center justify-content-center" disabled>
    @include('elements.icon',['icon'=> 'logo-twitter','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
    You are account is connected with twitter.
</button>
@else
<a href="https://twitter.com/i/oauth2/authorize?response_type=code&client_id=MGRmRnYyenktVTItWXhCd01ZOEw6MTpjaQ&redirect_uri={{ env('X_REDIRECT_URI', 'https://example.com') }}&scope=tweet.write tweet.read users.read offline.access&state=state&code_challenge=challenge&code_challenge_method=plain" class="btn btn-info rounded mr-0 d-flex align-items-center justify-content-center" type="submit">
    @include('elements.icon',['icon'=> 'logo-twitter','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
    {{__('ConnectWithTwitter')}}</a>
@endif

