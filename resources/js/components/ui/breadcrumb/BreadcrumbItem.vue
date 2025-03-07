<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    href?: string;
    isLast?: boolean;
}>();
</script>

<template>
    <li :class="cn('inline-flex items-center gap-1.5 sm:gap-2.5', props.class)">
        <component
            :is="href ? 'a' : 'span'"
            :href="href"
            :class="[
                'transition-colors hover:text-foreground',
                {
                    'font-medium text-foreground': isLast,
                    'hover:underline': href
                }
            ]"
        >
            <slot />
        </component>
        <ChevronRight v-if="!isLast" class="h-3 w-3" />
    </li>
</template>
