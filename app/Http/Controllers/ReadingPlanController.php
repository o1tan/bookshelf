<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $allowedStatuses = [
            ReadingPlan::STATUS_NOT_STARTED,
            ReadingPlan::STATUS_READING,
            ReadingPlan::STATUS_COMPLETED,
            ReadingPlan::STATUS_EXPIRED,
        ];

        $readingPlans = $request->user()
            ->readingPlans()
            ->with('book')
            ->when(
                in_array($status, $allowedStatuses, true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderBy('deadline')
            ->get();

        return view(
            'reading-plans.index',
            compact('readingPlans', 'status')
        );
    }

    public function create(): View
    {
        $books = Book::query()
            ->orderBy('title')
            ->get();

        return view('reading-plans.create', compact('books'));
    }

    public function store(
        StoreReadingPlanRequest $request
    ): RedirectResponse {
        $request->user()
            ->readingPlans()
            ->create($request->validated());

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を登録しました。');
    }

    public function edit(
        Request $request,
        ReadingPlan $readingPlan
    ): View {
        abort_unless(
            $request->user()->id === $readingPlan->user_id,
            403
        );

        $readingPlan->load('book');

        return view(
            'reading-plans.edit',
            compact('readingPlan')
        );
    }

    public function update(
        UpdateReadingPlanRequest $request,
        ReadingPlan $readingPlan
    ): RedirectResponse {
        $readingPlan->update($request->validated());

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    public function destroy(
        Request $request,
        ReadingPlan $readingPlan
    ): RedirectResponse {
        abort_unless(
            $request->user()->id === $readingPlan->user_id,
            403
        );

        $readingPlan->delete();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
}
