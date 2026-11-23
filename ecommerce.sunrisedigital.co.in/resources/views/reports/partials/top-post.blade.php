<div data-top-post class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_8rem_auto] sm:items-center">
    <input name="instagram[top][{{ $index }}][title]" value="{{ data_get($post, 'title') }}" placeholder="Post title" aria-label="Top post title" class="w-full rounded-xl border border-[#e4e0da] bg-white px-3 py-2 text-sm outline-none focus:border-[#f5b400]">
    <input name="instagram[top][{{ $index }}][views]" value="{{ data_get($post, 'views') }}" placeholder="Views" aria-label="Top post views" class="w-full rounded-xl border border-[#e4e0da] bg-white px-3 py-2 text-sm outline-none focus:border-[#f5b400]">
    <button type="button" data-remove-top class="text-sm font-medium text-[#9b2c2c]">Remove</button>
</div>
