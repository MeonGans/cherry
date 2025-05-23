<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>CHERRY CAMP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png')  }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"
          rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/perfect-scrollbar.min.css')  }}"/>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css')  }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/highlight.min.css')  }}" />
    <link defer rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/animate.css')  }}"/>
    <script src="{{ asset('assets/js/perfect-scrollbar.min.js')  }}"></script>
    <script defer src="{{ asset('assets/js/popper.min.js')  }}"></script>
    <script defer src="{{ asset('assets/js/tippy-bundle.umd.min.js')  }}"></script>
    <script defer src="{{ asset('assets/js/sweetalert.min.js')  }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select2.css')  }}" />
</head>



<body
    x-data="main"
    class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased vertical boxed-layout ltr"
    :class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ?  'dark' : '', $store.app.menu, $store.app.layout,$store.app.rtlClass]"
>

{{--<body--}}
{{--    x-data="main"--}}
{{--    class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased"--}}
{{--    :class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ?  'dark' : '', $store.app.menu, $store.app.layout,$store.app.rtlClass]"--}}
{{-->--}}
<!-- sidebar menu overlay -->
<div x-cloak class="fixed inset-0 z-50 bg-[black]/60 lg:hidden" :class="{'hidden' : !$store.app.sidebar}"
     @click="$store.app.toggleSidebar()"></div>

<!-- screen loader -->
<div
    class="screen_loader animate__animated fixed inset-0 z-[60] grid place-content-center bg-[#fafafa] dark:bg-[#060818]">
    <svg width="64" height="64" viewBox="0 0 135 135" xmlns="http://www.w3.org/2000/svg" fill="#4361ee">
        <path
            d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z"
        >
            <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s"
                              repeatCount="indefinite"/>
        </path>
        <path
            d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z"
        >
            <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s"
                              repeatCount="indefinite"/>
        </path>
    </svg>
</div>

<!-- scroll to top button -->
<div class="fixed bottom-6 z-50 ltr:right-6 rtl:left-6" x-data="scrollToTop">
    <template x-if="showTopButton">
        <button
            type="button"
            class="btn btn-outline-primary animate-pulse rounded-full bg-[#fafafa] p-2 dark:bg-[#060818] dark:hover:bg-primary"
            @click="goToTop"
        >
            <svg width="24" height="24" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path
                    opacity="0.5"
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 20.75C12.4142 20.75 12.75 20.4142 12.75 20L12.75 10.75L11.25 10.75L11.25 20C11.25 20.4142 11.5858 20.75 12 20.75Z"
                    fill="currentColor"
                />
                <path
                    d="M6.00002 10.75C5.69667 10.75 5.4232 10.5673 5.30711 10.287C5.19103 10.0068 5.25519 9.68417 5.46969 9.46967L11.4697 3.46967C11.6103 3.32902 11.8011 3.25 12 3.25C12.1989 3.25 12.3897 3.32902 12.5304 3.46967L18.5304 9.46967C18.7449 9.68417 18.809 10.0068 18.6929 10.287C18.5768 10.5673 18.3034 10.75 18 10.75L6.00002 10.75Z"
                    fill="currentColor"
                />
            </svg>
        </button>
    </template>
</div>

<div class="main-container min-h-screen text-black dark:text-white-dark" :class="[$store.app.navbar]">

    @include('layouts.nav')

    <div class="main-content flex min-h-screen flex-col">
        <!-- start header section -->
        <header class="z-40" :class="{'dark' : $store.app.semidark && $store.app.menu === 'horizontal'}">
            <div class="shadow-sm">
                <div class="relative flex w-full items-center bg-white px-5 py-2.5 dark:bg-[#0e1726]">
                    <div class="horizontal-logo flex items-center justify-between ltr:mr-2 rtl:ml-2 lg:hidden">
                        <a href="index.html" class="main-logo flex shrink-0 items-center">
                            <img class="inline w-8 ltr:-ml-1 rtl:-mr-1" src="{{ asset('assets/images/logo.png') }}" alt="image"/>
                            <span
                                class="hidden align-middle text-2xl font-semibold transition-all duration-300 ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light md:inline"
                            >CHERRY CAMP</span
                            >
                        </a>

                        <a
                            href="javascript:;"
                            class="collapse-icon flex flex-none rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary ltr:ml-2 rtl:mr-2 dark:bg-dark/40 dark:text-[#d0d2d6] dark:hover:bg-dark/60 dark:hover:text-primary lg:hidden"
                            @click="$store.app.toggleSidebar()"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 7L4 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path opacity="0.5" d="M20 12L4 12" stroke="currentColor" stroke-width="1.5"
                                      stroke-linecap="round"/>
                                <path d="M20 17L4 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </a>
                    </div>
                    <div
                        x-data="header"
                        class="flex items-center space-x-1.5 ltr:ml-auto rtl:mr-auto rtl:space-x-reverse dark:text-[#d0d2d6] sm:flex-1 ltr:sm:ml-0 sm:rtl:mr-0 lg:space-x-2"
                    >
                        <div class="sm:ltr:mr-auto sm:rtl:ml-auto" x-data="{ search: false }"
                             @click.outside="search = false">

                        </div>
                        <div>
                            <a
                                href="javascript:;"
                                x-cloak
                                x-show="$store.app.theme === 'light'"
                                href="javascript:;"
                                class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                                @click="$store.app.toggleTheme('dark')"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M12 2V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M12 20V22" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round"/>
                                    <path d="M4 12L2 12" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round"/>
                                    <path d="M22 12L20 12" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round"/>
                                    <path
                                        opacity="0.5"
                                        d="M19.7778 4.22266L17.5558 6.25424"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        opacity="0.5"
                                        d="M4.22217 4.22266L6.44418 6.25424"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        opacity="0.5"
                                        d="M6.44434 17.5557L4.22211 19.7779"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        opacity="0.5"
                                        d="M19.7778 19.7773L17.5558 17.5551"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </a>
                            <a
                                href="javascript:;"
                                x-cloak
                                x-show="$store.app.theme === 'dark'"
                                href="javascript:;"
                                class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                                @click="$store.app.toggleTheme('system')"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21.0672 11.8568L20.4253 11.469L21.0672 11.8568ZM12.1432 2.93276L11.7553 2.29085V2.29085L12.1432 2.93276ZM21.25 12C21.25 17.1086 17.1086 21.25 12 21.25V22.75C17.9371 22.75 22.75 17.9371 22.75 12H21.25ZM12 21.25C6.89137 21.25 2.75 17.1086 2.75 12H1.25C1.25 17.9371 6.06294 22.75 12 22.75V21.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75V1.25C6.06294 1.25 1.25 6.06294 1.25 12H2.75ZM15.5 14.25C12.3244 14.25 9.75 11.6756 9.75 8.5H8.25C8.25 12.5041 11.4959 15.75 15.5 15.75V14.25ZM20.4253 11.469C19.4172 13.1373 17.5882 14.25 15.5 14.25V15.75C18.1349 15.75 20.4407 14.3439 21.7092 12.2447L20.4253 11.469ZM9.75 8.5C9.75 6.41182 10.8627 4.5828 12.531 3.57467L11.7553 2.29085C9.65609 3.5593 8.25 5.86509 8.25 8.5H9.75ZM12 2.75C11.9115 2.75 11.8077 2.71008 11.7324 2.63168C11.6686 2.56527 11.6538 2.50244 11.6503 2.47703C11.6461 2.44587 11.6482 2.35557 11.7553 2.29085L12.531 3.57467C13.0342 3.27065 13.196 2.71398 13.1368 2.27627C13.0754 1.82126 12.7166 1.25 12 1.25V2.75ZM21.7092 12.2447C21.6444 12.3518 21.5541 12.3539 21.523 12.3497C21.4976 12.3462 21.4347 12.3314 21.3683 12.2676C21.2899 12.1923 21.25 12.0885 21.25 12H22.75C22.75 11.2834 22.1787 10.9246 21.7237 10.8632C21.286 10.804 20.7293 10.9658 20.4253 11.469L21.7092 12.2447Z"
                                        fill="currentColor"
                                    />
                                </svg>
                            </a>
                            <a
                                href="javascript:;"
                                x-cloak
                                x-show="$store.app.theme === 'system'"
                                href="javascript:;"
                                class="flex items-center rounded-full bg-white-light/40 p-2 hover:bg-white-light/90 hover:text-primary dark:bg-dark/40 dark:hover:bg-dark/60"
                                @click="$store.app.toggleTheme('light')"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3 9C3 6.17157 3 4.75736 3.87868 3.87868C4.75736 3 6.17157 3 9 3H15C17.8284 3 19.2426 3 20.1213 3.87868C21 4.75736 21 6.17157 21 9V14C21 15.8856 21 16.8284 20.4142 17.4142C19.8284 18 18.8856 18 17 18H7C5.11438 18 4.17157 18 3.58579 17.4142C3 16.8284 3 15.8856 3 14V9Z"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    />
                                    <path opacity="0.5" d="M22 21H2" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round"/>
                                    <path opacity="0.5" d="M15 15H9" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- end header section -->

        <div class="dvanimation animate__animated p-6" :class="[$store.app.animation]">
            <!-- start main content section -->
            <div x-data="basic">

                @yield('content')

            </div>
            <!-- end main content section -->
        </div>

    </div>


</div>
<script src="{{ asset('assets/js/highlight.min.js') }}"></script>
<script src="{{ asset('assets/js/alpine-collaspe.min.js') }}"></script>
<script src="{{ asset('assets/js/alpine-persist.min.js') }}"></script>
<script defer src="{{ asset('assets/js/alpine-ui.min.js') }}"></script>
<script defer src="{{ asset('assets/js/alpine-focus.min.js') }}"></script>
<script defer src="{{ asset('assets/js/alpine.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script defer src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/js/nice-select2.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function(e) {
        // default
        var els = document.querySelectorAll(".selectize");
        els.forEach(function(select) {
            NiceSelect.bind(select);
        });
    });
</script>
<script>
    document.addEventListener('alpine:init', () => {
        // main section
        Alpine.data('scrollToTop', () => ({
            showTopButton: false,
            init() {
                window.onscroll = () => {
                    this.scrollFunction();
                };
            },

            scrollFunction() {
                if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                    this.showTopButton = true;
                } else {
                    this.showTopButton = false;
                }
            },

            goToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            },
        }));

        // theme customization
        Alpine.data('customizer', () => ({
            showCustomizer: false,
        }));

        // sidebar section
        Alpine.data('sidebar', () => ({
            init() {
                const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
                if (selector) {
                    selector.classList.add('active');
                    const ul = selector.closest('ul.sub-menu');
                    if (ul) {
                        let ele = ul.closest('li.menu').querySelectorAll('.nav-link');
                        if (ele) {
                            ele = ele[0];
                            setTimeout(() => {
                                ele.click();
                            });
                        }
                    }
                }
            },
        }));

        // header section
        Alpine.data('header', () => ({
            init() {
                const selector = document.querySelector('ul.horizontal-menu a[href="' + window.location.pathname + '"]');
                if (selector) {
                    selector.classList.add('active');
                    const ul = selector.closest('ul.sub-menu');
                    if (ul) {
                        let ele = ul.closest('li.menu').querySelectorAll('.nav-link');
                        if (ele) {
                            ele = ele[0];
                            setTimeout(() => {
                                ele.classList.add('active');
                            });
                        }
                    }
                }
            },






        }));

        // content section
        Alpine.data('sales', () => ({
            init() {
                isDark = this.$store.app.theme === 'dark' || this.$store.app.isDarkMode ? true : false;
                isRtl = this.$store.app.rtlClass === 'rtl' ? true : false;

                const revenueChart = null;
                const salesByCategory = null;
                const dailySales = null;
                const totalOrders = null;

                // revenue
                setTimeout(() => {
                    this.revenueChart = new ApexCharts(this.$refs.revenueChart, this.revenueChartOptions);
                    this.$refs.revenueChart.innerHTML = '';
                    this.revenueChart.render();

                    // sales by category
                    this.salesByCategory = new ApexCharts(this.$refs.salesByCategory, this.salesByCategoryOptions);
                    this.$refs.salesByCategory.innerHTML = '';
                    this.salesByCategory.render();

                    // daily sales
                    this.dailySales = new ApexCharts(this.$refs.dailySales, this.dailySalesOptions);
                    this.$refs.dailySales.innerHTML = '';
                    this.dailySales.render();

                    // total orders
                    this.totalOrders = new ApexCharts(this.$refs.totalOrders, this.totalOrdersOptions);
                    this.$refs.totalOrders.innerHTML = '';
                    this.totalOrders.render();
                }, 300);

                this.$watch('$store.app.theme', () => {
                    isDark = this.$store.app.theme === 'dark' || this.$store.app.isDarkMode ? true : false;

                    this.revenueChart.updateOptions(this.revenueChartOptions);
                    this.salesByCategory.updateOptions(this.salesByCategoryOptions);
                    this.dailySales.updateOptions(this.dailySalesOptions);
                    this.totalOrders.updateOptions(this.totalOrdersOptions);
                });

                this.$watch('$store.app.rtlClass', () => {
                    isRtl = this.$store.app.rtlClass === 'rtl' ? true : false;
                    this.revenueChart.updateOptions(this.revenueChartOptions);
                });
            },

            // revenue
            get revenueChartOptions() {
                return {
                    series: [
                        {
                            name: 'Income',
                            data: [16800, 16800, 15500, 17800, 15500, 17000, 19000, 16000, 15000, 17000, 14000, 17000],
                        },
                        {
                            name: 'Expenses',
                            data: [16500, 17500, 16200, 17300, 16000, 19500, 16000, 17000, 16000, 19000, 18000, 19000],
                        },
                    ],
                    chart: {
                        height: 325,
                        type: 'area',
                        fontFamily: 'Nunito, sans-serif',
                        zoom: {
                            enabled: false,
                        },
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        width: 2,
                        lineCap: 'square',
                    },
                    dropShadow: {
                        enabled: true,
                        opacity: 0.2,
                        blur: 10,
                        left: -7,
                        top: 22,
                    },
                    colors: isDark ? ['#2196f3', '#e7515a'] : ['#1b55e2', '#e7515a'],
                    markers: {
                        discrete: [
                            {
                                seriesIndex: 0,
                                dataPointIndex: 6,
                                fillColor: '#1b55e2',
                                strokeColor: 'transparent',
                                size: 7,
                            },
                            {
                                seriesIndex: 1,
                                dataPointIndex: 5,
                                fillColor: '#e7515a',
                                strokeColor: 'transparent',
                                size: 7,
                            },
                        ],
                    },
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    xaxis: {
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        crosshairs: {
                            show: true,
                        },
                        labels: {
                            offsetX: isRtl ? 2 : 0,
                            offsetY: 5,
                            style: {
                                fontSize: '12px',
                                cssClass: 'apexcharts-xaxis-title',
                            },
                        },
                    },
                    yaxis: {
                        tickAmount: 7,
                        labels: {
                            formatter: (value) => {
                                return value / 1000 + 'K';
                            },
                            offsetX: isRtl ? -30 : -10,
                            offsetY: 0,
                            style: {
                                fontSize: '12px',
                                cssClass: 'apexcharts-yaxis-title',
                            },
                        },
                        opposite: isRtl ? true : false,
                    },
                    grid: {
                        borderColor: isDark ? '#191e3a' : '#e0e6ed',
                        strokeDashArray: 5,
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        padding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '16px',
                        markers: {
                            width: 10,
                            height: 10,
                            offsetX: -2,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 5,
                        },
                    },
                    tooltip: {
                        marker: {
                            show: true,
                        },
                        x: {
                            show: false,
                        },
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            inverseColors: !1,
                            opacityFrom: isDark ? 0.19 : 0.28,
                            opacityTo: 0.05,
                            stops: isDark ? [100, 100] : [45, 100],
                        },
                    },
                };
            },

            // sales by category
            get salesByCategoryOptions() {
                return {
                    series: [285, 237, 270, 100, 100],
                    chart: {
                        type: 'donut',
                        height: 460,
                        fontFamily: 'Nunito, sans-serif',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 25,
                        colors: isDark ? ['#0e1726', '#0e1726', '#0e1726', '#0e1726'] : ['#fff', '#fff', '#fff'],
                    },
                    colors: isDark ? ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'] : ['#e2a03f', '#5c1ac3', '#e7515a'],
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '14px',
                        markers: {
                            width: 10,
                            height: 10,
                            offsetX: -2,
                        },
                        height: 50,
                        offsetY: 20,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                background: 'transparent',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '29px',
                                        offsetY: -10,
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '26px',
                                        color: isDark ? '#bfc9d4' : undefined,
                                        offsetY: 16,
                                        formatter: (val) => {
                                            return val;
                                        },
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        color: '#888ea8',
                                        fontSize: '29px',
                                        formatter: (w) => {
                                            return w.globals.seriesTotals.reduce(function (a, b) {
                                                return a + b;
                                            }, 0);
                                        },
                                    },
                                },
                            },
                        },
                    },
                    labels: ['Apparel', 'Sports', 'Others', 'test', 'tets1'],
                    states: {
                        hover: {
                            filter: {
                                type: 'none',
                                value: 0.15,
                            },
                        },
                        active: {
                            filter: {
                                type: 'none',
                                value: 0.15,
                            },
                        },
                    },
                };
            },

            // daily sales
            get dailySalesOptions() {
                return {
                    series: [
                        {
                            name: 'Sales',
                            data: [44, 55, 41, 67, 22, 43, 21],
                        },
                        {
                            name: 'Last Week',
                            data: [13, 23, 20, 8, 13, 27, 33],
                        },
                    ],
                    chart: {
                        height: 160,
                        type: 'bar',
                        fontFamily: 'Nunito, sans-serif',
                        toolbar: {
                            show: false,
                        },
                        stacked: true,
                        stackType: '100%',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 1,
                    },
                    colors: ['#e2a03f', '#e0e6ed'],
                    responsive: [
                        {
                            breakpoint: 480,
                            options: {
                                legend: {
                                    position: 'bottom',
                                    offsetX: -10,
                                    offsetY: 0,
                                },
                            },
                        },
                    ],
                    xaxis: {
                        labels: {
                            show: false,
                        },
                        categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        opacity: 1,
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '25%',
                        },
                    },
                    legend: {
                        show: false,
                    },
                    grid: {
                        show: false,
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        padding: {
                            top: 10,
                            right: -20,
                            bottom: -20,
                            left: -20,
                        },
                    },
                };
            },

            // total orders
            get totalOrdersOptions() {
                return {
                    series: [
                        {
                            name: 'Sales',
                            data: [28, 40, 36, 52, 38, 60, 38, 52, 36, 40],
                        },
                    ],
                    chart: {
                        height: 290,
                        type: 'area',
                        fontFamily: 'Nunito, sans-serif',
                        sparkline: {
                            enabled: true,
                        },
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2,
                    },
                    colors: isDark ? ['#00ab55'] : ['#00ab55'],
                    labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
                    yaxis: {
                        min: 0,
                        show: false,
                    },
                    grid: {
                        padding: {
                            top: 125,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                    },
                    fill: {
                        opacity: 1,
                        type: 'gradient',
                        gradient: {
                            type: 'vertical',
                            shadeIntensity: 1,
                            inverseColors: !1,
                            opacityFrom: 0.3,
                            opacityTo: 0.05,
                            stops: [100, 100],
                        },
                    },
                    tooltip: {
                        x: {
                            show: false,
                        },
                    },
                };
            },
        }));
    });
</script>
</body>
</html>
