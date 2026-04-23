<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrEmployee;
use App\Models\HrLeave;
use App\Models\HrPayroll;

class HrDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $month = now()->month;
        $year = now()->year;

        $summary = [
            'active_employees' => HrEmployee::where('status', 1)->count(),
            'present_today' => HrAttendance::whereDate('attendance_date', $today)->where('status', 'present')->count(),
            'late_today' => HrAttendance::whereDate('attendance_date', $today)->where('status', 'late')->count(),
            'pending_leave' => HrLeave::where('status', 'pending')->count(),
            'paid_payroll' => HrPayroll::where('status', 'paid')->where('month', $month)->where('year', $year)->sum('net_salary'),
        ];

        $recentLeaves = HrLeave::with('employee')->latest('start_date')->limit(8)->get();
        $recentPayrolls = HrPayroll::with('employee')->latest('year')->latest('month')->limit(8)->get();
        $todayAttendance = HrAttendance::with('employee')->whereDate('attendance_date', $today)->latest()->get();

        return view('backEnd.hr.dashboard', compact('summary', 'recentLeaves', 'recentPayrolls', 'todayAttendance'));
    }
}
