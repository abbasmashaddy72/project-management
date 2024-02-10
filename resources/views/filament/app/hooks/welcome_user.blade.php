<div class="flex flex-col w-full text-xs text-gray-600 dark:text-gray-300">
    <span class="font-semibold">Welcome, {{ auth()->user()->name }}</span>
    <span>{{ now()->toFormattedDateString() }}</span>
</div>
