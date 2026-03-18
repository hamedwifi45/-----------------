<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة العمل الحر السورية | مستقل - خدمات - مشاريع</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        tajawal: ['Tajawal', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        secondary: {
                            500: '#3b82f6',
                            600: '#2563eb',
                        },
                        accent: {
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'slide-up': 'slideUp 0.6s ease',
                        'fade-in': 'fadeIn 0.8s ease',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            'from': { opacity: '0', transform: 'translateY(30px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            'from': { opacity: '0' },
                            'to': { opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    
    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .bg-gradient-hero {
            background: linear-gradient(135deg, #ecfdf5 0%, #dbeafe 50%, #ede9fe 100%);
        }
        
        .bg-gradient-auth {
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        .user-dropdown {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        
        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
        
        /* Category card hover effect */
        .category-card {
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        
        .category-card:hover .category-title,
        .category-card:hover .category-count {
            color: white;
        }
        
        /* Service card image hover */
        .service-image-container {
            overflow: hidden;
        }
        
        .service-image-container img {
            transition: transform 0.5s ease;
        }
        
        .service-card:hover .service-image-container img {
            transform: scale(1.1);
        }
        
        /* Rating stars */
        .rating-star {
            color: #fbbf24;
        }
        
        /* Mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-menu.active {
            transform: translateX(0);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md shadow-sm z-50" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center text-white text-2xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">شغلني</h1>
                        <p class="text-xs text-gray-500">اول منصة عمل حر في سوريا</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">الرئيسية</a>
                    <a href="#services" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">الخدمات</a>
                    <a href="#freelancers" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">المستقلين</a>
                    <a href="#projects" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">المشاريع</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">كيف يعمل</a>
                </div>
                @auth
                    <!-- Logged In User Actions -->
                    <div class="hidden lg:flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary-50 transition-colors">
                            <i class="fa-solid fa-bell text-gray-600"></i>
                            <span class="notification-badge">5</span>
                        </button>
                        
                        <!-- Messages -->
                        <button class="relative w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary-50 transition-colors">
                            <i class="fa-solid fa-envelope text-gray-600"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button onclick="toggleUserDropdown()" class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-100 transition-colors">
                                <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="المستخدم" class="w-10 h-10 rounded-full border-2 border-primary-500">
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800 text-sm">أحمد محمد</p>
                                    <p class="text-xs text-primary-600 font-medium">$1,250</p>
                                </div>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="userDropdown" class="user-dropdown absolute left-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-br from-primary-50 to-blue-50">
                                    <div class="flex items-center gap-3">
                                        <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="المستخدم" class="w-12 h-12 rounded-full border-2 border-primary-500">
                                        <div>
                                            <h4 class="font-bold text-gray-800">أحمد محمد</h4>
                                            <p class="text-xs text-gray-500">مطور واجهات أمامية</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <a href="dashboard.html" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-gauge text-primary-500"></i>
                                        <span class="font-medium">لوحة التحكم</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-folder-open text-primary-500"></i>
                                        <span class="font-medium">مشاريعي</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-store text-primary-500"></i>
                                        <span class="font-medium">خدماتي</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-wallet text-primary-500"></i>
                                        <span class="font-medium">المحفظة</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-gear text-primary-500"></i>
                                        <span class="font-medium">الإعدادات</span>
                                    </a>
                                </div>
                                <div class="p-2 border-t border-gray-100">
                                    <a href="index-logged-out.html" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span class="font-medium">تسجيل الخروج</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
                @guest
                    <!-- Auth Buttons -->
                    <div class="hidden lg:flex items-center gap-3">
                        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="px-6 py-2.5 text-primary-600 font-semibold border-2 border-primary-500 rounded-full hover:bg-primary-50 transition-all duration-300">
                            دخول
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : '#' }}" class="px-6 py-2.5 bg-gradient-primary text-white font-semibold rounded-full hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 transition-all duration-300">
                            حساب جديد
                        </a>
                    </div>
                @endguest
                <!-- Mobile Menu Button -->
                <button class="lg:hidden text-gray-700 text-2xl" onclick="toggleMobileMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu fixed top-20 left-0 right-0 bg-white shadow-lg lg:hidden p-6 space-y-4" id="mobileMenu">
            <a href="#" class="block text-gray-700 font-medium py-2 hover:text-primary-600">الرئيسية</a>
            <a href="#services" class="block text-gray-700 font-medium py-2 hover:text-primary-600">الخدمات</a>
            <a href="#freelancers" class="block text-gray-700 font-medium py-2 hover:text-primary-600">المستقلين</a>
            <a href="#projects" class="block text-gray-700 font-medium py-2 hover:text-primary-600">المشاريع</a>
            <a href="#how-it-works" class="block text-gray-700 font-medium py-2 hover:text-primary-600">كيف يعمل</a>
            <div class="pt-4 border-t border-gray-200 space-y-3">
                <a href="login.html" class="block text-center py-3 text-primary-600 font-semibold border-2 border-primary-500 rounded-full">دخول</a>
                <a href="register.html" class="block text-center py-3 bg-gradient-primary text-white font-semibold rounded-full">حساب جديد</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-hero pt-32 pb-20 relative overflow-hidden">
        <!-- Background Shapes -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary-200 rounded-full opacity-30 blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-3xl animate-float" style="animation-delay: 2s;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                @guest
                <!-- Hero Content -->
                <div class="text-center lg:text-right animate-slide-up">
                    <div class="inline-block px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-6">
                        <i class="fa-solid fa-star ml-2"></i>
                        أكبر منصة عمل حر في سوريا
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                        اعثر على <span class="text-gradient">المستقلين</span> المحترفين لمشروعك
                    </h1>
                    
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        منصة تجمع بين أصحاب المشاريع والمستقلين المحترفين في سوريا والعالم العربي. ابدأ مشروعك اليوم مع آلاف الخبراء المستعدين للمساعدة.
                    </p>
                    
                    <!-- Search Box -->
                    <div class="bg-white rounded-2xl shadow-xl p-3 mb-8">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input 
                                    type="text" 
                                    placeholder="ما الخدمة التي تبحث عنها؟"
                                    class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors"
                                >
                            </div>
                            <div class="md:w-64 relative">
                                <i class="fa-solid fa-folder absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors appearance-none bg-white">
                                    <option value="">جميع التصنيفات</option>
                                    <option value="programming">برمجة وتطوير</option>
                                    <option value="design">تصميم وإبداع</option>
                                    <option value="marketing">تسويق ومبيعات</option>
                                    <option value="writing">كتابة وترجمة</option>
                                    <option value="video">فيديو ومونتاج</option>
                                </select>
                            </div>
                            <button class="px-8 py-4 bg-gradient-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300">
                                <i class="fa-solid fa-magnifying-glass ml-2"></i>
                                بحث
                            </button>
                        </div>
                    </div>
                    
                    <!-- Popular Searches -->
                    <div class="flex flex-wrap gap-2 justify-center lg:justify-start">
                        <span class="text-gray-500 text-sm">شائع:</span>
                        <a href="#" class="px-3 py-1.5 bg-white rounded-full text-sm text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition-colors">تصميم شعار</a>
                        <a href="#" class="px-3 py-1.5 bg-white rounded-full text-sm text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition-colors">برمجة موقع</a>
                        <a href="#" class="px-3 py-1.5 bg-white rounded-full text-sm text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition-colors">كتابة محتوى</a>
                        <a href="#" class="px-3 py-1.5 bg-white rounded-full text-sm text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition-colors">تسويق إلكتروني</a>
                    </div>
                </div>
                @endguest
                @auth
                    <!-- Hero Content -->
                    <div class="text-center lg:text-right animate-slide-up">
                        <!-- Welcome Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-6">
                            <i class="fa-solid fa-hand-wave"></i>
                            <span>مرحباً بك، أحمد! 👋</span>
                        </div>
                        
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                            جاهز لـ <span class="text-gradient">مشروعك الجديد</span>؟
                        </h1>
                        
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            لديك 5 مشاريع نشطة و 3 طلبات جديدة في انتظارك. استمر في النجاح!
                        </p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="bg-white rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600">
                                        <i class="fa-solid fa-folder-open text-sm"></i>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold text-gray-800">5</p>
                                <p class="text-xs text-gray-500">مشاريع نشطة</p>
                            </div>
                            <div class="bg-white rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                                        <i class="fa-solid fa-cart-shopping text-sm"></i>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold text-gray-800">12</p>
                                <p class="text-xs text-gray-500">طلب جديد</p>
                            </div>
                            <div class="bg-white rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                                        <i class="fa-solid fa-dollar-sign text-sm"></i>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold text-gray-800">$2,450</p>
                                <p class="text-xs text-gray-500">أرباح الشهر</p>
                            </div>
                        </div>
                        
                        <!-- Search Box -->
                        <div class="bg-white rounded-2xl shadow-xl p-3 mb-8">
                            <div class="flex flex-col md:flex-row gap-3">
                                <div class="flex-1 relative">
                                    <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input 
                                        type="text" 
                                        placeholder="ما الخدمة التي تبحث عنها؟"
                                        class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors"
                                    >
                                </div>
                                <div class="md:w-64 relative">
                                    <i class="fa-solid fa-folder absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <select class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors appearance-none bg-white">
                                        <option value="">جميع التصنيفات</option>
                                        <option value="programming">برمجة وتطوير</option>
                                        <option value="design">تصميم وإبداع</option>
                                        <option value="marketing">تسويق ومبيعات</option>
                                        <option value="writing">كتابة وترجمة</option>
                                        <option value="video">فيديو ومونتاج</option>
                                    </select>
                                </div>
                                <button class="px-8 py-4 bg-gradient-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300">
                                    <i class="fa-solid fa-magnifying-glass ml-2"></i>
                                    بحث
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <a href="dashboard.html" class="px-6 py-3 bg-gradient-primary text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300">
                                <i class="fa-solid fa-gauge ml-2"></i>
                                لوحة التحكم
                            </a>
                            <a href="#" class="px-6 py-3 bg-white text-primary-600 font-semibold rounded-xl border-2 border-primary-500 hover:bg-primary-50 transition-all duration-300">
                                <i class="fa-solid fa-plus ml-2"></i>
                                إضافة خدمة
                            </a>
                            <a href="#" class="px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:bg-gray-50 transition-all duration-300">
                                <i class="fa-solid fa-comments ml-2"></i>
                                الرسائل
                            </a>
                        </div>
                    </div>
                @endauth
                <!-- Hero Image -->
                <div class="hidden lg:block animate-float">
                    <div class="relative">
                        
                        
                        <!-- Floating Cards -->
                        <div class="absolute -top-6 -right-6 bg-white rounded-2xl shadow-xl p-4 animate-float" style="animation-delay: 1s;">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">5000+</p>
                                    <p class="text-xs text-gray-500">مشروع مكتمل</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-4 animate-float" style="animation-delay: 2s;">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">4.9/5</p>
                                    <p class="text-xs text-gray-500">تقييم العملاء</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @auth
     <section class="py-16 bg-white -mt-8 relative z-20 mx-4 lg:mx-8 rounded-2xl shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">تابع عملك</h2>
                    <p class="text-gray-600">استمر من حيث توقفت</p>
                </div>
                <a href="dashboard.html" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors flex items-center gap-2">
                    عرض الكل
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Active Project 1 -->
                <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 card-hover">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-primary-600 shadow-sm">
                                <i class="fa-solid fa-code text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">برمجة موقع تعريفي</h3>
                                <p class="text-xs text-gray-500">العميل: سارة علي</p>
                            </div>
                        </div>
                        <span class="status-in-progress text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-600 font-semibold">قيد التنفيذ</span>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500">التقدم</span>
                            <span class="font-semibold text-primary-600">75%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-primary rounded-full" style="width: 75%;"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">تسليم خلال يومين</span>
                        <span class="font-bold text-primary-600">$350</span>
                    </div>
                </div>

                <!-- Active Project 2 -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 card-hover">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-amber-600 shadow-sm">
                                <i class="fa-solid fa-palette text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">تصميم شعار احترافي</h3>
                                <p class="text-xs text-gray-500">العميل: محمد الأحمد</p>
                            </div>
                        </div>
                        <span class="status-pending text-xs px-3 py-1 rounded-full bg-amber-100 text-amber-600 font-semibold">مراجعة</span>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500">التقدم</span>
                            <span class="font-semibold text-amber-600">100%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">في انتظار الموافقة</span>
                        <span class="font-bold text-amber-600">$150</span>
                    </div>
                </div>

                <!-- Active Project 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-5 card-hover">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-purple-600 shadow-sm">
                                <i class="fa-solid fa-bullhorn text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">إدارة حملة إعلانية</h3>
                                <p class="text-xs text-gray-500">العميل: خالد عمر</p>
                            </div>
                        </div>
                        <span class="status-in-progress text-xs px-3 py-1 rounded-full bg-purple-100 text-purple-600 font-semibold">نشط</span>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500">التقدم</span>
                            <span class="font-semibold text-purple-600">45%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: 45%;"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">3 أيام متبقية</span>
                        <span class="font-bold text-purple-600">$200</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endauth
    <!-- Statistics Section -->
    <section class="py-12 bg-white shadow-sm -mt-8 relative z-20 mx-4 lg:mx-8 rounded-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center" data-aos="fade-up">
                    <div class="text-4xl md:text-5xl font-bold text-gradient mb-2 counter" data-target="15000">0</div>
                    <p class="text-gray-600 font-medium">مستقل محترف</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold text-gradient mb-2 counter" data-target="8500">0</div>
                    <p class="text-gray-600 font-medium">خدمة متاحة</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold text-gradient mb-2 counter" data-target="12000">0</div>
                    <p class="text-gray-600 font-medium">عميل سعيد</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold text-gradient mb-2 counter" data-target="98">0</div>
                    <p class="text-gray-600 font-medium">% نسبة الرضا</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-20 bg-gray-50" id="categories">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">تصفح حسب التصنيف</h2>
                <p class="text-gray-600 text-lg">اختر التصنيف المناسب لاحتياجاتك</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Category 1 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">برمجة وتطوير</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">2,500+ خدمة</p>
                </a>
                
                <!-- Category 2 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">تصميم وإبداع</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">1,800+ خدمة</p>
                </a>
                
                <!-- Category 3 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">تسويق ومبيعات</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">1,200+ خدمة</p>
                </a>
                
                <!-- Category 4 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-pen"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">كتابة وترجمة</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">950+ خدمة</p>
                </a>
                
                <!-- Category 5 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">فيديو ومونتاج</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">780+ خدمة</p>
                </a>
                
                <!-- Category 6 -->
                <a href="#" class="category-card bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl">
                    <div class="category-icon w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 text-2xl mx-auto mb-4 transition-colors">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h3 class="category-title font-bold text-gray-800 mb-2 transition-colors">دعم وإدارة</h3>
                    <p class="category-count text-sm text-gray-500 transition-colors">650+ خدمة</p>
                </a>
            </div>
            
            <div class="text-center mt-10">
                <a href="#" class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
                    عرض جميع التصنيفات
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Services Section -->
    <section class="py-20 bg-white" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">خدمات مميزة</h2>
                    <p class="text-gray-600 text-lg">أفضل الخدمات المقدمة من مستقلين محترفين</p>
                </div>
                <a href="#" class="hidden md:inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
                    عرض جميع الخدمات
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Service Card 1 -->
                <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="service-image-container">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/192b357d2-afd7-441c-b4bb-0903f4cad306.png" 
                            alt="تصميم شعار"
                            class="w-full h-48 object-cover"
                        >
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="مستقل" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700">أحمد محمد</span>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">تصميم شعار احترافي لشركتك الناشئة</h3>
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.9</span>
                            <span class="text-sm text-gray-500">(127 تقييم)</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">يبدأ من</span>
                            <span class="text-lg font-bold text-primary-600">$50</span>
                        </div>
                    </div>
                </div>
                
                <!-- Service Card 2 -->
                <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="service-image-container">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/15732332f-32ef-4e60-b33d-8a78b1e397c7.png" 
                            alt="برمجة موقع"
                            class="w-full h-48 object-cover"
                        >
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/1cf81d151-d90e-4989-9025-43fd475e6a13.png" alt="مستقل" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700">سارة علي</span>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">برمجة موقع تعريفي متجاوب بالكامل</h3>
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">5.0</span>
                            <span class="text-sm text-gray-500">(89 تقييم)</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">يبدأ من</span>
                            <span class="text-lg font-bold text-primary-600">$150</span>
                        </div>
                    </div>
                </div>
                
                <!-- Service Card 3 -->
                <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="service-image-container">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/15eab5eaf-05cd-462e-b160-818878f202e6.png" 
                            alt="كتابة محتوى"
                            class="w-full h-48 object-cover"
                        >
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="مستقل" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700">منى خالد</span>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">كتابة محتوى إبداعي لموقعك الإلكتروني</h3>
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.8</span>
                            <span class="text-sm text-gray-500">(156 تقييم)</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">يبدأ من</span>
                            <span class="text-lg font-bold text-primary-600">$25</span>
                        </div>
                    </div>
                </div>
                
                <!-- Service Card 4 -->
                <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="service-image-container">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/164647cc5-51ff-4ea1-bba1-e516d66ca292.png" 
                            alt="تسويق إلكتروني"
                            class="w-full h-48 object-cover"
                        >
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/1cf81d151-d90e-4989-9025-43fd475e6a13.png" alt="مستقل" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-gray-700">خالد عمر</span>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">إدارة حملات إعلانية على فيسبوك وإنستغرام</h3>
                        <div class="flex items-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.9</span>
                            <span class="text-sm text-gray-500">(203 تقييم)</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500">يبدأ من</span>
                            <span class="text-lg font-bold text-primary-600">$100</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-10 md:hidden">
                <a href="#" class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
                    عرض جميع الخدمات
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Freelancers Section -->
    <section class="py-20 bg-gray-50" id="freelancers">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">مستقلين مميزين</h2>
                    <p class="text-gray-600 text-lg">تواصل مع أفضل المستقلين في المنطقة</p>
                </div>
                <a href="#" class="hidden md:inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
                    عرض جميع المستقلين
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Freelancer Card 1 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="h-32 bg-gradient-primary relative">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" 
                            alt="مستقل"
                            class="w-24 h-24 rounded-2xl border-4 border-white absolute -bottom-12 right-1/2 translate-x-1/2"
                        >
                    </div>
                    <div class="pt-14 pb-5 px-5 text-center">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">أحمد محمد</h3>
                        <p class="text-sm text-gray-500 mb-3">مطور واجهات أمامية</p>
                        <div class="flex items-center justify-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.9</span>
                            <span class="text-sm text-gray-500">(45 مشروع)</span>
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center mb-4">
                            <span class="px-2 py-1 bg-primary-50 text-primary-600 text-xs rounded-full">React</span>
                            <span class="px-2 py-1 bg-primary-50 text-primary-600 text-xs rounded-full">Vue</span>
                            <span class="px-2 py-1 bg-primary-50 text-primary-600 text-xs rounded-full">Tailwind</span>
                        </div>
                        <a href="#" class="block w-full py-2.5 border-2 border-primary-500 text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                            عرض الملف الشخصي
                        </a>
                    </div>
                </div>
                
                <!-- Freelancer Card 2 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="h-32 bg-gradient-to-br from-blue-500 to-blue-600 relative">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/1cf81d151-d90e-4989-9025-43fd475e6a13.png" 
                            alt="مستقل"
                            class="w-24 h-24 rounded-2xl border-4 border-white absolute -bottom-12 right-1/2 translate-x-1/2"
                        >
                    </div>
                    <div class="pt-14 pb-5 px-5 text-center">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">سارة علي</h3>
                        <p class="text-sm text-gray-500 mb-3">مصممة جرافيك</p>
                        <div class="flex items-center justify-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">5.0</span>
                            <span class="text-sm text-gray-500">(120 مشروع)</span>
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center mb-4">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">Photoshop</span>
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full">Illustrator</span>
                        </div>
                        <a href="#" class="block w-full py-2.5 border-2 border-primary-500 text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                            عرض الملف الشخصي
                        </a>
                    </div>
                </div>
                
                <!-- Freelancer Card 3 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="h-32 bg-gradient-to-br from-amber-500 to-amber-600 relative">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/15eab5eaf-05cd-462e-b160-818878f202e6.png" 
                            alt="مستقل"
                            class="w-24 h-24 rounded-2xl border-4 border-white absolute -bottom-12 right-1/2 translate-x-1/2"
                        >
                    </div>
                    <div class="pt-14 pb-5 px-5 text-center">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">منى خالد</h3>
                        <p class="text-sm text-gray-500 mb-3">كاتبة محتوى</p>
                        <div class="flex items-center justify-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.8</span>
                            <span class="text-sm text-gray-500">(80 مشروع)</span>
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center mb-4">
                            <span class="px-2 py-1 bg-amber-50 text-amber-600 text-xs rounded-full">SEO</span>
                            <span class="px-2 py-1 bg-amber-50 text-amber-600 text-xs rounded-full">Copywriting</span>
                        </div>
                        <a href="#" class="block w-full py-2.5 border-2 border-primary-500 text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                            عرض الملف الشخصي
                        </a>
                    </div>
                </div>
                
                <!-- Freelancer Card 4 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden">
                    <div class="h-32 bg-gradient-to-br from-purple-500 to-purple-600 relative">
                        <img 
                            src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/164647cc5-51ff-4ea1-bba1-e516d66ca292.png" 
                            alt="مستقل"
                            class="w-24 h-24 rounded-2xl border-4 border-white absolute -bottom-12 right-1/2 translate-x-1/2"
                        >
                    </div>
                    <div class="pt-14 pb-5 px-5 text-center">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">خالد عمر</h3>
                        <p class="text-sm text-gray-500 mb-3">خبير تسويق إلكتروني</p>
                        <div class="flex items-center justify-center gap-1 mb-3">
                            <i class="fa-solid fa-star rating-star text-sm"></i>
                            <span class="text-sm font-semibold text-gray-800">4.9</span>
                            <span class="text-sm text-gray-500">(95 مشروع)</span>
                        </div>
                        <div class="flex flex-wrap gap-1 justify-center mb-4">
                            <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs rounded-full">Facebook Ads</span>
                            <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs rounded-full">Google Ads</span>
                        </div>
                        <a href="#" class="block w-full py-2.5 border-2 border-primary-500 text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                            عرض الملف الشخصي
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-10 md:hidden">
                <a href="#" class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
                    عرض جميع المستقلين
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-white" id="how-it-works">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">كيف تعمل المنصة؟</h2>
                <p class="text-gray-600 text-lg">ثلاث خطوات بسيطة للوصول إلى محترفين متميزين</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center relative">
                    <div class="w-20 h-20 bg-gradient-primary rounded-3xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg shadow-primary-500/30">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">انشر مشروعك</h3>
                    <p class="text-gray-600 leading-relaxed">
                        اشرح متطلبات مشروعك بالتفصيل وحدد الميزانية المتوقعة والوقت اللازم للتنفيذ
                    </p>
                    <!-- Connector Line -->
                    <div class="hidden md:block absolute top-10 right-0 w-full h-0.5 bg-gradient-to-l from-primary-500 to-transparent -z-10"></div>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center relative">
                    <div class="w-20 h-20 bg-gradient-primary rounded-3xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg shadow-primary-500/30">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">اختر المستقل</h3>
                    <p class="text-gray-600 leading-relaxed">
                        تصفح العروض المقدمة من المستقلين وقارن بينها واختر الأنسب لمشروعك
                    </p>
                    <!-- Connector Line -->
                    <div class="hidden md:block absolute top-10 right-0 w-full h-0.5 bg-gradient-to-l from-primary-500 to-transparent -z-10"></div>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-primary rounded-3xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg shadow-primary-500/30">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">استلم عملك</h3>
                    <p class="text-gray-600 leading-relaxed">
                        تابع تقدم العمل واستلم مشروعك جاهزاً مع ضمان كامل الحقوق
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gradient-to-br from-primary-50 via-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">ماذا يقول عملاؤنا؟</h2>
                <p class="text-gray-600 text-lg">آراء حقيقية من عملاء سعداء بخدماتنا</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "تجربة رائعة! وجدت المستقل المناسب لمشروعي خلال ساعات. الجودة كانت فوق التوقعات والسعر معقول جداً."
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="عميل" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-gray-800">محمد الأحمد</h4>
                            <p class="text-sm text-gray-500">رائد أعمال</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "كمستقلة، هذه المنصة غيرت حياتي المهنية. حصلت على عشرات المشاريع وزدت دخلي بشكل ملحوظ."
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/1cf81d151-d90e-4989-9025-43fd475e6a13.png" alt="مستقلة" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-gray-800">ليلى حسن</h4>
                            <p class="text-sm text-gray-500">مصممة جرافيك</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                        <i class="fa-solid fa-star rating-star"></i>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "الدعم الفني ممتاز والتعامل احترافي. أنصح أي شخص يبحث عن خدمات رقمية بالتسجيل في المنصة."
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/15eab5eaf-05cd-462e-b160-818878f202e6.png" alt="عميل" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-bold text-gray-800">عمر فاروق</h4>
                            <p class="text-sm text-gray-500">مدير تسويق</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-auth relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">جاهز لبدء مشروعك؟</h2>
            <p class="text-xl text-white/90 mb-10 leading-relaxed">
                انضم إلى آلاف العملاء والمستقلين الناجحين وابدأ رحلتك اليوم
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.html" class="px-8 py-4 bg-white text-primary-600 font-bold rounded-full hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-user-plus ml-2"></i>
                    أنشئ حساب مجاني
                </a>
                <a href="#services" class="px-8 py-4 bg-white/20 backdrop-blur-sm text-white font-bold rounded-full border-2 border-white/50 hover:bg-white/30 transition-all duration-300">
                    <i class="fa-solid fa-search ml-2"></i>
                    تصفح الخدمات
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center text-white text-2xl">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">عمل حر سوريا</h3>
                            <p class="text-xs text-gray-400">أكبر منصة للعمل الحر</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        منصة تجمع بين أصحاب المشاريع والمستقلين المحترفين في سوريا والعالم العربي
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fa-brands fa-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fa-brands fa-linkedin"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-500 transition-colors">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-lg mb-6">روابط سريعة</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">الرئيسية</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">الخدمات</a></li>
                        <li><a href="#freelancers" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">المستقلين</a></li>
                        <li><a href="#projects" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">المشاريع</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">كيف يعمل</a></li>
                    </ul>
                </div>
                
                <!-- Categories -->
                <div>
                    <h4 class="font-bold text-lg mb-6">التصنيفات</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">برمجة وتطوير</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">تصميم وإبداع</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">تسويق ومبيعات</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">كتابة وترجمة</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">فيديو ومونتاج</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="font-bold text-lg mb-6">تواصل معنا</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-gray-400 text-sm">
                            <i class="fa-solid fa-envelope text-primary-500"></i>
                            info@freelance-syria.com
                        </li>
                        <li class="flex items-center gap-3 text-gray-400 text-sm">
                            <i class="fa-solid fa-phone text-primary-500"></i>
                            +963 XX XXX XXXX
                        </li>
                        <li class="flex items-center gap-3 text-gray-400 text-sm">
                            <i class="fa-solid fa-location-dot text-primary-500"></i>
                            سوريا، دمشق
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-gray-400 text-sm text-center md:text-right">
                        &copy; 2024 جميع الحقوق محفوظة - منصة عمل حر سوريا
                    </p>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">شروط الاستخدام</a>
                        <a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">سياسة الخصوصية</a>
                        <a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">سياسة الكوكيز</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }

        // Toggle User Dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick) {
                dropdown.classList.remove('active');
            }
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-md');
            } else {
                navbar.classList.remove('shadow-md');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu on link click
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-md');
            } else {
                navbar.classList.remove('shadow-md');
            }
        });

        // Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            const speed = 200;

            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText.replace('+', '').replace('%', '');
                const increment = target / speed;

                if (count < target) {
                    if (counter.getAttribute('data-target') === '98') {
                        counter.innerText = Math.ceil(count + increment) + '%';
                    } else {
                        counter.innerText = Math.ceil(count + increment).toLocaleString() + '+';
                    }
                    setTimeout(animateCounters, 10);
                } else {
                    if (counter.getAttribute('data-target') === '98') {
                        counter.innerText = target + '%';
                    } else {
                        counter.innerText = target.toLocaleString() + '+';
                    }
                }
            });
        }

        // Trigger counter animation when section is visible
        const statsSection = document.querySelector('.counter');
        if (statsSection) {
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            statsObserver.observe(statsSection);
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Close mobile menu on link click
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });
    </script>
</body>
</html>

