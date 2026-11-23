<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reporter;
use App\Models\User;
use App\Support\AdminFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => AdminFilter::text($request, 'q'),
        ];

        $customers = User::query()
            ->withCount([
                'orders as placed_orders_count' => fn ($query) => $query->whereIn('status', Order::settledStatuses()),
                'subscriptions',
            ]);

        if ($filters['q'] !== '') {
            $term = AdminFilter::like($filters['q']);
            $customers->where(function ($query) use ($term): void {
                $query->where('name', 'like', $term)->orWhere('email', 'like', $term);
            });
        }

        return view('admin.customers.index', [
            'customers' => $customers->orderBy('name')->get(),
            'filters' => $filters,
            'filtering' => AdminFilter::active($filters),
        ]);
    }

    public function show(User $customer): View
    {
        return view('admin.customers.show', [
            'customer' => $customer->load('assignedReporter'),
            'orders' => $customer->orders()->latest()->get(),
            'subscriptions' => $customer->subscriptions()->with('order')->latest()->get(),
            'reporters' => Reporter::query()->orderBy('name')->get(),
        ]);
    }

    public function assignReporter(Request $request, User $customer): RedirectResponse
    {
        $data = $request->validate([
            'assigned_reporter_id' => ['nullable', 'integer', 'exists:reporters,id'],
        ]);

        $customer->assigned_reporter_id = $data['assigned_reporter_id'] ?? null;
        $customer->save();

        return back()->with('status', 'The report assignee was saved.');
    }
}
