<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div class="w-full">
    <table :class="cn('w-full caption-bottom text-sm [&_tr:last-child]:border-0', props.class)">
      <slot />
    </table>
  </div>
</template>
