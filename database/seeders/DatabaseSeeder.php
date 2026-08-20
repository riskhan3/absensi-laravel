<?php

namespace Database\Seeders;

use App\Models\AttendanceStatus;
use App\Models\Classroom;
use App\Models\GeneralSetting;
use App\Models\Major;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. General Settings ──────────────────────────────────────────────
        GeneralSetting::instance();

        // ── 2. Attendance Statuses ───────────────────────────────────────────
        foreach (['Hadir', 'Sakit', 'Izin', 'Tanpa Keterangan'] as $s) {
            AttendanceStatus::firstOrCreate(['name' => $s]);
        }

        // ── 3. Subjects (Mata Pelajaran) ──────────────────────────────────────
        $subjectsData = [
            ['name' => 'Matematika',            'code' => 'MTK',    'class_group' => 'all'],
            ['name' => 'Bahasa Indonesia',       'code' => 'BIND',   'class_group' => 'all'],
            ['name' => 'Pendidikan Pancasila',   'code' => 'PPKN',   'class_group' => 'all'],
            ['name' => 'BAM',                    'code' => 'BAM',    'class_group' => 'all'],
            ['name' => 'Agama',                  'code' => 'AGM',    'class_group' => 'all'],
            ['name' => 'PJOK',                   'code' => 'PJOK',   'class_group' => 'all'],
            ['name' => 'Bahasa Inggris',         'code' => 'BING',   'class_group' => '3-6'],
            ['name' => 'IPAS',                   'code' => 'IPAS',   'class_group' => '3-6'],
        ];
        foreach ($subjectsData as $sd) {
            Subject::firstOrCreate(['code' => $sd['code']], $sd);
        }

        // ── 4. System Users (Admin, Superadmin, Scanner) ─────────────────────
        User::firstOrCreate(['email' => 'superadmin@sekolah.com'], [
            'name'     => 'Super Admin',
            'password' => Hash::make('admin123'),
            'role'     => 'superadmin',
        ]);
        User::firstOrCreate(['email' => 'admin@sekolah.com'], [
            'name'     => 'Admin Sekolah',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
        User::firstOrCreate(['email' => 'scanner@sekolah.com'], [
            'name'     => 'Petugas Scanner',
            'password' => Hash::make('scanner123'),
            'role'     => 'scanner',
        ]);

        // ── 5. Major ─────────────────────────────────────────────────────────
        $major = Major::firstOrCreate(['name' => 'Umum']);

        // ── 6. Teachers & Kepala Sekolah (Data Real Dapodik) ──────────────────
        // Format email: nama.tanpa.gelar@sdn30selayo.sch.id
        // Wali kelas belum ditetapkan (homeroom_teacher_id = null pada semua kelas)
        $teachersData = [
            [
                'nuptk'             => '1748773674230152',
                'nip'               => '199504162025212089',
                'nik'               => '1302105604950002',
                'name'              => 'Dian Yurnades',
                'gender'            => 'Perempuan',
                'birth_date'        => '1995-04-16',
                'employment_status' => 'PPPK Paruh Waktu',
                'position'          => 'Guru',
                'email'             => 'dian.yurnades@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '0037746647300033',
                'nip'               => '196807052008012002',
                'nik'               => '1302104507680003',
                'name'              => 'Elmi',
                'gender'            => 'Perempuan',
                'birth_date'        => '1968-07-05',
                'employment_status' => 'PNS',
                'position'          => 'Guru',
                'email'             => 'elmi@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '6460770671230213',
                'nip'               => '199211282022212013',
                'nik'               => '1372016811920021',
                'name'              => 'Nofia Lola Putri',
                'gender'            => 'Perempuan',
                'birth_date'        => '1992-11-28',
                'employment_status' => 'PPPK',
                'position'          => 'Guru',
                'email'             => 'nofia.lola.putri@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '7248745646200003',
                'nip'               => '196709161988021001',
                'nik'               => '1302101609670002',
                'name'              => 'Albaria',
                'gender'            => 'Laki-laki',
                'birth_date'        => '1967-09-16',
                'employment_status' => 'PNS',
                'position'          => 'Kepala Sekolah',
                'email'             => 'albaria@sdn30selayo.sch.id',
                'role'              => 'kepala_sekolah',
            ],
            [
                'nuptk'             => '9548759660130142',
                'nip'               => '198102162025211066',
                'nik'               => '1302101602810002',
                'name'              => 'Nur Aswat',
                'gender'            => 'Laki-laki',
                'birth_date'        => '1981-02-16',
                'employment_status' => 'PPPK Paruh Waktu',
                'position'          => 'Guru',
                'email'             => 'nur.aswat@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '8633762663300062',
                'nip'               => '198403012008012003',
                'nik'               => '1302104103840003',
                'name'              => 'Yonatha Mariza',
                'gender'            => 'Perempuan',
                'birth_date'        => '1984-03-01',
                'employment_status' => 'PNS',
                'position'          => 'Guru',
                'email'             => 'yonatha.mariza@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '6739770671230202',
                'nip'               => '199204072024212012',
                'nik'               => '1302104704920002',
                'name'              => 'Titi Suryani',
                'gender'            => 'Perempuan',
                'birth_date'        => '1992-04-07',
                'employment_status' => 'PPPK',
                'position'          => 'Guru',
                'email'             => 'titi.suryani@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '5149769670230453',
                'nip'               => '199108172025212142',
                'nik'               => '1302105708910004',
                'name'              => 'Gusrini',
                'gender'            => 'Perempuan',
                'birth_date'        => '1991-08-17',
                'employment_status' => 'PPPK Paruh Waktu',
                'position'          => 'Guru',
                'email'             => 'gusrini@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '3938744645300012',
                'nip'               => '196606062008012003',
                'nik'               => '1372014606660041',
                'name'              => 'Yelinda',
                'gender'            => 'Perempuan',
                'birth_date'        => '1966-06-06',
                'employment_status' => 'PNS',
                'position'          => 'Guru',
                'email'             => 'yelinda@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '5150769670230173',
                'nip'               => '199108182025212037',
                'nik'               => '1302105608910003',
                'name'              => 'Yevi Siska Dewi',
                'gender'            => 'Perempuan',
                'birth_date'        => '1991-08-18',
                'employment_status' => 'PPPK',
                'position'          => 'Guru',
                'email'             => 'yevi.siska.dewi@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
            [
                'nuptk'             => '7239770671130023',
                'nip'               => '199209072025212149',
                'nik'               => '1302104709920001',
                'name'              => 'Sri Diah Kurnia',
                'gender'            => 'Perempuan',
                'birth_date'        => '1992-09-07',
                'employment_status' => 'PPPK Paruh Waktu',
                'position'          => 'Guru',
                'email'             => 'sri.diah.kurnia@sdn30selayo.sch.id',
                'role'              => 'guru_mapel',
            ],
        ];

        foreach ($teachersData as $t) {
            $teacher = Teacher::firstOrCreate(['nuptk' => $t['nuptk']], [
                'nip'               => $t['nip'],
                'nik'               => $t['nik'],
                'name'              => $t['name'],
                'gender'            => $t['gender'],
                'birth_date'        => $t['birth_date'],
                'employment_status' => $t['employment_status'],
                'position'          => $t['position'],
                'unique_code'       => 'TCH-' . strtoupper(Str::random(8)),
            ]);

            $user = User::firstOrCreate(['email' => $t['email']], [
                'name'       => $t['name'],
                'password'   => Hash::make('guru123'),
                'role'       => $t['role'],
                'teacher_id' => $teacher->id,
            ]);

            // Pastikan teacher_id tersimpan di user
            if (!$user->teacher_id) {
                $user->update(['teacher_id' => $teacher->id]);
            }
        }

        // ── 7. Classrooms (6 Kelas, wali belum ditetapkan) ────────────────────
        $gradeLabels = [
            ['grade' => 'I',   'label' => 'A'],
            ['grade' => 'II',  'label' => 'A'],
            ['grade' => 'III', 'label' => 'A'],
            ['grade' => 'IV',  'label' => 'A'],
            ['grade' => 'V',   'label' => 'A'],
            ['grade' => 'VI',  'label' => 'A'],
        ];
        $classrooms = [];
        foreach ($gradeLabels as $gl) {
            $classroom = Classroom::firstOrCreate(
                ['grade' => $gl['grade'], 'label' => $gl['label'], 'major_id' => $major->id],
                ['homeroom_teacher_id' => null]   // Wali kelas belum ditetapkan
            );
            $classrooms[$gl['grade']] = $classroom;
        }

        // ── 8. Students (47 Siswa Aktif — Data Real Dapodik) ──────────────────
        // NIS menggunakan NISN. Kelas I kosong (menunggu PPDB).
        // Format: ['nisn', 'name', 'gender' (L/P), 'birth_date', 'mother_name', 'nik', 'grade']

        $studentsData = [
            // ── Kelas II (9 siswa) ────────────────────────────────────────────
            ['3186895369', 'Abdul Rahim',               'L', '2018-11-23', 'Bayu Mitra',               '1372012311180002', 'II'],
            ['3190509864', 'Arsyla Adinda Putri',        'P', '2019-01-20', 'Helti Destina',             '1302106001190003', 'II'],
            ['3192208419', 'Assyabiya Shaqueena Atian',  'P', '2019-04-24', 'Titi Suryani',              '3275086404190004', 'II'],
            ['3195857874', 'Dzakhira Aftani',            'P', '2019-04-24', 'Marno Fitri Yanti',         '1302106404190001', 'II'],
            ['3183597233', 'Khalifa Azza Abdullah',      'L', '2018-10-27', 'Efrichy Libo Frentati',     '1302102710180001', 'II'],
            ['3184325603', 'Nova Linata',                'P', '2018-10-10', 'Leni Herawati',             '1302105010180002', 'II'],
            ['3195811995', 'Rara Julya Putri',           'P', '2019-07-09', 'Diana Shintawati',          '1302104907190001', 'II'],
            ['3196568459', 'Sulthan Mauza',              'L', '2019-07-13', 'Weni',                      '1302101307190002', 'II'],
            ['3181658374', 'Zaigham Ramola',             'L', '2018-11-29', 'Larasswari',                '1302102911180001', 'II'],

            // ── Kelas III (6 siswa) ───────────────────────────────────────────
            ['3175261297', 'Aqhira Merila Putri',        'P', '2017-08-31', 'Helti Destina',             '1302107108170002', 'III'],
            ['3179864462', 'Azlan Hidayat',              'L', '2017-10-27', 'Hidayatul Asma',            '1302102710170002', 'III'],
            ['3175705090', 'Hanifah Atikah',             'P', '2017-12-17', 'Yenni',                     '1302105712170001', 'III'],
            ['3177769335', 'Muhammad Ilham',             'L', '2017-12-19', 'Reni Agustin',              '1302101912170003', 'III'],
            ['3189062489', 'Najmi Yama Husna',           'P', '2018-01-10', 'Desma Gustinora Sari',      '1302105001180002', 'III'],
            ['3181818172', 'Tsania Afia Rahmah',         'P', '2018-10-29', 'Riri Purnama Sari',         '1302106910180001', 'III'],

            // ── Kelas IV (15 siswa) ───────────────────────────────────────────
            ['3169268470', 'Alif Hafizh Sharkhan',       'L', '2016-07-23', 'Yenni',                     '1302102307160003', 'IV'],
            ['3175298491', 'Amelia Putri',               'P', '2017-11-26', 'Susi Susanti',              '1302106611170001', 'IV'],
            ['3178159077', 'Aqila Nisha Shafana',        'P', '2017-04-05', 'Dian Ariessa Fitri',        '1302104504170002', 'IV'],
            ['3169907454', 'Daffa Restu Ilhammy',        'L', '2016-12-19', 'Reska Mulya',               '1302101912160002', 'IV'],
            ['3169241057', 'Fatimah Az Zahra',           'P', '2016-04-23', 'Eri Susanti',               '1302106304160001', 'IV'],
            ['3153384240', 'Febi Nurain',                'P', '2015-03-11', 'Iis Dahlia',                '1302105103150001', 'IV'],
            ['3169917853', 'Ghailand Safaraz',           'L', '2016-09-07', 'Siska Febria Ningsih',      '1302040709160005', 'IV'],
            ['3179541520', 'Muhamad Alif Saputra',       'L', '2017-04-04', 'Agustini',                  '1302100404170003', 'IV'],
            ['3161821157', 'Muhammad Alfatih',           'L', '2016-07-12', 'Misna Nirda',               '1302101207170001', 'IV'],
            ['3161449559', 'Muhammad Yazid Albusthami',  'L', '2016-10-13', 'Siti Prangge Natalia',      '1302061301160003', 'IV'],
            ['3171963071', 'Naura Falisya Ardania',      'P', '2017-09-21', 'Liswarti',                  '1302106109170001', 'IV'],
            ['3163816680', 'Rafa Ahmad Albarack',        'L', '2016-04-18', 'Helfiyoris',                '1302101804160001', 'IV'],
            ['3176144449', 'Rafel Ferdinan',             'L', '2017-02-24', 'Noza Metra',                '1302102402170002', 'IV'],
            ['3173373206', 'Rafka Agustian Pratama',     'L', '2017-08-16', 'Novia Indah Permata Sari',  '1302101608170002', 'IV'],
            ['3170872030', 'Zildan Habibi Arsad',        'L', '2017-05-03', 'Erni',                      '1302100305170006', 'IV'],

            // ── Kelas V (10 siswa) ────────────────────────────────────────────
            ['3156292502', 'Abdil',                      'L', '2015-11-19', 'Ermawati',                  '1302101911150005', 'V'],
            ['3160001779', 'Afifah Nugraha',             'P', '2016-03-09', 'Nurhayati',                 '1302104903160001', 'V'],
            ['3167119216', 'Dafha Muhamad Syakban',      'L', '2016-05-16', 'Arma Yosisna',              '1302101605160001', 'V'],
            ['3146080305', 'Gresia Adinda Putri',        'P', '2013-11-13', 'Maria Fransiska',           '1302101311130003', 'V'],
            ['3158054508', 'Muhammad Khalid Syawaluddin','L', '2015-07-31', 'Siti Prangge Natalia',      '1302063107150002', 'V'],
            ['3161889719', 'Rahma Ayunda',               'P', '2015-12-27', 'Leni Herawati',             '1302106712150003', 'V'],
            ['3151903232', 'Siti Aisyah Kirani',         'P', '2015-05-26', 'Susi Susanti',              '1302106605150001', 'V'],
            ['3162009131', 'Siti Nur Azizah',            'P', '2016-09-21', 'Susi Fitri Yenti',          '1302106109160001', 'V'],
            ['3160876269', 'Yelsi Handayani',            'P', '2016-01-13', 'Nurhayati',                 '1302105301160001', 'V'],
            ['3162717295', 'Zaki Ramadhan',              'L', '2016-05-23', 'Eli Solma',                 '1302102305160002', 'V'],

            // ── Kelas VI (7 siswa) ────────────────────────────────────────────
            ['3152114990', 'Dany Fayyadhi Zhafar',       'L', '2015-03-03', 'Herlina',                   '1302100303150002', 'VI'],
            ['3155835530', 'Deanca Ibnu Adrianov',       'L', '2015-04-28', 'Misraweti',                 '1471122804150003', 'VI'],
            ['3151864895', 'Derma Erianti',              'P', '2015-05-20', 'Bayu Mitra',                '1302106005150001', 'VI'],
            ['3155713441', 'Halgi Fahriansyah',          'L', '2015-02-26', 'Dasri Yurianti',            '1302102602150001', 'VI'],
            ['3141711959', 'Khanza Azzahra',             'P', '2014-08-20', 'Yurna Putri',               '1302106008140001', 'VI'],
            ['3157664336', 'Muhammad Rafa Azka',         'L', '2015-09-24', 'Maizalina',                 '1302102409150003', 'VI'],
            ['0134714953', 'Rabby Zhikra Pratama',       'L', '2013-10-02', 'Mayang Alfakun Debi',       '1302100210130001', 'VI'],
        ];

        foreach ($studentsData as $s) {
            [$nisn, $name, $genderCode, $birthDate, $motherName, $nik, $grade] = $s;

            $gender = ($genderCode === 'L') ? 'Laki-laki' : 'Perempuan';

            Student::firstOrCreate(['nis' => $nisn], [
                'nisn'         => $nisn,
                'name'         => $name,
                'classroom_id' => $classrooms[$grade]->id,
                'gender'       => $gender,
                'birth_date'   => $birthDate,
                'mother_name'  => $motherName,
                'nik'          => $nik,
                'unique_code'  => 'STD-' . $nisn,
            ]);
        }

        // ── 9. Staff — Tenaga Kependidikan ────────────────────────────────────
        // Dona Handayani: Tenaga Kependidikan Honor → staff + akun TU (role: tu)
        // Melkoyama:      Tenaga Kependidikan Honor → staff only (tanpa akun login)
        $staffData = [
            [
                'nip'      => null,   // Tidak ber-NIP (Tenaga Honor)
                'name'     => 'Dona Handayani',
                'position' => 'Tenaga Kependidikan',
                'gender'   => 'Perempuan',
                'nik'      => '1302105112940003',
                'email'    => 'dona.handayani@sdn30selayo.sch.id',
                'role'     => 'tu',   // Akun admin TU
            ],
            [
                'nip'      => null,
                'name'     => 'Melkoyama',
                'position' => 'Tenaga Kependidikan',
                'gender'   => 'Laki-laki',
                'nik'      => '1302101209820001',
                'email'    => null,   // Tidak memiliki akun login
                'role'     => null,
            ],
        ];

        foreach ($staffData as $i => $sd) {
            // Gunakan NIK sebagai identifier unik karena tidak ada NIP
            $uniqueNip = $sd['nip'] ?? ('HON-' . $sd['nik']);

            $staff = Staff::firstOrCreate(['nip' => $uniqueNip], [
                'name'        => $sd['name'],
                'position'    => $sd['position'],
                'gender'      => $sd['gender'],
                'unique_code' => 'STF-' . strtoupper(Str::random(8)),
            ]);

            // Buat akun user untuk yang memiliki role (Dona Handayani)
            if ($sd['email'] && $sd['role']) {
                User::firstOrCreate(['email' => $sd['email']], [
                    'name'     => $sd['name'],
                    'password' => Hash::make('tu123'),
                    'role'     => $sd['role'],
                ]);
            }
        }

        // ── Ringkasan Output ──────────────────────────────────────────────────
        echo "\n╔══════════════════════════════════════════════════════╗\n";
        echo "║        ✅  SEEDER SD N 30 SELAYO SELESAI!           ║\n";
        echo "╠══════════════════════════════════════════════════════╣\n";
        echo "║  Siswa aktif: 47 (Kelas I: 0, II: 9, III: 6,       ║\n";
        echo "║               IV: 15, V: 10, VI: 7)                 ║\n";
        echo "║  Guru/KS    : 11 personel                           ║\n";
        echo "║  Staff TK   : 2 personel                            ║\n";
        echo "╠══════════════════════════════════════════════════════╣\n";
        echo "║  AKUN LOGIN:                                         ║\n";
        echo "║  superadmin@sekolah.com          / admin123          ║\n";
        echo "║  admin@sekolah.com               / admin123          ║\n";
        echo "║  scanner@sekolah.com             / scanner123        ║\n";
        echo "║  albaria@sdn30selayo.sch.id       / guru123          ║\n";
        echo "║  dian.yurnades@sdn30selayo.sch.id / guru123          ║\n";
        echo "║  dona.handayani@sdn30selayo.sch.id/ tu123            ║\n";
        echo "╚══════════════════════════════════════════════════════╝\n";
    }
}
