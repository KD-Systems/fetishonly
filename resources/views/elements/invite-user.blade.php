<div class="modal fade" tabindex="-1" role="dialog" id="invite-user-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__("Invite user who doesn't have account yet.")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <label for="">Invitation Message</label>
                        <textarea name="" id="invite_text" class="form-control">Hello I am {{ Auth::user()->name }}, Inviting you to join with me on {{ route('register') }}</textarea>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Email <span>(optional)</span></label>
                        <input id="inite_email" type="email" class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button class="btn btn-info">Copy Text</button>
                            <button onclick="PostCreate.sendInvitationEmail()" class="btn btn-primary">Send Email</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
