
@if ($twitter)
<button class="btn btn-info rounded mr-0 d-flex align-items-center justify-content-center" disabled>
    @include('elements.icon',['icon'=> 'logo-twitter','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
    Your account is connect to twitter.
</button>

<a href="{{ route('twitter-discounnect') }}" onclick="return confirm('Are you sure?')" class="btn btn-danger">Discounnect now</a>
@else
<a href="https://twitter.com/i/oauth2/authorize?response_type=code&client_id={{ env('X_CLIENT_ID') }}&redirect_uri={{ env('X_REDIRECT_URI', 'https://example.com') }}&scope=tweet.write tweet.read users.read offline.access&state=state&code_challenge=challenge&code_challenge_method=plain" class="btn btn-info rounded mr-0 d-flex align-items-center justify-content-center" type="submit">
    @include('elements.icon',['icon'=> 'logo-twitter','centered'=>'false','classes'=>'mr-3','variant'=>'medium'])
    {{__('ConnectWithTwitter')}}</a>
@endif

