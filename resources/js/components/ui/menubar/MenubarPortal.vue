<script setup lang="ts">
import { Teleport } from 'vue';

defineProps<{
  to?: string;
}>();
</script>

<template>
  <Teleport :to="to || 'body'">
    <slot />
  </Teleport>
</template> 