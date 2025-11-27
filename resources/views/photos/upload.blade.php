@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Upload Photos</h1>

        <form
            action="{{ route('photos.upload') }}"
            method="post"
            class="dropzone"
            id="my-awesome-dropzone"
        >
            @csrf
        </form>

        <div id="uploaded-images" class="mt-4"></div>
    </div>
@endsection

@section('scripts')
    <script>
        // Tell Dropzone not to auto discover all forms with class 'dropzone'
        Dropzone.autoDiscover = false;

        const myDropzone = new Dropzone("#my-awesome-dropzone", {
            paramName: "file",              // The name that will be used to transfer the file
            maxFilesize: 5,                 // MB
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            timeout: 0,                     // Disable timeout for large files if needed
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            init: function() {
                this.on("success", function(file, response) {
                    // Optional: show uploaded image below
                    const container = document.getElementById('uploaded-images');
                    const img = document.createElement('img');
                    img.src = response.url;
                    img.style.maxWidth = '200px';
                    img.style.marginRight = '10px';
                    container.appendChild(img);
                });

                this.on("error", function(file, errorMessage) {
                    console.error(errorMessage);
                });
            }
        });
    </script>
@endsection
