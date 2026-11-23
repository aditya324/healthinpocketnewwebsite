@extends('admin.layout')

@section('title', $customer->name)

@section('content')
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Customer</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $customer->name }}</h1>
    <p class="mt-2 text-sm text-[#6f6a64]">{{ $customer->email }}</p>
    <a href="{{ route('admin.customers.index') }}" class="mt-4 inline-flex text-sm font-medium text-[#1a1a1a] underline">All customers</a>

    <form method="POST" action="{{ route('admin.customers.reporter', $customer) }}" class="mt-6 max-w-xl rounded-2xl border border-[#ebe6df] bg-white p-5">
        @csrf
        @method('PUT')
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Report assignee</h2>
        <p class="mt-1 text-sm text-[#6f6a64]">This person files the weekly, social, and SEO reports. Leave it open and every reporter can see the client.</p>
        <label for="assigned_reporter_id" class="mt-4 block text-sm font-medium text-[#1a1a1a]">Reporter</label>
        <select id="assigned_reporter_id" name="assigned_reporter_id" class="mt-1.5 h-11 w-full rounded-xl border border-[#e4e0da] bg-white px-3 text-sm">
            <option value="">Not assigned</option>
            @foreach ($reporters as $reporter)
                <option value="{{ $reporter->id }}" @selected((string) old('assigned_reporter_id', $customer->assigned_reporter_id) === (string) $reporter->id)>{{ $reporter->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="mt-4 inline-flex h-11 items-center rounded-full bg-[#f5b400] px-5 text-sm font-semibold text-[#1a1a1a]">Save assignee</button>
    </form>

    <h2 class="mt-8 text-lg font-semibold text-[#1a1a1a]">Orders</h2>
    <div class="mt-3 overflow-hidden rounded-2xl border border-[#ebe6df] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#ebe6df] text-xs uppercase tracking-wide text-[#8a8680]">
                <tr>
                    <th class="px-4 py-3 font-medium">Order</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-[#f3eee6] last:border-0">
                        <td class="px-4 py-3 font-medium text-[#1a1a1a]">{{ $order->number }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-3">{{ $order->money() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-[#1a1a1a] underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-[#6f6a64]">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="mt-8 text-lg font-semibold text-[#1a1a1a]">Subscriptions</h2>
    <div class="mt-3 flex flex-col gap-3">
        @forelse ($subscriptions as $subscription)
            <article class="rounded-2xl border border-[#ebe6df] bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-[#1a1a1a]">{{ $subscription->name }}</p>
                        <p class="mt-1 text-sm text-[#6f6a64]">{{ $subscription->statusLabel() }} · renews {{ $subscription->period }} · {{ $subscription->paid_count }} of {{ $subscription->total_count }} paid</p>
                    </div>
                    <p class="font-semibold text-[#1a1a1a]">₹{{ number_format((float) $subscription->cycle_amount, 0, '.', ',') }}</p>
                </div>
                @if ($subscription->order)
                    <a href="{{ route('admin.orders.show', $subscription->order) }}" class="mt-3 inline-flex text-sm font-medium text-[#1a1a1a] underline">Order {{ $subscription->order->number }}</a>
                @endif
            </article>
        @empty
            <p class="rounded-2xl border border-[#ebe6df] bg-white px-5 py-8 text-sm text-[#6f6a64]">No subscriptions yet.</p>
        @endforelse
    </div>
@endsection
