<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Model Training') }}
        </h1>
    </x-slot>

    <div class="py-12">
        @if (session('status'))
            <div
                id="flash-message"
                class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 transition-opacity duration-500"
            >
                {{ session('status') }}
            </div>
        @endif

        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">
                        Upload photo(s) of the same Installation Status, Product, and Size to Train the AI model
                    </h3>

                    {{-- Required selections before upload --}}
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Installation Status --}}
                            <div>
                                <label for="installation_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Installation Status
                                </label>
                                <select
                                    id="installation_status"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                                >
                                    <option value="">Select status</option>
                                    <option value="Complete">Complete</option>
                                    <option value="Incomplete">Incomplete</option>
                                </select>
                            </div>

                            {{-- Product --}}
                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Product
                                </label>
                                <select
                                    id="product_id"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                                >
                                    <option value="">Select product</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Product Size (loads after product selection) --}}
                            <div>
                                <label for="product_size_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Product Size
                                </label>
                                <select
                                    id="product_size_id"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"
                                    disabled
                                >
                                    <option value="">Select size</option>
                                </select>
                            </div>
                        </div>

                        <p id="selection-hint" class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            Select Installation Status, Product, and Product Size to enable uploads.
                        </p>
                    </div>

                    {{-- Dropzone wrapper for fade in / out --}}
                    <div id="dropzone-wrapper" class="relative transition-opacity duration-500 hidden opacity-0 pointer-events-none">
                        <form
                            action="{{ route('photos.upload') }}"
                            method="post"
                            class="dropzone"
                            id="learn-dropzone"
                        >
                            @csrf
                            <input type="hidden" name="installationStatus" id="dz-installationStatus">
                            <input type="hidden" name="product_id" id="dz-product_id">
                            <input type="hidden" name="product_size_id" id="dz-product_size_id">



                            <div class="dz-message">
                                Please drop photos here that you would like the AI engine to learn.
                                You can also click to browse for files.
                            </div>
                        </form>

                        {{-- Loading overlay with rotating gif --}}
{{--                        <div id="upload-spinner" class="hidden absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 rounded-xl">--}}
{{--                            <img--}}
{{--                                src="{{ asset('images/loading.gif') }}"--}}
{{--                                alt="Uploading..."--}}
{{--                                class="w-16 h-16"--}}
{{--                            >--}}
{{--                        </div>--}}
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
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Uploaded AI Model Training Photos</h3>

                            <form
                                action="{{ route('photos.destroyAll') }}"
                                method="POST"
                                onsubmit="return confirm('Delete ALL photos? This cannot be undone.')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="px-3 py-2 text-xs font-semibold rounded bg-red-700 text-white hover:bg-red-800"
                                >
                                    Delete All
                                </button>
                            </form>
                        </div>
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
                                    <th class="px-3 py-2 border">Installation Status</th>
                                    <th class="px-3 py-2 border">Created</th>
                                    <th class="px-3 py-2 border">Updated</th>
                                    <th class="px-3 py-2 border">Actions</th>

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
                                                src="{{ Storage::disk($photo->disk)->url($photo->thumbPath) }}"
                                                data-full="{{ Storage::disk($photo->disk)->url($photo->scaledPath) }}"
                                                data-filename="{{ $photo->file_name }}"
                                                data-product="{{ $photo->product_name ?? $photo->product }}"
                                                data-size="{{ $photo->product_size ?? $photo->size }}"
                                                data-status="{{ $photo->installationStatus }}"
                                                alt="Image"
                                                class="w-20 h-auto rounded cursor-pointer hover:opacity-80 thumbnail-click"
                                            />
                                        </td>


                                        <td class="px-3 py-2 border">{{ $photo->product_name ?? $photo->product }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->product_size ?? $photo->size }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->installationStatus }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->created_at }}</td>
                                        <td class="px-3 py-2 border">{{ $photo->updated_at }}</td>
                                        <td class="px-3 py-2 border whitespace-nowrap">
                                            <form
                                                action="{{ route('photos.destroy', $photo->id) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Delete this photo? This cannot be undone.')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="px-2 py-1 text-xs font-semibold rounded bg-red-600 text-white hover:bg-red-700"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </td>

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


    {{-- Image Preview Modal --}}
    <div
        id="image-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80"
    >
        <div class="relative w-[95vw] h-[95vh] flex flex-col">
            {{-- Top bar --}}
            <div class="flex items-start justify-between gap-4 p-4 text-white">
                <div class="min-w-0">
                    <div id="image-modal-filename" class="text-base font-semibold truncate"></div>
                    <div class="mt-1 text-sm text-white/80">
                        <span id="image-modal-product"></span>
                        <span class="mx-2">•</span>
                        <span id="image-modal-size"></span>
                        <span class="mx-2">•</span>
                        <span id="image-modal-status"></span>
                    </div>
                </div>

                <button
                    type="button"
                    id="image-modal-close"
                    class="shrink-0 text-sm px-3 py-1 bg-black/60 rounded hover:bg-black"
                >
                    ✕ Close
                </button>
            </div>

            {{-- Image area --}}
            <div class="flex-1 px-4 pb-4">
                <div
                    id="image-modal-viewport"
                    class="w-full h-full flex items-center justify-center overflow-hidden rounded-lg"
                >
                    <img
                        id="image-modal-img"
                        src=""
                        alt="Full size preview"
                        class="max-w-full max-h-full object-contain bg-white select-none cursor-zoom-in"
                        draggable="false"
                    >
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
            width: 90vw;
            max-width: 100%;
            margin: 0 auto;
            border: 3px dashed #9ca3af;
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
        #image-modal-img {
            transition: transform 80ms linear;
        }

    </style>

    {{-- Dropzone JS --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    {{-- Dropdown controller: load sizes + show/hide dropzone --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const installEl = document.getElementById('installation_status');
            const productEl = document.getElementById('product_id');
            const sizeEl = document.getElementById('product_size_id');
            const dropzoneWrapper = document.getElementById('dropzone-wrapper');
            const hintEl = document.getElementById('selection-hint');

            if (!installEl || !productEl || !sizeEl || !dropzoneWrapper) return;

            function allSelected() {
                return Boolean(installEl.value && productEl.value && sizeEl.value);
            }

            function showDropzone() {
                dropzoneWrapper.classList.remove('hidden');
                requestAnimationFrame(() => {
                    dropzoneWrapper.classList.remove('opacity-0', 'pointer-events-none');
                });
            }

            function hideDropzone() {
                dropzoneWrapper.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    dropzoneWrapper.classList.add('hidden');
                }, 300);
            }

            function updateDropzoneVisibility() {
                if (allSelected()) {
                    showDropzone();
                    if (hintEl) hintEl.textContent = 'Ready to upload.';
                } else {
                    hideDropzone();
                    if (hintEl) hintEl.textContent = 'Select installation status, product, and product size to enable uploads.';
                }
            }

            function setSizeLoading() {
                sizeEl.disabled = true;
                sizeEl.innerHTML = '<option value="">Loading sizes...</option>';
            }

            function resetSizes() {
                sizeEl.disabled = true;
                sizeEl.innerHTML = '<option value="">Select size</option>';
                sizeEl.value = '';
            }

            async function loadSizes(productId) {
                resetSizes();
                updateDropzoneVisibility();

                if (!productId) return;

                setSizeLoading();

                try {
                    const res = await fetch("{{ url('/products') }}/" + productId + "/sizes", {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const sizes = await res.json();

                    sizeEl.innerHTML = '<option value="">Select size</option>';

                    if (!Array.isArray(sizes) || sizes.length === 0) {
                        sizeEl.innerHTML = '<option value="">No sizes found</option>';
                        sizeEl.disabled = true;
                        updateDropzoneVisibility();
                        return;
                    }

                    sizes.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.size;
                        sizeEl.appendChild(opt);
                    });

                    sizeEl.disabled = false;
                    updateDropzoneVisibility();
                } catch (err) {
                    console.error('Failed to load sizes', err);
                    sizeEl.innerHTML = '<option value="">Error loading sizes</option>';
                    sizeEl.disabled = true;
                    updateDropzoneVisibility();
                }
            }

            installEl.addEventListener('change', updateDropzoneVisibility);

            productEl.addEventListener('change', function () {
                loadSizes(productEl.value);
            });

            sizeEl.addEventListener('change', updateDropzoneVisibility);

            // Initial state
            resetSizes();
            updateDropzoneVisibility();
        });
    </script>

    {{-- Dropzone init: append metadata + block uploads if not selected --}}
    <script>
        Dropzone.autoDiscover = false;

        document.addEventListener('DOMContentLoaded', function () {
            const dropzoneEl = document.getElementById('learn-dropzone');
            if (!dropzoneEl) return;

            const spinner = document.getElementById('upload-spinner');
            const statusEl = document.getElementById('upload-status');
            const imageWrapper = document.getElementById('uploaded-image-wrapper');
            const imageEl = document.getElementById('uploaded-image');
            const dropzoneWrapper = document.getElementById('dropzone-wrapper');
            const uploadAnotherBtn = document.getElementById('upload-another-btn');
            const dzMessage = document.querySelector('#learn-dropzone .dz-message');

            const installEl = document.getElementById('installation_status');
            const productEl = document.getElementById('product_id');
            const sizeEl = document.getElementById('product_size_id');

            function allSelected() {
                return Boolean(installEl?.value && productEl?.value && sizeEl?.value);
            }

            function hideDropzone() {
                if (!dropzoneWrapper) return;
                dropzoneWrapper.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => dropzoneWrapper.classList.add('hidden'), 300);
            }

            // Reuse existing instance if already attached, otherwise create it.
// Reuse existing instance if already attached, otherwise create it.
            let dz = dropzoneEl.dropzone;
            if (!dz) {
                dz = new Dropzone(dropzoneEl, {
                    paramName: "file",
                    maxFilesize: 200,
                    parallelUploads: 10,
                    uploadMultiple: false, // ✅ IMPORTANT: one file per request
                    timeout: 300000,
                    maxFiles: 50,
                    acceptedFiles: "image/*",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}"
                    },
                    accept: function (file, done) {
                        if (!allSelected()) {
                            done("Please select installation status, product, and product size before uploading.");
                            return;
                        }
                        done();
                    }
                });
            }

// Prevent binding handlers multiple times
            if (dz.__learnImgBound) return;
            dz.__learnImgBound = true;

            dz.on("sending", function (file, xhr, formData) {
                formData.set("installationStatus", document.getElementById('installation_status')?.value || '');
                formData.set("product_id", document.getElementById('product_id')?.value || '');
                formData.set("product_size_id", document.getElementById('product_size_id')?.value || '');

                if (spinner) spinner.classList.remove('hidden');
                if (statusEl) statusEl.textContent = 'Uploading file...';
            });

            dz.on("success", function (file, response) {
                dz.removeFile(file);
                if (spinner) spinner.classList.add('hidden');

                if (dzMessage) dzMessage.textContent = 'Uploaded. Continuing...';

                // Do NOT hide dropzone per-file when batch uploading
                // hideDropzone(); // optional: comment out while doing multiple uploads
            });

            dz.on("queuecomplete", function () {
                if (dzMessage) dzMessage.textContent = 'All uploads complete. Updating grid...';
                setTimeout(() => window.location.reload(), 800);
            });

            dz.on("error", function (file, errorMessage, xhr) {
                if (spinner) spinner.classList.add('hidden');

                let msg = 'Error uploading file.';
                if (xhr && xhr.responseText) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.errors) msg = Object.values(res.errors).flat().join(' ');
                        else if (res.message) msg = res.message;
                    } catch (e) {}
                } else if (typeof errorMessage === 'string') {
                    msg = errorMessage;
                }

                if (statusEl) statusEl.textContent = 'Error uploading file: ' + msg;
                console.error('Dropzone error details:', { file, errorMessage, xhr });
            });


            if (uploadAnotherBtn) {
                uploadAnotherBtn.addEventListener('click', function () {
                    if (statusEl) statusEl.textContent = '';
                    if (dzMessage) {
                        dzMessage.textContent =
                            'Please drop photos here that you would like the AI engine to learn. You can also click to browse for files.';
                    }
                    uploadAnotherBtn.classList.add('hidden');
                }, { once: true });
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const installEl = document.getElementById('installation_status');
            const productEl = document.getElementById('product_id');
            const sizeEl = document.getElementById('product_size_id');

            const dzInstall = document.getElementById('dz-installationStatus');
            const dzProduct = document.getElementById('dz-product_id');
            const dzSize = document.getElementById('dz-product_size_id');

            function syncHidden() {
                if (dzInstall) dzInstall.value = installEl?.value || '';
                if (dzProduct) dzProduct.value = productEl?.value || '';
                if (dzSize) dzSize.value = sizeEl?.value || '';
            }

            if (installEl) installEl.addEventListener('change', syncHidden);
            if (productEl) productEl.addEventListener('change', syncHidden);
            if (sizeEl) sizeEl.addEventListener('change', syncHidden);

            // initial
            syncHidden();
        });
    </script>


        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('image-modal');
            const viewport = document.getElementById('image-modal-viewport');
            const modalImg = document.getElementById('image-modal-img');
            const closeBtn = document.getElementById('image-modal-close');

            const filenameEl = document.getElementById('image-modal-filename');
            const productEl = document.getElementById('image-modal-product');
            const sizeEl = document.getElementById('image-modal-size');
            const statusEl = document.getElementById('image-modal-status');

            if (!modal || !viewport || !modalImg) return;

            // Zoom state
            let scale = 1;
            let translateX = 0;
            let translateY = 0;

            let isPanning = false;
            let panStartX = 0;
            let panStartY = 0;

            const MIN_SCALE = 1;
            const MAX_SCALE = 6;
            const CLICK_ZOOM = 2;

            function applyTransform() {
            modalImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            modalImg.style.transformOrigin = 'center center';

            if (scale > 1) {
            modalImg.classList.remove('cursor-zoom-in');
            modalImg.classList.add('cursor-grab');
        } else {
            modalImg.classList.remove('cursor-grab');
            modalImg.classList.add('cursor-zoom-in');
        }
        }

            function resetZoom() {
            scale = 1;
            translateX = 0;
            translateY = 0;
            isPanning = false;
            applyTransform();
        }

            function clampScale(next) {
            return Math.min(MAX_SCALE, Math.max(MIN_SCALE, next));
        }

            function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalImg.src = '';

            if (filenameEl) filenameEl.textContent = '';
            if (productEl) productEl.textContent = '';
            if (sizeEl) sizeEl.textContent = '';
            if (statusEl) statusEl.textContent = '';

            resetZoom();
        }

            function openModalFromThumb(thumbEl) {
            const fullSrc = thumbEl.getAttribute('data-full');
            if (!fullSrc) return;

            modalImg.src = fullSrc;

            if (filenameEl) filenameEl.textContent = thumbEl.getAttribute('data-filename') || '';
            if (productEl) productEl.textContent = thumbEl.getAttribute('data-product') || '';
            if (sizeEl) sizeEl.textContent = thumbEl.getAttribute('data-size') || '';
            if (statusEl) statusEl.textContent = thumbEl.getAttribute('data-status') || '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            resetZoom();
        }

            // Open modal on thumbnail click
            document.querySelectorAll('.thumbnail-click').forEach(img => {
            img.addEventListener('click', function () {
            openModalFromThumb(this);
        });
        });

            // Close button
            if (closeBtn) closeBtn.addEventListener('click', closeModal);

            // Click outside (background)
            modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

            // ESC
            document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

            // Toggle zoom on click (on the image only)
            modalImg.addEventListener('click', function (e) {
            e.stopPropagation(); // don't trigger background close

            if (scale === 1) {
            scale = CLICK_ZOOM;
        } else {
            resetZoom();
            return;
        }

            applyTransform();
        });

            // Double click zoom in/out
            modalImg.addEventListener('dblclick', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (scale < CLICK_ZOOM) {
            scale = CLICK_ZOOM;
        } else {
            resetZoom();
            return;
        }
            applyTransform();
        });

            // Wheel zoom (trackpad/mouse)
            viewport.addEventListener('wheel', function (e) {
            // Allow normal scroll when modal is closed
            if (modal.classList.contains('hidden')) return;

            e.preventDefault();

            const delta = e.deltaY;
            const zoomFactor = delta > 0 ? 0.9 : 1.1;
            const nextScale = clampScale(scale * zoomFactor);

            // If we are at 1 and zooming out, keep it at 1
            if (scale === 1 && nextScale === 1) return;

            scale = nextScale;
            applyTransform();
        }, { passive: false });

            // Pan by dragging when zoomed
            modalImg.addEventListener('mousedown', function (e) {
            if (scale <= 1) return;

            isPanning = true;
            modalImg.classList.remove('cursor-grab');
            modalImg.classList.add('cursor-grabbing');

            panStartX = e.clientX - translateX;
            panStartY = e.clientY - translateY;
            e.preventDefault();
        });

            window.addEventListener('mousemove', function (e) {
            if (!isPanning) return;

            translateX = e.clientX - panStartX;
            translateY = e.clientY - panStartY;
            applyTransform();
        });

            window.addEventListener('mouseup', function () {
            if (!isPanning) return;
            isPanning = false;

            modalImg.classList.remove('cursor-grabbing');
            if (scale > 1) modalImg.classList.add('cursor-grab');
        });

            // Touch support: pinch-to-zoom is non-trivial without a lib.
            // If you want mobile pinch, tell me and I'll add it cleanly.
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flash = document.getElementById('flash-message');
            if (!flash) return;

            setTimeout(() => {
                flash.classList.add('opacity-0');

                // Remove from DOM after fade completes
                setTimeout(() => {
                    flash.remove();
                }, 3000);
            }, 3000); // 3 seconds
        });
    </script>





</x-app-layout>
