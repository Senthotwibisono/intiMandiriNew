<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ContainerFCL extends Model
{
    protected static function booted()
    {
        // static::addGlobalScope(new FlagSegelMerahScope());
        parent::boot();

        static::saving(function ($model) {
            if ($model->flag_segel_merah === 'Y') {
                if (!Auth::user()->hasRole('bcP2')) {
                    throw ValidationException::withMessages([
                        'error' => 'Tidak dapat melakukan perubahan karena sedang segel merah.'
                    ]);
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->flag_segel_merah === 'Y' && !Auth::user()->hasRole('bcP2')) {
                throw ValidationException::withMessages([
                    'error' => 'Tidak dapat menghapus data karena sedang segel merah.'
                ]);
            }
        });

        // static::retrieved(function ($model) {
        //     // Hanya untuk mendeteksi pengambilan data, tidak bisa mencegahnya
        //     if ($model->flag_segel_merah === 'Y' && !Auth::user()->hasRole('BCP2')) {
        //         throw ValidationException::withMessages([
        //             'error' => 'Tidak dapat melakukan perubahan karena sedang segel merah.'
        //         ]);
        //     }
        // });
    }
    
    use HasFactory;
    protected $table = 'tcontainer_fcl';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nocontainer',
        'joborder_id',
        'cust_id',
        'type',
        'ctr_type',
        'size',
        'teus',
        'layout',
        'nojob',
        'nospk',
        'uid',
        'coordinator_stripping',
        'operator_forklif',
        'meas',
        'weight',
        'nosegel',
        'tglkeluar',
        'jamkeluar',
        'uidkeluar',
        'tglmasuk',
        'jammasuk',
        'uidmasuk',
        'nopol',
        'id_cont',
        'tglkeluar_tpk',
        'jamkeluar_tpk',
        'jict_number',
        'id_booking',
        'no_bc11',
        'tgl_bc11',
        'no_plp',
        'tgl_plp',
        'consolidator_id',
        'namaconsolidator',
        'tglentry',
        'jamentry',
        'lokasisandar_id',
        'kd_tps_asal',
        'eta',
        'etd',
        'vessel',
        'voy',
        'pelabuhan_id',
        'namapelabuhan',
        'pel_muat',
        'pel_bongkar',
        'pel_transit',
        'no_seal',
        'nombl',
        'tgl_master_bl',
        'nobl',
        'tgl_bl_awb',
        'jumlah_bl',
        'kd_tps_tujuan',
        'tglstripping',
        'jamstripping',
        'tglbuangmty',
        'jambuangmty',
        'id_consolidator',
        'nopol_mty',
        'lokasi_gudang',
        'call_sign',
        'startstripping',
        'endstripping',
        'tglcetakcir',
        'jamcetakcir',
        'ref_number_in',
        'ref_number_out',
        'esealcode',
        'status_dispatche',
        'tgl_dispatche',
        'jam_dispatche',
        'response_dispatche',
        'do_id',
        'kode_dispatche',
        'tgl_keluar_tpk_eseal',
        'jam_keluar_tpk_eseal',
        'tgl_masuk_tujuan_eseal',
        'jam_masuk_tujuan_eseal',
        'tgl_respon',
        'jam_respon',
        'flag_ar',
        'flag_mty',
        'key_eseal',
        'nopol_vendor',
        // Izin Stripping
        'status_ijin',
        'tgl_ijin_stripping',
        'jam_ijin_stripping',
        'ijin_stripping_by',
        'eta_jam',
        
        'ver_app',
        'p_tglkeluar',
        'no_sp2',
        'tgl_sp2',
        'mulai_tunda',
        'selesai_tunda',
        'working_hours',
        'keterangan',
        'uidstripping',
        'uidmty',
        'lokasi_mty',
        'tujuan_mty',
        'last_update',
        'upload_status',
        'flag_bc',
        'photo_get_in',
        'photo_get_out',
        'photo_empty_in',
        'photo_empty_out',
        'photo_gatein_extra',
        'photo_stripping',
        'photo_empty',
        'status_bc',
        'alasan_hold',
        'release_bc',
        'release_bc_date',
        'release_bc_uid',
        'release_host',
        'release_ip_host',
        'yard_id',
        'yard_detil_id',
        'coari_flag',
        'codeco_flag',

        'status_behandle',
        'date_ready_behandle',
        'date_check_behandle',
        'date_finish_behandle',
        'desc_check_behandle',
        'desc_finish_behandle',

        'kode_dokumen',
        'kd_dok_inout',
        'no_dok',
        'tgl_dok',
        // BCP2
        'alasan_segel',
        'alasan_lepas_segel',
        'flag_segel_merah',
        'tanggal_segel_merah',
        'tanggal_lepas_segel_merah',
        'uid_segel',
        'uid_lepas_segel',

        'active_to',
        'type_class',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
    public function uidSegel()
    {
        return $this->belongsTo(User::class, 'uid_segel', 'id');
    }

    public function uidLepasSegel()
    {
        return $this->belongsTo(User::class, 'uid_lepas_segel', 'id');
    }

    public function Customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id');
    }

    public function userMasuk()
    {
        return $this->belongsTo(User::class, 'uidmasuk', 'id');
    }

    public function job()
    {
        return $this->belongsTo(JobOrderFCL::class, 'joborder_id', 'id');
    }

    public function dokumen()
    {
        return $this->belongsTo(KodeDok::class, 'kd_dok_inout', 'kode');
    }

    public function seal()
    {
        return $this->belongsTo(Eseal::class, 'no_seal', 'id');
    }

    public function Yard()
    {
        return $this->belongsTo(YardDesign::class, 'yard_id', 'id');
    }

    public function DetilYard()
    {
        return $this->belongsTo(YardDetil::class, 'yard_detil_id', 'id');
    }
}
