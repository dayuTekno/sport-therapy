<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = \App\Models\MasterDoctorSchedule::with(['doctor', 'polyclinic'])->orderBy('day_of_week')->paginate(10);
        return view('admin.pages.doctor-schedule.index', compact('schedules'));
    }

    public function create()
    {
        $doctors = \App\Models\MasterDoctor::all();
        $polyclinics = \App\Models\MasterPolyclinic::all();
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        return view('admin.pages.doctor-schedule.create', compact('doctors', 'polyclinics', 'days'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:master_doctors,id',
            'poly_id' => 'required|exists:master_polyclinics,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'quota' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        \App\Models\MasterDoctorSchedule::create($request->all());

        return redirect()->route('doctor-schedules.index')->with('success', 'Jadwal dokter berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $schedule = \App\Models\MasterDoctorSchedule::findOrFail($id);
        $doctors = \App\Models\MasterDoctor::all();
        $polyclinics = \App\Models\MasterPolyclinic::all();
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        return view('admin.pages.doctor-schedule.edit', compact('schedule', 'doctors', 'polyclinics', 'days'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'doctor_id' => 'required|exists:master_doctors,id',
            'poly_id' => 'required|exists:master_polyclinics,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'quota' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $schedule = \App\Models\MasterDoctorSchedule::findOrFail($id);
        
        $data = $request->all();
        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }
        
        $schedule->update($data);

        return redirect()->route('doctor-schedules.index')->with('success', 'Jadwal dokter berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $schedule = \App\Models\MasterDoctorSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('doctor-schedules.index')->with('success', 'Jadwal dokter berhasil dihapus.');
    }
}
