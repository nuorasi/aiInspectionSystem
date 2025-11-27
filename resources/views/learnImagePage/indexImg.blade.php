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

                        <div id="upload-spinner" class="hidden absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 rounded-xl">
                            <img
                                src="{{ asset('images/loading.gif') }}"
                                alt="Uploading..."
                                class="w-16 h-16"
                            >
                        </div>
                    </div>

                    <div id="upload-status" class="mt-4 text-sm text-gray-700 dark:text-gray-300"></div>

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

    <link
        rel="stylesheet"
        href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"
    />

    <style>
        #learn-dropzone {
            width: 90vw;
            max-width: 100%;
            margin: 0 auto;
            border: 2px dashed #9ca3af;
            border-radius: 0.75rem;
            background: #f9fafb;
            padding: 2.5rem 1.5rem;
            text-align: center;
            cursor: pointer;
        }

        #learn-dropzone .dz-message {
            font-size: 1.1rem;
            color: #4b5563;
        }
    </style>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        const dz = new Dropzone("#learn-dropzone", {
            paramName: "file",
            maxFilesize: 5,
            acceptedFiles: "image/*",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf
