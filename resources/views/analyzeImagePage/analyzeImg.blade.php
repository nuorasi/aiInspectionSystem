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
                                            <img>
                                            <!--src="{{ Storage::disk($photo->disk)->url($photo->path) }}" -->
                                            src="{{ Storage::disk($photo->disk)->url($photo->path_thumb) }}"

                                            alt="Image"
                                            class="w-20 h-auto rounded"
                                            <img>
                                        </td>>
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

    <script>
        // IMPORTANT: disable auto-discovery ONCE
        Dropzone.autoDiscover = false;

        document.addEventListener('DOMContentLoaded', function () {

            /* ============================
               GUARDED DROPZONE INIT
            ============================ */

            const dropzoneEl = document.querySelector('#photo-dropzone');

            if (dropzoneEl && !dropzoneEl.dropzone) {

                const dz = new Dropzone(dropzoneEl, {
                    // Use URL to avoid named route errors until you define photos.store
                    url: "{{ url('/photos') }}",
                    paramName: 'file',
                    maxFiles: 1,
                    acceptedFiles: 'image/*',
                    timeout: 120000,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                dz.on('success', function (file, response) {
                    console.log('Dropzone success response:', response);

                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.reload();
                    }
                });

                dz.on('error', function (file, errorMessage, xhr) {
                    console.error('Dropzone upload error:', errorMessage);
                    if (xhr && xhr.responseText) {
                        console.error(xhr.responseText);
                    }
                });

                dz.on('maxfilesexceeded', function(file) {
                    dz.removeAllFiles();
                    dz.addFile(file);
                });
            }

            /* ============================
               EXISTING META MODAL LOGIC
            ============================ */

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product,
                        size,
                        installationStatus,
                        confidence
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
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error(error);
                        statusEl.textContent = 'Unexpected error saving metadata.';
                    });
            });

        });
    </script>
</x-app-layout>
