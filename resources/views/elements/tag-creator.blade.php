<div class="modal fade" tabindex="-1" role="dialog" id="post-tag-creator-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Tag Other Creator')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Search and select creator to tag.')}}</p>
                <div class="input-group">
                    <input id="post-tag" list="tag-list" type="text" class="form-control" name="text" required  placeholder="{{__('Tag Creator')}}" value="" onkeyup="PostCreate.getCreator(event)">
                    <div id="creator-suggest" style="position: absolute; top: 50px;">

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
