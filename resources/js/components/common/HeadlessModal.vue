<template>
    <TransitionRoot appear :show="modelValue" as="template">
        <Dialog as="div" @close="close" class="relative z-50">
            <!-- Backdrop -->
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            :class="[
                                'w-full transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl transition-all',
                                sizeClasses[size]
                            ]"
                        >
                            <!-- Header -->
                            <div v-if="title || $slots.header" class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                                <slot name="header">
                                    <DialogTitle as="h3" class="text-lg font-semibold text-slate-900">
                                        {{ title }}
                                    </DialogTitle>
                                </slot>
                                <button
                                    v-if="closable"
                                    @click="close"
                                    class="p-2 -m-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div :class="['px-6 py-4', { 'max-h-[calc(100vh-200px)] overflow-y-auto': scrollable }]">
                                <slot />
                            </div>

                            <!-- Footer -->
                            <div v-if="$slots.footer" class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                                <slot name="footer" />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    },
    closable: {
        type: Boolean,
        default: true
    },
    scrollable: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue', 'close']);

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    full: 'max-w-4xl'
};

const close = () => {
    if (props.closable) {
        emit('update:modelValue', false);
        emit('close');
    }
};
</script>
