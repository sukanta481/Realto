<template>
    <section class="py-16 lg:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Content -->
                <div>
                    <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-600 text-sm font-semibold rounded-full mb-4">About Us</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6">
                        Why Us
                    </h2>
                    <p class="text-slate-600 text-lg mb-8 leading-relaxed">
                        At Realto, we've been helping customers through the Gurugram, Delhi NCR India region find their dream homes since 2010. We understand your needs and provide personalized solutions to match your lifestyle and budget.
                    </p>
                    <p class="text-slate-600 mb-8">
                        Our commitment to professionalism and customer satisfaction sets us apart. We believe in building lasting relationships with our clients.
                    </p>

                    <!-- Benefits List -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div v-for="benefit in benefits" :key="benefit" 
                             class="flex items-center gap-3 p-3 bg-white rounded-xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 cursor-default group">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-slate-700 font-medium">{{ benefit }}</span>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl group">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="House model"
                             class="w-full h-auto group-hover:scale-105 transition-transform duration-700">
                    </div>
                    <!-- Floating Card -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl hidden lg:block hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900">15+</p>
                                <p class="text-slate-500 text-sm">Years Experience</p>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Element -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const benefits = [
    'Best Dealership',
    'Years Consultancy',
    'Wide Range',
    'Support 24 Measures',
];
</script>
