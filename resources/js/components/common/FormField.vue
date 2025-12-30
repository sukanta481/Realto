<template>
    <div class="space-y-1.5">
        <!-- Label -->
        <label v-if="label" :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-rose-500 ml-0.5">*</span>
        </label>

        <!-- Input -->
        <div class="relative">
            <!-- Text/Email/Tel/Number Input -->
            <input
                v-if="['text', 'email', 'tel', 'number', 'password', 'date', 'datetime-local'].includes(type)"
                :id="inputId"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :min="min"
                :max="max"
                :step="step"
                class="form-input"
                :class="{ 
                    'border-rose-300 focus:border-rose-400 focus:ring-rose-50': error,
                    'pl-10': prefixIcon,
                    'pr-10': suffixIcon,
                }"
                @input="$emit('update:modelValue', $event.target.value)"
                @blur="$emit('blur')"
            />

            <!-- Textarea -->
            <textarea
                v-else-if="type === 'textarea'"
                :id="inputId"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :rows="rows"
                class="form-input resize-none"
                :class="{ 'border-rose-300 focus:border-rose-400 focus:ring-rose-50': error }"
                @input="$emit('update:modelValue', $event.target.value)"
                @blur="$emit('blur')"
            ></textarea>

            <!-- Select -->
            <select
                v-else-if="type === 'select'"
                :id="inputId"
                :value="modelValue"
                :disabled="disabled"
                class="form-select"
                :class="{ 'border-rose-300 focus:border-rose-400 focus:ring-rose-50': error }"
                @change="$emit('update:modelValue', $event.target.value)"
                @blur="$emit('blur')"
            >
                <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
                <option
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>

            <!-- Prefix Icon -->
            <div v-if="prefixIcon" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <component :is="prefixIcon" class="w-5 h-5" />
            </div>

            <!-- Suffix Icon -->
            <div v-if="suffixIcon" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                <component :is="suffixIcon" class="w-5 h-5" />
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="error" class="text-sm text-rose-500 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ error }}
        </p>

        <!-- Hint -->
        <p v-else-if="hint" class="text-sm text-slate-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
    },
    rows: {
        type: Number,
        default: 3,
    },
    min: {
        type: [String, Number],
        default: undefined,
    },
    max: {
        type: [String, Number],
        default: undefined,
    },
    step: {
        type: [String, Number],
        default: undefined,
    },
    prefixIcon: {
        type: Object,
        default: null,
    },
    suffixIcon: {
        type: Object,
        default: null,
    },
});

defineEmits(['update:modelValue', 'blur']);

const inputId = computed(() => {
    return `field-${Math.random().toString(36).substr(2, 9)}`;
});
</script>
