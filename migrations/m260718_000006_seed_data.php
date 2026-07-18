<?php

use yii\db\Migration;

/**
 * Seed data awal, diambil dari file "LOGBOOK VALIDASI 2026.xlsx":
 * - 24 unit (dari nama sheet)
 * - indikator per unit beserta jenis (INM/IMP-RS/IMU), target, dan arah target
 * - 3 akun contoh (password: password123)
 *
 * Format $penugasan: 'NAMA UNIT' => [ [nama indikator, jenis, target%, arah], ... ]
 * Indikator dengan nama+jenis sama otomatis menjadi satu record master
 * yang ditugaskan ke banyak unit.
 */
class m260718_000006_seed_data extends Migration
{
    public function safeUp()
    {
        $penugasan = [
            'DIKLIT' => [
                ['Karyawan yang mendapatkan pelatihan minimal 20 jam pelajaran per tahun', 'IMU', 80, '>='],
            ],
            'AMBULANCE' => [
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Ketepatan waktu penggunaan mobil ambulance pada saat pasien darurat', 'IMU', 100, '>='],
            ],
            'PERENCANAAN' => [
                ['Ketersediaan dokumen perencanaan', 'IMU', 100, '>='],
            ],
            'KEUANGAN' => [
                ['Ketepatan waktu pemberian imbalan (insentif) sesuai kondisi keuangan', 'IMU', 100, '>='],
            ],
            'KEPEGAWAIAN' => [
                ['Kepatuhan presensi menggunakan aplikasi Sigma', 'IMU', 100, '>='],
            ],
            'HUMAS' => [
                ['Kecepatan waktu tanggap komplain', 'INM', 80, '>='],
                ['Kepuasan pasien', 'INM', 76.61, '>='],
                ['Terlaksananya pelayanan informasi dan penyelesaian pengaduan masyarakat', 'IMU', 100, '>='],
            ],
            'GUDANG' => [
                ['Ketepatan waktu penyusunan laporan ketersediaan barang setiap bulan', 'IMU', 100, '>='],
            ],
            'REHAB MEDIK' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kejadian drop out pasien terhadap pelayanan rehabilitasi medik yang direncanakan', 'IMU', 25, '<='],
            ],
            'PKRS' => [
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kelengkapan pencatatan pemberian edukasi pasien dan keluarga di Rekam Medis Elektronik', 'IMU', 100, '>='],
            ],
            'IPSPRS' => [
                ['Ketepatan waktu pemeliharaan alat non medis', 'IMU', 100, '>='],
            ],
            'CSSD' => [
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Mutu terhadap pelayanan Sterilisasi Instrumen/Alat Steril (CSSD)', 'IMU', 100, '>='],
            ],
            'LAUNDRY' => [
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Ketepatan waktu penyediaan linen untuk ruang rawat inap', 'IMU', 100, '>='],
            ],
            'SIMRS' => [
                ['SIMRS terintegrasi dengan program pemerintah', 'IMP-RS', 100, '>='],
                ['Tercapainya arsitektur sistem informasi manajemen rumah sakit (SIMRS)', 'IMU', 50, '>='],
            ],
            'REHAB PSIKOSOSIAL' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Tercapainya paket pelayanan pasien sebanyak 12 kali', 'IMU', 80, '>='],
            ],
            'KESLING' => [
                ['Ketersediaan tempat sampah infeksius dan non infeksius', 'IMP-RS', 100, '>='],
                ['Menyimpan sementara limbah domestik yang tidak sesuai standar', 'IMU', 100, '>='],
            ],
            'REKAM MEDIS' => [
                ['Kelengkapan pengisian Rekam Medis Elektronik (RME) rawat inap 24 jam setelah pasien keluar dari rumah sakit', 'INM', 100, '>='],
            ],
            'GIZI' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'IMU', 100, '>='],
            ],
            'RADIOLOGI' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Waktu tunggu pelayanan foto thoraks maksimal 2 jam', 'IMU', 100, '>='],
            ],
            'LABOR' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Pelaporan hasil kritis laboratorium', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Waktu tunggu pemeriksaan hematologi dan kimia klinik < 2 jam', 'IMU', 100, '>='],
            ],
            'FARMASI' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan formularium nasional', 'INM', 80, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Ketepatan pendistribusian obat dari farmasi ke ruang rawat inap 1 x 24 jam', 'IMP-RS', 100, '>='],
                ['Ketepatan double check pendistribusian obat high alert', 'IMP-RS', 100, '>='],
                ['Kepatuhan return obat dari ruang rawat inap ke depo farmasi IGD dan rawat inap', 'IMU', 80, '>='],
            ],
            'NAPZA' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan waktu visite dokter', 'INM', 80, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan upaya pencegahan resiko pasien jatuh', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kepatuhan identifikasi pasien dengan nama, tanggal lahir, nomor rekam medis dan foto sebelum memberikan obat', 'IMP-RS', 100, '>='],
                ['Penerapan metode komunikasi efektif saat transfer internal', 'IMP-RS', 100, '>='],
                ['Kelengkapan assesmen ulang resiko jatuh setiap pasien rawat inap', 'IMP-RS', 100, '>='],
                ['Tidak ada kejadian pasien lari', 'IMU', 95, '>='],
            ],
            'RANAP' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan waktu visite dokter', 'INM', 80, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan terhadap alur klinis (clinical pathway)', 'INM', 80, '>='],
                ['Kepatuhan upaya pencegahan resiko pasien jatuh', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kepatuhan identifikasi pasien dengan nama, tanggal lahir, nomor rekam medis dan foto sebelum memberikan obat', 'IMP-RS', 100, '>='],
                ['Penerapan metode komunikasi efektif saat transfer internal', 'IMP-RS', 100, '>='],
                ['Kelengkapan assesmen ulang resiko jatuh setiap pasien rawat inap', 'IMP-RS', 100, '>='],
                ['Pasien dipulangkan maksimal 10 hari setelah disetujui rawat jalan oleh Dokter Penanggung Jawab Pasien', 'IMP-RS', 100, '>='],
                ['Kapasitas jumlah pasien melebihi jumlah tempat tidur', 'IMP-RS', 0, '<='],
                ['Pasien jiwa yang dapat ditenangkan dalam waktu <= 48 jam (Ruang UPIP)', 'IMU', 95, '>='],
                ['Tidak adanya angka pasien jatuh dengan cidera >= level 2 (Ruang Sebayang)', 'IMU', 100, '>='],
                ['Ketepatan pemasangan baju identifikasi pasien berisiko (risk) pada pasien risiko jatuh (Ruang Kuantan)', 'IMU', 100, '>='],
                ['Lama hari perawatan pasien gangguan jiwa < 6 minggu (Ruang Siak)', 'IMU', 100, '>='],
                ['Kepatuhan upaya pencegahan resiko pasien cedera akibat pasien jatuh (Ruang Mandau 1)', 'IMU', 100, '>='],
                ['Tidak ada kejadian readmission < 1 bulan (Ruang Indragiri)', 'IMU', 95, '>='],
                ['Kelengkapan asesmen awal setiap pasien rawat inap dalam 1x24 jam (Ruang Rokan)', 'IMU', 100, '>='],
                ['Tidak ada kejadian pasien lari (Ruang Mandau 2)', 'IMU', 95, '>='],
            ],
            'IGD' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan upaya pencegahan resiko pasien jatuh', 'INM', 100, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kepatuhan identifikasi pasien dengan nama, tanggal lahir, nomor rekam medis dan foto sebelum memberikan obat', 'IMP-RS', 100, '>='],
                ['Penerapan metode komunikasi efektif saat transfer internal', 'IMP-RS', 100, '>='],
                ['Pemberian pelayanan kegawatdaruratan yang bersertifikasi ATLS/BTLS/ACLS/PPDGJ', 'IMU', 100, '>='],
            ],
            'RAJAL' => [
                ['Kepatuhan identifikasi pasien', 'INM', 100, '>='],
                ['Waktu tunggu rawat jalan', 'INM', 80, '>='],
                ['Kepatuhan kebersihan tangan', 'INM', 85, '>='],
                ['Kepatuhan penggunaan alat pelindung diri', 'INM', 100, '>='],
                ['Kepatuhan identifikasi pasien dengan nama, tanggal lahir, nomor rekam medis dan foto sebelum memberikan obat', 'IMP-RS', 100, '>='],
                ['Ketepatan anastesi, ketepatan lokasi pada pencabutan gigi', 'IMP-RS', 100, '>='],
                ['Kepatuhan kunjungan rehabilitasi rawat jalan pasca rawat inap napza', 'IMP-RS', 100, '>='],
                ['Waktu tunggu rawat jalan', 'IMU', 80, '>='],
            ],
        ];

        // 1. Insert unit + indikator + penugasan
        $indikatorId = []; // cache: "nama|jenis" => id
        foreach ($penugasan as $namaUnit => $daftarIndikator) {
            $this->insert('{{%unit}}', ['nama' => $namaUnit]);
            $unitId = $this->db->getLastInsertID();

            foreach ($daftarIndikator as [$nama, $jenis, $target, $arah]) {
                $kunci = $nama . '|' . $jenis;
                if (!isset($indikatorId[$kunci])) {
                    $this->insert('{{%indikator}}', [
                        'nama' => $nama,
                        'jenis' => $jenis,
                        'target' => $target,
                        'arah_target' => $arah,
                    ]);
                    $indikatorId[$kunci] = $this->db->getLastInsertID();
                }
                $this->insert('{{%indikator_unit}}', [
                    'indikator_id' => $indikatorId[$kunci],
                    'unit_id' => $unitId,
                ]);
            }
        }

        // 2. Akun contoh (password semua: password123)
        $security = Yii::$app->security;
        $igdId = $this->db->createCommand("SELECT id FROM {{%unit}} WHERE nama = 'IGD'")->queryScalar();

        $users = [
            ['superadmin', 'Administrator Sistem', 'super_admin', null],
            ['komitemutu', 'Komite Mutu', 'admin', null],
            ['igd', 'Kepala Unit IGD', 'kepala_unit', $igdId],
        ];
        foreach ($users as [$username, $namaLengkap, $role, $unitId]) {
            $this->insert('{{%user}}', [
                'username' => $username,
                'password_hash' => $security->generatePasswordHash('password123'),
                'auth_key' => $security->generateRandomString(),
                'nama_lengkap' => $namaLengkap,
                'role' => $role,
                'unit_id' => $unitId,
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('{{%logbook}}');
        $this->delete('{{%user}}');
        $this->delete('{{%indikator_unit}}');
        $this->delete('{{%indikator}}');
        $this->delete('{{%unit}}');
    }
}
