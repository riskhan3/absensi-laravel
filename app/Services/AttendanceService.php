<?php

namespace App\Services;

use App\Exceptions\AlreadyCheckedInException;
use App\Exceptions\InvalidScanCodeException;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\{Student, Teacher, Staff, StudentAttendance, TeacherAttendance, StaffAttendance, AttendanceStatus};
use Carbon\Carbon;

class AttendanceService
{
    public function __construct(private readonly GeofencingService $geofencingService) {}

    /**
     * @param  array<int> $subjectIds  ID mata pelajaran yang dipilih (untuk siswa)
     * @param  int|null   $teacherId   ID guru yang mengambil absen
     */
    public function processCheckIn(string $scanCode, float $lat, float $lon, array $subjectIds = [], ?int $teacherId = null): array
    {
        $this->geofencingService->validate($lat, $lon);
        [$person, $type] = $this->identify($scanCode);
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $today = Carbon::today($tz)->toDateString();
        $now   = Carbon::now($tz)->toTimeString();
        $record = $this->recordIn($person, $type, $today, $now, $lat, $lon, $subjectIds, $teacherId);
        $this->dispatchNotification($person, $type, $today, $now, 'masuk');
        return compact('person', 'type', 'record');
    }

    public function processCheckOut(string $scanCode, float $lat, float $lon): array
    {
        $this->geofencingService->validate($lat, $lon);
        [$person, $type] = $this->identify($scanCode);
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $today = Carbon::today($tz)->toDateString();
        $now   = Carbon::now($tz)->toTimeString();
        $record = $this->recordOut($person, $type, $today, $now);
        $this->dispatchNotification($person, $type, $today, $now, 'pulang');
        return compact('person', 'type', 'record');
    }

    private function identify(string $code): array
    {
        $student = Student::where('unique_code', $code)->orWhere('rfid_code', $code)->first();
        if ($student) return [$student, 'student'];

        $teacher = Teacher::where('unique_code', $code)->orWhere('rfid_code', $code)->first();
        if ($teacher) return [$teacher, 'teacher'];

        $staff = Staff::where('unique_code', $code)->orWhere('rfid_code', $code)->first();
        if ($staff) return [$staff, 'staff'];

        throw new InvalidScanCodeException("Kode '{$code}' tidak terdaftar di sistem.");
    }

    private function recordIn(mixed $person, string $type, string $date, string $time, float $lat, float $lon, array $subjectIds = [], ?int $teacherId = null): mixed
    {
        // Ambil ID status 'Hadir' secara dinamis dari database
        $hadirId = AttendanceStatus::where('name', 'Hadir')->value('id') ?? 1;

        if ($type === 'student') {
            if (StudentAttendance::where('student_id', $person->id)->where('date', $date)->exists()) {
                throw new AlreadyCheckedInException("{$person->name} sudah absen masuk hari ini.");
            }
            $record = StudentAttendance::create([
                'student_id'   => $person->id,
                'classroom_id' => $person->classroom_id,
                'teacher_id'   => $teacherId,
                'date' => $date, 'time_in' => $time, 'status_id' => $hadirId,
                'latitude' => $lat, 'longitude' => $lon,
            ]);
            if (!empty($subjectIds)) {
                $record->subjects()->sync($subjectIds);
            }
            return $record;
        } elseif ($type === 'staff') {
            if (StaffAttendance::where('staff_id', $person->id)->where('date', $date)->exists()) {
                throw new AlreadyCheckedInException("{$person->name} sudah absen masuk hari ini.");
            }
            return StaffAttendance::create([
                'staff_id' => $person->id, 'date' => $date, 'check_in' => $time,
                'latitude' => $lat, 'longitude' => $lon,
            ]);
        }

        if (TeacherAttendance::where('teacher_id', $person->id)->where('date', $date)->exists()) {
            throw new AlreadyCheckedInException("{$person->name} sudah absen masuk hari ini.");
        }
        return TeacherAttendance::create([
            'teacher_id' => $person->id, 'date' => $date, 'time_in' => $time,
            'status_id' => $hadirId, 'latitude' => $lat, 'longitude' => $lon,
        ]);
    }

    private function recordOut(mixed $person, string $type, string $date, string $time): mixed
    {
        if ($type === 'staff') {
            $record = StaffAttendance::where('staff_id', $person->id)->where('date', $date)->firstOrFail();
            $record->update(['check_out' => $time]);
            return $record->fresh();
        }

        $model = $type === 'student'
            ? StudentAttendance::where('student_id', $person->id)
            : TeacherAttendance::where('teacher_id', $person->id);
        $record = $model->where('date', $date)->firstOrFail();
        $record->update(['time_out' => $time]);
        return $record->fresh();
    }

    private function dispatchNotification(mixed $person, string $type, string $date, string $time, string $action): void
    {
        $phone = $person->phone ?? '';
        if (empty($phone)) return;

        $idLabel = $type === 'student' ? "NIS: {$person->nis}" : ($type === 'staff' ? "NIP: {$person->nip}" : "NUPTK: {$person->nuptk}");
        $actionLabel = $action === 'masuk' ? 'MASUK ✅' : 'PULANG 🏠';

        $message = "📋 *Notifikasi Absensi*\n\n*{$person->name}*\n{$idLabel}\n\n"
                 . "Telah absen *{$actionLabel}*\nTanggal: {$date}\nPukul: {$time}\n\n"
                 . "_Pesan otomatis — Smart Absensi SDN 30 Salayo_";

        SendWhatsAppNotificationJob::dispatch($phone, $message)->onQueue('whatsapp');
    }
}
