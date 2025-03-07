<template>
  <div :class="classes">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => [
  'flex h-full w-full flex-col overflow-hidden rounded-md bg-popover text-popover-foreground',
  props.class
]);
</script> 