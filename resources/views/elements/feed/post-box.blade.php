<div class="post-box" data-postID="{{$post->id}}">
    <div class="post-header pl-3 pr-3">
        <div class="d-flex">
            <div class="avatar-wrapper">
                <img class="avatar rounded-circle" src="{{$post->user->avatar}}">
            </div>
            <div class="post-details pl-2 w-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-bold"><a href="{{route('profile',['username'=>$post->user->username])}}" class="text-dark-r">{{$post->user->name}}</a></div>
                        <div class="d-flex">
                            <a href="{{route('profile',['username'=>$post->user->username])}}" class="text-dark-r text-hover"><span>@</span>{{$post->user->username}}</a>
                            @if ($post->tags->count() > 0)
                                <div style="margin-left: 10px"> with @foreach ($post->tags as $item)
                                    <span><a href="{{ route('profile', ['username' => $item->user->username]) }}">{{ '@'.$item->user->username }}</a></span>
                                @endforeach </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex">
                        @if(Auth::check() && (($post->user_id === Auth::user()->id && $post->status == 0) || (Auth::user()->role_id === 1) && $post->status == 0) )
                            <div class="pr-3 pr-md-3"><span class="badge badge-pill bg-gradient-faded-secondary">{{ucfirst(__("pending"))}}</span></div>
                        @endif

                        {{--                        {{$post->release_date}}--}}
                        @if($post->expire_date)
                            <div class="pr-3 pr-md-3">
                                    <span class="badge badge-pill bg-gradient-faded-primary"  data-toggle="{{!$post->is_expired ? 'tooltip' : ''}}" data-placement="bottom" title="{{!$post->is_expired ? __('Expiring in') .''. \Carbon\Carbon::parse($post->expire_date)->diffForHumans(null,false,true) : ''}}">
                                        {{!$post->is_expired ? ucfirst(__("Expiring")) : ucfirst(__("Expired"))}}
                                    </span>
                            </div>
                        @endif
                        @if(Auth::check() && $post->release_date && Auth::user()->id === $post->user_id && $post->is_scheduled)
                            @if($post->release_date > \Carbon\Carbon::now())
                                <div class="pr-3 pr-md-3">
                                        <span class="badge badge-pill bg-gradient-faded-primary" data-toggle="{{$post->is_scheduled ? 'tooltip' : ''}}" data-placement="bottom" title="{{$post->is_scheduled ? __('Posting in') .''. \Carbon\Carbon::parse($post->release_date)->diffForHumans(null,false,true) : ''}}">
                                            {{ucfirst(__("Scheduled"))}}
                                        </span>
                                </div>
                            @endif
                        @endif
                        @if(Auth::check() && $post->user_id === Auth::user()->id && $post->price > 0)
                            <div class="pr-3 pr-md-3"><span class="badge badge-pill bg-gradient-faded-primary">{{ucfirst(__("PPV"))}}</span></div>
                        @endif

                        <div class="pr-3 pr-md-3"><a class="text-dark-r text-hover" onclick="PostsPaginator.goToPostPageKeepingNav({{$post->id}},{{$post->postPage}},'{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')" href="javascript:void(0)">{{$post->created_at->diffForHumans(null,false,true)}}</a></div>
                        <div class="dropdown {{GenericHelper::getSiteDirection() == 'rtl' ? 'dropright' : 'dropleft'}}">
                            <a class="btn btn-sm text-dark-r text-hover btn-outline-{{(Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? 'dark' : 'light') : (Cookie::get('app_theme') == 'dark' ? 'dark' : 'light'))}} dropdown-toggle px-2 py-1 m-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                @include('elements.icon',['icon'=>'ellipsis-horizontal-outline'])
                            </a>
                            <div class="dropdown-menu">
                                <!-- Dropdown menu links -->
                                <a class="dropdown-item" href="javascript:void(0)" onclick="shareOrCopyLink('{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')">{{__('Copy post link')}}</a>
                                @if (Auth::check() && Auth::user()->load('twitter')->twitter != null)
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="sharePostOnTwitter('{{route('posts.share',['post_id'=>$post->id,'username'=>$post->user->username])}}')">Share post on Twitter</a>
                                @endif
                                @if(Auth::check())
                                    <a class="dropdown-item bookmark-button {{PostsHelper::isPostBookmarked($post->bookmarks) ? 'active' : ''}}" href="javascript:void(0);" onclick="Post.togglePostBookmark({{$post->id}});">{{PostsHelper::isPostBookmarked($post->bookmarks) ? __('Remove the bookmark') : __('Bookmark this post') }} </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showListManagementConfirmation('{{'unfollow'}}', {{$post->user->id}});">{{__('Unfollow')}}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showListManagementConfirmation('{{'block'}}', {{$post->user->id}});">{{__('Block')}}</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="Lists.showReportBox({{$post->user->id}},{{$post->id}});">{{__('Report')}}</a>
                                    @if(Auth::check() && Auth::user()->id == $post->user->id)
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{route('posts.edit',['post_id'=>$post->id])}}">{{__('Edit post')}}</a>
                                        @if(!getSetting('compliance.minimum_posts_deletion_limit') || (getSetting('compliance.minimum_posts_deletion_limit') > 0 && count($post->user->posts) > getSetting('compliance.minimum_posts_deletion_limit')))
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="Post.confirmPostRemoval({{$post->id}});">{{__('Delete post')}}</a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="post-content mt-3  pl-3 pr-3">
        <p class="text-break post-content-data {{getSetting('feed.enable_post_description_excerpts') && (strlen($post->text) >= 85 || substr_count($post->text,"\r\n") > 1) ? 'line-clamp-1 pb-0 mb-0' : ''}}">{{$post->text}}</p>
        @if(getSetting('feed.enable_post_description_excerpts') && (strlen($post->text) >= 85 || substr_count($post->text,"\r\n") > 1))
            <span class="text-primary pointer-cursor" onclick="Post.toggleFullDescription({{$post->id}})">
                <span class="label-more">{{__('More info')}}</span>
                <span class="label-less d-none">{{__('Show less')}}</span>
            </span>
        @endif
    </div>

    <div class="d-flex mb-2">
        @if ($post->categories->count() > 0)
            <div style="margin-left: 10px"> Categories: @foreach ($post->categories as $item)
                <span><a href="{{ route('feed', ['slug' => $item->slug]) }}">{{ $item->name }}</a>@if(!$loop->last), @endif</span>
            @endforeach </div>
        @endif
    </div>
    @if(count($post->attachments))
        <div class="post-media">
            @if($post->isSubbed || (getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
                @if((Auth::check() && Auth::user()->id !== $post->user_id && (!PostsHelper::hasUserUnlockedPost($post->postPurchases) && $post->price > 0)) || (!Auth::check() && $post->price > 0 ))
                    @include('elements.feed.post-locked',['type'=>'post','post'=>$post])
                @else
                    @if(count($post->attachments) > 1)
                        <div class="swiper-container mySwiper pointer-cursor">
                            <div class="swiper-wrapper">
                                @foreach($post->attachments as $attachment)
                                    <div class="swiper-slide">
                                        @include('elements.feed.post-box-media-wrapper',[
                                            'attachment' => $attachment,
                                            'isGallery' => true,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button swiper-button-next p-pill-white">@include('elements.icon',['icon'=>'chevron-forward-outline'])</div>
                            <div class="swiper-button swiper-button-prev p-pill-white">@include('elements.icon',['icon'=>'chevron-back-outline'])</div>
                            <div class="swiper-pagination"></div>
                        </div>
                    @else
                        @include('elements.feed.post-box-media-wrapper',[
                            'attachment' => $post->attachments[0],
                            'isGallery' => false,
                        ])
                    @endif
                @endif
            @else
                @include('elements.feed.post-locked',['type'=>'subscription',])
            @endif
        </div>
    @endif
    <div class="post-footer mt-3 pl-3 pr-3">
        <div class="footer-actions d-flex justify-content-between">
            <div class="d-flex">
                {{-- Likes --}}
                @if($post->isSubbed || (Auth::check() && getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
                    <div class="h-pill h-pill-primary mr-1 rounded react-button {{PostsHelper::didUserReact($post->reactions) ? 'active' : ''}}" data-toggle="tooltip" data-placement="top" title="{{__('Like')}}" onclick="Post.reactTo('post',{{$post->id}})">
                        @if(PostsHelper::didUserReact($post->reactions))
                            @include('elements.icon',['icon'=>'heart', 'variant' => 'medium', 'classes' =>"text-primary"])
                        @else
                            @include('elements.icon',['icon'=>'heart-outline', 'variant' => 'medium'])
                        @endif
                    </div>
                @else
                    <div class="h-pill h-pill-primary mr-1 rounded react-button disabled">
                        @include('elements.icon',['icon'=>'heart-outline', 'variant' => 'medium'])
                    </div>
                @endif
                {{-- Comments --}}
                @if(Route::currentRouteName() != 'posts.get')
                    @if($post->isSubbed || (Auth::check() && getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
                        <div class="h-pill h-pill-primary mr-1 rounded" data-toggle="tooltip" data-placement="top" title="{{__('Show comments')}}" onClick="Post.showPostComments({{$post->id}},6)">
                            @include('elements.icon',['icon'=>'chatbubble-outline', 'variant' => 'medium'])
                        </div>
                    @else
                        <div class="h-pill h-pill-primary mr-1 disabled rounded">
                            @include('elements.icon',['icon'=>'chatbubble-outline', 'variant' => 'medium'])
                        </div>
                    @endif
                @endif
                {{-- Tips --}}
                @if(Auth::check() && $post->user->id != Auth::user()->id)
                    @if($post->isSubbed || (getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
                        <div class="h-pill h-pill-primary send-a-tip to-tooltip poi {{(!GenericHelper::creatorCanEarnMoney($post->user)) ? 'disabled' : ''}}"
                             @if(!GenericHelper::creatorCanEarnMoney($post->user))
                             data-placement="top"
                             title="{{__('This creator cannot earn money yet')}}">
                            @else
                                data-toggle="modal"
                                data-target="#checkout-center"
                                data-post-id="{{$post->id}}"
                                data-type="tip"
                                data-first-name="{{Auth::user()->first_name}}"
                                data-last-name="{{Auth::user()->last_name}}"
                                data-billing-address="{{Auth::user()->billing_address}}"
                                data-country="{{Auth::user()->country}}"
                                data-city="{{Auth::user()->city}}"
                                data-state="{{Auth::user()->state}}"
                                data-postcode="{{Auth::user()->postcode}}"
                                data-available-credit="{{Auth::user()->wallet->total}}"
                                data-username="{{$post->user->username}}"
                                data-name="{{$post->user->name}}"
                                data-avatar="{{$post->user->avatar}}"
                                data-recipient-id="{{$post->user_id}}">
                            @endif
                            <div class=" d-flex align-items-center">
                                @include('elements.icon',['icon'=>'gift-outline', 'variant' => 'medium'])
                                <div class="ml-1 d-none d-lg-block"> {{__('Send a tip')}} </div>
                            </div>
                        </div>
                    @else
                        <div class="h-pill h-pill-primary send-a-tip disabled">
                            <div class=" d-flex align-items-center">
                                @include('elements.icon',['icon'=>'gift-outline', 'variant' => 'medium'])
                                <div class="ml-1 d-none d-md-block"> {{__('Send a tip')}} </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="mt-0 d-flex align-items-center justify-content-center post-count-details">
                <span class="ml-2-h">
                    <strong class="text-bold post-reactions-label-count">{{count($post->reactions)}}</strong>
                    <span class="post-reactions-label">{{trans_choice('likes', count($post->reactions))}}</span>
                </span>
                @if($post->isSubbed || (Auth::check() && getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
                    <span class="ml-2-h d-none d-lg-block">
                    <a href="{{Route::currentRouteName() != 'posts.get' ? route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username]) : '#comments'}}" class="text-dark-r text-hover">
                        <strong class="post-comments-label-count">{{count($post->comments)}}</strong>
                       <span class="post-comments-label">
                        {{trans_choice('comments',  count($post->comments))}}
                       </span>
                    </a>
                </span>
                @else
                    <span class="ml-2-h d-none d-lg-block">
                        <strong class="post-comments-label-count">{{count($post->comments)}}</strong>
                       <span class="post-comments-label">
                        {{trans_choice('comments',  count($post->comments))}}
                       </span>
                </span>
                @endif
                <span class="ml-2-h d-none d-lg-block">
                    <strong class="post-tips-label-count">{{$post->tips_count}}</strong>
                    <span class="post-tips-label">{{trans_choice('tips',$post->tips_count)}}</span>
                </span>
            </div>
        </div>
    </div>

    @if($post->isSubbed || (Auth::check() && getSetting('profiles.allow_users_enabling_open_profiles') && $post->user->open_profile))
        <div class="post-comments d-none" {{Route::currentRouteName() == 'posts.get' ? 'id="comments"' : ''}}>
            <hr>

            <div class="px-3 post-comments-wrapper">
                <div class="comments-loading-box">
                    @include('elements.preloading.messenger-contact-box',['limit'=>1])
                </div>
            </div>
            <div class="show-all-comments-label pl-3 d-none">
                @if(Route::currentRouteName() != 'posts.get')
                    <a href="javascript:void(0)" onclick="PostsPaginator.goToPostPageKeepingNav({{$post->id}},{{$post->postPage}},'{{route('posts.get',['post_id'=>$post->id,'username'=>$post->user->username])}}')">{{__('Show more')}}</a>
                @else
                    <a onClick="CommentsPaginator.loadResults({{$post->id}});" href="javascript:void(0);">{{__('Show more')}}</a>
                @endif
            </div>
            <div class="no-comments-label pl-3 d-none">
                {{__('No comments yet.')}}
            </div>
            @if(Auth::check())
                <hr>
                @include('elements.feed.post-new-comment')
            @endif
        </div>
    @endif

</div>
