<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ProjectNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Rizki Pratama',
            'email' => 'admin@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'Project Management Office (PMO)',
            'phone' => '081234567890',
        ]);

        $pm1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
            'department' => 'Digital Transformation Unit',
            'phone' => '081234567891',
        ]);

        $pm2 = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'siti.rahmawati@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
            'department' => 'Infrastructure & Cloud Solutions',
            'phone' => '081234567892',
        ]);

        $dev1 = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'member',
            'department' => 'Core Software Engineering',
            'phone' => '081234567893',
        ]);

        $designer = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi.lestari@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'member',
            'department' => 'UI/UX & Product Design',
            'phone' => '081234567894',
        ]);

        $qa = User::create([
            'name' => 'Rian Saputra',
            'email' => 'rian.saputra@protrack.id',
            'password' => Hash::make('password'),
            'role' => 'member',
            'department' => 'Quality Assurance & Security',
            'phone' => '081234567895',
        ]);

        $today = Carbon::today();

        // 2. Projects
        $p1 = Project::create([
            'code' => 'PRJ-2026-001',
            'name' => 'Pengembangan Sistem Informasi Pelayanan Publik Terpadu',
            'description' => 'Platform digital one-stop service untuk mempermudah perizinan, pengaduan masyarakat, serta integrasi layanan kependudukan daerah secara terpusat.',
            'client' => 'Dinas Komunikasi dan Informatika Provinsi',
            'manager_id' => $pm1->id,
            'start_date' => $today->copy()->subDays(45),
            'deadline' => $today->copy()->addDays(25),
            'budget' => 450000000,
            'priority' => 'high',
            'status' => 'in_progress',
            'progress' => 68,
            'notes' => 'Integrasi API Capil telah mendapatkan sertifikasi sandbox dari Pusat.',
        ]);

        $p2 = Project::create([
            'code' => 'PRJ-2026-002',
            'name' => 'Digitalisasi Monitoring Infrastruktur Jalan dan Jembatan',
            'description' => 'Sistem pemantauan kondisi jalan berbasis sensor IoT, telemetry, dan peta GIS interaktif untuk mendeteksi kerusakan jalan secara real-time.',
            'client' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
            'manager_id' => $pm2->id,
            'start_date' => $today->copy()->subDays(75),
            'deadline' => $today->copy()->subDays(4), // Overdue!
            'budget' => 620000000,
            'priority' => 'critical',
            'status' => 'overdue',
            'progress' => 82,
            'notes' => 'Perlu percepatan sinkronisasi data sensor lapangan zona utara.',
        ]);

        $p3 = Project::create([
            'code' => 'PRJ-2026-003',
            'name' => 'Modernisasi Portal Layanan Satu Pintu & Retribusi Daerah',
            'description' => 'Pembaruan sistem billing retribusi terpadu dengan gateway pembayaran multi-bank dan QRIS nasional.',
            'client' => 'Badan Pendapatan Daerah Kota',
            'manager_id' => $pm1->id,
            'start_date' => $today->copy()->subDays(20),
            'deadline' => $today->copy()->addDays(60),
            'budget' => 380000000,
            'priority' => 'medium',
            'status' => 'in_progress',
            'progress' => 35,
            'notes' => 'Menunggu verifikasi teknis konektivitas host-to-host bank daerah.',
        ]);

        $p4 = Project::create([
            'code' => 'PRJ-2026-004',
            'name' => 'Implementasi Enterprise Data Warehouse & BI Dashboard Eksekutif',
            'description' => 'Konsolidasi data historis lintas OPD untuk penyusunan analitik visual bagi pengambil kebijakan kepala daerah.',
            'client' => 'Bappeda Litbang Daerah',
            'manager_id' => $pm1->id,
            'start_date' => $today->copy()->subDays(120),
            'deadline' => $today->copy()->subDays(10),
            'budget' => 750000000,
            'priority' => 'high',
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => $today->copy()->subDays(10),
            'notes' => 'Serah terima Berita Acara Penerimaan (BAP) selesai 100%.',
        ]);

        $p5 = Project::create([
            'code' => 'PRJ-2026-005',
            'name' => 'Sistem Rekam Medis Elektronik (RME) Terintegrasi SatuSehat',
            'description' => 'Digitalisasi rekam medis rawat inap dan rawat jalan sesuai standar HL7 FHIR Kementerian Kesehatan RI.',
            'client' => 'RSUD Cipto Husada Pratama',
            'manager_id' => $pm2->id,
            'start_date' => $today->copy()->subDays(15),
            'deadline' => $today->copy()->addDays(5), // Approaching deadline!
            'budget' => 490000000,
            'priority' => 'critical',
            'status' => 'in_progress',
            'progress' => 75,
            'notes' => 'Sprint final bridging BPJS VClaim dan bridging API SatuSehat.',
        ]);

        $p6 = Project::create([
            'code' => 'PRJ-2026-006',
            'name' => 'Revitalisasi Arsitektur Keamanan Siber SOC & Jaringan Kampus',
            'description' => 'Peningkatan kapasitas core network switch, implementasi SD-WAN, dan konfigurasi next-generation firewall.',
            'client' => 'Universitas Negeri Sejahtera',
            'manager_id' => $pm2->id,
            'start_date' => $today->copy()->subDays(7),
            'deadline' => $today->copy()->addDays(90),
            'budget' => 520000000,
            'priority' => 'low',
            'status' => 'in_progress',
            'progress' => 15,
            'notes' => 'Pengadaan perangkat keras tahap I sedang berjalan.',
        ]);

        $p7 = Project::create([
            'code' => 'PRJ-2026-007',
            'name' => 'Pengembangan Portal E-Procurement dan Pengadaan Elektronik',
            'description' => 'Aplikasi tender lelang digital mandiri yang transparan, akuntabel, dan terhubung dengan LKPP.',
            'client' => 'Sekretariat Bagian Pengadaan Barang Jasa',
            'manager_id' => $pm1->id,
            'start_date' => $today->copy()->addDays(10),
            'deadline' => $today->copy()->addDays(150),
            'budget' => 310000000,
            'priority' => 'medium',
            'status' => 'not_started',
            'progress' => 0,
            'notes' => 'Kickoff meeting dijadwalkan minggu depan.',
        ]);

        // 3. Tasks for Project 1
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $pm1->id,
            'title' => 'Penyusunan Software Requirement Specification (SRS)',
            'description' => 'Finalisasi dokumen spesifikasi teknis dan matriks fungsionalitas aplikasi bersama stakeholder Diskominfo.',
            'priority' => 'high',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(45),
            'due_date' => $today->copy()->subDays(35),
            'completed_at' => $today->copy()->subDays(36),
            'order' => 1,
        ]);
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $designer->id,
            'title' => 'Wireframing & Desain UI/UX Design System Figma',
            'description' => 'Pembuatan komponen visual standar, color palette, responsive design layout, dan alur perizinan warga.',
            'priority' => 'medium',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(35),
            'due_date' => $today->copy()->subDays(20),
            'completed_at' => $today->copy()->subDays(21),
            'order' => 2,
        ]);
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $dev1->id,
            'title' => 'Pembangunan Modul Autentikasi SSO & Role Management',
            'description' => 'Implementasi login warga via NIK serta login ASN dengan multifactor authentication.',
            'priority' => 'high',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(25),
            'due_date' => $today->copy()->subDays(10),
            'completed_at' => $today->copy()->subDays(9),
            'order' => 3,
        ]);
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $dev1->id,
            'title' => 'Integrasi API Gateway Disdukcapil & Verifikasi Biometrik',
            'description' => 'Menghubungkan endpoint verifikasi data penduduk secara secure menggunakan mutual TLS.',
            'priority' => 'critical',
            'status' => 'in_progress',
            'start_date' => $today->copy()->subDays(10),
            'due_date' => $today->copy()->addDays(5),
            'order' => 4,
        ]);
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $qa->id,
            'title' => 'Pengujian Keamanan Vulnerability Assessment & Pen-Test',
            'description' => 'Pemeriksaan potensi celah SQL injection, XSS, dan kepatuhan standar keamanan data pribadi.',
            'priority' => 'high',
            'status' => 'review',
            'start_date' => $today->copy()->subDays(5),
            'due_date' => $today->copy()->addDays(12),
            'order' => 5,
        ]);
        Task::create([
            'project_id' => $p1->id,
            'assigned_to' => $qa->id,
            'title' => 'User Acceptance Testing (UAT) Tahap 1 bersama Operator',
            'description' => 'Pengujian skenario pengajuan dokumen izin oleh 15 operator percontohan kecamatan.',
            'priority' => 'medium',
            'status' => 'todo',
            'start_date' => $today->copy()->addDays(10),
            'due_date' => $today->copy()->addDays(22),
            'order' => 6,
        ]);

        // Tasks for Project 2 (Overdue)
        Task::create([
            'project_id' => $p2->id,
            'assigned_to' => $dev1->id,
            'title' => 'Instalasi & Kalibrasi Sensor Vibrasi Jembatan',
            'description' => 'Pemasangan mikro-akselerometer dan unit telemetri di jembatan penghubung utama.',
            'priority' => 'high',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(70),
            'due_date' => $today->copy()->subDays(50),
            'completed_at' => $today->copy()->subDays(48),
            'order' => 1,
        ]);
        Task::create([
            'project_id' => $p2->id,
            'assigned_to' => $dev1->id,
            'title' => 'Integrasi Engine GIS Tile Server OpenStreetMap & PostGIS',
            'description' => 'Rendering layer spasial kondisi jalan rusak dengan polygon dan heat map.',
            'priority' => 'high',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(50),
            'due_date' => $today->copy()->subDays(30),
            'completed_at' => $today->copy()->subDays(28),
            'order' => 2,
        ]);
        Task::create([
            'project_id' => $p2->id,
            'assigned_to' => $dev1->id,
            'title' => 'Sinkronisasi Data Real-Time Gateway Sensor Lapangan',
            'description' => 'Penyelesaian latency pengiriman data MQTT broker dari titik remote ke cloud server.',
            'priority' => 'critical',
            'status' => 'in_progress',
            'start_date' => $today->copy()->subDays(25),
            'due_date' => $today->copy()->subDays(3), // Overdue task!
            'order' => 3,
        ]);
        Task::create([
            'project_id' => $p2->id,
            'assigned_to' => $qa->id,
            'title' => 'Verifikasi Lapangan Akurasi Algoritma Deteksi Keretakan',
            'description' => 'Uji akurasi computer vision kamera drone terhadap retakan aspal.',
            'priority' => 'high',
            'status' => 'review',
            'start_date' => $today->copy()->subDays(10),
            'due_date' => $today->copy()->subDays(1), // Overdue!
            'order' => 4,
        ]);

        // Tasks for Project 5 (Critical, deadline in 5 days)
        Task::create([
            'project_id' => $p5->id,
            'assigned_to' => $dev1->id,
            'title' => 'Bridging Metadata Resource Patient & Encounter SatuSehat',
            'description' => 'Validasi mapping data pasien rawat inap ke standar HL7 FHIR versi 4.0.',
            'priority' => 'critical',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(14),
            'due_date' => $today->copy()->subDays(7),
            'completed_at' => $today->copy()->subDays(6),
            'order' => 1,
        ]);
        Task::create([
            'project_id' => $p5->id,
            'assigned_to' => $dev1->id,
            'title' => 'Integrasi Modul E-Prescription & E-Resep Farmasi',
            'description' => 'Digitalisasi pengiriman resep dokter langsung ke instalasi farmasi rumah sakit.',
            'priority' => 'high',
            'status' => 'in_progress',
            'start_date' => $today->copy()->subDays(6),
            'due_date' => $today->copy()->addDays(2),
            'order' => 2,
        ]);
        Task::create([
            'project_id' => $p5->id,
            'assigned_to' => $qa->id,
            'title' => 'Stress Test Beban Transaksi Poli Spesialis Saat Jam Sibuk',
            'description' => 'Simulasi serentak 200 tenaga medis input tindakan rekam medis secara bersamaan.',
            'priority' => 'high',
            'status' => 'todo',
            'start_date' => $today->copy()->subDays(2),
            'due_date' => $today->copy()->addDays(4),
            'order' => 3,
        ]);

        // Tasks for Project 3
        Task::create([
            'project_id' => $p3->id,
            'assigned_to' => $designer->id,
            'title' => 'Desain Antarmuka Pembayaran Retribusi Online Mobile',
            'description' => 'Flow transaksi pembayaran retribusi pasar, sampah, dan reklame.',
            'priority' => 'medium',
            'status' => 'done',
            'start_date' => $today->copy()->subDays(18),
            'due_date' => $today->copy()->subDays(8),
            'completed_at' => $today->copy()->subDays(7),
            'order' => 1,
        ]);
        Task::create([
            'project_id' => $p3->id,
            'assigned_to' => $dev1->id,
            'title' => 'Setup Modul Webhook Payment Gateway Multi-Channel',
            'description' => 'Handling notifikasi realtime status bayar dari Bank BPD dan QRIS.',
            'priority' => 'high',
            'status' => 'in_progress',
            'start_date' => $today->copy()->subDays(7),
            'due_date' => $today->copy()->addDays(14),
            'order' => 2,
        ]);

        // 4. Milestones
        // P1 Milestones
        Milestone::create([
            'project_id' => $p1->id,
            'name' => '1. Requirement Analysis & SRS Sign-off',
            'description' => 'Penyelarasan kebutuhan antar pemangku kepentingan dan penandatanganan dokumen KAK.',
            'target_date' => $today->copy()->subDays(35),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(35),
            'order' => 1,
        ]);
        Milestone::create([
            'project_id' => $p1->id,
            'name' => '2. UI/UX Design System & Prototype Approval',
            'description' => 'Persetujuan desain visual antarmuka sistem oleh Kepala Dinas Kominfo.',
            'target_date' => $today->copy()->subDays(20),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(19),
            'order' => 2,
        ]);
        Milestone::create([
            'project_id' => $p1->id,
            'name' => '3. Integrasi Modul Kependudukan & Database Core',
            'description' => 'Penyelesaian konektivitas API data penduduk dan modul inti sistem.',
            'target_date' => $today->copy()->addDays(8),
            'status' => 'in_progress',
            'order' => 3,
        ]);
        Milestone::create([
            'project_id' => $p1->id,
            'name' => '4. User Acceptance Testing & Security Audit',
            'description' => 'Pengujian menyeluruh performa, beban, dan audit keamanan sistem.',
            'target_date' => $today->copy()->addDays(20),
            'status' => 'pending',
            'order' => 4,
        ]);
        Milestone::create([
            'project_id' => $p1->id,
            'name' => '5. Go-Live Production & Bimbingan Teknis ASN',
            'description' => 'Peluncuran resmi ke publik dan workshop penggunaan untuk operator kecamatan.',
            'target_date' => $today->copy()->addDays(25),
            'status' => 'pending',
            'order' => 5,
        ]);

        // P2 Milestones
        Milestone::create([
            'project_id' => $p2->id,
            'name' => '1. Survey Lapangan & Kalibrasi Perangkat Sensor',
            'description' => 'Penentuan 12 titik jembatan strategis dan pemasangan hardware.',
            'target_date' => $today->copy()->subDays(45),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(44),
            'order' => 1,
        ]);
        Milestone::create([
            'project_id' => $p2->id,
            'name' => '2. Pembangunan GIS Engine & Web Dashboard',
            'description' => 'Penyajian peta geospasial kondisi jalan dan data telemetry real-time.',
            'target_date' => $today->copy()->subDays(15),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(12),
            'order' => 2,
        ]);
        Milestone::create([
            'project_id' => $p2->id,
            'name' => '3. Integrasi Komprehensif Sensor & Alerting Bot',
            'description' => 'Sistem peringatan dini otomatis via notifikasi jika terdeteksi getaran abnormal.',
            'target_date' => $today->copy()->subDays(4), // Overdue milestone
            'status' => 'in_progress',
            'order' => 3,
        ]);
        Milestone::create([
            'project_id' => $p2->id,
            'name' => '4. Serah Terima Pekerjaan & Pelatihan Operator',
            'description' => 'Penyusunan Berita Acara Serah Terima (BAST) dan manual book.',
            'target_date' => $today->copy()->addDays(10),
            'status' => 'pending',
            'order' => 4,
        ]);

        // P5 Milestones
        Milestone::create([
            'project_id' => $p5->id,
            'name' => '1. Sertifikasi Sandbox SatuSehat Kemenkes RI',
            'description' => 'Kelulusan verifikasi integrasi format data FHIR tingkat fasilitas kesehatan.',
            'target_date' => $today->copy()->subDays(7),
            'status' => 'completed',
            'completed_at' => $today->copy()->subDays(6),
            'order' => 1,
        ]);
        Milestone::create([
            'project_id' => $p5->id,
            'name' => '2. Modul Rekam Medis Rawat Jalan & Farmasi',
            'description' => 'Penerapan rekam medis digital di 14 poliklinik rawat jalan.',
            'target_date' => $today->copy()->addDays(2),
            'status' => 'in_progress',
            'order' => 2,
        ]);
        Milestone::create([
            'project_id' => $p5->id,
            'name' => '3. Full Deployment RME Rawat Inap & IGD',
            'description' => 'Implementasi sistem menyeluruh di seluruh unit rumah sakit.',
            'target_date' => $today->copy()->addDays(5),
            'status' => 'pending',
            'order' => 3,
        ]);

        // 5. Activities
        Activity::log(
            $admin->id,
            $p1->id,
            'created',
            'project',
            $p1->id,
            'Rizki Pratama membuat proyek baru "Pengembangan Sistem Informasi Pelayanan Publik Terpadu"'
        );
        Activity::log(
            $pm1->id,
            $p1->id,
            'updated',
            'project',
            $p1->id,
            'Budi Santoso memperbarui progress proyek menjadi 68%'
        );
        Activity::log(
            $dev1->id,
            $p1->id,
            'completed',
            'task',
            3,
            'Ahmad Fauzi menyelesaikan task "Pembangunan Modul Autentikasi SSO & Role Management"'
        );
        Activity::log(
            $pm2->id,
            $p2->id,
            'status_changed',
            'project',
            $p2->id,
            'Siti Rahmawati memperbarui status proyek menjadi Overdue (Perlu Perhatian Khusus)'
        );
        Activity::log(
            $designer->id,
            $p1->id,
            'completed',
            'task',
            2,
            'Dewi Lestari menyelesaikan task "Wireframing & Desain UI/UX Design System Figma"'
        );
        Activity::log(
            $pm1->id,
            $p1->id,
            'created',
            'task',
            4,
            'Budi Santoso menambahkan task "Integrasi API Gateway Disdukcapil & Verifikasi Biometrik"'
        );
        Activity::log(
            $pm2->id,
            $p5->id,
            'completed',
            'milestone',
            7,
            'Siti Rahmawati menandai milestone "Sertifikasi Sandbox SatuSehat Kemenkes RI" sebagai Selesai'
        );
        Activity::log(
            $admin->id,
            $p4->id,
            'completed',
            'project',
            $p4->id,
            'Rizki Pratama menyelesaikan proyek "Implementasi Enterprise Data Warehouse & BI Dashboard Eksekutif"'
        );

        // 6. Project Files
        ProjectFile::create([
            'project_id' => $p1->id,
            'user_id' => $pm1->id,
            'name' => 'Dokumen_Spesifikasi_Kebutuhan_SRS_v2.pdf',
            'file_path' => 'projects/files/srs_v2.pdf',
            'file_size' => 3450200,
            'file_type' => 'application/pdf',
        ]);
        ProjectFile::create([
            'project_id' => $p1->id,
            'user_id' => $designer->id,
            'name' => 'Design_System_Export_Palette_and_Tokens.fig',
            'file_path' => 'projects/files/design_system.fig',
            'file_size' => 12500000,
            'file_type' => 'application/octet-stream',
        ]);
        ProjectFile::create([
            'project_id' => $p2->id,
            'user_id' => $pm2->id,
            'name' => 'Laporan_Kalibrasi_Sensor_Jembatan_PUPR.pdf',
            'file_path' => 'projects/files/kalibrasi_sensor.pdf',
            'file_size' => 4820100,
            'file_type' => 'application/pdf',
        ]);

        // 7. Notifications
        $admin->notify(new ProjectNotification(
            title: 'Proyek Terlambat',
            message: 'Proyek "Digitalisasi Monitoring Infrastruktur Jalan dan Jembatan" melewati deadline 4 hari yang lalu.',
            type: 'danger',
            url: "/projects/{$p2->id}",
            icon: 'alert-triangle'
        ));

        $admin->notify(new ProjectNotification(
            title: 'Deadline Mendekat',
            message: 'Proyek "Sistem Rekam Medis Elektronik (RME) Terintegrasi SatuSehat" jatuh tempo dalam 5 hari.',
            type: 'warning',
            url: "/projects/{$p5->id}",
            icon: 'clock'
        ));

        $admin->notify(new ProjectNotification(
            title: 'Task Overdue',
            message: 'Task "Sinkronisasi Data Real-Time Gateway Sensor Lapangan" mengalami keterlambatan.',
            type: 'danger',
            url: "/projects/{$p2->id}",
            icon: 'alert-circle'
        ));

        $admin->notify(new ProjectNotification(
            title: 'Milestone Diselesaikan',
            message: 'Milestone "Sertifikasi Sandbox SatuSehat Kemenkes RI" telah diselesaikan oleh Siti Rahmawati.',
            type: 'success',
            url: "/projects/{$p5->id}",
            icon: 'check-circle'
        ));
    }
}
