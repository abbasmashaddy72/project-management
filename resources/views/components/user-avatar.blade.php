@if ($user)
    <div class="relative inline-block group">
        @php($uniqid = uniqid())
        @if ($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                data-popover-target="popover-user-{{ $user->id }}-{{ $uniqid }}"
                class="object-cover w-12 h-12 transition-transform transform rounded-full cursor-pointer group-hover:scale-110" />
        @else
            <div data-popover-target="popover-user-{{ $user->id }}-{{ $uniqid }}"
                class="flex items-center justify-center w-12 h-12 text-white bg-blue-500 rounded-full cursor-pointer group-hover:scale-110">
                <span class="text-lg font-semibold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            </div>
        @endif

        <div data-popover id="popover-user-{{ $user->id }}-{{ $uniqid }}" role="tooltip"
            class="absolute z-10 w-64 text-sm font-light text-gray-700 transition-opacity duration-300 transform scale-0 bg-white border border-gray-200 rounded-lg shadow-md opacity-0 group-hover:opacity-100 group-hover:scale-100 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600">
            <div class="p-4">
                <p class="text-xl font-semibold leading-none text-gray-900 dark:text-white">
                    <a>{{ $user->name }}</a>
                </p>
                <p class="mb-2 text-sm font-normal">
                    <a href="mailto:{{ $user->email }}" class="hover:underline">
                        {{ $user->email }}
                    </a>
                </p>
                <p class="mb-2 text-sm font-light">
                    {{ __('Member since') }}
                    <span class="text-blue-600 dark:text-blue-500">
                        {{ $user->created_at->format('Y-m-d') }}
                    </span>
                </p>
                <ul class="flex mb-4 text-sm font-light">
                    <li class="mr-4">
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ collect(($user->ticketsOwned ?? collect())->merge($user->ticketsResponsible ?? collect()))->unique('id')->count() }}
                        </span>
                        <span class="ml-1">{{ __('Tickets') }}</span>
                    </li>
                    <li>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ collect(($user->projectsOwning ?? collect())->merge($user->projectsAffected ?? collect()))->unique('id')->count() }}
                        </span>
                        <span class="ml-1">{{ __('Projects') }}</span>
                    </li>
                </ul>
                <div class="flex justify-end">
                    <a href="#"
                        class="text-blue-600 dark:text-blue-500 hover:underline">{{ __('View Profile') }}</a>
                </div>
            </div>
            <div data-popper-arrow class="bg-white"></div>
        </div>
    </div>
@endif
