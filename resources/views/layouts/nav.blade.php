@php
    $activeGroup = match (true) {
        request()->routeIs('admin.dashboard') => 'overview',
        request()->routeIs('list', 'random_list', 'sessions.*', 'users.*', 'cherries.*') => 'camp',
        request()->routeIs('test.*', 'sorting2.*', 'wednesday-quest-routes.*', 'wednesday.quest.*', 'zootopia-quest-routes.*', 'zootopia.quest.*') => 'games',
        request()->routeIs('votes.*') => 'votes',
        request()->routeIs('products.*', 'fortune', 'fortune.two') => 'fortune',
        request()->routeIs('music-clip-cards.*', 'music.clip') => 'clip',
        default => 'camp',
    };

    $menuGroups = [
        [
            'key' => 'camp',
            'label' => 'Заїзди',
            'icon' => 'camp',
            'items' => [
                ['label' => 'Список заїзду', 'route' => 'list', 'active' => 'list', 'icon' => 'list'],
                ['label' => 'Рандомний список', 'route' => 'random_list', 'active' => 'random_list', 'icon' => 'shuffle'],
                ['label' => 'Сесії', 'route' => 'sessions.index', 'active' => 'sessions.*', 'icon' => 'calendar'],
                ['label' => 'Облік Черіків', 'route' => 'cherries.index', 'active' => 'cherries.*', 'icon' => 'spark'],
            ],
        ],
        [
            'key' => 'games',
            'label' => 'Ігри та квести',
            'icon' => 'games',
            'items' => [
                ['label' => 'Сортування', 'route' => 'test.show', 'active' => 'test.*', 'icon' => 'sort'],
                ['label' => 'Сортування 2.0', 'route' => 'sorting2.show', 'active' => 'sorting2.*', 'icon' => 'spark'],
                ['label' => 'Маршрути Wednesday', 'route' => 'wednesday-quest-routes.index', 'active' => 'wednesday-quest-routes.*', 'icon' => 'route'],
                ['label' => 'Відкрити Wednesday-квест', 'route' => 'wednesday.quest.index', 'active' => 'wednesday.quest.*', 'target' => true, 'icon' => 'play'],
                ['label' => 'Маршрути Zootopia', 'route' => 'zootopia-quest-routes.index', 'active' => 'zootopia-quest-routes.*', 'icon' => 'route'],
                ['label' => 'Відкрити Zootopia-квест', 'route' => 'zootopia.quest.index', 'active' => 'zootopia.quest.*', 'target' => true, 'icon' => 'play'],
            ],
        ],
        [
            'key' => 'votes',
            'label' => 'Голосування',
            'icon' => 'votes',
            'items' => [
                ['label' => 'Створити голосування', 'route' => 'votes.create', 'active' => 'votes.create', 'icon' => 'plus'],
                ['label' => 'Список голосувань', 'route' => 'votes.index', 'active' => 'votes.index', 'icon' => 'ballot'],
            ],
        ],
        [
            'key' => 'fortune',
            'label' => 'Колесо фортуни',
            'icon' => 'fortune',
            'items' => [
                ['label' => 'Товари', 'route' => 'products.index', 'active' => 'products.*', 'icon' => 'bag'],
                ['label' => 'Відкрити колесо', 'route' => 'fortune', 'active' => 'fortune', 'target' => true, 'icon' => 'wheel'],
                ['label' => 'Відкрити колесо 2.0', 'route' => 'fortune.two', 'active' => 'fortune.two', 'target' => true, 'icon' => 'wheel'],
            ],
        ],
        [
            'key' => 'clip',
            'label' => 'Кліп',
            'icon' => 'clip',
            'items' => [
                ['label' => 'Картки кліпу', 'route' => 'music-clip-cards.index', 'active' => 'music-clip-cards.*', 'icon' => 'cards'],
                ['label' => 'Відкрити музичний кліп', 'route' => 'music.clip', 'active' => 'music.clip', 'target' => true, 'icon' => 'music'],
            ],
        ],
    ];

    $navIcons = [
        'dashboard' => '<path opacity="0.5" d="M4 13h6V4H4v9ZM14 20h6V4h-6v16ZM4 20h6v-3H4v3Z" fill="currentColor"/><path d="M4 15h6v-2H4v2ZM4 4h6v9H4V4ZM14 4h6v16h-6V4Z" fill="currentColor"/>',
        'camp' => '<path opacity="0.5" d="M12 3.5c-2.2 2.3-4.1 5.6-5.5 9.7L4.9 18c-.3.9.4 1.8 1.3 1.8h11.6c.9 0 1.6-.9 1.3-1.8l-1.6-4.8c-1.4-4.1-3.3-7.4-5.5-9.7Z" fill="currentColor"/><path d="M12 3.5v16.3M8.2 19.8l3.8-7.1 3.8 7.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'games' => '<path opacity="0.5" d="M6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11A2.5 2.5 0 0 1 6.5 4Z" fill="currentColor"/><path d="M8.5 9.5h3m-1.5-1.5v3M15.5 9.2v.1M16.8 12v.1M14.2 12v.1M15.5 14.8v.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
        'votes' => '<path opacity="0.5" d="M6.5 10h11l1.5 3v5a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5l1.5-3Z" fill="currentColor"/><path d="M8 10l4-6 4 6M9 14h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'fortune' => '<path opacity="0.5" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" fill="currentColor"/><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>',
        'clip' => '<path opacity="0.5" d="M5 5.8A2.8 2.8 0 0 1 7.8 3h8.4A2.8 2.8 0 0 1 19 5.8v12.4a2.8 2.8 0 0 1-2.8 2.8H7.8A2.8 2.8 0 0 1 5 18.2V5.8Z" fill="currentColor"/><path d="M8 3v18M16 3v18M5 8h14M5 16h14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        'list' => '<path opacity="0.5" d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" fill="currentColor"/><path d="M8.5 8h7M8.5 12h7M8.5 16h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'shuffle' => '<path d="M4 7h2.2c2.8 0 4 10 6.8 10H20M17 4l3 3-3 3M17 14l3 3-3 3M4 17h2.3c1.2 0 2.1-1.7 3-3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'calendar' => '<path opacity="0.5" d="M5 8h14v9.2A2.8 2.8 0 0 1 16.2 20H7.8A2.8 2.8 0 0 1 5 17.2V8Z" fill="currentColor"/><path d="M8 4v3M16 4v3M5 8h14M9 12h.01M12 12h.01M15 12h.01M9 16h.01M12 16h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'sort' => '<path d="M7 4v13M7 17l-3-3M7 17l3-3M17 20V7M17 7l-3 3M17 7l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path opacity="0.5" d="M10 5h4M10 19h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'spark' => '<path opacity="0.5" d="M12 4l1.5 4.4L18 10l-4.5 1.6L12 16l-1.5-4.4L6 10l4.5-1.6L12 4Z" fill="currentColor"/><path d="M18.5 14l.7 2.1 2.1.7-2.1.7-.7 2.1-.7-2.1-2.1-.7 2.1-.7.7-2.1ZM5.5 14l.5 1.5 1.5.5-1.5.5-.5 1.5-.5-1.5-1.5-.5 1.5-.5.5-1.5Z" fill="currentColor"/>',
        'route' => '<path opacity="0.5" d="M6.5 8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM17.5 20.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" fill="currentColor"/><path d="M6.5 8.5v2.2A2.3 2.3 0 0 0 8.8 13h6.4a2.3 2.3 0 0 1 2.3 2.3v.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'play' => '<path opacity="0.5" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" fill="currentColor"/><path d="M10 8.8v6.4c0 .7.8 1.1 1.4.7l4.6-3.2a.9.9 0 0 0 0-1.4l-4.6-3.2c-.6-.4-1.4 0-1.4.7Z" fill="currentColor"/>',
        'plus' => '<path opacity="0.5" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" fill="currentColor"/><path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
        'ballot' => '<path opacity="0.5" d="M6 4h12a2 2 0 0 1 2 2v13H4V6a2 2 0 0 1 2-2Z" fill="currentColor"/><path d="M8 9h8M8 13h5M5 19h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'bag' => '<path opacity="0.5" d="M6.5 8h11l1 10.2A2.5 2.5 0 0 1 16 21H8a2.5 2.5 0 0 1-2.5-2.8L6.5 8Z" fill="currentColor"/><path d="M9 8V6a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'wheel' => '<path opacity="0.5" d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" fill="currentColor"/><path d="M12 4v16M4 12h16M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        'cards' => '<path opacity="0.5" d="M7 5h8a2 2 0 0 1 2 2v10H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" fill="currentColor"/><path d="M9 9h4M9 12h6M9 15h3M17 8h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-1" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        'music' => '<path opacity="0.5" d="M14 4v10.7A2.7 2.7 0 1 1 12.5 12V7l7-2v7.7A2.7 2.7 0 1 1 18 10V5.8L14 7v-3Z" fill="currentColor"/><path d="M14 7l5-1.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        'default' => '<path opacity="0.5" d="M5 5h14v14H5V5Z" fill="currentColor"/><path d="M8 9h8M8 12h8M8 15h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
    ];
@endphp

<style>
    .nav-menu-icon {
        height: 20px;
        width: 20px;
        flex: 0 0 20px;
        transition: color 160ms ease, transform 160ms ease;
    }

    .nav-link:hover .nav-menu-icon,
    .nav-link.active .nav-menu-icon {
        transform: translateY(-1px);
    }

    .menu-sub-link {
        display: flex !important;
        align-items: center;
        gap: 10px;
    }

    .menu-sub-icon {
        height: 16px;
        width: 16px;
        flex: 0 0 16px;
        color: #888ea8;
        transition: color 160ms ease, transform 160ms ease;
    }

    .menu-sub-link:hover .menu-sub-icon,
    .menu-sub-link.active .menu-sub-icon {
        color: #4361ee;
        transform: translateX(2px);
    }

    .menu-sub-label {
        min-width: 0;
        flex: 1 1 auto;
    }
</style>

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
                            <svg class="nav-menu-icon group-hover:!text-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                {!! $navIcons['dashboard'] !!}
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
                                <svg class="nav-menu-icon group-hover:!text-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    {!! $navIcons[$group['icon']] ?? $navIcons['default'] !!}
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
                                        class="menu-sub-link {{ request()->routeIs($item['active']) ? 'active' : '' }}"
                                        @if($item['target'] ?? false) target="_blank" rel="noopener" @endif
                                    >
                                        <svg class="menu-sub-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                            {!! $navIcons[$item['icon']] ?? $navIcons['default'] !!}
                                        </svg>
                                        <span class="menu-sub-label">{{ $item['label'] }}</span>
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
