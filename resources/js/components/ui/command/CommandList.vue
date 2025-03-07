<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => [
  'max-h-[300px] overflow-y-auto overflow-x-hidden',
  props.class
]);
</script>

<template>
  <div :class="classes" role="listbox">
    <slot />
  </div>
</template> 