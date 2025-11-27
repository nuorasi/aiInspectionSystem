{{-- resources/views/reviewYourImagesPage/indexRyi.blade.php --}}

{{--<x-app-layout>--}}
{{--    <x-slot name="header">--}}
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
{{--            {{ __('Learn an Image') }}--}}
{{--        </h2>--}}
{{--    </x-slot>--}}

{{--    <div class="py-12">--}}
{{--        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">--}}
{{--            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--                <div class="p-6 text-gray-900 dark:text-gray-100">--}}
{{--                    Your Learn Your Images content goes here.--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</x-app-layout>--}}

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

                    {{-- Preview of uploaded images --}}
                    <div id="uploaded-images" class="mt-6 flex flex-wrap gap-4"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dropzone CSS (ok to keep in body, or move to your main layout if you prefer) --}}
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
            border: 2px dashed #9ca3af;  /* Tailwind gray-400 */
            border-radius: 0.75rem;      /* Rounded corners */
            background: #f9fafb;         /* Tailwind gray-50 */
            padding: 2.5rem 1.5rem;
            text-align: center;
            cursor: pointer;
        }

        #learn-dropzone .dz-message {
            font-size: 1.1rem;
            color: #4b5563; /* gray-700 */
        }

        #uploaded-images img {
            max-width: 160px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
    </style>

    {{-- Dropzone JS --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        const dz = new Dropzone("#learn-dropzone", {
            paramName: "file",
            maxFilesize: 5,               // MB
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

                this.on("error", function (file, errorMessage) {
                    console.error(errorMessage);
                });
            }
        });
    </script>
</x-app-layout>
