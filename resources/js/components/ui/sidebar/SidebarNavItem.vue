<script setup lang="ts">
import { inject } from 'vue';
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
  href: string;
  icon?: any;
  title: string;
  active?: boolean;
  class?: HTMLAttributes['class'];
}>();

const { isOpen } = inject('sidebar', { isOpen: true });
</script>

<template>
  <Link
    :href="href"
    :class="cn(
      'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors',
      active ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
      props.class
    )"
  >
    <component :is="icon" v-if="icon" class="h-4 w-4" />
    <span v-if="isOpen">{{ title }}</span>
  </Link>
</template> 