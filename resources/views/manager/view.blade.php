@extends('layouts.manager')

@push('styles')
<link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">

@endpush

@push('scripts')
<script src="{{ asset('manager-assets') }}/vendors/select2/select2.min.js"></script>
<script src="{{ asset('manager-assets') }}/js/select2.js"></script>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title"> Upload Media for your creator </h3>
    <nav aria-label="breadcrumb">
        {{-- <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Tables</a></li>
            <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
        </ol> --}}
    </nav>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form class="forms-sample" action="{{ route('manager.update', ['id' => $media->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Write a new post</label>
                                <textarea name="text" id="" cols="30" rows="10" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Select Genre (Upto 3)</label>
                                <select style="width: 100%;" name="categories[]" multiple="multiple"
                                    class="form-control category-select" id="category-select" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="creator-select">Select Performers</label>
                                <select style="width: 100%;" name="creators[]" multiple="multiple"
                                    class="form-control creator-select" id="creator-select"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="files">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                @if ($media->attachment->attachmentType == "image")
                    <img style="width: 100%" src="{{$media->attachment->thumbnail}}" alt="">
                @else
                    <video style="width: 100%" src="{{$media->attachment->thumbnail}}" controls></video>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
        $('.select-creator').select2();
        $(document).ready(function() {
            $('#category-select').select2({
                ajax: {
                    url: function(params) {
                        return '/fetch-categories?name=' + params.term;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    minimumInputLength: 1
                },
                maximumSelectionLength: 3,
                placeholder: "Select up to 3 items",
            });

            $('#creator-select').select2({
                ajax: {
                    url: function(params) {
                        return '/fetch-creator-tags?name='+params.term;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    minimumInputLength: 1
                },
                placeholder: "Tag creator",
            });
        });


        $('.category-select').on('change', function(){
            var categories = $('.category-select').val();
            console.log(categories);
        });

        function addFile(response) {
           var element = `<div class="uploaded-file" onclick="removeFile(this)">
            ${(response.type == 'video') ? `<video src="${response.thumbnail}"></video>` : `<img src="${response.thumbnail}" alt="">`}
            <button type="button" class="remove-file">X</button><input type="hidden" name="files[]" value="${response.attachmentID}"></div>`;

           $("#files").append(element);
        }


        function removeFile(e) {
            console.log(this, $(e).remove());
        }
</script>
@endpush
