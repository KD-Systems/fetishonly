@extends('layouts.user-no-nav')
@section('page_title', __('Your feed'))

{{-- Page specific CSS --}}
@section('styles')
{!!
Minify::stylesheet([
'/libs/swiper/swiper-bundle.min.css',
'/libs/photoswipe/dist/photoswipe.css',
'/css/pages/checkout.css',
'/libs/photoswipe/dist/default-skin/default-skin.css',
'/css/pages/feed.css',
'/css/posts/post.css',
'/css/pages/search.css',
])->withFullUrl()
!!}
@if(getSetting('feed.post_box_max_height'))
@include('elements.feed.fixed-height-feed-posts', ['height' => getSetting('feed.post_box_max_height')])
@endif
@stop

{{-- Page specific JS --}}
@section('scripts')
{!!
Minify::javascript([
'/js/PostsPaginator.js',
'/js/CommentsPaginator.js',
'/js/Post.js',
'/js/SuggestionsSlider.js',
'/js/pages/lists.js',
'/js/pages/feed.js',
'/js/pages/checkout.js',
'/libs/swiper/swiper-bundle.min.js',
'/js/plugins/media/photoswipe.js',
'/libs/photoswipe/dist/photoswipe-ui-default.min.js',
'/libs/@joeattardi/emoji-button/dist/index.js',
'/js/plugins/media/mediaswipe.js',
'/js/plugins/media/mediaswipe-loader.js',
])->withFullUrl()
!!}
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-8 col-md-7 second p-0 ">
            <div class="d-flex d-md-none px-3 py-3 feed-mobile-search neutral-bg fixed-top-m">
                @include('elements.search-box')
            </div>

            @if(!getSetting('feed.hide_suggestions_slider'))
            <div class="d-block d-md-none d-lg-none m-pt-70 feed-suggestions-wrapper">
                @include('elements.feed.suggestions-box',['profiles'=>$suggestions, 'isMobile'=> true])
            </div>
            @endif

            {{-- @include('elements.user-stories-box')--}}

            <div class="">
                @include('elements.message-alert',['classes'=>'pt-4 pb-4 px-2'])
                @include('elements.feed.posts-load-more')
                <div class="feed-box mt-0 pt-4 px-4 pb-4 posts-wrapper">
                    {{-- @include('elements.feed.posts-wrapper',['posts'=>$posts]) --}}
                    @foreach ($categories as $item)
                        <a href="{{ route('feed.category', ['slug' => $item->slug]) }}" class="">{{ ucwords($item->name) }} ({{ $item->category_post_count }})</a>, &nbsp;
                    @endforeach
                </div>
                @include('elements.feed.posts-loading-spinner')
            </div>
        </div>
        <div
            class="col-12 col-sm-12 col-md-5 col-lg-4 first border-left order-0 pt-4 pb-5 min-vh-100 suggestions-wrapper d-none d-md-block">

            <div class="feed-widgets">
                <div class="mb-4">
                    @include('elements.search-box')
                </div>

                @if(!getSetting('feed.hide_suggestions_slider'))
                @include('elements.feed.suggestions-box',['profiles'=>$suggestions, 'isMobile'=> false])
                @if(Auth::user()->identity_verified_at == NULL)
                    <a role="button" class="btn btn-round btn-primary btn-block" style="margin-top: 30px" href="/my/settings/verify">
                        <span class="d-none d-md-block d-xl-block d-lg-block ml-2 text-truncate new-post-label">Become a
                            Creator</span>
                        <span class="d-block d-md-none d-flex align-items-center justify-content-center">
                            <div
                                class="ion-icon-wrapper flex-shrink-0 icon-medium d-flex justify-content-center align-items-center">
                                <div class="ion-icon-inner">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                        <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z"
                                            fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32">
                                        </path>
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="32" d="M256 176v160M336 256H176"></path>
                                    </svg>
                                </div>
                            </div>
                        </span>
                    </a>
                @endif
                @endif
                @if(getSetting('custom-code-ads.sidebar_ad_spot'))
                <div class="mt-4">
                    {!! getSetting('custom-code-ads.sidebar_ad_spot') !!}
                </div>
                @endif
            </div>

        </div>
    </div>
    @include('elements.checkout.checkout-box')
</div>

<div class="d-none">
    <ion-icon name="heart"></ion-icon>
    <ion-icon name="heart-outline"></ion-icon>
</div>

@include('elements.standard-dialog',[
'dialogName' => 'comment-delete-dialog',
'title' => __('Delete comment'),
'content' => __('Are you sure you want to delete this comment?'),
'actionLabel' => __('Delete'),
'actionFunction' => 'Post.deleteComment();',
])

@stop
