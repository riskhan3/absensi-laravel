<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    // ────────────────────── INDEX (Staf + Guru) ──────────────────────────────
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'staff'); // 'staff' | 'guru'

        $staffQuery = Staff::latest();
        if ($request->filled('search') && $tab === 'staff') {
            $q = $request->search;
            $staffQuery->where(fn($b) => $b->where('name', 'like', "%$q%")->orWhere('nip', 'like', "%$q%"));
        }
        $staffList = $staffQuery->paginate(20)->withQueryString();

        $teacherQuery = Teacher::with('user')->latest();
        if ($request->filled('search') && $tab === 'guru') {
            $q = $request->search;
            $teacherQuery->where(fn($b) => $b->where('name', 'like', "%$q%")->orWhere('nuptk', 'like', "%$q%"));
        }
        $teacherList = $teacherQuery->paginate(20)->withQueryString();

        return view('admin.staff.index', compact('staffList', 'teacherList', 'tab'));
    }

    // ────────────────────── STAF CRUD ────────────────────────────────────────
    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'       => ['nullable', 'string', 'max:30', 'unique:staff,nip'],
            'name'      => ['required', 'string', 'max:255'],
            'position'  => ['nullable', 'string', 'max:100'],
            'gender'    => ['required', 'in:Laki-laki,Perempuan'],
            'phone'     => ['nullable', 'string', 'max:32'],
            'rfid_code' => ['nullable', 'string', 'max:100', 'unique:staff,rfid_code'],
        ]);

        Staff::create(array_merge($validated, [
            'unique_code' => 'STF-' . strtoupper(Str::random(8)),
        ]));

        return redirect()->route('admin.staff.index', ['tab' => 'staff'])
            ->with('success', 'Staf berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.create', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'nip'       => ['nullable', 'string', 'max:30', 'unique:staff,nip,' . $staff->id],
            'name'      => ['required', 'string', 'max:255'],
            'position'  => ['nullable', 'string', 'max:100'],
            'gender'    => ['required', 'in:Laki-laki,Perempuan'],
            'phone'     => ['nullable', 'string', 'max:32'],
            'rfid_code' => ['nullable', 'string', 'max:100', 'unique:staff,rfid_code,' . $staff->id],
        ]);

        $staff->update($validated);
        return redirect()->route('admin.staff.index', ['tab' => 'staff'])
            ->with('success', 'Data staf diperbarui.');
    }

    public function show(Staff $staff)
    {
        return redirect()->route('admin.staff.index', ['tab' => 'staff']);
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index', ['tab' => 'staff'])
            ->with('success', 'Staf dihapus.');
    }

    /** QR Code data untuk staf */
    public function generateQr(Staff $staff)
    {
        return response()->json(['code' => $staff->unique_code, 'name' => $staff->name]);
    }

    // ────────────────────── GURU CRUD ────────────────────────────────────────
    public function createTeacher()
    {
        $subjects = Subject::where('is_active', true)->orderBy('class_group')->orderBy('name')->get();
        return view('admin.staff.create-teacher', compact('subjects'));
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'nuptk'       => ['nullable', 'string', 'max:20', 'unique:teachers,nuptk'],
            'name'        => ['required', 'string', 'max:255'],
            'gender'      => ['required', 'in:Laki-laki,Perempuan'],
            'phone'       => ['nullable', 'string', 'max:32'],
            'address'     => ['nullable', 'string', 'max:500'],
            'rfid_code'   => ['nullable', 'string', 'max:100', 'unique:teachers,rfid_code'],
            // Akun login guru (opsional)
            'email'       => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role'        => ['nullable', 'in:wali_kelas,guru_mapel'],
            'password'    => ['nullable', 'string', 'min:6', 'confirmed'],
            // Mapel yang diajar
            'subject_ids'   => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $teacher = Teacher::create(array_merge(
            array_intersect_key($validated, array_flip(['nuptk','name','gender','phone','address','rfid_code'])),
            ['unique_code' => 'TCH-' . strtoupper(Str::random(8))]
        ));

        // Sync mapel yang diajar (tanpa classroom — nullable)
        if (!empty($validated['subject_ids'])) {
            $syncData = [];
            foreach ($validated['subject_ids'] as $sid) {
                $syncData[$sid] = ['classroom_id' => null];
            }
            $teacher->subjects()->sync($syncData);
        }

        // Buat akun login jika email diisi
        if (!empty($validated['email']) && !empty($validated['password'])) {
            User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'role'       => $validated['role'] ?? 'guru_mapel',
                'teacher_id' => $teacher->id,
            ]);
        }

        return redirect()->route('admin.staff.index', ['tab' => 'guru'])
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function editTeacher(Teacher $teacher)
    {
        $teacher->load('user', 'subjects');
        $subjects = Subject::where('is_active', true)->orderBy('class_group')->orderBy('name')->get();
        return view('admin.staff.create-teacher', compact('teacher', 'subjects'));
    }

    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'nuptk'     => ['nullable', 'string', 'max:20', 'unique:teachers,nuptk,' . $teacher->id],
            'name'      => ['required', 'string', 'max:255'],
            'gender'    => ['required', 'in:Laki-laki,Perempuan'],
            'phone'     => ['nullable', 'string', 'max:32'],
            'address'   => ['nullable', 'string', 'max:500'],
            'rfid_code' => ['nullable', 'string', 'max:100', 'unique:teachers,rfid_code,' . $teacher->id],
            // Akun login guru
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->user?->id)],
            'role'          => ['nullable', 'in:wali_kelas,guru_mapel'],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
            // Mapel yang diajar
            'subject_ids'   => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $teacher->update(array_intersect_key($validated, array_flip(['nuptk','name','gender','phone','address','rfid_code'])));

        // Sync mapel yang diajar (pertahankan classroom_id yang sudah ada, hanya ubah set mapel)
        $existingBySubject = $teacher->subjects()->get()->keyBy('id');
        $newIds = $validated['subject_ids'] ?? [];
        $syncData = [];
        foreach ($newIds as $sid) {
            $existing = $existingBySubject->get($sid);
            $syncData[$sid] = ['classroom_id' => $existing ? $existing->pivot->classroom_id : null];
        }
        $teacher->subjects()->sync($syncData);

        // Update atau buat akun login
        if (!empty($validated['email'])) {
            $userPayload = [
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'role'       => $validated['role'] ?? 'guru_mapel',
                'teacher_id' => $teacher->id,
            ];
            if (!empty($validated['password'])) {
                $userPayload['password'] = Hash::make($validated['password']);
            }

            if ($teacher->user) {
                $teacher->user->update($userPayload);
            } else {
                // Buat akun baru, wajib ada password
                if (!empty($validated['password'])) {
                    User::create($userPayload);
                }
            }
        }

        return redirect()->route('admin.staff.index', ['tab' => 'guru'])
            ->with('success', 'Data guru diperbarui.');
    }

    public function destroyTeacher(Teacher $teacher)
    {
        // Nonaktifkan akun user terhubung jika ada (ubah email agar tidak bisa login)
        if ($teacher->user) {
            $teacher->user->update(['email' => 'deleted_' . $teacher->user->id . '_' . $teacher->user->email]);
        }
        $teacher->delete();
        return redirect()->route('admin.staff.index', ['tab' => 'guru'])
            ->with('success', 'Guru dihapus.');
    }

    /** QR Code data untuk guru */
    public function generateQrTeacher(Teacher $teacher)
    {
        return response()->json(['code' => $teacher->unique_code, 'name' => $teacher->name]);
    }
}
