@extends('layouts.user-no-nav')

@section('page_title',  ucfirst(__($activeSettingsTab)))

@section('scripts')
    {!!
        Minify::javascript(
            array_merge($additionalAssets['js'],[
                '/js/pages/settings/settings.js',
                '/js/suggestions.js',
         ])
        )->withFullUrl()
    !!}
    @if(getSetting('profiles.allow_profile_bio_markdown'))
        <script src="{{asset('/libs/easymde/dist/easymde.min.js')}}"></script>
    @endif
    <script>
        $(".update_withdraw_btn").on('click', function(){
            $(".withdraw_method_update").css('display', 'none');
            if($(this).data('type') == 'paypal') {
                $('#update_paypal_email').val($(this).data('email'));
                $('#update_paypal_id').val($(this).data('id'));
                $('#paypal_update').css('display', 'block');
            }else{
                $('#update_bank_name').val($(this).data('bank-name'));
                $('#update_account_name').val($(this).data('account-name'));
                $('#update_account_number').val($(this).data('account-number'));
                $('#update_swift_code').val($(this).data('code'));
                $('#update_bank_id').val($(this).data('id'));
                $('#bank_update').css('display', 'block');
            }
        })

        $(".select_method").click(function(){
            $('.method').attr("checked", false);
            $(this).find('.method').attr("checked", true);
        })
    </script>
@stop

@section('styles')
    {!!
        Minify::stylesheet(
            array_merge($additionalAssets['css'],[
                '/css/pages/settings.css',
                ])
         )->withFullUrl()
    !!}
    <style>
        .selectize-control.multi .selectize-input>div.active {
            background:#{{getSetting('colors.theme_color_code')}};
        }
    </style>
    @if(getSetting('profiles.allow_profile_bio_markdown'))
        <link href="{{asset('/libs/easymde/dist/easymde.min.css')}}" rel="stylesheet">
    @endif
@stop

@section('content')
    <div class="">
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3 mb-3 pr-0 settings-menu">
                <div class="settings-menu-wrapper">
                    <div class="d-none d-md-block">
                        @include('elements.settings.settings-header',['type'=>'generic'])
                    </div>
                    <div class="d-block d-md-none mt-3">
                        @include('elements.settings.settings-header',['type'=>'settingTab'])
                    </div>
                    <hr class="mb-0">
                    <div class="d-none d-md-block">
                        @include('elements.settings.settings-menu',['availableSettings' => $availableSettings])
                    </div>
                    <div class="setting-menu-mobile d-block d-md-none mt-3">
                        @include('elements.settings.settings-menu-mobile',['availableSettings' => $availableSettings])
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-lg-9 mb-5 mb-lg-0 min-vh-100 border-left border-right settings-content mt-1 mt-md-0 pl-md-0 pr-md-0">
                <div class="ml-3 d-none d-md-flex justify-content-between">
                    <div>
                        <h5 class="text-bold mt-0 mt-md-3 mb-0 {{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? '' : 'text-dark-r') : (Cookie::get('app_theme') == 'dark' ? '' : 'text-dark-r'))}}">{{ $activeSettingsTab == 'verify' ? 'Become a creator' : ucfirst(__($activeSettingsTab))}}</h5>
                        <h6 class="mt-2 text-muted">{{__($currentSettingTab['heading'])}}</h6>
                    </div>
{{--                    @include('elements.table-filter')--}}
                </div>
                <hr class="{{in_array($activeSettingsTab, ['subscriptions','payments']) ? 'mb-0' : ''}} d-none d-md-block mt-2">
                <div class="{{in_array($activeSettingsTab, ['subscriptions','payments', 'referrals']) ? '' : 'px-4 px-md-3'}}">
                    @include('elements.settings.settings-'.$activeSettingsTab)
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-danger fade" tabindex="-1" id="update_withdraw_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update withdraw method</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 w-100 withdraw_method_update" id="bank_update" style="display: none">
                            <form action="{{ route('my.settings.withdraw.update', ['type' => 'bank']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="bank">
                                <input type="hidden" name="id" id="update_bank_id">
                                <div class="form-group w-100">
                                    <label id="" for="withdrawal-payment-identifier">Bank Name</label>
                                    <input class="form-control" type="text" id="update_bank_name" name="bank_name" required>
                                </div>
                                <div class="form-group w-100">
                                    <label id="" for="withdrawal-payment-identifier">Account Name</label>
                                    <input class="form-control" type="text" id="update_account_name" name="account_name" required>
                                </div>
                                <div class="form-group w-100">
                                    <label id="" for="withdrawal-payment-identifier">Account Number/IBAN</label>
                                    <input class="form-control" type="text" id="update_account_number" name="account_number" required>
                                </div>
                                <div class="form-group w-100">
                                    <label id="" for="withdrawal-payment-identifier">SWIFT/BIC Code</label>
                                    <input class="form-control" type="text" id="update_swift_code" name="swift_code" required>
                                </div>

                                <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Submit')}}</button>
                            </form>
                        </div>
                        <div class="col-12 w-100 withdraw_method_update" id="paypal_update" style="display: none">
                            <form action="{{ route('my.settings.withdraw.update', ['type' => 'paypal']) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="paypal">
                                <input type="hidden" name="id" id="update_paypal_id">
                                <div class="form-group w-100">
                                    <label id="" for="withdrawal-payment-identifier">PayPal Email</label>
                                    <input class="form-control" type="email" id="update_paypal_email" name="paypal_email" required>
                                </div>

                                <button class="btn btn-primary btn-block rounded mr-0 withdrawal-continue-btn" type="submit">{{__('Submit')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop
