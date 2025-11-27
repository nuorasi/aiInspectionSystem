<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Learn an Image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">

            <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">
                        Upload photos for the AI engine
                    </h3>

                    {{-- Dropzone wrapper for fade in / out --}}
                    <div id="dropzone-wrapper" class="relative transition-opacity duration-500">
                        <form
                            action="{{ route('photos.upload') }}"
                            method="post"
                            class="dropzone"
                            id="learn-dropzone"
                        >
                            @csrf

                            <div class="dz-message">
                                Please drop photos here that you would like the AI engine to learn.
                                You can also click to browse for files.
                            </div>
                        </form>

                        {{-- Loading overlay with rotating gif --}}
                        <div id="upload-spinner" class="hidden absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 rounded-xl">
                            <img
                                src="{{ asset('images/loading.gif') }}"
                                alt="Uploading..."
                                class="w-16 h-16"
                            >
                        </div>
                    </div>

                    {{-- Status message --}}
                    <div id="upload-status" class="mt-4 text-sm text-gray-700 dark:text-gray-300"></div>

                    {{-- Full width uploaded image --}}
                    <div id="uploaded-image-wrapper" class="mt-6 hidden">
                        <img
                            id="uploaded-image"
                            src=""
                            alt="Uploaded photo"
                            class="w-full h-auto rounded-md shadow-md"
                        >
                    </div>

                    {{-- Upload another file button --}}
                    <button
                        id="upload-another-btn"
                        type="button"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden"
                    >
                        Upload another file
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Dropzone CSS --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"
    />

    <style>
        /* Custom Dropzone styling integrated with Tailwind container */
        #learn-dropzone {
            width: 90vw;              /* 90 percent of viewport width */
            max-width: 100%;
            margin: 0 auto;
            border: 3px dashed #9ca3af;  /* gray-400 */
            border-radius: 0.75rem;
            background: #f9fafb;         /* gray-50 */
            padding: 2.5rem 1.5rem;
            text-align: center;
            cursor: pointer;
        }

        #learn-dropzone .dz-message {
            font-size: 1.1rem;
            color: #4b5563; /* gray-700 */
        }
    </style>

    {{-- Dropzone JS --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        const dz = new Dropzone("#learn-dropzone", {
            paramName: "file",
            maxFilesize: 200,            // or whatever MB limit you decided
            acceptedFiles: "image/*",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            init: function () {
                const spinner = document.getElementById('upload-spinner');
                const statusEl = document.getElementById('upload-status');
                const imageWrapper = document.getElementById('uploaded-image-wrapper');
                const imageEl = document.getElementById('uploaded-image');
                const dropzoneWrapper = document.getElementById('dropzone-wrapper');
                const uploadAnotherBtn = document.getElementById('upload-another-btn');
                const dzMessage = document.querySelector('#learn-dropzone .dz-message');

                this.on("sending", function () {
                    dropzoneWrapper.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
                    spinner.classList.remove('hidden');
                    statusEl.textContent = 'Uploading file...';
                });

                this.on("success", function (file, response) {
                    // Remove dropzone thumbnail
                    this.removeFile(file);

                    spinner.classList.add('hidden');

                    // Compute file size for display
                    const bytes = file.size;
                    let sizeText;
                    if (bytes > 1024 * 1024) {
                        sizeText = (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                    } else {
                        sizeText = (bytes / 1024).toFixed(2) + ' KB';
                    }

                    const fileDate = new Date(file.lastModified || Date.now());
                    const dateText = fileDate.toLocaleString();

                    statusEl.textContent =
                        'File was loaded successfully. Size: ' + sizeText + '. Date: ' + dateText + '.';

                    // Show full width image
                    imageEl.src = response.url;
                    imageWrapper.classList.remove('hidden');

                    if (dzMessage) {
                        dzMessage.textContent =
                            'File uploaded. Use "Upload another file" if you want to add more images for the AI engine to learn.';
                    }

                    dropzoneWrapper.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(function () {
                        dropzoneWrapper.classList.add('hidden');
                    }, 500);

                    uploadAnotherBtn.classList.remove('hidden');
                });

                // IMPORTANT: new error handler
                this.on("error", function (file, errorMessage, xhr) {
                    spinner.classList.add('hidden');

                    let msg = 'Error uploading file.';

                    if (xhr && xhr.responseText) {
                        // Try to parse JSON from Laravel
                        try {
                            const res = JSON.parse(xhr.responseText);

                            if (res.errors && res.errors.file && res.errors.file.length) {
                                msg = res.errors.file[0];
                            } else if (res.message) {
                                msg = res.message;
                            } else {
                                msg = 'Server error: ' + xhr.status + ' ' + xhr.statusText;
                            }
                        } catch (e) {
                            // Not JSON, show a trimmed version of the raw response
                            msg = xhr.responseText.substring(0, 200);
                        }
                    } else if (typeof errorMessage === 'string') {
                        msg = errorMessage;
                    } else if (typeof errorMessage === 'object') {
                        // Fall back to JSON string so we never see [object Object]
                        msg = JSON.stringify(errorMessage);
                    }

                    statusEl.textContent = 'Error uploading file: ' + msg;

                    console.error('Dropzone error details:', {
                        file,
                        errorMessage,
                        xhr
                    });
                });

                uploadAnotherBtn.addEventListener('click', function () {
                    statusEl.textContent = '';

                    dropzoneWrapper.classList.remove('hidden');
                    setTimeout(function () {
                        dropzoneWrapper.classList.remove('opacity-0', 'pointer-events-none');
                    }, 10);

                    if (dzMessage) {
                        dzMessage.textContent =
                            'Please drop photos here that you would like the AI engine to learn. You can also click to browse for files.';
                    }

                    uploadAnotherBtn.classList.add('hidden');
                });
            }
        });
    </script>

</x-app-layout>
