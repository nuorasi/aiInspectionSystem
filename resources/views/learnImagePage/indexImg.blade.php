<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Learn an Image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">
                        Upload photos for the AI engine
                    </h3>

                    {{-- Dropzone form --}}
                    <div class="relative">
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
            width: 75vw;                 /* 75 percent of viewport width */
            max-width: 100%;             /* Do not overflow container */
            margin: 0 auto;              /* Center horizontally */
            border: 2px dashed #9ca3af;  /* gray-400 */
            border-radius: 0.75rem;      /* Rounded corners */
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
            maxFilesize: 5,
            acceptedFiles: "image/*",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            init: function () {
                const spinner = document.getElementById('upload-spinner');
                const statusEl = document.getElementById('upload-status');
                const imageWrapper = document.getElementById('uploaded-image-wrapper');
                const imageEl = document.getElementById('uploaded-image');

                this.on("sending", function (file) {
                    // Show loading spinner
                    spinner.classList.remove('hidden');
                    statusEl.textContent = 'Uploading file...';
                });

                this.on("success", function (file, response) {
                    // Hide spinner
                    spinner.classList.add('hidden');

                    // Compute file size in KB or MB
                    const bytes = file.size;
                    let sizeText;
                    if (bytes > 1024 * 1024) {
                        sizeText = (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                    } else {
                        sizeText = (bytes / 1024).toFixed(2) + ' KB';
                    }

                    // Use client side date info (last modified) as "file date"
                    const fileDate = new Date(file.lastModified || Date.now());
                    const dateText = fileDate.toLocaleString();

                    statusEl.textContent =
                        'File was loaded successfully. Size: ' + sizeText + '. Date: ' + dateText + '.';

                    // Show full width image below the drop zone
                    imageEl.src = response.url;
                    imageWrapper.classList.remove('hidden');
                });

                this.on("error", function (file, errorMessage) {
                    spinner.classList.add('hidden');
                    statusEl.textContent = 'There was an error uploading the file: ' + errorMessage;
                });
            }
        });
    </script>
</x-app-layout>
