<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classroom.major'])->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($b) => $b->where('name', 'like', "%$q%")->orWhere('nis', 'like', "%$q%"));
        }

        if ($request->filled('class')) {
            $query->where('classroom_id', $request->class);
        }

        $students   = $query->paginate(20)->withQueryString();
        $classrooms = Classroom::with('major')->orderBy('grade')->orderBy('label')->get();

        return view('admin.students.index', compact('students', 'classrooms'));
    }

    public function create()
    {
        $classrooms = Classroom::with('major')->orderBy('grade')->orderBy('label')->get();
        return view('admin.students.create', compact('classrooms'));
    }

    public function store(StoreStudentRequest $request)
    {
        Student::create(array_merge($request->validated(), [
            'unique_code' => 'STD-' . strtoupper(Str::random(8)),
        ]));

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['classroom.major', 'attendances.status']);
        $recentAttendances = $student->attendances()->with('status')->latest('date')->limit(30)->get();
        return view('admin.students.show', compact('student', 'recentAttendances'));
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::with('major')->orderBy('grade')->orderBy('label')->get();
        return view('admin.students.edit', compact('student', 'classrooms'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    /** Generate QR Code data URL untuk siswa */
    public function generateQr(Student $student)
    {
        return response()->json(['code' => $student->unique_code, 'name' => $student->name]);
    }
}
