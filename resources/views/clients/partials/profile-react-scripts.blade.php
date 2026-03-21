<form id="client-delete-form" action="{{ route('clients.destroy', $client) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script src="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/image-upload.js') }}"></script>
<script>
(function () {
    window.openAvatarUploadModal = function () {
        var el = document.getElementById('avatarUploadModal');
        if (el && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        }
    };
    window.openCoverUploadModal = function () {
        var el = document.getElementById('coverUploadModal');
        if (el && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        }
    };

    window.submitClientDeleteForm = function () {
        var form = document.getElementById('client-delete-form');
        if (!form) return;

        var clientName = @json($client->name);

        if (typeof Swal === 'undefined') {
            if (confirm('Delete client "' + clientName + '"?')) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: @json(__('Delete client?')),
            text: 'Delete "' + clientName + '"? ' + @json(__('This cannot be undone.')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: @json(__('Yes, delete')),
            cancelButtonText: @json(__('Cancel')),
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    };

    // Cover drag reposition (when cover image exists)
    window.addEventListener('load', function () {
        var coverContainer = document.getElementById('profileCover');
        var coverImage = document.getElementById('coverImage');
        if (!coverImage || !coverContainer) return;

        var isDragging = false;
        var startX = 0;
        var startY = 0;
        var currentX = 0;
        var currentY = 0;

        function getCurrentPosition() {
            var style = window.getComputedStyle(coverImage);
            var objectPosition = style.objectPosition || coverImage.getAttribute('data-initial-position') || 'center center';
            var parts = objectPosition.trim().split(/\s+/);
            var x = 50;
            var y = 50;
            if (parts.length >= 1 && parts[0] !== 'center') {
                var xVal = parseFloat(parts[0]);
                if (!isNaN(xVal)) x = xVal;
            }
            if (parts.length >= 2 && parts[1] !== 'center') {
                var yVal = parseFloat(parts[1]);
                if (!isNaN(yVal)) y = yVal;
            }
            return { x: x, y: y };
        }

        function setPosition(x, y) {
            coverImage.style.objectPosition = x + '% ' + y + '%';
        }

        coverContainer.addEventListener('mousedown', function (e) {
            if (e.target.closest('button')) return;
            isDragging = true;
            var rect = coverContainer.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            var pos = getCurrentPosition();
            currentX = pos.x;
            currentY = pos.y;
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            var rect = coverContainer.getBoundingClientRect();
            var mouseX = e.clientX - rect.left;
            var mouseY = e.clientY - rect.top;
            var deltaX = ((mouseX - startX) / rect.width) * 100;
            var deltaY = ((mouseY - startY) / rect.height) * 100;
            var newX = Math.max(0, Math.min(100, currentX + deltaX));
            var newY = Math.max(0, Math.min(100, currentY + deltaY));
            setPosition(newX, newY);
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            var pos = getCurrentPosition();
            saveCoverPosition(pos.x, pos.y);
        });

        function saveCoverPosition(x, y) {
            var clientId = {{ $client->id }};
            var position = x + '% ' + y + '%';
            fetch('/clients/' + clientId + '/cover-position', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ position: position, x: x, y: y }),
            }).catch(function (err) {
                console.error('Failed to save cover position:', err);
            });
        }
    });
})();
</script>
