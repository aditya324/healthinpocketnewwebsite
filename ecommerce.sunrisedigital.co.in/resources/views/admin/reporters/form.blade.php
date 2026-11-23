@extends('admin.layout')

@section('title', $reporter->exists ? 'Edit reporter' : 'New reporter')

@section('content')
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Reports</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $reporter->exists ? 'Edit reporter' : 'New reporter' }}</h1>
    <p class="mt-2 text-sm text-[#6f6a64]">They sign in at the reports login, separate from this admin.</p>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">This reporter was not saved.</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $reporter->exists ? route('admin.reporters.update', $reporter) : route('admin.reporters.store') }}" class="mt-6 max-w-xl rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm">
        @csrf
        @if ($reporter->exists)
            @method('PUT')
        @endif

        <div class="grid gap-4">
            <div>
                <label for="name" class="text-sm font-medium text-[#1a1a1a]">Name</label>
                <input id="name" name="name" value="{{ old('name', $reporter->name) }}" required class="mt-1.5 w-full rounded-xl border border-[#e4e0da] bg-white px-4 py-3 text-sm outline-none focus:border-[#f5b400]">
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-[#1a1a1a]">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $reporter->email) }}" required autocomplete="off" class="mt-1.5 w-full rounded-xl border border-[#e4e0da] bg-white px-4 py-3 text-sm outline-none focus:border-[#f5b400]">
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-[#1a1a1a]">Password</label>
                <input id="password" name="password" type="password" @unless ($reporter->exists) required @endunless autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-[#e4e0da] bg-white px-4 py-3 text-sm outline-none focus:border-[#f5b400]">
                @if ($reporter->exists)
                    <p class="mt-1 text-xs text-[#8a8680]">Leave this blank to keep the current password.</p>
                @endif
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-medium text-[#1a1a1a]">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" @unless ($reporter->exists) required @endunless autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-[#e4e0da] bg-white px-4 py-3 text-sm outline-none focus:border-[#f5b400]">
            </div>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit" class="inline-flex h-11 items-center rounded-full bg-[#111111] px-5 text-sm font-semibold text-white">Save</button>
            <a href="{{ route('admin.reporters.index') }}" class="text-sm font-medium text-[#6f6a64] underline">Cancel</a>
        </div>
    </form>
@endsection
