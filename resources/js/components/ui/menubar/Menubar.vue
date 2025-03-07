<template>
  <div
    ref="menubarRef"
    :class="classes"
    role="menubar"
  >
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
  'flex h-10 items-center space-x-1 rounded-md border bg-background p-1',
  props.class
]);
</script> 