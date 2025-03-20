
@foreach($headers as $header)
<html><head>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet">
 <style>*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }/* ! tailwindcss v3.4.16 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.mx-auto{margin-left:auto;margin-right:auto}.mb-2{margin-bottom:0.5rem}.mb-4{margin-bottom:1rem}.w-full{width:100%}.max-w-md{max-width:28rem}.border-collapse{border-collapse:collapse}.border{border-width:1px}.border-gray-300{--tw-border-opacity:1;border-color:rgb(209 213 219 / var(--tw-border-opacity, 1))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.p-2{padding:0.5rem}.p-4{padding:1rem}.p-6{padding:1.5rem}.text-center{text-align:center}.text-xl{font-size:1.25rem;line-height:1.75rem}.font-bold{font-weight:700}</style></head>
 <body class="font-roboto bg-white p-4">
  <div class="max-w-md mx-auto bg-white p-6 border border-gray-300">
   <div class="text-center mb-4">
    <img alt="Pelindo logo" class="mx-auto mb-2" src="{{ asset('/logo/pelindo.png')}}">

   </div>
   <h2 class="text-center font-bold mb-2">
    BUKTI PEMBAYARAN ORDER
   </h2>
   <p class="text-center mb-4">
    CFS CARGO
   </p>
   <div class="mb-4">
    <p>
     <strong>
      Tanggal
     </strong>
     : {{$header->lunas_at ?? '-'}}
    </p>
    <p>
     <strong>
      No. Order
     </strong>
     : {{$header->no_order ?? '-'}}
    </p>
    <p>
     <strong>
      No. Bukti Bayar
     </strong>
     : {{$header->no_invoice}}
    </p>
    <p>
     <strong>
      No. Faktur
     </strong>
     :
    </p>
   </div>
   <div class="mb-4">
    <p>
     <strong>
      {{$header->consignee ?? '-'}}
     </strong>
    </p>
    <p>
     {{$header->npwp_consignee ?? '-'}}
    </p>
    <p>
     JL. BINTARA 9 NO. 158 RT. 001 RW. 005 BINTARA
    </p>
    <p>
     BEKASI BARAT KOTA BEKASI JAWA BARAT 97134
    </p>
   </div>
   <div class="mb-4">
    <p>
     <strong>
      Gudang
     </strong>
     : INTI MANDIRI UTAMA
    </p>
    <p>
     <strong>
      No. B/L
     </strong>
     : {{$header->no_bl_awb}}
    </p>
    <p>
     <strong>
      Weight/Measure
     </strong>
     : {{number_format($header->weight ?? 0)}} KG / {{number_format($header->measure ?? 0)}} M³ / {{$header->jml_kms ?? '-'}} {{$header->jns_kms ?? '-'}}
    </p>
    <p>
     <strong>
      Tanggal Stripping
     </strong>
     : {{$header->manifest->tglstripping}}
    </p>
    <p>
     <strong>
      Tanggal Kegiatan
     </strong>
     : {{$header->rencana_keluar_lama ?? $header->manifest->tglstripping ?? '-'}} s/d {{$header->rencana_keluar}}
    </p>
   </div>
   <table class="w-full mb-4 border-collapse border border-gray-300">
    <thead>
     <tr>
      <th class="border border-gray-300 p-2">
       Tagihan
      </th>
      <th class="border border-gray-300 p-2">
       QTY
      </th>
      <th class="border border-gray-300 p-2">
       Hari
      </th>
      <th class="border border-gray-300 p-2">
       Jumlah
      </th>
     </tr>
    </thead>
    <tbody>
        @foreach($tarifs as $tarif)
            @if($tarif->header_id == $header->id)
            <tr>
             <td class="border border-gray-300 p-2">
              {{$tarif->desc->description ?? '-'}}
             </td>
             <td class="border border-gray-300 p-2">
              {{number_format($tarif->qty ?? 0)}}
             </td>
             <td class="border border-gray-300 p-2">
              {{$tarif->hari ?? 0}}
             </td>
             <td class="border border-gray-300 p-2">
              {{number_format($tarif->nilai ?? 0)}}
             </td>
            </tr>
            @endif
        @endforeach
    </tbody>
   </table>
   <div class="mb-4">
    <p>
     <strong>
      Sub Total
     </strong>
     : Rp. {{number_format($header->subtotal ?? 0)}}
    </p>
    <p>
     <strong>
      Dasar Pengenaan Pajak
     </strong>
     : Rp. {{number_format(((11/12)*$header->subtotal) ?? 0)}}
    </p>
    <p>
     <strong>
      Nilai Lain
     </strong>
     : Rp. 0
    </p>
    <p>
     <strong>
      PPn
     </strong>
     : Rp. {{number_format($header->ppn ?? 0)}}
    </p>
    <p>
     <strong>
      Materai
     </strong>
     : Rp. 0
    </p>
    <p>
     <strong>
      Total
     </strong>
     : Rp. {{number_format($header->total ?? 0)}}
    </p>
   </div>
   <div>
    <p>
     <strong>
      Terbilang
     </strong>
     :  <p><strong>Terbilang</strong>: {{ strtoupper($terbilang($header->total ?? 0)) }} RUPIAH</p>
    </p>
   </div>
  </div>
 

</body></html>
@endforeach