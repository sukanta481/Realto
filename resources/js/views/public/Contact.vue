<template>
    <PublicLayout>
        <PageHero 
            title="Contact Us"
            subtitle="Get in touch with our expert team for all your real estate needs"
            backgroundImage="https://images.unsplash.com/photo-1423666639041-f56000c27a9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
        />

        <!-- Contact Info Cards -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 -mt-32 relative z-20">
                    <div v-for="info in contactInfo" :key="info.title"
                         class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 group text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                            <component :is="info.icon" class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ info.title }}</h3>
                        <p class="text-slate-600 text-sm">{{ info.line1 }}</p>
                        <p v-if="info.line2" class="text-slate-600 text-sm">{{ info.line2 }}</p>
                        <a v-if="info.link" :href="info.link" class="inline-block mt-3 text-blue-600 font-medium text-sm hover:text-blue-700 transition-colors">
                            {{ info.linkText }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form & Map Section -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg">
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">Send us a Message</h2>
                        <p class="text-slate-600 mb-8">Fill out the form and our team will get back to you within 24 hours</p>
                        
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Full Name *</label>
                                    <input v-model="form.name" type="text" required
                                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                           placeholder="John Doe">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address *</label>
                                    <input v-model="form.email" type="email" required
                                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                           placeholder="john@example.com">
                                </div>
                            </div>
                            
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Phone Number *</label>
                                    <input v-model="form.phone" type="tel" required
                                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                           placeholder="+91 98765 43210">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                                    <select v-model="form.subject"
                                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Select a subject</option>
                                        <option value="buying">Buying a Property</option>
                                        <option value="selling">Selling a Property</option>
                                        <option value="renting">Renting a Property</option>
                                        <option value="commercial">Commercial Inquiry</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Message *</label>
                                <textarea v-model="form.message" rows="5" required
                                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                          placeholder="Tell us about your requirements..."></textarea>
                            </div>
                            
                            <button type="submit" :disabled="isSubmitting"
                                    class="w-full px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ isSubmitting ? 'Sending...' : 'Send Message' }}</span>
                                <svg v-if="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>

                        <!-- Success Message -->
                        <transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-2">
                            <div v-if="showSuccess" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-green-700">Thank you! Your message has been sent successfully. We'll get back to you soon.</p>
                            </div>
                        </transition>
                    </div>

                    <!-- Map Section -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl p-4 shadow-lg overflow-hidden">
                            <div class="rounded-xl overflow-hidden">
                                <!-- Google Maps Embed -->
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3507.003227423255!2d77.03508867549746!3d28.472937875751476!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d18ed0696ae4f%3A0x1e99e5e03f19a3e4!2sCyber%20City%2C%20DLF%20Cyber%20City%2C%20Gurugram%2C%20Haryana%20122002!5e0!3m2!1sen!2sin!4v1704517200000!5m2!1sen!2sin"
                                    width="100%" 
                                    height="350" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade"
                                    class="w-full">
                                </iframe>
                            </div>
                        </div>

                        <!-- Quick Contact Actions -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <a href="https://maps.google.com?q=Cyber+City+Gurugram" target="_blank"
                               class="flex items-center gap-4 p-5 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                    <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">Get Directions</h4>
                                    <p class="text-sm text-slate-600">Open in Google Maps</p>
                                </div>
                            </a>

                            <a href="tel:+919876543210"
                               class="flex items-center gap-4 p-5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white">Call Us Now</h4>
                                    <p class="text-sm text-blue-100">+91 98765 43210</p>
                                </div>
                            </a>
                        </div>

                        <!-- Business Hours -->
                        <div class="bg-white rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Business Hours
                            </h3>
                            <div class="space-y-3">
                                <div v-for="schedule in businessHours" :key="schedule.day" 
                                     class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                    <span class="text-slate-700 font-medium">{{ schedule.day }}</span>
                                    <span :class="schedule.closed ? 'text-red-500' : 'text-slate-600'">{{ schedule.hours }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Office Locations -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Our Offices</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Visit us at any of our convenient locations across NCR</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div v-for="office in offices" :key="office.city"
                         class="bg-slate-50 rounded-2xl p-6 hover:bg-white hover:shadow-xl transition-all duration-500 group">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900 mb-2">{{ office.city }}</h3>
                                <p class="text-slate-600 text-sm mb-3">{{ office.address }}</p>
                                <a :href="office.mapLink" target="_blank" 
                                   class="inline-flex items-center gap-1 text-blue-600 font-medium text-sm hover:text-blue-700 transition-colors">
                                    View on Map
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<script setup>
import { ref, h } from 'vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import PageHero from '@/components/public/sections/PageHero.vue';

const isSubmitting = ref(false);
const showSuccess = ref(false);

const form = ref({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
});

const handleSubmit = async () => {
    isSubmitting.value = true;
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    isSubmitting.value = false;
    showSuccess.value = true;
    
    // Reset form
    form.value = {
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
    };
    
    // Hide success message after 5 seconds
    setTimeout(() => {
        showSuccess.value = false;
    }, 5000);
};

// Icon components
const PhoneIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' })
        ]);
    }
};

const EmailIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
        ]);
    }
};

const LocationIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' }),
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M15 11a3 3 0 11-6 0 3 3 0 016 0z' })
        ]);
    }
};

const ClockIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
    }
};

const contactInfo = [
    {
        title: 'Phone',
        line1: '+91 98765 43210',
        line2: '+91 11 4567 8900',
        icon: PhoneIcon,
        link: 'tel:+919876543210',
        linkText: 'Call Now'
    },
    {
        title: 'Email',
        line1: 'info@corerealtors.com',
        line2: 'support@corerealtors.com',
        icon: EmailIcon,
        link: 'mailto:info@corerealtors.com',
        linkText: 'Send Email'
    },
    {
        title: 'Head Office',
        line1: 'Tower B, Cyber City',
        line2: 'Gurugram, Haryana 122002',
        icon: LocationIcon,
        link: 'https://maps.google.com?q=Cyber+City+Gurugram',
        linkText: 'Get Directions'
    },
    {
        title: 'Working Hours',
        line1: 'Mon - Sat: 9 AM - 7 PM',
        line2: 'Sunday: Closed',
        icon: ClockIcon
    }
];

const businessHours = [
    { day: 'Monday', hours: '9:00 AM - 7:00 PM' },
    { day: 'Tuesday', hours: '9:00 AM - 7:00 PM' },
    { day: 'Wednesday', hours: '9:00 AM - 7:00 PM' },
    { day: 'Thursday', hours: '9:00 AM - 7:00 PM' },
    { day: 'Friday', hours: '9:00 AM - 7:00 PM' },
    { day: 'Saturday', hours: '10:00 AM - 5:00 PM' },
    { day: 'Sunday', hours: 'Closed', closed: true }
];

const offices = [
    {
        city: 'Gurugram (Head Office)',
        address: 'Tower B, 5th Floor, Cyber City, DLF Phase 2, Gurugram 122002',
        mapLink: 'https://maps.google.com?q=Cyber+City+Gurugram'
    },
    {
        city: 'Delhi',
        address: 'C-12, Connaught Place, New Delhi 110001',
        mapLink: 'https://maps.google.com?q=Connaught+Place+Delhi'
    },
    {
        city: 'Noida',
        address: 'A-62, Sector 63, Noida, UP 201301',
        mapLink: 'https://maps.google.com?q=Sector+63+Noida'
    }
];
</script>
