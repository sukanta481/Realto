<template>
    <PublicLayout>
        <PageHero 
            title="Our Services"
            subtitle="Comprehensive real estate solutions tailored to your needs - from property search to possession"
            backgroundImage="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
        />

        <!-- Services Overview Grid -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">What We Offer</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Complete range of services to make your real estate journey smooth and hassle-free</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <div v-for="service in services" :key="service.id"
                         class="group bg-slate-50 rounded-2xl p-6 text-center transition-all duration-500 hover:bg-white hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="relative w-20 h-20 mx-auto mb-6 bg-white rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-blue-600 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                            <component :is="service.icon" class="w-10 h-10 text-blue-600 group-hover:text-white transition-colors duration-500" />
                        </div>

                        <h3 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">{{ service.title }}</h3>
                        <p class="text-slate-600 text-sm mb-5 leading-relaxed">{{ service.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Process Section -->
        <section class="py-16 lg:py-20 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">How We Work</h2>
                    <p class="text-slate-400 max-w-2xl mx-auto">Our streamlined process ensures you get your dream property with ease</p>
                </div>

                <div class="relative">
                    <!-- Connection Line (Desktop) -->
                    <div class="hidden lg:block absolute top-24 left-1/2 w-[80%] h-0.5 bg-gradient-to-r from-blue-500/0 via-blue-500 to-blue-500/0 -translate-x-1/2"></div>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="(step, index) in processSteps" :key="step.id" 
                             class="relative text-center group">
                            <div class="w-20 h-20 mx-auto mb-6 relative">
                                <div class="absolute inset-0 bg-blue-500/20 rounded-full animate-pulse group-hover:scale-110 transition-transform"></div>
                                <div class="relative w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all">
                                    <span class="text-2xl font-bold text-white">{{ index + 1 }}</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-3">{{ step.title }}</h3>
                            <p class="text-slate-400 text-sm">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Services -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Detailed Services</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Explore our comprehensive range of real estate services</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8">
                    <div v-for="detail in detailedServices" :key="detail.id"
                         class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-500 group">
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-colors duration-300">
                                <component :is="detail.icon" class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">{{ detail.title }}</h3>
                                <p class="text-slate-600 mb-4">{{ detail.description }}</p>
                                <ul class="space-y-2">
                                    <li v-for="feature in detail.features" :key="feature" class="flex items-center gap-2 text-sm text-slate-600">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ feature }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-slate-600">Find answers to common questions about our services</p>
                </div>

                <div class="space-y-4">
                    <div v-for="(faq, index) in faqs" :key="faq.id"
                         class="border border-slate-200 rounded-xl overflow-hidden">
                        <button @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 transition-colors">
                            <span class="font-semibold text-slate-900">{{ faq.question }}</span>
                            <svg :class="['w-5 h-5 text-slate-500 transition-transform duration-300', openFaq === index ? 'rotate-180' : '']" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2">
                            <div v-if="openFaq === index" class="px-6 pb-6">
                                <p class="text-slate-600 leading-relaxed">{{ faq.answer }}</p>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </section>

        <TalkToAdvisor />
    </PublicLayout>
</template>

<script setup>
import { ref, h } from 'vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import PageHero from '@/components/public/sections/PageHero.vue';
import TalkToAdvisor from '@/components/public/sections/TalkToAdvisor.vue';

const openFaq = ref(null);

const toggleFaq = (index) => {
    openFaq.value = openFaq.value === index ? null : index;
};

// Icon components
const SearchIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' })
        ]);
    }
};

const CalendarIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
        ]);
    }
};

const CurrencyIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
    }
};

const KeyIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z' })
        ]);
    }
};

const DocumentIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ]);
    }
};

const HomeIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
        ]);
    }
};

const InteriorIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z' })
        ]);
    }
};

const SupportIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z' })
        ]);
    }
};

const services = [
    {
        id: 1,
        title: 'Property Search',
        description: 'Find the perfect property from our vast collection of residential and commercial listings.',
        icon: SearchIcon
    },
    {
        id: 2,
        title: 'Site Visits',
        description: 'Schedule property visits at your convenience with our expert guides.',
        icon: CalendarIcon
    },
    {
        id: 3,
        title: 'Home Loan',
        description: 'Get the best home loan rates from leading banks with our assistance.',
        icon: CurrencyIcon
    },
    {
        id: 4,
        title: 'Possession',
        description: 'Complete guidance until you receive the keys to your new home.',
        icon: KeyIcon
    }
];

const processSteps = [
    {
        id: 1,
        title: 'Share Requirements',
        description: 'Tell us about your property preferences and budget'
    },
    {
        id: 2,
        title: 'Property Matches',
        description: 'We curate properties that match your criteria'
    },
    {
        id: 3,
        title: 'Site Visits',
        description: 'Visit shortlisted properties with our experts'
    },
    {
        id: 4,
        title: 'Closing & Possession',
        description: 'Complete paperwork and get your keys'
    }
];

const detailedServices = [
    {
        id: 1,
        title: 'Property Buying Assistance',
        description: 'End-to-end support for purchasing your dream property, from search to possession.',
        icon: HomeIcon,
        features: ['Curated property listings', 'Price negotiation support', 'Document verification', 'Registry assistance']
    },
    {
        id: 2,
        title: 'Home Loan Services',
        description: 'Connect with top banks and get the best interest rates for your home loan.',
        icon: CurrencyIcon,
        features: ['Multiple bank options', 'Best interest rates', 'Quick approvals', 'Minimal documentation']
    },
    {
        id: 3,
        title: 'Legal Documentation',
        description: 'Complete legal support for all property-related documentation and agreements.',
        icon: DocumentIcon,
        features: ['Title verification', 'Agreement drafting', 'Registry support', 'Due diligence']
    },
    {
        id: 4,
        title: 'Interior Design',
        description: 'Transform your new property with our partner interior design services.',
        icon: InteriorIcon,
        features: ['Modern designs', 'Budget-friendly options', 'Quality materials', 'Timely delivery']
    }
];

const faqs = [
    {
        id: 1,
        question: 'How do I start my property search?',
        answer: 'Simply contact us through our website or call our helpline. Our property advisor will understand your requirements and start curating properties that match your criteria. You can also browse our listings online and shortlist properties for site visits.'
    },
    {
        id: 2,
        question: 'Do you charge any brokerage fee?',
        answer: 'Our brokerage structure varies based on the property type and transaction value. For residential properties, we typically charge a nominal percentage. We are transparent about all charges upfront with no hidden fees.'
    },
    {
        id: 3,
        question: 'Can you help with home loan processing?',
        answer: 'Yes! We have tie-ups with major banks and NBFCs. Our team will help you compare interest rates, process your loan application, and ensure quick approvals with minimal documentation.'
    },
    {
        id: 4,
        question: 'What documents are required for property purchase?',
        answer: 'Typically, you need identity proof (Aadhaar/PAN), address proof, income proof (salary slips/ITR), bank statements, and property-related documents. Our team will provide a detailed checklist based on your specific case.'
    },
    {
        id: 5,
        question: 'Do you provide after-sales support?',
        answer: 'Absolutely! Our relationship doesn\'t end at possession. We provide continued support for any issues related to your property, documentation, or if you need interior design services.'
    }
];
</script>
