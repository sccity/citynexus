<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => [
  'py-6 text-center text-sm text-muted-foreground',
  props.class
]);
</script>

<template>
  <div :class="classes">
    <slot />
  </div>
</template> 