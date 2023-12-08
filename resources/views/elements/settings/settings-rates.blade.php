@if(session('success'))
    <div class="alert alert-success text-white font-weight-bold mt-2" role="alert">
        {{session('success')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{__('Close')}}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="POST" action="{{route('my.settings.rates.save')}}">
    @csrf
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="paid-profile" name="paid-profile"
                    {{isset(Auth::user()->paid_profile) ? (Auth::user()->paid_profile == '1' ? 'checked' : '') : false}}>
            <label class="custom-control-label" for="paid-profile">{{__('Paid profile')}}</label>
        </div>
    </div>
    <div class="paid-profile-rates {{isset(Auth::user()->paid_profile) ? (Auth::user()->paid_profile == '1' ? '' : 'd-none') : ''}}">
        <div class="form-group">
            <label for="name">{{__('Your profile subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price') ? 'is-invalid' : '' }}" id="profile_access_price" name="profile_access_price" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price}}">
            @if($errors->has('profile_access_price'))
                <span class="invalid-feedback" role="alert">
                <strong>{{__($errors->first('profile_access_price'))}}</strong>
            </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('3 months subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price_3_months') ? 'is-invalid' : '' }}" id="profile_access_price" name="profile_access_price_3_months" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price_3_months}}">
            @if($errors->has('profile_access_price_3_months'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_price_3_months'))}}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('6 months subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price_6_months') ? 'is-invalid' : '' }}" id="profile_access_price" name="profile_access_price_6_months" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price_6_months}}">
            @if($errors->has('profile_access_price_6_months'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_price_6_months'))}}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('12 months subscription price')}}</label>
            <input class="form-control {{ $errors->has('profile_access_price_12_months') ? 'is-invalid' : '' }}" id="profile_access_price_12_months" name="profile_access_price_12_months" aria-describedby="emailHelp" value="{{Auth::user()->profile_access_price_12_months}}">
            @if($errors->has('profile_access_price_12_months'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_price_12_months'))}}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <label for="name">{{__('Is offer until')}}</label>
            <div class="input-group-prepend">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="Checkbox for following text input" name="is_offer" id="is_offer" {{Auth::user()->offer && Auth::user()->offer->expires_at ? 'checked' : ''}}>
                    </div>
                </div>
                <input type="date" class="form-control {{ $errors->has('profile_access_offer_date') ? 'is-invalid' : '' }}" id="profile_access_offer_date" name="profile_access_offer_date" aria-describedby="emailHelp" value="{{Auth::user()->offer && Auth::user()->offer->expires_at ? Auth::user()->offer->expires_at->format('Y-m-d') : ''}}">

            </div>
            <small class="form-text text-muted">
                {{__("In order to start a promotion, reduce your monthly prices and select a future promotion end date.")}}
            </small>
            @if($errors->has('profile_access_offer_date'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{__($errors->first('profile_access_offer_date'))}}</strong>
                </span>
            @endif
        </div>
        <button class="btn btn-primary btn-block rounded mr-0" type="submit">{{__('Save')}}</button>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <h3>Manage Trail Links</h3>
        <hr>
    </div>
    <br>
    <div class="col-md-12">
        @foreach ($trail_links as $item)
            <h3><b>{{ $item->name }}</b> ({{ $item->duration }} days free trail)</h3>
            <table class="table">
                <tr>
                    <td><p style="margin: 0; pedding: 0;">Link created</p></td>
                    <td><p class="text-right" style="margin: 0; pedding: 0;">{{ $item->created_at->isoFormat("Do MMM YYYY") }}</p></td>
                </tr>
                <tr>
                    <td><p style="margin: 0; pedding: 0;">Link expires</p></td>
                    <td><p class="text-right" style="margin: 0; pedding: 0;">{{ $item->expire_at->isoFormat("Do MMM YYYY") }}</p></td>
                </tr>
                <tr>
                    <td><p style="margin: 0; pedding: 0;">Offer limit</p></td>
                    <td><p class="text-right" style="margin: 0; pedding: 0;">{{ $item->limit }}</p></td>
                </tr>
                <tr>
                    <td><p style="margin: 0; pedding: 0;">Claims count</p></td>
                    <td><p class="text-right" style="margin: 0; pedding: 0;">{{ $item->trailLog->count() }}</p></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="{{ route('free-trail-delete', ['id' => $item->id]) }}" class="btn btn-sm btn-danger">Delete Link</a>
                        <button class="btn btn-sm btn-info" onclick="navigator.clipboard.writeText('{{ route('free-trail-link', ['slug' => $item->slug]) }}');alert('Link copied!')">Copy Link</button>
                    </td>
                </tr>
            </table>
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#addTrailLink">Create Trial Link</button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addTrailLink" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Free Trail Link</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('my.settings.rates.free-trail') }}" id="free_trail_link_form" method="POST">
            <div class="modal-body">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Trail link name</label>
                        <input type="text" class="form-control" name="name" placeholder="Trail link name" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Offer Limit (Subscribers)</label>
                        <input type="number" min="1" class="form-control" name="limit" placeholder="10 Subscribers" required>
                    </div>
                    <div class="col-md-6">
                        <label for="">Offer Expiration (Days)</label>
                        <input type="number" min="1" class="form-control" name="expire" placeholder="7 Days" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Free trail duration (Days)</label>
                        <input type="number" min="1" class="form-control" name="duration" placeholder="7 Days" required="required">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>
