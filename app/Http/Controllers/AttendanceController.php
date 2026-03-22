<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.index');
    }

    public function create()
    {
        return view('attendance.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('attendance.index');
    }

    public function show($id)
    {
        return view('attendance.show');
    }

    public function edit($id)
    {
        return view('attendance.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('attendance.index');
    }

    public function destroy($id)
    {
        return redirect()->route('attendance.index');
    }
}