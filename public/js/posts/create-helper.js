/**
 * Post create (helper) component
 */
"use strict";
/* global app, Post, user, FileUpload, updateButtonState, launchToast, trans, redirect, trans_choice, mediaSettings, passesMinMaxPPVContentCreationLimits */

$(function () {
    $("#post-price").keypress(function(e) {
        if(e.which === 13) {
            PostCreate.savePostPrice();
        }
    });
});

var PostCreate = {
    // Paid post price
    postPrice : 0,
    isSavingRedirect: false,
    postNotifications: false,
    postReleaseDate: null,
    postExpireDate: null,
    suggessionList: [],
    taggedList: [],
    isCategoryRequired: false,
    categories: [],

    updateCategoryOption: function(){
        var hasVideo = FileUpload.attachaments.filter(item => item.type == 'video');

        if(hasVideo.length > 0) {
            PostCreate.isCategoryRequired = true;
            $("#select-category").removeClass('d-none');
            $("#select-category").addClass('d-flex');

        } else {
            PostCreate.categories = [];

            PostCreate.isCategoryRequired = false;
            $("#select-category").removeClass('d-flex');
            $("#select-category").addClass('d-none');
        }

    },

    /**
     * Toggles post notification state
     */
    togglePostNotifications: function(){
        let buttonIcon = '';
        if(PostCreate.postNotifications === true){
            PostCreate.postNotifications = false;
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-off-outline"></ion-icon></div>`;
        }
        else{
            buttonIcon = `<div class="d-flex justify-content-center align-items-center mr-1"><ion-icon class="icon-medium" name="notifications-outline"></ion-icon></div>`;
            PostCreate.postNotifications = true;
        }
        $('.post-notification-icon').html(buttonIcon);
    },

    /**
     * Shows up the post price setter dialog
     */
    showSetPricePostDialog: function(){
        $('#post-set-price-dialog').modal('show');
    },

    /**
     * Saves the post price into the state
     */
    savePostPrice: function(){
        PostCreate.postPrice = $('#post-price').val();
        let hasError = false;
        if(!passesMinMaxPPVContentCreationLimits(PostCreate.postPrice)){
            hasError = 'min';
        }
        if(PostCreate.postExpireDate !== null){
            hasError = 'ppv';
        }
        if(hasError){
            $('.post-price-error').addClass('d-none');
            $('#post-set-price-dialog .'+hasError+'-error').removeClass('d-none');
            $('#post-price').addClass('is-invalid');
            return false;
        }
        $('.post-price-label').html('('+(app.currencySymbol ? app.currencySymbol : '' )+PostCreate.postPrice+(app.currencySymbol ? '' : '' + app.currency)+')');
        $('#post-set-price-dialog').modal('hide');
        $('#post-price').removeClass('is-invalid');
    },
    /**
     * Clears up post price
     */
    clearPostPrice: function(){
        PostCreate.postPrice = 0;
        $('#post-price').val(0);
        $('.post-price-label').html('');
        $('#post-set-price-dialog').modal('hide');
        $('#post-price').removeClass('is-invalid');
    },


    /**
     * Shows up the post price setter dialog
     */
    showSetTagPostDialog: function(){
        $('#post-tag-creator-dialog').modal('show');
    },

    /**
     * Saves the post price into the state
     */
    savePostTags: function(){
        console.log('Working 2...');
    },


    /**
     * Clears up post price
     */
    clearPostTag: function(){
        console.log("Working...");
    },

    getCreator: function(e) {

        var name = e.target.value;

        if(name.length === 0) {
            return;
        }

        var tags = null;

        if(PostCreate.taggedList.length === 0) {
            tags = '';
        }

        PostCreate.taggedList.map(item => {
            if(tags === null) {
                tags = '&not[]='+item.id;
            } else {
                tags += '&not[]='+item.id;
            }
        });

        $.ajax({
            type: 'GET',
            url: '/fetch-creator?mention='+name+tags,
            success: function(data) {
                if(data.length === 0)
                    return;

                var html = '<div style="background-color: white; min-width: 250px; padding: 20px; border-radius: 6px; color: black;">';

                data.map(item => {
                    console.log(item);
                    PostCreate.suggessionList.push(item);
                    html += `<div style="padding: 5px; border-bottom: 1px solid #bbbbbb;" onclick="PostCreate.selectCreator(${item.id})">${item.name} (@${item.username})</div>`;
                });

                html += '</div>';

                $("#creator-suggest").append(html);
            }
        });
    },


    selectCreator: function(id) {
        var item = PostCreate.suggessionList.find(item => item.id === id);
        PostCreate.taggedList.push(item);
        PostCreate.updateTaggedList();
        $("#post-tag").val("");
        $("#creator-suggest").children().remove();
        $("#post-tag-creator-dialog").modal('hide');
    },

    updateTaggedList: function() {

        $("#tagged-list").children().remove();

        if(PostCreate.taggedList.length === 0) {
            return;
        }

        var html = '<span style="font-size: 12px; color:#C1262C;">tags: </span>';

        PostCreate.taggedList.map(item => {
            html += `<span style="font-size: 12px; color:#C1262C;" title="Click to remove" onclick="PostCreate.removeTag(${item.id})">@${item.username} </span>`;
        });

        $("#tagged-list").append(html);
    },


    removeTag: function(id) {

        var items = PostCreate.taggedList.filter(item => item.id !== id);

        PostCreate.taggedList = items;

        PostCreate.updateTaggedList();
    },


    showInvite: function() {
        $("#post-tag-creator-dialog").modal('hide');
        $('#invite-user-dialog').modal('show');
        console.log("Hello");
    },


    sendInvitationEmail: function() {
        var email = $("#inite_email").val();

        if(email.length === 0) {
            alert("Email is emtpy!");
            return;
        }

        $.ajax({
            'url': '/send-invitation/email?email='+email,
            'method': 'GET',
            success: function(data) {
                $('#invite-user-dialog').modal('hide');
            }
        });
    },

    /**
     * Initiates the post draft data, if available
     * @param data
     * @param type
     */
    initPostDraft: function(data, type = 'draft'){
        Post.initialDraftData = Post.draftData;
        if(data){
            Post.draftData = data;
            if(type === 'draft'){
                FileUpload.attachaments = data.attachments;
            }
            else{
                data.attachments.map(function (item) {
                    FileUpload.attachaments.push({attachmentID: item.id, path: item.path, type:item.attachmentType, thumbnail:item.thumbnail});
                });
            }
            $('#dropzone-uploader').val(Post.draftData.text);
        }
    },

    /**
     * Clears up post draft data
     */
    clearDraft: function(){
        // Clearing attachments from the backend
        Post.draftData.attachments.map(function (value) {
            FileUpload.removeAttachment(value.attachmentID);
        });
        // Removing previews
        $('.dropzone-previews .dz-preview ').each(function (index, item) {
            $(item).remove();
        });
        // Clearing Fileupload class attachments
        FileUpload.attachaments = [];
        // Clearing up the local storage object
        PostCreate.clearDraftData();
        // Clearing up the text area value
    },

    /**
     * Saves post draft data
     */
    saveDraftData: function(){
        Post.draftData.attachments = FileUpload.attachaments;
        Post.draftData.text = $('#dropzone-uploader').val();
        localStorage.setItem('draftData', JSON.stringify(Post.draftData));
    },

    /**
     * Clears up draft data
     * @param callback
     */
    clearDraftData: function(callback = null){
        localStorage.removeItem('draftData');
        Post.draftData = Post.initialDraftData;
        if(callback !== null){
            callback;
        }
        $('#dropzone-uploader').val(Post.draftData.text);
    },


    /**
     * Populates create/edit post form with draft data
     * @returns {boolean|any}
     */
    populateDraftData: function(){
        const draftData = localStorage.getItem('draftData');
        if(draftData){
            return JSON.parse(draftData);
        }
        else{
            return false;
        }
    },

    /**
     * Save new / update post
     * @param type
     * @param postID
     */
    save: function (type = 'create', postID = false, forceSave = false) {
        if(FileUpload.isLoading === true && forceSave === false){
            $('.confirm-post-save').unbind('click');
            $('.confirm-post-save').on('click',function () {
                PostCreate.save(type, postID, true);
            });
            $('#confirm-post-save').modal('show');
            return false;
        }

        console.log(PostCreate.categories, FileUpload.attachaments);

        if(PostCreate.isCategoryRequired === true && PostCreate.categories.length === 0) {
            alert("You have to add minoum 1 category to post any video.");
            return false;
        }

        updateButtonState('loading',$('.post-create-button'));
        PostCreate.savePostScheduleSettings();
        let route = app.baseUrl + '/posts/save';
        let data = {
            'attachments': FileUpload.attachaments,
            'text': $('#dropzone-uploader').val(),
            'price': PostCreate.postPrice,
            'postNotifications' : PostCreate.postNotifications,
            'postReleaseDate': PostCreate.postReleaseDate,
            'postExpireDate': PostCreate.postExpireDate,
            'postTagged': PostCreate.taggedList,
            'postCategories': PostCreate.categories,
            'isCategoryRequired': PostCreate.isCategoryRequired
        };
        if(type === 'create'){
            data.type = 'create';
        }
        else{
            data.type = 'update';
            data.id = postID;
        }
        $.ajax({
            type: 'POST',
            data: data,
            url: route,
            success: function () {
                if(type === 'create'){
                    PostCreate.isSavingRedirect = true;
                    PostCreate.clearDraftData(redirect(app.baseUrl+'/'+user.username));
                }
                else{
                    redirect(app.baseUrl+'/posts/'+postID+'/'+user.username);
                }
                updateButtonState('loaded',$('.post-create-button'), trans('Save'));
                $('#confirm-post-save').modal('hide');
            },
            error: function (result) {
                if(result.status === 422 || result.status === 500) {
                    $.each(result.responseJSON.errors, function (field, error) {
                        if (field === 'text') {
                            $('.post-invalid-feedback').html(trans_choice('Your post must contain more than 10 characters.',mediaSettings.max_post_description_size, {'num':mediaSettings.max_post_description_size}));
                            $('#dropzone-uploader').addClass('is-invalid');
                            $('#dropzone-uploader').focus();
                        }
                        if (field === 'attachments') {
                            $('.post-invalid-feedback').html(trans('Your post must contain at least one attachment.'));
                            $('#dropzone-uploader').addClass('is-invalid');
                            $('#dropzone-uploader').focus();
                        }

                        if(field === 'permissions'){
                            launchToast('danger',trans('Error'),error);
                        }
                    });
                }
                else if(result.status === 403){
                    launchToast('danger',trans('Error'),'Post not found.');
                }
                $('#confirm-post-save').modal('hide');
                updateButtonState('loaded',$('.post-create-button'), trans('Save'));
            }
        });
    },

    /**
     * Shows up the post scheduling setting setter dialog
     */
    showPostScheduleDialog: function(){
        $('#post-set-schedule-dialog').modal('show');
    },

    /**
     * Saves the post post scheduling setting into the state
     */
    savePostScheduleSettings: function(){

        if(PostCreate.postPrice !== 0){
            $('#post_expire_date').addClass('is-invalid');
            return false;
        }

        PostCreate.postReleaseDate = $('#post_release_date').val();
        PostCreate.postExpireDate = $('#post_expire_date').val();
        $('#post-set-schedule-dialog').modal('hide');
        $('#post_expire_date').removeClass('is-invalid');

    },
    /**
     * Clears up post scheduling setting
     */
    clearPostScheduleSettings: function(){
        PostCreate.postReleaseDate = null;
        PostCreate.postExpireDate = null;
        $('#post_release_date').val('');
        $('#post_expire_date').val('');
        $('#post-set-schedule-dialog').modal('hide');
    },

};
