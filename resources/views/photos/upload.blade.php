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

        /* Custom Dropzone Style */
        .custom-dropzone {
            width: 75vw; /* 75 percent of screen width */
            margin: 0 auto;
            border: 2px dashed #999;
            border-radius: 15px; /* Rounded corners */
            background: #f9f9f9;
            padding: 40px;
            text-align: center;
            cursor: pointer;
        }

        .custom-dropzone .dz-message {
            font-size: 18px;
            color: #444;
            opacity: 0.9;
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
    class="dropzone custom-dropzone"
    id="my-dropzone"
>
    @csrf

    <div class="dz-message">
        Please drop photos here that you would like the AI engine to learn.
    </div>
</form>

<div id="uploaded-images" class="mt-4"></div>

<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
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
