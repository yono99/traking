<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;

class LacakBerkasController extends Controller
{
    private array $allSteps = [
        'FORWARD LOKET'               => ['label' => 'Loket',              'icon' => 'loket'],
        'PROSES LOKET'                => ['label' => 'Proses Loket',       'icon' => 'loket'],
        'FORWARD BUKU TANAH'          => ['label' => 'Forward Buku Tanah', 'icon' => 'buku'],
        'PROSES BUKU TANAH'           => ['label' => 'Proses Buku Tanah',  'icon' => 'buku'],
        'FORWARD PENGUKURAN'          => ['label' => 'Forward Pengukuran', 'icon' => 'ukur'],
        'PROSES PENGUKURAN'           => ['label' => 'Proses Pengukuran',  'icon' => 'ukur'],
        'FORWARD VALIDASI BIDANG'     => ['label' => 'Validasi Bidang',    'icon' => 'validasi'],
        'PROSES VALIDASI BIDANG'      => ['label' => 'Proses Validasi',    'icon' => 'validasi'],
        'FORWARD VALIDASI BUKU TANAH' => ['label' => 'Validasi Buku Tanah','icon' => 'validasi'],
        'PROSES VALIDASI BUKU TANAH'  => ['label' => 'Proses Validasi BT', 'icon' => 'validasi'],
        'FORWARD LOKET PENYERAHAN'    => ['label' => 'Loket Penyerahan',   'icon' => 'serah'],
        'PROSES LOKET PENYERAHAN'     => ['label' => 'Proses Penyerahan',  'icon' => 'serah'],
        'SELESAI DISERAHKAN'          => ['label' => 'Selesai',            'icon' => 'done'],
    ];

    /**
     * Halaman awal — form pencarian kosong
     * GET /lacak
     */
    public function index()
    {
        return Inertia::render('LacakBerkas', [
            'found'   => null,
            'kode'    => null,
            'service' => null,
            'steps'   => [],
            'history' => [],
        ]);
    }

    /**
     * Tampilkan hasil pencarian berdasarkan kode
     * GET /lacak/{kode}
     */
    public function show(string $kode)
    {
        $kodeBersih = strtoupper(trim($kode));

        $service = Service::with([
            'landBook',
            'activities' => fn ($q) => $q->orderBy('created_at', 'asc'),
        ])
        ->whereRaw('UPPER(kode_berkas) = ?', [$kodeBersih])
        ->first();

        if (!$service) {
            return Inertia::render('LacakBerkas', [
                'found'   => false,
                'kode'    => $kodeBersih,
                'service' => null,
                'steps'   => $this->buildSteps(null),
                'history' => [],
            ]);
        }

        return Inertia::render('LacakBerkas', [
            'found'   => true,
            'kode'    => $service->kode_berkas,
            'service' => [
                'kode_berkas'    => $service->kode_berkas,
                'nama_pemohon'   => $service->nama_pemohon,
                'status'         => $service->status,
                'nomer_hak'      => $service->landBook?->nomer_hak      ?? '—',
                'jenis_hak'      => $service->landBook?->jenis_hak      ?? '—',
                'desa_kecamatan' => $service->landBook?->desa_kecamatan ?? '—',
                'created_at'     => $service->created_at?->format('d M Y'),
                'updated_at'     => $service->updated_at?->format('d M Y, H:i'),
            ],
            'steps'   => $this->buildSteps($service->status),
            'history' => $service->activities->map(fn ($a) => [
                'status'     => $a->status,
                'remarks'    => $a->remarks,
                'created_at' => $a->created_at?->format('d M Y, H:i'),
            ])->values()->all(),
        ]);
    }

    /**
     * Bangun array steps dengan flag done/active
     */
    private function buildSteps(?string $currentStatus): array
    {
        $statusKeys = array_keys($this->allSteps);
        $currentIdx = ($currentStatus !== null)
            ? array_search($currentStatus, $statusKeys)
            : false;

        $steps = [];
        foreach ($this->allSteps as $key => $meta) {
            $idx     = array_search($key, $statusKeys);
            $steps[] = [
                'status' => $key,
                'label'  => $meta['label'],
                'icon'   => $meta['icon'],
                'done'   => $currentIdx !== false && $idx < $currentIdx,
                'active' => $key === $currentStatus,
            ];
        }

        return $steps;
    }
}