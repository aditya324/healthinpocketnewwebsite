<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReporterRequest;
use App\Models\Reporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReporterController extends Controller
{
    public function index(): View
    {
        return view('admin.reporters.index', [
            'reporters' => Reporter::query()->withCount('weeklyReports')->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.reporters.form', [
            'reporter' => new Reporter,
        ]);
    }

    public function store(ReporterRequest $request): RedirectResponse
    {
        Reporter::query()->create($request->safe()->only(['name', 'email', 'password']));

        return redirect()
            ->route('admin.reporters.index')
            ->with('status', 'Reporter saved. They can log in on the reports dashboard.');
    }

    public function edit(Reporter $reporter): View
    {
        return view('admin.reporters.form', [
            'reporter' => $reporter,
        ]);
    }

    public function update(ReporterRequest $request, Reporter $reporter): RedirectResponse
    {
        $attributes = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $attributes['password'] = $request->validated('password');
        }

        $reporter->update($attributes);

        return redirect()
            ->route('admin.reporters.index')
            ->with('status', 'Reporter saved.');
    }

    public function destroy(Reporter $reporter): RedirectResponse
    {
        $reporter->delete();

        return redirect()
            ->route('admin.reporters.index')
            ->with('status', 'Reporter removed. Reports they already filed stay with the client.');
    }
}
