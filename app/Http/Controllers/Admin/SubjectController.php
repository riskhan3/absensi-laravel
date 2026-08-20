<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('class_group')->orderBy('name')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'class_group' => ['required', 'in:all,1-2,3-6'],
        ]);
        Subject::create($request->only('name', 'code', 'class_group'));
        return back()->with('success', 'Mata pelajaran ditambahkan.');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'class_group' => ['required', 'in:all,1-2,3-6'],
            'is_active'   => ['boolean'],
        ]);
        $subject->update($request->only('name', 'class_group', 'is_active'));
        return back()->with('success', 'Mata pelajaran diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata pelajaran dihapus.');
    }
}
