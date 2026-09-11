<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReadingReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $readingPlans = $user->readingPlans()
            ->with('book')
            ->orderBy('deadline')
            ->get();

        $statusCounts = [
            'not_started' => $readingPlans
                ->where('status', ReadingPlan::STATUS_NOT_STARTED)
                ->count(),
            'reading' => $readingPlans
                ->where('status', ReadingPlan::STATUS_READING)
                ->count(),
            'completed' => $readingPlans
                ->where('status', ReadingPlan::STATUS_COMPLETED)
                ->count(),
        ];

        $reviewCount = $user->reviews()->count();
        $averageRating = $user->reviews()->avg('rating');

        return view('reading-reports.show', compact(
            'readingPlans',
            'statusCounts',
            'reviewCount',
            'averageRating'
        ));
    }
}