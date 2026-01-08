<template>
    <section class="py-16 lg:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">What our clients say</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Real experiences from our satisfied customers</p>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div v-for="testimonial in testimonials" :key="testimonial.id"
                     class="group bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1 relative">
                    <!-- Quote Icon -->
                    <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>

                    <!-- Avatar & Info -->
                    <div class="flex items-center gap-4 mb-4">
                        <img :src="testimonial.avatar" 
                             :alt="testimonial.name"
                             class="w-14 h-14 rounded-full object-cover ring-4 ring-slate-100 group-hover:ring-blue-100 transition-all">
                        <div>
                            <h4 class="font-semibold text-slate-900">{{ testimonial.name }}</h4>
                            <p class="text-slate-500 text-sm">{{ testimonial.role }}</p>
                        </div>
                    </div>

                    <!-- Stars -->
                    <div class="flex gap-1 mb-4">
                        <svg v-for="n in 5" :key="n" 
                             class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>

                    <!-- Testimonial Text -->
                    <p class="text-slate-600 leading-relaxed">{{ testimonial.text }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const testimonials = [
    {
        id: 1,
        name: 'Rohit Sharma',
        role: 'Business Owner',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        text: 'Realto made our home buying journey so smooth. They understood our needs perfectly and found us a beautiful property in DLF Phase 3.'
    },
    {
        id: 2,
        name: 'Priyanka Verma',
        role: 'IT Professional',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        text: 'Professional team with deep market knowledge. They helped us get the best deal on our apartment. Highly recommend their services!'
    },
    {
        id: 3,
        name: 'Avinash Singh',
        role: 'Entrepreneur',
        avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        text: 'Excellent experience from start to finish. The team was responsive and transparent throughout. Found my dream villa in Gurugram.'
    },
];
</script>
