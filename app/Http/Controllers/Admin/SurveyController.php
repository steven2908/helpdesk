<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;

class SurveyController extends Controller
{
    public function show(Survey $survey)
    {
        return view('admin.surveys.show', compact('survey'));
    }

    public function csIndex()
{
    $surveys = Survey::whereNotNull('cs_q1')
        ->with(['user', 'ticket'])
        ->latest()
        ->get();

    return view('admin.surveys.cs-index', compact('surveys'));
}

}
