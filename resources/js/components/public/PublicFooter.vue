<template>
    <footer class="bg-slate-900 text-white">
        <!-- Newsletter Section -->
        <div class="border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Subscribe to our Newsletter</h3>
                        <p class="text-slate-400">Get the latest properties and news updates directly in your inbox</p>
                    </div>
                    <form @submit.prevent="handleSubscribe" class="flex w-full lg:w-auto gap-3">
                        <input v-model="email" 
                               type="email" 
                               placeholder="Enter your email address"
                               class="flex-1 lg:w-80 px-5 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 whitespace-nowrap">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">The Core Realtors</span>
                    </div>
                    <p class="text-slate-400 mb-6 leading-relaxed">
                        Your trusted partner in finding the perfect property. We make your dream home a reality.
                    </p>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-sm text-slate-400">Download Our App</span>
                    </div>
                    <div class="flex gap-3">
                        <a href="#" class="group">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" 
                                 alt="Google Play" 
                                 class="h-10 opacity-80 group-hover:opacity-100 transition-opacity">
                        </a>
                        <a href="#" class="group">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" 
                                 alt="App Store" 
                                 class="h-10 opacity-80 group-hover:opacity-100 transition-opacity">
                        </a>
                    </div>
                    <div class="flex items-center gap-4 mt-6">
                        <a v-for="social in socials" :key="social.name" 
                           :href="social.href"
                           class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all duration-300 hover:scale-110">
                            <component :is="social.icon" class="w-5 h-5" />
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-3">
                        <li v-for="link in quickLinks" :key="link.name">
                            <a :href="link.href" 
                               class="text-slate-400 hover:text-white hover:pl-2 transition-all duration-200 inline-flex items-center gap-2 group">
                               <span class="w-0 group-hover:w-2 h-0.5 bg-blue-500 transition-all duration-200"></span>
                               {{ link.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Properties -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Properties</h4>
                    <ul class="space-y-3">
                        <li v-for="link in propertyLinks" :key="link.name">
                            <a :href="link.href" 
                               class="text-slate-400 hover:text-white hover:pl-2 transition-all duration-200 inline-flex items-center gap-2 group">
                               <span class="w-0 group-hover:w-2 h-0.5 bg-blue-500 transition-all duration-200"></span>
                               {{ link.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-400">Golf Course Road, DLF Phase 5</p>
                                <p class="text-slate-400">Gurugram, Haryana 122002</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-400">+91 98765 43210</p>
                                <p class="text-slate-400">+91 98765 12170</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-slate-400">info@thecorerealtors.com</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-slate-400 text-sm">
                        © 2026 The Core Realtors. All Rights Reserved.
                    </p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="text-slate-400 text-sm hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="text-slate-400 text-sm hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="text-slate-400 text-sm hover:text-white transition-colors">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref, h } from 'vue';

const email = ref('');

const handleSubscribe = () => {
    alert(`Thanks for subscribing with ${email.value}!`);
    email.value = '';
};

const quickLinks = [
    { name: 'About Us', href: '#about' },
    { name: 'Services', href: '#services' },
    { name: 'Blog', href: '#blog' },
    { name: 'Contact', href: '#contact' },
    { name: 'Careers', href: '#careers' },
];

const propertyLinks = [
    { name: 'For Residential', href: '#residential' },
    { name: 'For Commercial', href: '#commercial' },
    { name: 'Property for Sale', href: '#sale' },
    { name: 'Property for Rent', href: '#rent' },
    { name: 'New Projects', href: '#projects' },
];

// Social Media Icons as render functions
const FacebookIcon = {
    render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })
        ]);
    }
};

const TwitterIcon = {
    render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { d: 'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z' })
        ]);
    }
};

const InstagramIcon = {
    render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { d: 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.757-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z' })
        ]);
    }
};

const LinkedInIcon = {
    render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { d: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' })
        ]);
    }
};

const socials = [
    { name: 'Facebook', href: '#', icon: FacebookIcon },
    { name: 'Twitter', href: '#', icon: TwitterIcon },
    { name: 'Instagram', href: '#', icon: InstagramIcon },
    { name: 'LinkedIn', href: '#', icon: LinkedInIcon },
];
</script>
