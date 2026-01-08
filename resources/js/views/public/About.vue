<template>
    <PublicLayout>
        <PageHero 
            title="About Us"
            subtitle="Your trusted partner in finding the perfect property for over a decade"
            backgroundImage="https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
        />

        <!-- Our Story Section -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div class="relative">
                        <div class="rounded-2xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Our team" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-6 -right-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                            <div class="text-4xl font-bold mb-1">10+</div>
                            <div class="text-blue-100 text-sm">Years of Excellence</div>
                        </div>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 font-semibold text-sm rounded-full mb-4">Our Story</span>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6">
                            Building Dreams, 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-cyan-500">One Home at a Time</span>
                        </h2>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Realto was founded with a simple mission: to make property buying a delightful experience. What started as a small team with big dreams has grown into one of the NCR region's most trusted real estate consultancies.
                        </p>
                        <p class="text-slate-600 mb-8 leading-relaxed">
                            Over the years, we've helped thousands of families find their dream homes and assisted businesses in securing the perfect commercial spaces. Our success is built on transparency, expertise, and an unwavering commitment to our clients.
                        </p>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">500+</div>
                                <div class="text-slate-600 text-sm">Happy Families</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">2000+</div>
                                <div class="text-slate-600 text-sm">Properties</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">₹500 Cr</div>
                                <div class="text-slate-600 text-sm">Deals Closed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 group">
                        <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-500 transition-colors duration-300">
                            <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Our Mission</h3>
                        <p class="text-slate-600 leading-relaxed">
                            To simplify the real estate journey for every customer by providing transparent, reliable, and personalized property solutions. We strive to be the bridge that connects dreams with reality.
                        </p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 group">
                        <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-500 transition-colors duration-300">
                            <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Our Vision</h3>
                        <p class="text-slate-600 leading-relaxed">
                            To become India's most trusted real estate consultancy, known for integrity, innovation, and exceptional customer service. We envision a future where every property transaction is seamless and stress-free.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Values -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Our Core Values</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">The principles that guide everything we do</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="value in coreValues" :key="value.title"
                         class="text-center group">
                        <div class="w-20 h-20 mx-auto mb-6 bg-slate-100 rounded-2xl flex items-center justify-center group-hover:bg-blue-500 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                            <component :is="value.icon" class="w-10 h-10 text-blue-600 group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ value.title }}</h3>
                        <p class="text-slate-600 text-sm">{{ value.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Meet Our Team</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">The passionate professionals behind Realto</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <div v-for="member in teamMembers" :key="member.name"
                         class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                        <div class="relative overflow-hidden aspect-square">
                            <img :src="member.image" :alt="member.name"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-4 left-4 right-4 flex justify-center gap-3">
                                    <a href="#" class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="p-5 text-center">
                            <h3 class="text-lg font-semibold text-slate-900 mb-1">{{ member.name }}</h3>
                            <p class="text-blue-600 text-sm font-medium">{{ member.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <StatsSection />

        <!-- Testimonials -->
        <Testimonials />

        <TalkToAdvisor />
    </PublicLayout>
</template>

<script setup>
import { h } from 'vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import PageHero from '@/components/public/sections/PageHero.vue';
import StatsSection from '@/components/public/sections/StatsSection.vue';
import Testimonials from '@/components/public/sections/Testimonials.vue';
import TalkToAdvisor from '@/components/public/sections/TalkToAdvisor.vue';

// Icon components
const TransparencyIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
        ]);
    }
};

const IntegrityIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })
        ]);
    }
};

const ExcellenceIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' })
        ]);
    }
};

const CustomerIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
        ]);
    }
};

const coreValues = [
    {
        title: 'Transparency',
        description: 'Complete honesty in all dealings with no hidden charges',
        icon: TransparencyIcon
    },
    {
        title: 'Integrity',
        description: 'Building trust through ethical practices',
        icon: IntegrityIcon
    },
    {
        title: 'Excellence',
        description: 'Striving for the best in everything we do',
        icon: ExcellenceIcon
    },
    {
        title: 'Customer First',
        description: 'Your needs are our top priority',
        icon: CustomerIcon
    }
];

const teamMembers = [
    {
        name: 'Rajesh Kumar',
        role: 'Founder & CEO',
        image: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    {
        name: 'Priya Sharma',
        role: 'Head of Sales',
        image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    {
        name: 'Amit Patel',
        role: 'Property Consultant',
        image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    {
        name: 'Neha Gupta',
        role: 'Customer Relations',
        image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    }
];
</script>
