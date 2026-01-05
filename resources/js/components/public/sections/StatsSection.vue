<template>
    <section class="py-16 lg:py-20 bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <div v-for="stat in stats" :key="stat.label" 
                     class="text-center group">
                    <div class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-2 transition-transform duration-300 group-hover:scale-110">
                        <span ref="counterRefs" class="counter" :data-target="stat.value">0</span>{{ stat.suffix }}
                    </div>
                    <p class="text-blue-100 text-sm sm:text-base font-medium">{{ stat.label }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const stats = [
    { value: 70, suffix: 'K+', label: 'Properties Sold' },
    { value: 7300, suffix: '+', label: 'Sq. Feet' },
    { value: 530, suffix: '+', label: 'Happy Clients' },
    { value: 4.9, suffix: '', label: 'Star Rating' },
];

const counterRefs = ref([]);

onMounted(() => {
    // Animate counters when section comes into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    }, { threshold: 0.5 });

    const section = document.querySelector('.counter');
    if (section) {
        observer.observe(section.closest('section'));
    }
});

const animateCounters = () => {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach((counter) => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const isDecimal = target % 1 !== 0;
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = isDecimal ? current.toFixed(1) : Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = isDecimal ? target.toFixed(1) : target;
            }
        };

        updateCounter();
    });
};
</script>
