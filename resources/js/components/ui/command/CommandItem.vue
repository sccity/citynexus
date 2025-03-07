<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
  disabled?: boolean;
}

const props = defineProps<Props>();
defineEmits<{
  (e: 'select'): void;
}>();

const classes = computed(() => [
  'relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
  'aria-selected:bg-accent aria-selected:text-accent-foreground',
  props.class
]);
</script>

<template>
  <div
    :class="classes"
    :data-disabled="disabled"
    @click="$emit('select')"
    role="option"
  >
    <slot />
  </div>
</template> 