<script setup lang="ts">
import { Search } from 'lucide-vue-next';

interface Props {
  class?: string;
  placeholder?: string;
  modelValue?: string;
}

defineProps<Props>();
defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
  <div class="flex items-center border-b px-3" :class="class">
    <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
    <input
      type="text"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
    />
  </div>
</template> 