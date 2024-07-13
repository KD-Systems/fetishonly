@extends('layouts.manager')

@push('styles')
<link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/select2/select2.min.css">
<link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

<style>

#files {
    display: flex;
    flex-wrap: wrap;
    text-align: center;
    margin-bottom: 20px;
}

#files .uploaded-file {
    width: 180px;
    height: auto;
    position: relative;
    margin: 0, 10px;
}


#files .uploaded-file img,
#files .uploaded-file video {
    width: 100%;
    height: auto;
}

#files .uploaded-file .remove-file {
    border: none;
    background: none;
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translate(-50%, 0);
    color: red;
    background-color: #000000;
}

</style>

@endpush

@push('scripts')
<script src="{{ asset('manager-assets') }}/vendors/select2/select2.min.js"></script>
<script src="{{ asset('manager-assets') }}/js/select2.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Media Upload</h4>
                <p class="card-description"> Select creator and media </p>
                <form class="dropzone" id="myDropzone" action="{{ route('manager.upload.file') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </form>
                <br>
                <form class="forms-sample" action="{{ route('manager.save.post') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Select Creator/Model</label>
                                <select class="select-creator" style="width:100%" name="user" required>
                                    <option value="">SELECT CREATOR</option>
                                    @foreach ($referred as $item)
                                    <option value="{{ $item->usedBy->id }}">{{ $item->usedBy->name }}</option>
                                    @endforeach
                                </select>
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
</div>
@endsection

@push('scripts')
<script>
    $('.select-creator').select2();
        // $("#myDropZone").dropzone({ url: "/file/post" });
        // let myDropzone = new Dropzone("div#dropzone", { url: "/file/post"})

        let myDropzone = new Dropzone("#myDropzone", {
            thumbnailWidth: 200,
            chunking: true,
            forceChunking: true,
            chunkSize: 200000,
            retryChunks: true,
            retryChunksLimit: 3,
            parallelChunkUploads: false,
         });

         myDropzone.on("addedfile", file => {
            // console.log(file);
        });

        myDropzone.on("complete", function(file) {
            myDropzone.removeFile(file);
        });

        myDropzone.on("success", function(file, response) {
            // myDropzone.removeFile(file);
            console.log(response, file);
            addFile(response);
        });


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
