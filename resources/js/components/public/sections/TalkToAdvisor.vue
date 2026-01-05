<template>
    <section class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl overflow-hidden relative">
                <!-- Background Decorations -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500/10 rounded-full blur-2xl transform -translate-x-1/2 translate-y-1/2"></div>
                </div>

                <div class="relative grid lg:grid-cols-2 gap-8 lg:gap-12 p-8 lg:p-12 items-center">
                    <!-- Content -->
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Talk to our advisor</h2>
                        <p class="text-slate-300 text-lg mb-8 leading-relaxed">
                            Book a consultation with our expert real estate advisors. Get personalized guidance for your property investment.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="tel:+919876543210" 
                               class="inline-flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Request a Call
                            </a>
                            <a href="#" 
                               class="inline-flex items-center justify-center gap-3 px-6 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 hover:border-white/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Chat with Us
                            </a>
                        </div>
                    </div>

                    <!-- Image/Illustration -->
                    <div class="hidden lg:flex justify-center items-center">
                        <div class="relative">
                            <div class="w-64 h-64 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-full flex items-center justify-center">
                                <div class="w-48 h-48 bg-gradient-to-br from-blue-500/30 to-cyan-500/30 rounded-full flex items-center justify-center animate-pulse">
                                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Floating Elements -->
                            <div class="absolute -top-4 -right-4 w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg animate-bounce">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="absolute -bottom-2 -left-2 w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
</script>
