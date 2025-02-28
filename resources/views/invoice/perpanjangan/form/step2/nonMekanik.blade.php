<section>
        <div class="card">
            <div class="card-body">
                <div class="table">
                    <table class="tabel-stripped table-responsive">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Harga Satuan</th>
                                <th>Jumlah (Volume)</th>
                                <th>Jumlah Hari</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedTarif as $index => $tarif)
                                <tr>
                                    <th>{{$tarif->Tarif->nama_tarif}}</th>
                                    <td>
                                        <input type="hidden" name="tarif_id[{{$index}}]" value="{{$tarif->Tarif->id}}">
                                        <input type="number" class="form-control harga-satuan" name="harga_satuan[{{$index}}]" step="0.01" id="harga_satuan_{{$index}}" value="{{$tarif->harga ?? 0}}" data-index="{{$index}}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control jumlah-volume" name="jumlah_volume[{{$index}}]" value="{{$tarif->jumlah ?? $form->cbm}}" step="0.01" id="jumlah_volume_{{$index}}" data-index="{{$index}}">
                                    </td>
                                    <td>
                                        @if($tarif->Tarif->day == 'Y')
                                            @if($tarif->Tarif->period == '1')
                                                <input type="number" name="jumlah_hari[{{$index}}]" class="form-control jumlah-hari" value="{{$periode1}}" step="0.01" id="jumlah_hari_{{$index}}" data-index="{{$index}}">
                                            @elseif($tarif->Tarif->period == '2')
                                                <input type="number" name="jumlah_hari[{{$index}}]" class="form-control jumlah-hari" value="{{$periode2}}" step="0.01" id="jumlah_hari_{{$index}}" data-index="{{$index}}">
                                            @elseif($tarif->Tarif->period == '3')
                                                <input type="number" name="jumlah_hari[{{$index}}]" class="form-control jumlah-hari" value="{{$periode3}}" step="0.01" id="jumlah_hari_{{$index}}" data-index="{{$index}}">
                                            @endif
                                        @else
                                            <input type="number" class="form-control jumlah-hari" name="jumlah_hari[{{$index}}]" value="0" step="0.01"  id="jumlah_hari_{{$index}}" data-index="{{$index}}" disabled>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" class="form-control total" name="total[{{$index}}]" value="{{$tarif->total}}" step="0.01" id="total_{{$index}}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>Administrasi</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="number" class="form-control" name="admin" id="admin" value="{{$form->admin ?? ''}}" step="0.01">
                                </td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="number" class="form-control" name="discount" id="discount" value="{{$form->discount ?? ''}}" step="0.01">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="card" style="border-radius:15px !important; background-color:#435ebe !important;">
            <div class="card-body">
                <div class="row text-white p-3">
                    <div class="col-6">
                        <h1 class="lead text-white">Total</h1>
                        <h4 class="lead text-white">PPN (%)</h4>
                        <h4 class="lead text-white">PPN (Amount)</h4>
                    </div>
                    <div class="col-6" style="text-align:right;">
                        <h1 class="lead text-white"><span id="grand_total_display">0</span></h1>
                        <h4 class="lead text-white">
                            <input type="number" name="pajak" class="form-control form-control-sm" id="ppn_percentage" value="11" value="{{$form->pajak ?? ''}}" style="width: 70px; display: inline-block;"> %
                        </h4>
                        <h4 class="lead text-white"><span id="ppn_amount_display">{{$form->pajak_amount ?? ''}}</span></h4>
                    </div>
                </div>
                <hr>
                <div class="row text-white mt-0">
                    <div class="col-6">
                        <h4 class="text-white">Grand Total</h4>
                    <div class="col-6" style="text-align:right;">
                        <h4 class="color:#ff5265;"><span id="final_grand_total_display">{{$form->grand_total ?? ''}}</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    