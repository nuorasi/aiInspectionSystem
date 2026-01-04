<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Analyze an Image') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Upload card --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-lg font-semibold text-gray-800">Upload</div>
                    <div class="mt-1 text-sm text-gray-600">Upload one image at a time.</div>

                    <form
                        id="photo-dropzone"
                        class="dropzone mt-4 border-2 border-dashed rounded-lg"
                        action="{{ url('/photos') }}"
                        method="post"
                        enctype="multipart/form-data">
                        @csrf
                    </form>
                </div>
            </div>

            {{-- Your existing table/results section can stay here --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-lg font-semibold text-gray-800">Recent Uploads</div>
                    <div class="mt-3 text-sm text-gray-600">
                        {{-- Keep your existing markup/table here --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- META MODAL --}}
    <div id="meta-modal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="px-5 py-4 border-b font-semibold text-gray-800">
                    Add Manual Metadata
                </div>

                <form id="meta-form" class="p-5">
                    <input type="hidden" id="meta-photo-id" value="">

                    <label for="meta-product" class="block text-sm font-medium text-gray-700 mt-2">Product</label>
                    <input id="meta-product" type="text" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Product">

                    <label for="meta-size" class="block text-sm font-medium text-gray-700 mt-4">Size</label>
                    <input id="meta-size" type="text" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Size">

                    <label for="meta-installationStatus" class="block text-sm font-medium text-gray-700 mt-4">Installation Status</label>
                    <select id="meta-installationStatus" class="mt-1 w-full rounded-lg border-gray-300">
                        <option value="">Select...</option>
                        <option value="complete">complete</option>
                        <option value="incomplete">incomplete</option>
                    </select>

                    <label for="meta-confidence" class="block text-sm font-medium text-gray-700 mt-4">Confidence</label>
                    <input id="meta-confidence" type="text" class="mt-1 w-full rounded-lg border-gray-300" placeholder="0.00">

                    <div id="meta-status" class="mt-3 text-sm text-red-700"></div>

                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" id="meta-cancel" class="px-4 py-2 rounded-lg border border-gray-300 bg-white">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white">
                            Save
                        </button>
                    </div>
                </form>
            </div>
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
