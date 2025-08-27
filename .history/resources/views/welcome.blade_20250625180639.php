<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class = "scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>City Engineering Office</title>
        <link rel="icon" href="{{ asset('frontend/img/baguio-logo.png')}}" type="image/png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <!-- Simple Navigation with Slide-in Animation -->
        <nav id="navbar" class="pointer-events-none bg-transparent fixed w-full z-50 transition-all duration-300 -translate-y-full opacity-0">
            <div class="px-10 py-3">
                <div class="flex justify-between items-center h-16">
                    <div class="text-2xl font-bold text-neutral animate-fadeInDown">
                        City Engineering Office
                    </div>
                    @if (Route::has('login'))
                        <div class="flex space-x-12">
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                class="text-neutral font-semibold text-lg relative after:absolute after:left-0 after:bottom-0 after:w-0 after:h-[2px] after:bg-green-700 after:transition-all after:duration-300 hover:after:w-full hover:scale-110 transition-transform">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                class="text-neutral font-semibold text-lg relative after:absolute after:left-0 after:bottom-0 after:w-0 after:h-[2px] after:bg-green-700 after:transition-all after:duration-300 hover:after:w-full hover:scale-110 transition-transform">
                                    Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                    class="text-neutral font-semibold text-lg relative after:absolute after:left-0 after:bottom-0 after:w-0 after:h-[2px] after:bg-green-700 after:transition-all after:duration-300 hover:after:w-full hover:scale-110 transition-transform">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </nav>
        
        <!-- Hero Section with Fade-in and Bounce Effects -->
        <main>
            <div class="relative h-screen animate-fadeIn">
                <div class="absolute inset-0">
                    <img src="{{ asset('frontend/img/BagCityHall.jpg') }}" 
                         class="w-full h-full object-cover object-center opacity-90" 
                         alt="Baguio City Hall">
                    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                </div>
                
                <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                    <img src="{{ asset('frontend/img/baguio-logo.png') }}" class="w-56 h-56 animate-fadeIn transition-transform duration-500 hover:scale-105" alt="Baguio Logo">
                    <h1 class="text-5xl font-bold text-white mb-8 animate-fadeInDown">Planning, Programming,</h1>
                    <h1 class="text-5xl font-bold text-white mb-5 animate-fadeInDown">and Construction Division </h1>
                    <div class="flex gap-4">
                        <a href="#visionMission" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 hover:scale-105 transition-transform">Our Vision & Mission</a>
                        <a href="#contact" class="border border-white text-white px-6 py-3 rounded-lg hover:bg-white/10 hover:scale-105 transition-transform">Contact Us</a>
                    </div>
                </div>
            </div>
    
            <!-- Vision & Mission Section with Scroll Animations -->
            <section id="visionMission" class="py-16 bg-white animate-fadeIn">
                <div class="max-w-4xl mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-neural-900 mb-4 animate-fadeInDown">Vision</h2>
                        <p class="text-neural-600 animate-fadeIn">A livable and resilient city secured by significant, quiality and sustainable infrastructure by 2043</p>
                    </div>
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-neutral-900 mb-8 animate-fadeInDown">Mission</h2>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="bg-gray-50 p-6 rounded-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <p class="text-neutral-600">To provide significant, quiality and sustainable infrastructure that promote socio economic development, comfort and resiliency to Baguio City</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Mission Section -->
                <section id="mission" class="py-20 bg-gradient-to-b from-green-50 to-white">
                    <div class="max-w-3xl mx-auto px-4">
                        <div class="flex flex-col items-center mb-12">
                            <div class="bg-green-600 rounded-full p-4 mb-4 shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-4xl font-bold text-green-800 mb-4">Our Mission</h2>
                            <p class="text-lg text-gray-700 bg-white rounded-xl shadow-md px-8 py-6">
                                To provide significant, quality and sustainable infrastructure that promote socio economic development, comfort and resiliency to Baguio City
                            </p>
                        </div>
                    </div>
                </section>
    
            <!-- Contact Us Section -->
            <section id="contact" class="py-20 bg-gray-50">
                <div class="max-w-4xl mx-auto px-4">
                    <h2 class="text-3xl font-bold text-center text-green-800 mb-10">Contact Us</h2>
                    <div class="bg-white rounded-2xl shadow-xl p-10 grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Office Address</h3>
                            <p class="text-gray-700 mb-6">Jose Felipe St., Motorpool Compound, Lower Rock Quarry</p>
                            <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                            <p class="text-gray-700 mb-6">ceobaguio@yahoo.com</p>
                            <h3 class="font-semibold text-gray-900 mb-2">Phone</h3>
                            <p class="text-gray-700">0744452058</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Department Head</h3>
                            <p class="text-gray-700 mb-2">Engr. Richard Benjamin L. Lardizabal</p>
                            <p class="text-gray-700">Acting City Engineer</p>
                            <div class="mt-10 flex items-center space-x-3">
                                <img src="{{ asset('frontend/img/baguio-logo.png') }}" alt="Baguio Logo" class="w-16 h-16 rounded-full shadow">
                                <span class="text-green-700 font-semibold">City Engineering Office</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    
        <!-- Footer -->
        <footer class="bg-green-700 text-white py-8 animate-fadeIn">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} City Engineering Office. All rights reserved.</p>
            </div>
        </footer>
    </body>
    <script>
        window.addEventListener("scroll", function() {
    var navbar = document.getElementById("navbar");
    if (window.scrollY > 50) {
        navbar.classList.remove("-translate-y-full", "opacity-0", "pointer-events-none", "bg-transparent");
        navbar.classList.add("bg-white", "shadow-md", "opacity-100", "translate-y-0", "pointer-events-auto");
    } else {
        navbar.classList.add("-translate-y-full", "opacity-0", "pointer-events-none", "bg-transparent");
        navbar.classList.remove("bg-white", "shadow-md", "opacity-100", "translate-y-0", "pointer-events-auto");
    }
});
    </script>
</html>