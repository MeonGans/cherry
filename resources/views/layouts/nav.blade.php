@php
    $activeGroup = match (true) {
        request()->routeIs('admin.dashboard') => 'overview',
        request()->routeIs('list', 'random_list', 'sessions.*', 'users.*') => 'camp',
        request()->routeIs('test.*', 'sorting2.*', 'wednesday-quest-routes.*', 'zootopia-quest-routes.*') => 'games',
        request()->routeIs('votes.*') => 'votes',
        request()->routeIs('products.*', 'fortune', 'fortune.two') => 'fortune',
        request()->routeIs('music-clip-cards.*', 'music.clip') => 'clip',
        default => 'camp',
    };

    $menuGroups = [
        [
            'key' => 'camp',
            'label' => 'Заїзди',
            'items' => [
                ['label' => 'Список заїзду', 'route' => 'list', 'active' => 'list'],
                ['label' => 'Рандомний список', 'route' => 'random_list', 'active' => 'random_list'],
                ['label' => 'Сесії', 'route' => 'sessions.index', 'active' => 'sessions.*'],
            ],
        ],
        [
            'key' => 'games',
            'label' => 'Ігри та квести',
            'items' => [
                ['label' => 'Сортування', 'route' => 'test.show', 'active' => 'test.*'],
                ['label' => 'Сортування 2.0', 'route' => 'sorting2.show', 'active' => 'sorting2.*'],
                ['label' => 'Маршрути Wednesday', 'route' => 'wednesday-quest-routes.index', 'active' => 'wednesday-quest-routes.*'],
                ['label' => 'Відкрити Wednesday-квест', 'route' => 'wednesday.quest.index', 'active' => 'wednesday.quest.*', 'target' => true],
                ['label' => 'Маршрути Zootopia', 'route' => 'zootopia-quest-routes.index', 'active' => 'zootopia-quest-routes.*'],
                ['label' => 'Відкрити Zootopia-квест', 'route' => 'zootopia.quest.index', 'active' => 'zootopia.quest.*', 'target' => true],
            ],
        ],
        [
            'key' => 'votes',
            'label' => 'Голосування',
            'items' => [
                ['label' => 'Створити голосування', 'route' => 'votes.create', 'active' => 'votes.create'],
                ['label' => 'Список голосувань', 'route' => 'votes.index', 'active' => 'votes.index'],
            ],
        ],
        [
            'key' => 'fortune',
            'label' => 'Колесо фортуни',
            'items' => [
                ['label' => 'Товари', 'route' => 'products.index', 'active' => 'products.*'],
                ['label' => 'Відкрити колесо', 'route' => 'fortune', 'active' => 'fortune', 'target' => true],
                ['label' => 'Відкрити колесо 2.0', 'route' => 'fortune.two', 'active' => 'fortune.two', 'target' => true],
            ],
        ],
        [
            'key' => 'clip',
            'label' => 'Кліп',
            'items' => [
                ['label' => 'Картки кліпу', 'route' => 'music-clip-cards.index', 'active' => 'music-clip-cards.*'],
                ['label' => 'Відкрити музичний кліп', 'route' => 'music.clip', 'active' => 'music.clip', 'target' => true],
            ],
        ],
    ];
@endphp

<!-- start sidebar section -->
<div :class="{'dark text-white-dark' : $store.app.semidark}">
    <nav
        x-data="sidebar"
        class="sidebar fixed bottom-0 top-0 z-50 h-full min-h-screen w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300"
    >
        <div class="flex h-full flex-col bg-white dark:bg-[#0e1726]">
            <div class="flex items-center justify-between px-4 py-3">
                <a href="{{ route('admin.dashboard') }}" class="main-logo flex shrink-0 items-center">
                    <img class="ml-[5px] w-8 flex-none" src="{{ asset('assets/images/logo.png') }}" alt="CHERRY CAMP"/>
                    <span class="align-middle text-2xl font-semibold ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light lg:inline">CHERRY CAMP</span>
                </a>
                <a
                    href="javascript:;"
                    class="collapse-icon flex h-8 w-8 items-center rounded-full transition duration-300 hover:bg-gray-500/10 rtl:rotate-180 dark:text-white-light dark:hover:bg-dark-light/10"
                    @click="$store.app.toggleSidebar()"
                    aria-label="Згорнути меню"
                >
                    <svg class="m-auto h-5 w-5" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <ul
                class="perfect-scrollbar relative flex-1 space-y-0.5 overflow-y-auto overflow-x-hidden p-4 py-0 font-semibold"
                x-data="{ activeDropdown: '{{ $activeGroup }}' }"
            >
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link group {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="flex items-center">
                            <svg class="shrink-0 group-hover:!text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M4 13h6V4H4v9ZM14 20h6V4h-6v16ZM4 20h6v-3H4v3Z" fill="currentColor"/>
                                <path d="M4 15h6v-2H4v2ZM4 4h6v9H4V4ZM14 4h6v16h-6V4Z" fill="currentColor"/>
                            </svg>
                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Огляд</span>
                        </div>
                    </a>
                </li>

                @foreach($menuGroups as $group)
                    <li class="menu nav-item">
                        <button
                            type="button"
                            class="nav-link group"
                            :class="{'active' : activeDropdown === '{{ $group['key'] }}'}"
                            @click="activeDropdown === '{{ $group['key'] }}' ? activeDropdown = null : activeDropdown = '{{ $group['key'] }}'"
                        >
                            <div class="flex items-center">
                                <svg class="shrink-0 group-hover:!text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.5" d="M3 7.2C3 6.08 3 5.52 3.22 5.09C3.41 4.72 3.72 4.41 4.09 4.22C4.52 4 5.08 4 6.2 4H17.8C18.92 4 19.48 4 19.91 4.22C20.28 4.41 20.59 4.72 20.78 5.09C21 5.52 21 6.08 21 7.2V8H3V7.2Z" fill="currentColor"/>
                                    <path d="M3 10H21V16.8C21 17.92 21 18.48 20.78 18.91C20.59 19.28 20.28 19.59 19.91 19.78C19.48 20 18.92 20 17.8 20H6.2C5.08 20 4.52 20 4.09 19.78C3.72 19.59 3.41 19.28 3.22 18.91C3 18.48 3 17.92 3 16.8V10Z" fill="currentColor"/>
                                </svg>
                                <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">{{ $group['label'] }}</span>
                            </div>
                            <div class="rtl:rotate-180" :class="{'!rotate-90' : activeDropdown === '{{ $group['key'] }}'}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </button>

                        <ul x-cloak x-show="activeDropdown === '{{ $group['key'] }}'" x-collapse class="sub-menu text-gray-500">
                            @foreach($group['items'] as $item)
                                <li>
                                    <a
                                        href="{{ route($item['route']) }}"
                                        class="{{ request()->routeIs($item['active']) ? 'active' : '' }}"
                                        @if($item['target'] ?? false) target="_blank" rel="noopener" @endif
                                    >
                                        {{ $item['label'] }}
                                        @if($item['target'] ?? false)
                                            <span class="ml-1 text-xs">↗</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
<!-- end sidebar section -->
