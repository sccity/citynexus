<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const classes = computed(() => [
  '-mx-1 h-px bg-border',
  props.class
]);
</script>

<template>
  <div :class="classes" />
</template> 