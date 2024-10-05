@extends('partial.android.main')
@section('custom_styles')

@endsection

@section('content')
<div id="reader"></div>
@endsection

@section('custom_js')

<script>
    // inisiasi html5QRCodeScanner
    let html5QRCodeScanner = new Html5QrcodeScanner(
        // target id dengan nama reader, lalu sertakan juga 
        // pengaturan untuk qrbox (tinggi, lebar, dll)
        "reader", {
            fps: 10,
            qrbox: {
                width: 600,
                height: 600,
            },
        }
    );

    // function yang dieksekusi ketika scanner berhasil
    // membaca suatu QR Code
    function onScanSuccess(decodedText, decodedResult) {
        // redirect ke link hasil scan
        window.location.href = '/android/lcl/rackingDetail-' + decodedText;

        // membersihkan scan area ketika sudah menjalankan 
        // action diatas
        html5QRCodeScanner.clear();
    }

    // render qr code scannernya
    html5QRCodeScanner.render(onScanSuccess);
</script>

@endsection