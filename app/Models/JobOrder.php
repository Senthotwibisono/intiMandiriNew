<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    use HasFactory;
    protected $table = 'tjoborder';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tglentry',
        'plp_id',
        'noplp',
        'party',
        'nombl',
        'tgl_master_bl',
        'vessel',
        'callsign',
        'voy',
        'eta',
        'etd',
        'grossweight',
        'jumlahhbl',
        'measurement',
        'keterangan',
        'jabatan',
        'alamat',
        'jeniskegiatan',
        'consolidator_id',
        'negara_id',
        'pelabuhan_id',
        'history',
        'nojoborder',
        'lcl',
        'nospk',
        'lokasisandar_id',
        'tglmasukapw',
        'tglbuangmty',
        'uid',
        'invoice_id',
        'no_bc11',
        'tgl_bc11',
        'status_plp',
        'gab_no_bc11',
        'iso_code',
        'pel_muat',
        'pel_transit',
        'pel_bongkar',
        'gudang_tujuan',
        'shipping_id',
        'shippingline',
        'uraian_opr',
        'uraian_fin',
        'etiketdate',
        'etikettime',
        'etiket_by',
        'eplpdate',
        'eplptime',
        'eplp_by',
        'eplpfinaldate',
        'eplpfinaltime',
        'eplpfinal_by',
        'cprid',
        'tgl_buk',
        'tgl_truck',
        'kode_gudang',
        'truck_by',
        'namaconsolidator',
        'namanegara',
        'namapelabuhan',
        'namalokasisandar',
        'tno_bc11',
        'ttgl_bc11',
        'tno_plp',
        'ttgl_plp',
        'jamentry',
        'c_datetime',
        'lastupdate',
        'lokasi_gudang',
        'kd_tps_asal',
        'id_consolidator',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function PLP()
    {
        return $this->belongsTo(TpsPLP::class, 'plp_id', 'id');
    }

    public function Kapal()
    {
        return $this->belongsTo(Vessel::class, 'vessel', 'id');
    }

    public function consolidator()
    {
        return $this->belongsTo(Consolidator::class, 'consolidator_id', 'id');
    }

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'negara_id', 'id');
    }

    public function port()
    {
        return $this->belongsTo(Pelabuhan::class, 'pelabuhan_id', 'id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingLine::class, 'shipping_id', 'id');
    }

    public function sandar()
    {
        return $this->belongsTo(LokasiSandar::class, 'lokasisandar_id', 'id');
    }

    public function gudang()
    {
        return $this->belongsTo(LokasiSandar::class, 'gudang_tujuan', 'id');
    }

    public function muat()
    {
        return $this->belongsTo(Pelabuhan::class, 'pel_muat', 'id');
    }

    public function bongkar()
    {
        return $this->belongsTo(Pelabuhan::class, 'pel_bongkar', 'id');
    }
}
