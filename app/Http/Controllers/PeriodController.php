<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function setPeriod(Request $request)
    {
        $request->validate([
            'period_type' => 'required|in:current_fy,previous_fy,custom_year,custom_dates',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'label' => 'required|string'
        ]);

        $request->session()->put('working_period_type', $request->period_type);
        $request->session()->put('working_period_start', $request->start_date);
        $request->session()->put('working_period_end', $request->end_date);
        $request->session()->put('working_period_label', $request->label);

        return response()->json(['status' => 'success']);
    }
}
