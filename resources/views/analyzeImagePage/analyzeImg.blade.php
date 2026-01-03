<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analyze Image</title>

    {{-- Dropzone (include once) --}}
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #fafafa; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 18px; }
        .topbar { display: flex; align-items: center; gap: 12px; padding: 14px 18px; background: #fff; border-bottom: 1px solid #eee; }
        .topbar img { height: 34px; }
        .card { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 16px; margin-top: 16px; }
        .hidden { display: none !important; }

        /* Modal */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: flex; align-items: center; justify-content: center; padding: 18px; }
        .modal { width: 100%; max-width: 560px; background: #fff; border-radius: 12px; overflow: hidden; }
        .modal-header { padding: 14px 16px; border-bottom: 1px solid #eee; font-weight: 600; }
        .modal-body { padding: 16px; }
        .modal-footer { padding: 12px 16px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; }
        label { display: block; font-size: 13px; margin-top: 10px; margin-bottom: 6px; color: #333; }
        input, select { width: 100%; padding: 10px 10px; border: 1px solid #ddd; border-radius: 8px; }
        button { padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; background: #fff; cursor: pointer; }
        button.primary { background: #111; color: #fff; border-color: #111; }
        #meta-status { margin-top: 10px; font-size: 13px; color: #b00020; }
    </style>
</head>
<body>

<div class="topbar">
    {{-- Fix 404s by using asset() and putting files in public/images --}}
    <img src="{{ asset('images/victaulicLogo_200.png') }}" alt="Victaulic">
    <div>Analyze Image</div>
</div>

<div class="wrap">

    <div class="card">
        <h2 style="margin: 0 0 10px 0;">Upload</h2>

        <form id="photo-dropzone" class="dropzone" action="{{ route('photos.store') }}" method="post" enctype="multipart/form-data">
            @csrf
        </form>

        <div style="margin-top: 10px; font-size: 13px; color: #666;">
            Tip: Upload one image at a time.
        </div>
    </div>

    {{-- Example area for table/results (replace with your real markup) --}}
    <div class="card">
        <h2 style="margin: 0 0 10px 0;">Recent Uploads</h2>

        {{-- If you already have a table here, keep yours and remove this placeholder --}}
        <div style="color:#666; font-size: 13px;">
            Your uploads table goes here.
        </div>
    </div>

</div>

{{-- META MODAL --}}
<div id="meta-modal" class="modal-backdrop hidden" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="meta-title">
        <div class="modal-header" id="meta-title">Add Manual Metadata</div>

        <form id="meta-form">
            <div class="modal-body">
                <input type="hidden" id="meta-photo-id" value="">

                <label for="meta-product">Product</label>
                <input id="meta-product" type="text" placeholder="Product">

                <label for="meta-size">Size</label>
                <input id="meta-size" type="text" placeholder="Size">

                <label for="meta-installationStatus">Installation Status</label>
                <select id="meta-installationStatus">
                    <option value="">Select...</option>
                    <option value="complete">complete</option>
                    <option value="incomplete">incomplete</option>
                </select>

                <label for="meta-confidence">Confidence</label>
                <input id="meta-confidence" type="text" placeholder="0.00">

                <div id="meta-status"></div>
            </div>

            <div class="modal-footer">
                <button type="button" id="meta-cancel">Cancel</button>
                <button type="submit" class="primary">Save</button>
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

        if (dropzoneEl) {
            // Guard: do not reattach Dropzone
            if (!dropzoneEl.dropzone) {

                const dz = new Dropzone(dropzoneEl, {
                    url: "{{ route('photos.store') }}",
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

                    // Optional: if you want immediate redirect after upload
                    if (response.redirect) {
                        window.location.href = response.redirect;
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

</body>
</html>
