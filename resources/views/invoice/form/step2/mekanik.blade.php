<section>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="tabel-stripped table-responsive table-hover">
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
                        @foreach($selectedTarifMekanik as $index => $tarif)
                            <tr>
                                <th>{{$tarif->Tarif->nama_tarif}}</th>
                                <td>
                                    <input type="hidden" name="tarif_id_mekanik[{{$index}}]" value="{{$tarif->Tarif->id}}">
                                    <input type="number" class="form-control harga-satuan-mekanik" name="harga_satuan_mekanik[{{$index}}]" step="0.01" id="harga_satuan_{{$index}}_mekanik" value="{{$tarif->harga ?? 0}}" data-index="{{$index}}">
                                </td>
                                <td>
                                    <input type="number" class="form-control jumlah-volume-mekanik" name="jumlah_volume_mekanik[{{$index}}]" value="{{$form->cbm}}" step="0.01" id="jumlah_volume_{{$index}}_mekanik" data-index="{{$index}}">
                                </td>
                                <td>
                                    <input type="number" class="form-control jumlah-hari-mekanik" name="jumlah_hari_mekanik[{{$index}}]" value="0" step="0.01" id="jumlah_hari_{{$index}}_mekanik" data-index="{{$index}}" disabled>
                                </td>
                                <td>
                                    <input type="number" class="form-control total_mekanik" name="total_mekanik[{{$index}}]" value="{{$tarif->total}}" step="0.01" id="total_{{$index}}_mekanik" readonly>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>Administrasi</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="number" class="form-control" name="admin_m" id="admin_mekanik" value="{{$form->admin ?? ''}}" step="0.01">
                            </td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="number" class="form-control" name="discount_m" id="discount_mekanik" value="{{$form->discount ?? ''}}" step="0.01">
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
                    <h1 class="lead text-white"><span id="grand_total_display_mekanik">0</span></h1>
                    <h4 class="lead text-white">
                        <input type="number" name="pajak_m" class="form-control form-control-sm" id="ppn_percentage_mekanik" value="11" style="width: 70px; display: inline-block;"> %
                    </h4>
                    <h4 class="lead text-white"><span id="ppn_amount_display_mekanik">{{$form->pajak_amount ?? ''}}</span></h4>
                </div>
            </div>
            <hr>
            <div class="row text-white mt-0">
                <div class="col-6">
                    <h4 class="text-white">Grand Total</h4>
                </div>
                <div class="col-6" style="text-align:right;">
                    <h4 class="color:#ff5265;"><span id="final_grand_total_display_mekanik">{{$form->grand_total ?? ''}}</span></h4>
                </div>
            </div>
        </div>
    </div>
</section>
