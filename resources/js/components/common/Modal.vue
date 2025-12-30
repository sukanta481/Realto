<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="modelValue" 
                class="fixed inset-0 z-50 overflow-y-auto"
                @keydown.esc="close"
            >
                <!-- Backdrop -->
                <div 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    @click="closeOnBackdrop && close()"
                ></div>

                <!-- Modal Container -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-4"
                    >
                        <div 
                            v-if="modelValue"
                            class="relative w-full bg-white rounded-2xl shadow-2xl"
                            :class="sizeClasses"
                            @click.stop
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                                <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
                                <button 
                                    @click="close"
                                    class="p-2 -mr-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="p-6 max-h-[70vh] overflow-y-auto">
                                <slot></slot>
                            </div>

                            <!-- Footer -->
                            <div v-if="$slots.footer" class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                                <slot name="footer"></slot>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'md',
        validator: (val) => ['sm', 'md', 'lg', 'xl', 'full'].includes(val),
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue', 'close']);

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'max-w-sm',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl',
        full: 'max-w-[90vw]',
    };
    return sizes[props.size] || sizes.md;
});

const close = () => {
    emit('update:modelValue', false);
    emit('close');
};

// Handle body scroll lock
watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

// Handle ESC key
const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.modelValue) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>
