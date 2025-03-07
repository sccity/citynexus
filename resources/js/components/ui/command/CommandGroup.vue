<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/utils';

interface Props {
  class?: string;
  heading?: string;
}

const props = defineProps<Props>();

const classes = computed(() => [
  'overflow-hidden p-1',
  props.class
]);
</script>

<template>
  <div :class="classes">
    <p v-if="heading" class="px-2 py-1.5 text-xs font-medium text-muted-foreground">
      {{ heading }}
    </p>
    <slot />
  </div>
</template> 