<?php

use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

new class extends Component
{
    // حالة التبويب
    public $tab = 'login';
    
    // متغيرات تسجيل الدخول
    public $loginEmail = '';
    public $loginPassword = '';
    public $rememberMe = false;
    
    // متغيرات التسجيل
    public $regName = '';
    public $regUsername = '';
    public $regEmail = '';
    public $regPhoneCode = '+963';
    public $regPhone = '';
    public $regPassword = '';
    public $regPasswordConfirm = ''; 
    public $regAgreeTerms = false;
    
    // متغيرات المودال
    public $showModal = false;
    public $modalType = '';

    // التبديل بين التبويبات
    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // فتح المودال
    public function openModal($type)
    {
        $this->modalType = $type;
        $this->showModal = true;
    }

    // إغلاق المودال
    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
    }

    // قواعد التحقق
    protected function rules()
    {
        if ($this->tab === 'login') {
            return [
                'loginEmail' => 'required|email',
                'loginPassword' => 'required|min:6',
            ];
        } else {
            return [
                'regName' => 'required|string|min:3|max:50',
                'regUsername' => [
                    'required',
                    'string',
                    'min:4',
                    'max:20',
                    Rule::unique('users', 'username'),
                    'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
                ],
                'regEmail' => 'required|email|unique:users,email',
                'regPhone' => 'nullable|string|digits_between:9,10', // ✅ ليس مطلوباً
                'regPassword' => 'required|min:8',
                'regPasswordConfirm' => 'required|min:8',
                'regAgreeTerms' => 'accepted',
            ];
        }
    }

    // رسائل الخطأ المخصصة
    protected function messages() 
    {
        return [
            // تسجيل الدخول
            'loginEmail.required' => 'البريد الإلكتروني مطلوب',
            'loginEmail.email' => 'الرجاء إدخال بريد إلكتروني صحيح',
            'loginPassword.required' => 'كلمة المرور مطلوبة',
            'loginPassword.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            
            // التسجيل
            'regName.required' => 'الاسم الكامل مطلوب',
            'regName.min' => 'الاسم يجب أن يكون 3 أحرف على الأقل',
            'regUsername.required' => 'اسم المستخدم مطلوب',
            'regUsername.unique' => 'عذراً، اسم المستخدم هذا محجوز. اختر اسماً آخر.',
            'regUsername.regex' => 'اسم المستخدم يجب أن يبدأ بحرف إنجليزي ويحتوي على أحرف وأرقام وشرطة سفلية فقط.',
            'regEmail.required' => 'البريد الإلكتروني مطلوب',
            'regEmail.email' => 'الرجاء إدخال بريد إلكتروني صحيح',
            'regEmail.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل. هل تريد <a href="#" wire:click="switchTab(\'login\')" class="text-primary-500 font-bold underline">تسجيل الدخول</a> بدلاً من ذلك؟',
            'regPhone.digits_between' => 'رقم الهاتف يجب أن يكون بين 9 و 10 أرقام.',
            'regPassword.required' => 'كلمة المرور مطلوبة',
            'regPassword.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'regPassword.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'regAgreeTerms.accepted' => 'يجب الموافقة على الشروط والأحكام للمتابعة.',
        ];
    }

    // التحقق الفوري أثناء الكتابة
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // معالجة تسجيل الدخول
    public function login()
    {
        $this->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->loginEmail, 'password' => $this->loginPassword], $this->rememberMe)) {
            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'loginEmail' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ]);
    }

    // معالجة التسجيل
    public function register()
    {
        $this->validate();
        if($this->regPassword !== $this->regPasswordConfirm) {
            throw ValidationException::withMessages([
                'regPasswordConfirm' => 'كلمتا المرور غير متطابقتين.',
            ]);
        }
        $user = User::create([
            'username' => $this->regUsername,
            'name' => $this->regName,
            'email' => $this->regEmail,
            'phone' => $this->regPhone,
            'password' => Hash::make($this->regPassword),
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

};?> 
<div class="bg-gradient-to-br from-primary-50 via-blue-50 to-purple-50 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">
    <!-- Background Animated Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute w-96 h-96 bg-gradient-primary rounded-full opacity-10 -top-24 -right-24 animate-float"></div>
        <div class="absolute w-72 h-72 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full opacity-10 -bottom-16 -left-16 animate-float" style="animation-delay: 5s;"></div>
        <div class="absolute w-48 h-48 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full opacity-10 top-8 left-1/2 animate-float" style="animation-delay: 10s;"></div>
    </div>

    <!-- Main Auth Container -->
    <div class="relative z-10 w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2 min-h-[600px]">
        
        <!-- Left Side Panel (Branding) -->
        <div class="bg-gradient-auth p-12 flex flex-col justify-center items-center text-center text-white relative overflow-hidden hidden md:flex">
            <div class="absolute inset-0 bg-grid-pattern opacity-20 animate-spin-slow"></div>
            <div class="relative z-10">
                <div class="text-6xl mb-6 animate-pulse-slow">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">منصة العمل الحر السورية</h2>
                <p class="text-white/90 text-sm leading-relaxed mb-8">
                    انضم إلى آلاف المستقلين والعملاء في أكبر منصة عمل حر في سوريا
                </p>
                <ul class="text-right space-y-3 max-w-xs mx-auto">
                    <li class="flex items-center gap-3 text-sm">
                        <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <span>اعرض خدماتك أو ابحث عن مستقلين محترفين</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm">
                        <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <span>نظام دفع آمن يحمي حقوق الجميع</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm">
                        <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <span>دعم فني متواجد على مدار الساعة</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm">
                        <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <span>مجتمع نشط من المحترفين العرب</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Side (Forms) -->
        <div class="p-8 md:p-12 flex flex-col justify-center">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">مرحباً بك</h1>
                <p class="text-gray-500 text-sm">سجل الدخول أو أنشئ حساباً جديداً للبدء</p>
            </div>

            <!-- Tabs (Livewire Controlled) -->
            <div class="flex gap-2 mb-8 bg-gray-100 p-1.5 rounded-full">
                <button
                    wire:click="switchTab('login')"
                    class="flex-1 py-3 px-6 rounded-full font-semibold text-sm transition-all duration-300 {{ $tab === 'login' ? 'bg-gradient-primary text-white shadow-md' : 'text-gray-500 hover:bg-primary-100 hover:text-primary-600' }}"
                >
                    <i class="fa-solid fa-right-to-bracket ml-2"></i>
                    تسجيل الدخول
                </button>
                <button
                    wire:click="switchTab('register')"
                    class="flex-1 py-3 px-6 rounded-full font-semibold text-sm transition-all duration-300 {{ $tab === 'register' ? 'bg-gradient-primary text-white shadow-md' : 'text-gray-500 hover:bg-primary-100 hover:text-primary-600' }}"
                >
                    <i class="fa-solid fa-user-plus ml-2"></i>
                    حساب جديد
                </button>
            </div>

            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="bg-primary-50 border border-primary-500 rounded-xl p-4 mb-6 flex items-center gap-3 text-primary-700 animate-slide-in">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- ================= FORM: LOGIN ================= -->
            @if($tab === 'login')
            <form wire:submit="login" class="space-y-5 animate-fade-in">
                
                <!-- Email Input -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        البريد الإلكتروني <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            wire:model.live="loginEmail"
                            placeholder="example@email.com"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('loginEmail') border-red-500 bg-red-50 @enderror"
                        >
                        <i class="fa-solid fa-envelope absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('loginEmail') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            wire:model.live="loginPassword"
                            id="loginPassword"
                            placeholder="••••••••"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('loginPassword') border-red-500 bg-red-50 @enderror"
                        >
                        <i class="fa-solid fa-lock absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                        <button 
                            type="button" 
                            class="absolute left-4 top-8 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors"
                            onclick="togglePassword('loginPassword', this)"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('loginPassword') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                    
                    <div class="flex items-center justify-between text-sm mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="rememberMe" class="w-4 h-4 text-primary-500 rounded focus:ring-primary-500">
                            <span class="text-gray-500">تذكرني</span>
                        </label>
                        <button type="button" wire:click="openModal('forgotPassword')" class="text-primary-500 font-semibold hover:underline">
                            نسيت كلمة المرور؟
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 bg-gradient-primary text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="login">تسجيل الدخول</span>
                    <span wire:loading wire:target="login">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> جاري المعالجة...
                    </span>
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <!-- Divider -->
                <div class="flex items-center gap-4 text-gray-400 text-xs my-6">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span>أو سجل الدخول عبر</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Social Login -->
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="social-btn py-3 border-2 border-gray-200 bg-white rounded-xl hover:border-primary-500 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-google text-xl" style="color: #db4437;"></i>
                    </button>
                    <button type="button" class="social-btn py-3 border-2 border-gray-200 bg-white rounded-xl hover:border-primary-500 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-facebook text-xl" style="color: #4267B2;"></i>
                    </button>
                    <button type="button" class="social-btn py-3 border-2 border-gray-200 bg-white rounded-xl hover:border-primary-500 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-apple text-xl" style="color: #000;"></i>
                    </button>
                </div>
            </form>
            @endif

            <!-- ================= FORM: REGISTER ================= -->
            @if($tab === 'register')
            <form wire:submit="register" class="space-y-5 animate-fade-in">
                
                <!-- Username Input -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        اسم المستخدم <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live="regUsername"
                            placeholder="ahmed_dev"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('regUsername') border-red-500 bg-red-50 @enderror"
                        >
                        <i class="fa-solid fa-at absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                        @if($this->regUsername && !$errors->has('regUsername'))
                            <span class="absolute left-4 top-8 -translate-y-1/2 text-xs text-primary-500 font-semibold">
                                <i class="fa-solid fa-circle-check"></i> متاح
                            </span>
                        @endif
                    </div>
                    @error('regUsername') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @else
                        <p class="text-gray-400 text-xs mt-1.5">
                            <i class="fa-solid fa-info-circle ml-1"></i>
                            احرف إنجليزية، أرقام، وشرطة سفلية فقط
                        </p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        الاسم الكامل <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live="regName"
                            placeholder="محمد أحمد"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('regName') border-red-500 bg-red-50 @enderror"
                        >
                        <i class="fa-solid fa-user absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('regName') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        البريد الإلكتروني <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            wire:model.live="regEmail"
                            placeholder="example@email.com"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('regEmail') border-red-500 bg-red-50 @enderror"
                        >
                        <i class="fa-solid fa-envelope absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('regEmail') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                </div>

                <!-- Phone (Optional) -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        رقم الهاتف <span class="text-gray-400 text-xs font-normal">(اختياري)</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 font-semibold flex items-center gap-2">
                            <span>🇸🇾</span>
                            <select wire:model.live="regPhoneCode" class="bg-transparent text-sm cursor-pointer outline-none">
                                <option value="+963">+963</option>
                            </select>
                        </div>
                        <input
                            type="tel"
                            wire:model.live="regPhone"
                            placeholder="912345678"
                            class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('regPhone') border-red-500 bg-red-50 @enderror"
                        >
                    </div>
                    @error('regPhone') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                </div>
                <!-- Password with Strength -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative ">
                        <input 
                            type="password" 
                            id="registerPassword" 
                            wire:model.live="regPassword"
                            placeholder="••••••••"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)] @error('regPassword') border-red-500 bg-red-50 @enderror"
                            required
                            oninput="checkPasswordStrength(this.value)"
                        >
                        <i class="fa-solid fa-lock absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                        <button 
                            type="button" 
                            class="absolute left-4 top-8 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors"
                            onclick="togglePassword('registerPassword', this)"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2" wire:ignore>
                        <div class="h-1 bg-gray-200 rounded-full overflow-hidden mb-1.5">
                            <div id="strengthFill" class="h-full w-0 transition-all duration-300 rounded-full"></div>
                        </div>
                        <p id="strengthText" class="text-xs text-gray-400">قوة كلمة المرور</p>
                    </div>
                    <p class="text-red-500 text-xs mt-1.5 hidden" id="registerPasswordError">كلمة المرور ضعيفة جداً</p>
                    @error('regPassword') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p> 
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="block text-gray-700 font-semibold text-sm mb-2">
                        تأكيد كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="confirmPassword" 
                            wire:model.live="regPasswordConfirm"
                            placeholder="••••••••"
                            class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm transition-all duration-300 focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)]@error('regPasswordConfirm') border-red-500 bg-red-50 @enderror"
                            required
                        >
                        <i class="fa-solid fa-lock absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                        <button 
                            type="button" 
                            class="absolute left-4 top-8 -translate-y-1/2 text-gray-400 hover:text-primary-500 transition-colors"
                            onclick="togglePassword('confirmPassword', this)"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-red-500 text-xs mt-1.5 hidden" id="confirmPasswordError">كلمتا المرور غير متطابقتين</p>
                    @error('regPasswordConfirm') 
                        <p class="text-red-500 text-xs mt-1.5">{!! $message !!}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start gap-2 text-sm">
                    <input type="checkbox" id="agreeTerms" class="w-4 h-4 text-primary-500 rounded mt-0.5 focus:ring-primary-500"
                    wire:model.live="regAgreeTerms"
                    required>
                    <label class="text-gray-500 cursor-pointer">
                        أوافق على
                        <button type="button" wire:click="openModal('terms')" class="text-primary-500 font-semibold hover:underline">شروط الاستخدام</button>
                        و
                        <button type="button" wire:click="openModal('privacy')" class="text-primary-500 font-semibold hover:underline">سياسة الخصوصية</button>
                    </label>
                    @error('regAgreeTerms') 
                        <p class="text-red-500 text-xs">{!! $message !!}</p> 
                    @enderror
                </div>
                <!-- Submit Button -->
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 bg-gradient-primary text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="register">إنشاء حساب جديد</span>
                    <span wire:loading wire:target="register">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> جاري الإنشاء...
                    </span>
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </form>
            @endif

        </div>
    </div>

    <!-- ================= MODAL (Livewire Controlled) ================= -->
    @if($showModal)
    <div
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 animate-fade-in"
        wire:click="closeModal"
    >
        <div class="modal bg-white rounded-2xl max-w-md w-[90%] max-h-[80vh] overflow-y-auto animate-modal-in" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">
                    @if($modalType === 'terms') شروط الاستخدام
                    @elseif($modalType === 'privacy') سياسة الخصوصية
                    @elseif($modalType === 'forgotPassword') استعادة كلمة المرور
                    @endif
                </h3>
                <button
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-red-500 text-2xl transition-colors"
                >
                    &times;
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-6 text-gray-600 leading-relaxed">
                @if($modalType === 'terms')
                    <h4 class="font-bold text-gray-800 mb-3">شروط الاستخدام</h4>
                    <p class="mb-4">مرحباً بك في منصة العمل الحر السورية. باستخدامك للمنصة، فإنك توافق على الشروط التالية:</p>
                    <h4 class="font-bold text-gray-800 mt-4 mb-2">1. الأهلية</h4>
                    <ul class="list-disc pr-5 space-y-1 text-sm">
                        <li>يجب أن يكون عمرك 18 عاماً أو أكثر</li>
                        <li>يجب أن تكون قادراً قانوناً على الدخول في عقود</li>
                    </ul>
                    <h4 class="font-bold text-gray-800 mt-4 mb-2">2. حساب المستخدم</h4>
                    <ul class="list-disc pr-5 space-y-1 text-sm">
                        <li>أنت مسؤول عن الحفاظ على سرية حسابك</li>
                        <li>يجب إعلامنا فوراً عن أي استخدام غير مصرح به</li>
                        <li>يمنع إنشاء حسابات متعددة</li>
                    </ul>
                @elseif($modalType === 'privacy')
                    <h4 class="font-bold text-gray-800 mb-3">سياسة الخصوصية</h4>
                    <p class="mb-4">نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية:</p>
                    <h4 class="font-bold text-gray-800 mt-4 mb-2">1. المعلومات التي نجمعها</h4>
                    <ul class="list-disc pr-5 space-y-1 text-sm">
                        <li>المعلومات الشخصية (الاسم، البريد، الهاتف)</li>
                        <li>معلومات الملف الشخصي والمهارات</li>
                        <li>سجل المعاملات والمدفوعات</li>
                    </ul>
                @elseif($modalType === 'forgotPassword')
                    <h4 class="font-bold text-gray-800 mb-3">استعادة كلمة المرور</h4>
                    <p class="mb-4">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور:</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">البريد الإلكتروني</label>
                            <div class="relative">
                                <input
                                    type="email"
                                    placeholder="example@email.com"
                                    class="w-full px-12 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-primary-500 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1)]"
                                >
                                <i class="fa-solid fa-envelope absolute right-4 top-8 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <button class="w-full py-3.5 bg-gradient-primary text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300 flex items-center justify-center gap-2">
                            <span>إرسال رابط الاستعادة</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <p class="mt-4 text-xs text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        سيتم إرسال رابط الاستعادة إلى بريدك الإلكتروني خلال دقائق
                    </p>
                @endif
            </div>
        </div>
    </div>
    @endif
    @push('scripts')
    <script>
        // Password Toggle (Cosmetic - Safe)
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    
        function checkPasswordStrength(password) {
                const fill = document.getElementById('strengthFill');
                const text = document.getElementById('strengthText');
                
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                
                fill.className = 'h-full w-0 transition-all duration-300 rounded-full';
                text.className = 'text-xs';
                
                if (strength <= 1) {
                    fill.classList.add('password-strength-weak', 'w-1/4');
                    text.classList.add('text-red-500');
                    text.textContent = 'ضعيفة جداً';
                } else if (strength <= 2) {
                    fill.classList.add('password-strength-fair', 'w-2/4');
                    text.classList.add('text-amber-500');
                    text.textContent = 'ضعيفة';
                } else if (strength <= 3) {
                    fill.classList.add('password-strength-good', 'w-3/4');
                    text.classList.add('text-blue-500');
                    text.textContent = 'جيدة';
                } else {
                    fill.classList.add('password-strength-strong', 'w-full');
                    text.classList.add('text-green-500');
                    text.textContent = 'قوية';
                }
        }
    
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                @this.call('closeModal');
            }
        });
    </script>
    @endpush
</div>