<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصفح الخدمات | منصة العمل الحر السورية</title>
    
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
                        'fade-in': 'fadeIn 0.6s ease',
                        'slide-up': 'slideUp 0.5s ease',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        fadeIn: {
                            'from': { opacity: '0' },
                            'to': { opacity: '1' },
                        },
                        slideUp: {
                            'from': { opacity: '0', transform: 'translateY(20px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' },
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
        
        /* Filter sidebar */
        .filter-section {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-section:last-child {
            border-bottom: none;
        }
        
        /* Checkbox custom style */
        .custom-checkbox:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        
        /* Price range slider */
        .price-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            outline: none;
        }
        
        .price-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #10b981;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
        }
        
        /* Mobile filter overlay */
        .filter-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        /* Pagination */
        .pagination-btn {
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .pagination-btn.active {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        /* Sort dropdown */
        .sort-dropdown {
            min-width: 200px;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center text-white text-2xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">عمل حر سوريا</h1>
                        <p class="text-xs text-gray-500">أكبر منصة للعمل الحر</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">الرئيسية</a>
                    <a href="#" class="nav-link text-primary-600 font-medium">الخدمات</a>
                    <a href="#freelancers" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">المستقلين</a>
                    <a href="#projects" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">المشاريع</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 font-medium hover:text-primary-600 transition-colors">كيف يعمل</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-3">
                    <a href="login.html" class="px-6 py-2.5 text-primary-600 font-semibold border-2 border-primary-500 rounded-full hover:bg-primary-50 transition-all duration-300">
                        دخول
                    </a>
                    <a href="register.html" class="px-6 py-2.5 bg-gradient-primary text-white font-semibold rounded-full hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 transition-all duration-300">
                        حساب جديد
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="lg:hidden text-gray-700 text-2xl" onclick="toggleMobileMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu fixed top-20 left-0 right-0 bg-white shadow-lg lg:hidden p-6 space-y-4 hidden" id="mobileMenu">
            <a href="index.html" class="block text-gray-700 font-medium py-2 hover:text-primary-600">الرئيسية</a>
            <a href="#" class="block text-primary-600 font-medium py-2">الخدمات</a>
            <a href="#freelancers" class="block text-gray-700 font-medium py-2 hover:text-primary-600">المستقلين</a>
            <a href="#projects" class="block text-gray-700 font-medium py-2 hover:text-primary-600">المشاريع</a>
            <div class="pt-4 border-t border-gray-200 space-y-3">
                <a href="login.html" class="block text-center py-3 text-primary-600 font-semibold border-2 border-primary-500 rounded-full">دخول</a>
                <a href="register.html" class="block text-center py-3 bg-gradient-primary text-white font-semibold rounded-full">حساب جديد</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-primary-50 via-blue-50 to-purple-50 pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">تصفح الخدمات</h1>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">اكتشف آلاف الخدمات الاحترافية المقدمة من مستقلين متميزين في مختلف المجالات</p>
            </div>
            
            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-2xl shadow-xl p-4 max-w-4xl mx-auto">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            placeholder="ابحث عن خدمة..."
                            class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors"
                            id="searchInput"
                        >
                    </div>
                    <div class="md:w-64 relative">
                        <i class="fa-solid fa-folder absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select class="w-full pr-12 pl-4 py-4 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-colors appearance-none bg-white" id="categoryFilter">
                            <option value="">جميع التصنيفات</option>
                            <option value="programming">برمجة وتطوير</option>
                            <option value="design">تصميم وإبداع</option>
                            <option value="marketing">تسويق ومبيعات</option>
                            <option value="writing">كتابة وترجمة</option>
                            <option value="video">فيديو ومونتاج</option>
                            <option value="support">دعم وإدارة</option>
                        </select>
                    </div>
                    <button class="px-8 py-4 bg-gradient-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300" onclick="filterServices()">
                        <i class="fa-solid fa-filter ml-2"></i>
                        تصفية
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Sidebar Filters -->
                <aside class="lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">تصفية النتائج</h3>
                            <button class="text-primary-600 text-sm font-semibold hover:text-primary-700" onclick="resetFilters()">
                                إعادة تعيين
                            </button>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="filter-section">
                            <h4 class="font-bold text-gray-800 mb-4">نطاق السعر</h4>
                            <div class="space-y-3">
                                <input type="range" class="price-slider" min="5" max="1000" value="500" id="priceRange">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>$5</span>
                                    <span class="font-bold text-primary-600" id="priceValue">$500</span>
                                    <span>$1000+</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Rating -->
                        <div class="filter-section">
                            <h4 class="font-bold text-gray-800 mb-4">التقييم</h4>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <div class="flex items-center gap-1">
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <span class="text-sm text-gray-600 mr-2">5 نجوم</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <div class="flex items-center gap-1">
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-regular fa-star rating-star"></i>
                                        <span class="text-sm text-gray-600 mr-2">4 نجوم فأكثر</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <div class="flex items-center gap-1">
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-solid fa-star rating-star"></i>
                                        <i class="fa-regular fa-star rating-star"></i>
                                        <i class="fa-regular fa-star rating-star"></i>
                                        <span class="text-sm text-gray-600 mr-2">3 نجوم فأكثر</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Delivery Time -->
                        <div class="filter-section">
                            <h4 class="font-bold text-gray-800 mb-4">وقت التسليم</h4>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">تسليم خلال 24 ساعة</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">تسليم خلال 3 أيام</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">تسليم خلال أسبوع</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Seller Level -->
                        <div class="filter-section">
                            <h4 class="font-bold text-gray-800 mb-4">مستوى البائع</h4>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">مستقل مميز <i class="fa-solid fa-badge-check text-primary-500 mr-1"></i></span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">مستقل محترف</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">مستقل جديد</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Service Type -->
                        <div class="filter-section">
                            <h4 class="font-bold text-gray-800 mb-4">نوع الخدمة</h4>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">خدمة مصغرة</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">مشروع كامل</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Mobile Filter Button -->
                        <button class="lg:hidden w-full py-3 bg-gradient-primary text-white font-bold rounded-xl mt-4" onclick="toggleMobileFilters()">
                            <i class="fa-solid fa-filter ml-2"></i>
                            تطبيق التصفية
                        </button>
                    </div>
                </aside>

                <!-- Services Grid -->
                <main class="flex-1">
                    <!-- Results Info and Sort -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <p class="text-gray-600">
                                عرض <span class="font-bold text-gray-800" id="resultsCount">24</span> من <span class="font-bold text-gray-800">8,500+</span> خدمة
                            </p>
                        </div>
                        <div class="relative">
                            <select class="sort-dropdown px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none appearance-none bg-white cursor-pointer" id="sortSelect">
                                <option value="recommended">الأكثر توصية</option>
                                <option value="newest">الأحدث</option>
                                <option value="price-low">السعر: من الأقل للأعلى</option>
                                <option value="price-high">السعر: من الأعلى للأقل</option>
                                <option value="rating">الأعلى تقييماً</option>
                                <option value="sales">الأكثر مبيعاً</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6" id="servicesGrid">
                        <!-- Service Card 1 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up">
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
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">أحمد محمد</span>
                                        <span class="text-xs text-primary-600 font-semibold"><i class="fa-solid fa-badge-check ml-1"></i>مميز</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">تصميم شعار احترافي لشركتك الناشئة</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">4.9</span>
                                    <span class="text-sm text-gray-500">(127)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$50</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Card 2 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up" style="animation-delay: 0.1s;">
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
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">سارة علي</span>
                                        <span class="text-xs text-primary-600 font-semibold"><i class="fa-solid fa-badge-check ml-1"></i>مميز</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">برمجة موقع تعريفي متجاوب بالكامل</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">5.0</span>
                                    <span class="text-sm text-gray-500">(89)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$150</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Card 3 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up" style="animation-delay: 0.2s;">
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
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">منى خالد</span>
                                        <span class="text-xs text-gray-500 font-semibold">محترف</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">كتابة محتوى إبداعي لموقعك الإلكتروني</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">4.8</span>
                                    <span class="text-sm text-gray-500">(156)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$25</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Card 4 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up" style="animation-delay: 0.3s;">
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
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">خالد عمر</span>
                                        <span class="text-xs text-primary-600 font-semibold"><i class="fa-solid fa-badge-check ml-1"></i>مميز</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">إدارة حملات إعلانية على فيسبوك وإنستغرام</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">4.9</span>
                                    <span class="text-sm text-gray-500">(203)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$100</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Card 5 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up" style="animation-delay: 0.4s;">
                            <div class="service-image-container">
                                <img 
                                    src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/192b357d2-afd7-441c-b4bb-0903f4cad306.png" 
                                    alt="تصميم هوية"
                                    class="w-full h-48 object-cover"
                                >
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/177afdf71-fe6a-4e2c-a017-13946ea0bf53.png" alt="مستقل" class="w-8 h-8 rounded-full">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">ليلى حسن</span>
                                        <span class="text-xs text-gray-500 font-semibold">محترف</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">تصميم هوية بصرية كاملة لشركتك</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">4.7</span>
                                    <span class="text-sm text-gray-500">(78)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$200</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Card 6 -->
                        <div class="service-card bg-white rounded-2xl shadow-sm hover:shadow-xl card-hover overflow-hidden animate-slide-up" style="animation-delay: 0.5s;">
                            <div class="service-image-container">
                                <img 
                                    src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/15732332f-32ef-4e60-b33d-8a78b1e397c7.png" 
                                    alt="تطبيق موبايل"
                                    class="w-full h-48 object-cover"
                                >
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="https://image.qwenlm.ai/public_source/17c64722-9495-475b-8f7e-49384cac28a7/1cf81d151-d90e-4989-9025-43fd475e6a13.png" alt="مستقل" class="w-8 h-8 rounded-full">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 block">عمر فاروق</span>
                                        <span class="text-xs text-primary-600 font-semibold"><i class="fa-solid fa-badge-check ml-1"></i>مميز</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2 hover:text-primary-600 cursor-pointer transition-colors">برمجة تطبيق موبايل iOS و Android</h3>
                                <div class="flex items-center gap-1 mb-3">
                                    <i class="fa-solid fa-star rating-star text-sm"></i>
                                    <span class="text-sm font-semibold text-gray-800">5.0</span>
                                    <span class="text-sm text-gray-500">(45)</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <span class="text-lg font-bold text-primary-600">$500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex items-center justify-center gap-2">
                        <button class="pagination-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-600 hover:border-primary-500 transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button class="pagination-btn active px-4 py-2 border-2 border-primary-500 rounded-lg font-semibold">1</button>
                        <button class="pagination-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-600 hover:border-primary-500 transition-colors">2</button>
                        <button class="pagination-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-600 hover:border-primary-500 transition-colors">3</button>
                        <span class="px-2 text-gray-400">...</span>
                        <button class="pagination-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-600 hover:border-primary-500 transition-colors">12</button>
                        <button class="pagination-btn px-4 py-2 border-2 border-gray-200 rounded-lg text-gray-600 hover:border-primary-500 transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Mobile Filter Overlay -->
    <div class="fixed inset-0 filter-overlay z-40 hidden lg:hidden" id="filterOverlay" onclick="toggleMobileFilters()">
        <div class="absolute left-0 top-0 bottom-0 w-80 bg-white p-6 overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">تصفية النتائج</h3>
                <button class="text-gray-400 hover:text-gray-600 text-2xl" onclick="toggleMobileFilters()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <!-- Price Range -->
            <div class="filter-section">
                <h4 class="font-bold text-gray-800 mb-4">نطاق السعر</h4>
                <div class="space-y-3">
                    <input type="range" class="price-slider" min="5" max="1000" value="500">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>$5</span>
                        <span class="font-bold text-primary-600">$500</span>
                        <span>$1000+</span>
                    </div>
                </div>
            </div>
            
            <!-- Rating -->
            <div class="filter-section">
                <h4 class="font-bold text-gray-800 mb-4">التقييم</h4>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">5 نجوم</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">4 نجوم فأكثر</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">3 نجوم فأكثر</span>
                    </label>
                </div>
            </div>
            
            <!-- Delivery Time -->
            <div class="filter-section">
                <h4 class="font-bold text-gray-800 mb-4">وقت التسليم</h4>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">تسليم خلال 24 ساعة</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">تسليم خلال 3 أيام</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" class="custom-checkbox w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">تسليم خلال أسبوع</span>
                    </label>
                </div>
            </div>
            
            <button class="w-full py-3 bg-gradient-primary text-white font-bold rounded-xl mt-4" onclick="toggleMobileFilters()">
                عرض النتائج
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8 mt-20">
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
                        <li><a href="index.html" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">الرئيسية</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">الخدمات</a></li>
                        <li><a href="#freelancers" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">المستقلين</a></li>
                        <li><a href="#projects" class="text-gray-400 hover:text-primary-400 transition-colors text-sm">المشاريع</a></li>
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
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Mobile Filters Toggle
        function toggleMobileFilters() {
            const overlay = document.getElementById('filterOverlay');
            overlay.classList.toggle('hidden');
        }

        // Price Range Slider
        const priceRange = document.getElementById('priceRange');
        const priceValue = document.getElementById('priceValue');
        
        if (priceRange) {
            priceRange.addEventListener('input', function() {
                priceValue.textContent = '$' + this.value;
            });
        }

        // Filter Services
        function filterServices() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const serviceCards = document.querySelectorAll('.service-card');
            let visibleCount = 0;

            serviceCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const matchesSearch = title.includes(searchInput);
                const matchesCategory = !categoryFilter || true; // Add category logic here

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    card.style.animation = 'slideUp 0.5s ease';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('resultsCount').textContent = visibleCount;
        }

        // Reset Filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('priceRange').value = '500';
            document.getElementById('priceValue').textContent = '$500';
            
            // Reset checkboxes
            document.querySelectorAll('.custom-checkbox').forEach(cb => {
                cb.checked = false;
            });

            // Reset sort
            document.getElementById('sortSelect').value = 'recommended';

            // Show all services
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach((card, index) => {
                card.style.display = 'block';
                card.style.animation = 'slideUp 0.5s ease';
                card.style.animationDelay = (index * 0.1) + 's';
            });

            document.getElementById('resultsCount').textContent = serviceCards.length;
        }

        // Sort Services
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortValue = this.value;
            const servicesGrid = document.getElementById('servicesGrid');
            const serviceCards = Array.from(servicesGrid.querySelectorAll('.service-card'));

            serviceCards.sort((a, b) => {
                const priceA = parseFloat(a.querySelector('.text-primary-600').textContent.replace('$', ''));
                const priceB = parseFloat(b.querySelector('.text-primary-600').textContent.replace('$', ''));
                const ratingA = parseFloat(a.querySelector('.rating-star + span').textContent);
                const ratingB = parseFloat(b.querySelector('.rating-star + span').textContent);

                switch(sortValue) {
                    case 'price-low':
                        return priceA - priceB;
                    case 'price-high':
                        return priceB - priceA;
                    case 'rating':
                        return ratingB - ratingA;
                    default:
                        return 0;
                }
            });

            serviceCards.forEach((card, index) => {
                servicesGrid.appendChild(card);
                card.style.animation = 'slideUp 0.5s ease';
                card.style.animationDelay = (index * 0.1) + 's';
            });
        });

        // Search on Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterServices();
            }
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('nav');
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
    </script>
</body>
</html>

