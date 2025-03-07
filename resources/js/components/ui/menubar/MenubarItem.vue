<template>
  <div :class="classes" :data-disabled="disabled" role="menuitem">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
  disabled?: boolean;
}

const props = defineProps<Props>();

const classes = computed(() => [
  'relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
  props.class
]);
</script> 