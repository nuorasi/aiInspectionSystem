<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload Photos</title>

    <!-- Dropzone CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"/>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        #uploaded-images img {
            max-width: 200px;
            margin: 10px;
        }
    </style>
</head>

<body>

<h1>Upload Photos</h1>

<form
    action="{{ route('photos.upload') }}"
    method="post"
    class="dropzone"
    id="my-dropzone"
>
    @csrf
</form>

<div id="uploaded-images" class="mt-4"></div>

<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
    // Disable auto discover
    Dropzone.autoDiscover = false;

    const dz = new Dropzone("#my-dropzone", {
        paramName: "file",
        maxFilesize: 5,
        acceptedFiles: "image/*",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        init: function () {
            this.on("success", function (file, response) {
                const container = document.getElementById('uploaded-images');
                const img = document.createElement('img');
                img.src = response.url;
                container.appendChild(img);
            });
        }
    });
</script>

</body>
</html>
