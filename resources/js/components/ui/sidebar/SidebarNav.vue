<script setup lang="ts">
import { inject } from 'vue';
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();

const { isOpen } = inject('sidebar', { isOpen: true });
</script>

<template>
  <nav
    :class="cn(
      'flex flex-col gap-1',
      props.class
    )"
  >
    <slot />
  </nav>
</template> 