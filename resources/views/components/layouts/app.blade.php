<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Poliklinik' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>

    <div class="app-wrapper">

        {{-- SIDEBAR --}}
        <div id="appSidebar" class="sidebar-fixed">
            @include('components.partials.sidebar')
        </div>

        {{-- OVERLAY --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- MAIN --}}
        <div class="main-content">

            @include('components.partials.header')

            <div class="main-scroll">

                @if (session('success'))
                    <div class="alert alert-success mb-4 rounded-xl shadow-sm">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error mb-4 rounded-xl shadow-sm">
                        <i class="fas fa-circle-xmark"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}

            </div>

            @include('components.partials.footer')

        </div>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar')
            const overlay = document.getElementById('sidebarOverlay')

            sidebar.classList.toggle('open')

            overlay.style.display =
                sidebar.classList.contains('open') ?
                'block' :
                'none'
        }

        function toggleFullscreen() {
            const icon = document.getElementById('fsIcon')

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen()
                icon.className = 'fas fa-compress'
            } else {
                document.exitFullscreen()
                icon.className = 'fas fa-expand'
            }
        }
    </script>

    @stack('scripts')

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | SweetAlert Session Message
            |--------------------------------------------------------------------------
            | Untuk menampilkan alert dari controller:
            | ->with('message', 'Berhasil ...')
            | ->with('type', 'success')
            |
            | Atau:
            | ->with('success', 'Berhasil ...')
            | ->with('error', 'Gagal ...')
            |--------------------------------------------------------------------------
            */

            @if (session('message'))
                Swal.fire({
                    icon: "{{ session('type', 'success') }}",
                    title: "{{ session('type') === 'error' ? 'Gagal!' : 'Berhasil!' }}",
                    text: @json(session('message')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d4499'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d4499'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d4499'
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: @json(session('warning')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d4499'
                });
            @endif

            /*
            |--------------------------------------------------------------------------
            | SweetAlert Validation Error
            |--------------------------------------------------------------------------
            | Kalau validasi Laravel gagal, otomatis muncul SweetAlert.
            |--------------------------------------------------------------------------
            */

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Data belum valid!',
                    html: `
                    <div style="text-align:left">
                        <ul style="margin:0; padding-left:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                    confirmButtonText: 'Perbaiki',
                    confirmButtonColor: '#2d4499'
                });
            @endif

            /*
            |--------------------------------------------------------------------------
            | SweetAlert Confirmation
            |--------------------------------------------------------------------------
            | Tambahkan class .btn-confirm ke form yang butuh konfirmasi.
            |--------------------------------------------------------------------------
            */

            document.querySelectorAll('.form-confirm').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const title = form.dataset.title || 'Apakah Anda yakin?';
                    const text = form.dataset.text || 'Data akan diproses.';
                    const icon = form.dataset.icon || 'warning';
                    const confirmText = form.dataset.confirm || 'Ya, lanjutkan';
                    const cancelText = form.dataset.cancel || 'Batal';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#2d4499',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>
