<template>
    <PublicLayout>
        <HeroSection />
        <PopularLocations />
        <WhyUsSection />
        <ProjectCategories />
        <FeaturedResidentials />
        <OurServices />
        <StatsSection />
        <Testimonials />
        <TalkToAdvisor />
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/components/public/PublicLayout.vue';
import HeroSection from '@/components/public/sections/HeroSection.vue';
import PopularLocations from '@/components/public/sections/PopularLocations.vue';
import WhyUsSection from '@/components/public/sections/WhyUsSection.vue';
import ProjectCategories from '@/components/public/sections/ProjectCategories.vue';
import FeaturedResidentials from '@/components/public/sections/FeaturedResidentials.vue';
import OurServices from '@/components/public/sections/OurServices.vue';
import StatsSection from '@/components/public/sections/StatsSection.vue';
import Testimonials from '@/components/public/sections/Testimonials.vue';
import TalkToAdvisor from '@/components/public/sections/TalkToAdvisor.vue';
</script>
