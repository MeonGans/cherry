<!-- start sidebar section -->
<div :class="{'dark text-white-dark' : $store.app.semidark}">
    <nav
        x-data="sidebar"
        class="sidebar fixed bottom-0 top-0 z-50 h-full min-h-screen w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300"
    >
        <div class="h-full bg-white dark:bg-[#0e1726]">
            <div class="flex items-center justify-between px-4 py-3">
                <a href="index.html" class="main-logo flex shrink-0 items-center">
                    <img class="ml-[5px] w-8 flex-none" src="{{ asset('assets/images/logo.png') }}" alt="image" />
                    <span class="align-middle text-2xl font-semibold ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light lg:inline">CHERRY CAMP</span>
                </a>
                <a
                    href="javascript:;"
                    class="collapse-icon flex h-8 w-8 items-center rounded-full transition duration-300 hover:bg-gray-500/10 rtl:rotate-180 dark:text-white-light dark:hover:bg-dark-light/10"
                    @click="$store.app.toggleSidebar()"
                >
                    <svg class="m-auto h-5 w-5" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            opacity="0.5"
                            d="M16.9998 19L10.9998 12L16.9998 5"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>
            </div>
            <ul
                class="perfect-scrollbar relative h-[calc(100vh-80px)] space-y-0.5 overflow-y-auto overflow-x-hidden p-4 py-0 font-semibold"
                x-data="{ activeDropdown: 'dashboard' }"
            >
                <li class="menu nav-item">
                    <button
                        type="button"
                        class="nav-link group"
                        :class="{'active' : activeDropdown === 'dashboard'}"
                        @click="activeDropdown === 'dashboard' ? activeDropdown = null : activeDropdown = 'dashboard'"
                    >
                        <div class="flex items-center">
                            <svg
                                class="shrink-0 group-hover:!text-primary"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    opacity="0.5"
                                    d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                    fill="currentColor"
                                />
                                <path
                                    d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                    fill="currentColor"
                                />
                            </svg>

                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Основне</span>
                        </div>
                        <div class="rtl:rotate-180" :class="{'!rotate-90' : activeDropdown === 'dashboard'}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </button>
                    <ul x-cloak x-show="activeDropdown === 'dashboard'" x-collapse class="sub-menu text-gray-500">
                        <li>
                            <a href="{{ route('list') }}" class="{{ Request::routeIs('list') ? 'active' : '' }}">Список заїзду</a>
                        </li>
                        <li>
                            <a href="{{ route('random_list') }}" class="{{ Request::routeIs('random_list') ? 'active' : '' }}">Рандомний список</a>
                        </li>
                        <li>
                            <a href="{{ route('test.show') }}" class="{{ Request::routeIs('test.show') ? 'active' : '' }}">Сортування</a>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item">
                    <button
                        type="button"
                        class="nav-link group"
                        :class="{'active' : activeDropdown === 'vote'}"
                        @click="activeDropdown === 'vote' ? activeDropdown = null : activeDropdown = 'vote'"
                    >
                        <div class="flex items-center">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="shrink-0 group-hover:!text-primary"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M8.73167 5.77133L5.66953 9.91436C4.3848 11.6526 3.74244 12.5217 4.09639 13.205C4.10225 13.2164 4.10829 13.2276 4.1145 13.2387C4.48945 13.9117 5.59888 13.9117 7.81775 13.9117C9.05079 13.9117 9.6673 13.9117 10.054 14.2754L10.074 14.2946L13.946 9.72466L13.926 9.70541C13.5474 9.33386 13.5474 8.74151 13.5474 7.55682V7.24712C13.5474 3.96249 13.5474 2.32018 12.6241 2.03721C11.7007 1.75425 10.711 3.09327 8.73167 5.77133Z"
                                    fill="currentColor"
                                ></path>
                                <path
                                    opacity="0.5"
                                    d="M10.4527 16.4432L10.4527 16.7528C10.4527 20.0374 10.4527 21.6798 11.376 21.9627C12.2994 22.2457 13.2891 20.9067 15.2685 18.2286L18.3306 14.0856C19.6154 12.3474 20.2577 11.4783 19.9038 10.7949C19.8979 10.7836 19.8919 10.7724 19.8857 10.7613C19.5107 10.0883 18.4013 10.0883 16.1824 10.0883C14.9494 10.0883 14.3329 10.0883 13.9462 9.72461L10.0742 14.2946C10.4528 14.6661 10.4527 15.2585 10.4527 16.4432Z"
                                    fill="currentColor"
                                ></path>
                            </svg>

                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Голосування</span>
                        </div>
                        <div class="rtl:rotate-180" :class="{'!rotate-90' : activeDropdown === 'vote'}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </button>
                    <ul x-cloak x-show="activeDropdown === 'vote'" x-collapse class="sub-menu text-gray-500">
                        <li>
                            <a href="{{ route('votes.create') }}" class="{{ Request::routeIs('votes.create') ? 'active' : '' }}">Створити голосування</a>
                        </li>
                        <li>
                            <a href="{{ route('votes.index') }}" class="{{ Request::routeIs('votes.index') ? 'active' : '' }}">Список голосувань</a>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item">
                    <button
                        type="button"
                        class="nav-link group"
                        :class="{'active' : activeDropdown === 'products'}"
                        @click="activeDropdown === 'products' ? activeDropdown = null : activeDropdown = 'products'"
                    >
                        <div class="flex items-center">
                            <svg class="shrink-0 group-hover:!text-primary" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 5.25C12.4142 5.25 12.75 5.58579 12.75 6V6.31673C14.3804 6.60867 15.75 7.83361 15.75 9.5C15.75 9.91421 15.4142 10.25 15 10.25C14.5858 10.25 14.25 9.91421 14.25 9.5C14.25 8.82154 13.6859 8.10339 12.75 7.84748V11.3167C14.3804 11.6087 15.75 12.8336 15.75 14.5C15.75 16.1664 14.3804 17.3913 12.75 17.6833V18C12.75 18.4142 12.4142 18.75 12 18.75C11.5858 18.75 11.25 18.4142 11.25 18V17.6833C9.61957 17.3913 8.25 16.1664 8.25 14.5C8.25 14.0858 8.58579 13.75 9 13.75C9.41421 13.75 9.75 14.0858 9.75 14.5C9.75 15.1785 10.3141 15.8966 11.25 16.1525V12.6833C9.61957 12.3913 8.25 11.1664 8.25 9.5C8.25 7.83361 9.61957 6.60867 11.25 6.31673V6C11.25 5.58579 11.5858 5.25 12 5.25ZM11.25 7.84748C10.3141 8.10339 9.75 8.82154 9.75 9.5C9.75 10.1785 10.3141 10.8966 11.25 11.1525V7.84748ZM14.25 14.5C14.25 13.8215 13.6859 13.1034 12.75 12.8475V16.1525C13.6859 15.8966 14.25 15.1785 14.25 14.5Z" fill="currentColor"></path>
                            </svg>

                            <span class="text-black ltr:pl-3 rtl:pr-3 dark:text-[#506690] dark:group-hover:text-white-dark">Колесо фортуни</span>
                        </div>
                        <div class="rtl:rotate-180" :class="{'!rotate-90' : activeDropdown === 'products'}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </button>
                    <ul x-cloak x-show="activeDropdown === 'products'" x-collapse class="sub-menu text-gray-500">
                        <li>
                            <a href="{{ route('products.index') }}" class="{{ Request::routeIs('products') ? 'active' : '' }}">Товари</a>
                        </li>
                        <li>
                            <a href="{{ route('fortune') }}" class="{{ Request::routeIs('fortune') ? 'active' : '' }}" target="_blank">Колесо</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
<!-- end sidebar section -->
