@extends('partial.android.main')
@section('custom_styles')

@endsection

@section('content')
<div class="card">
    <div class="card-header text-center">
        <h4>Scan Here</h4>
    </div>
    <div class="card-body text-center">
        <div id="qr-reader-container">
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas" hidden></canvas>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const canvasContext = canvas.getContext('2d');

    // Fungsi untuk memulai kamera
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }, // Gunakan kamera belakang
            });
            video.srcObject = stream;
            video.setAttribute('playsinline', true); // Untuk iOS
            video.play();
            scanQRCode();
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan.');
        }
    }

    // Fungsi untuk memindai QR Code
    function scanQRCode() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Gambar frame video ke canvas
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Ambil data gambar dari canvas
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);

            // Dekode QR Code menggunakan jsQR
            const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

            if (qrCode) {
                // Redirect ke URL berdasarkan hasil scan
                window.location.href = '/android/muat/detil/' + qrCode.data;

                // Hentikan kamera setelah menemukan QR Code
                stopCamera();
            }
        }

        // Lakukan pemindaian secara terus-menerus
        requestAnimationFrame(scanQRCode);
    }

    // Fungsi untuk menghentikan kamera
    function stopCamera() {
        const stream = video.srcObject;
        const tracks = stream.getTracks();

        tracks.forEach((track) => track.stop());
        video.srcObject = null;
    }

    // Mulai kamera saat halaman dimuat
    startCamera();
</script>

@endsection