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
                    {{-- Photos Table --}}
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold mb-4">Uploaded Photos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left border border-gray-600">
                                <thead class="bg-gray-700 text-white">
                                <tr>
                                    <th class="px-3 py-2 border">ID</th>
                                    <th class="px-3 py-2 border">Disk</th>
                                    <th class="px-3 py-2 border">Path</th>
                                    <th class="px-3 py-2 border">File Name</th>
                                    <th class="px-3 py-2 border">Mime</th>
                                    <th class="px-3 py-2 border">Size (bytes)</th>
                                    <th class="px-3 py-2 border">Width</th>
                                    <th class="px-3 py-2 border">Height</th>

                                    <th class="px-3 py-2 border">EXIF</th>

                                    <th class="px-3 py-2 border">Image</th>
                                    <th class="px-3 py-2 border">Product</th>
                                    <th class="px-3 py-2 border">Size</th>
                                    <th class="px-3 py-2 border">Type</th>
                                    <th class="px-3 py-2 border">Installation Status</th>
                                    <th class="px-3 py-2 border">Confidence</th>
                                    <th class="px-3 py-2 border">Created</th>
                                    <th class="px-3 py-2 border">Updated</th>
                                </tr>
                                </thead>

                                <tbody class="text-gray-900 dark:text-gray-200">
                                @foreach ($photos as $photo)
                                    <tr class="border-t border-gray-600">
                                        <td class="px-3 py-2 border">{{ $photo->id }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->disk }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->path }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->file_name }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->mime_type }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->size_bytes }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->width }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->height }}</td>

                                        {{-- EXIF column - starts hidden --}}
                                        <td class="px-3 py-2 border max-w-[300px] overflow-x-auto">
                                            <button
                                                type="button"
                                                class="mb-2 inline-flex items-center px-2 py-1 text-xs font-semibold border border-gray-500 rounded-md bg-gray-800 text-white hover:bg-gray-700 exif-toggle-btn"
                                            >
                                                <svg
                                                    class="w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                <span class="exif-toggle-label">Show</span>
                                            </button>

                                            <pre class="whitespace-pre-wrap text-xs exif-content hidden">
{{ json_encode($photo->exif, JSON_PRETTY_PRINT) }}
    </pre>
                                        </td>



                                        {{-- Thumbnail column - always visible --}}
                                        <td class="px-3 py-2 border">
                                            <img
                                                src="{{ Storage::disk($photo->disk)->url($photo->path_thumb) }}"
                                                alt="Image"
                                                class="w-20 h-auto rounded"
                                            />

                                        </td>


                                        <td class="px-3 py-2 border">{{ $photo->product }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->size }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->type }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->installationStatus }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->confidence }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->created_at }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->updated_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    {{-- Manual Metadata Modal --}}
    <div
        id="meta-modal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Enter Image Details
            </h3>

            <form id="meta-form" class="space-y-4">
                <input type="hidden" id="meta-photo-id" name="photo_id">

                {{-- Product --}}
                <div>
                    <label for="meta-product" class="block text-sm font-medium mb-1">
                        Product
                    </label>
                    <select
                        id="meta-product"
                        name="product"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                    >
                        <option value="">Select product</option>
                        <option value="Rigid Coupling">Rigid Coupling</option>
                        <option value="Flexible Coupling">Flexible Coupling</option>
                        <option value="Grooved Fitting">Grooved Fitting</option>
                    </select>
                </div>

                {{-- Size --}}
                <div>
                    <label for="meta-size" class="block text-sm font-medium mb-1">
                        Size
                    </label>
                    <select
                        id="meta-size"
                        name="size"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                    >
                        <option value="">Select size</option>
                        <option value="2 inch">2 inch</option>
                        <option value="3 inch">3 inch</option>
                        <option value="4 inch">4 inch</option>
                        <option value="6 inch">6 inch</option>
                    </select>
                </div>

                {{-- Installation Status --}}
                <div>
                    <label for="meta-installationStatus" class="block text-sm font-medium mb-1">
                        Installation Status
                    </label>
                    <select
                        id="meta-installationStatus"
                        name="installationStatus"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                    >
                        <option value="">Select status</option>
                        <option value="Complete">Complete</option>
                        <option value="Incomplete">Incomplete</option>
                        <option value="Uncertain">Uncertain</option>
                    </select>
                </div>

                {{-- Confidence --}}
                <div>
                    <label for="meta-confidence" class="block text-sm font-medium mb-1">
                        Confidence (0.0 to 100.0)
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        id="meta-confidence"
                        name="confidence"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                    >
                </div>

                <div id="meta-status" class="text-sm text-red-500"></div>

                <div class="flex justify-end gap-2 pt-4">
                    <button
                        type="button"
                        id="meta-cancel"
                        class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700"
                    >
                        Save
                    </button>
                </div>
            </form>
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
                    console.log('Dropzone success response:', response);

                    // Remove dropzone thumbnail
                    this.removeFile(file);

                    spinner.classList.add('hidden');

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

                    // Handle case where response might be a string
                    let photoId = null;
                    if (typeof response === 'string') {
                        try {
                            const parsed = JSON.parse(response);
                            photoId = parsed.photo?.id ?? null;
                        } catch (e) {
                            console.error('Failed to parse response JSON', e);
                        }
                    } else if (response && response.photo && response.photo.id) {
                        photoId = response.photo.id;
                    }

                    console.log('Resolved photoId for modal:', photoId);

                    if (photoId) {
                        openMetaModal(photoId);
                    }
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

    <script>
        function openMetaModal(photoId) {
            const modal = document.getElementById('meta-modal');
            const idInput = document.getElementById('meta-photo-id');
            const statusEl = document.getElementById('meta-status');

            if (!modal || !idInput) {
                console.error('Meta modal elements not found');
                return;
            }

            idInput.value = photoId;
            statusEl.textContent = '';

            // Clear previous values
            const productEl = document.getElementById('meta-product');
            const sizeEl = document.getElementById('meta-size');
            const statusSelect = document.getElementById('meta-installationStatus');
            const confidenceEl = document.getElementById('meta-confidence');

            if (productEl) productEl.value = '';
            if (sizeEl) sizeEl.value = '';
            if (statusSelect) statusSelect.value = '';
            if (confidenceEl) confidenceEl.value = '';

            modal.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('meta-modal');
            const form = document.getElementById('meta-form');
            const cancelBtn = document.getElementById('meta-cancel');
            const statusEl = document.getElementById('meta-status');

            if (!modal || !form) {
                console.warn('Meta modal or form not found on page');
                return;
            }

            cancelBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                statusEl.textContent = '';

                const photoId = document.getElementById('meta-photo-id').value;
                const product = document.getElementById('meta-product').value;
                const size = document.getElementById('meta-size').value;
                const installationStatus = document.getElementById('meta-installationStatus').value;
                const confidence = document.getElementById('meta-confidence').value;

                const url = "{{ url('/photos') }}/" + photoId + "/manual-meta";

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product: product,
                        size: size,
                        installationStatus: installationStatus,
                        confidence: confidence
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Meta save response:', data);

                        if (!data.success) {
                            statusEl.textContent = 'Error saving metadata.';
                            return;
                        }

                        modal.classList.add('hidden');

                        // Easiest way to see updated table
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error(error);
                        statusEl.textContent = 'Unexpected error saving metadata.';
                    });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.exif-toggle-btn');

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const row = btn.closest('td');
                    if (!row) return;

                    const exifBlock = row.querySelector('.exif-content');
                    const label = btn.querySelector('.exif-toggle-label');

                    if (!exifBlock) return;

                    const isHidden = exifBlock.classList.contains('hidden');

                    if (isHidden) {
                        exifBlock.classList.remove('hidden');
                        if (label) label.textContent = 'Hide';
                    } else {
                        exifBlock.classList.add('hidden');
                        if (label) label.textContent = 'Show';
                    }
                });
            });
        });
    </script>




</x-app-layout>
