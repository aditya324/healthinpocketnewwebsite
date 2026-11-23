<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $pending = Order::openOnboardingFor($request->user());

        if ($pending !== null) {
            return redirect()->route('orders.onboarding', $pending);
        }

        $orders = $request->user()
            ->orders()
            ->whereIn('status', ['placed', 'in_progress', 'delivered'])
            ->with('items')
            ->latest()
            ->get();

        $items = $orders->flatMap(fn (Order $order) => $order->items);

        return view('dashboard.home', [
            'orders' => $orders,
            'summary' => [
                'services' => $items->count(),
                'orders' => $orders->count(),
                'onboarding' => $items->filter(fn (OrderItem $item): bool => $item->onboardingComplete())->count(),
                'spent' => (float) $orders->sum(fn (Order $order): float => (float) $order->total),
                'reports' => $request->user()->weeklyReports()->published()->count(),
            ],
        ]);
    }
}
