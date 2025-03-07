<script setup lang="ts">
import { Dialog, DialogContent } from '@/components/ui/dialog';

interface Props {
  open?: boolean;
}

defineProps<Props>();
defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();
</script>

<template>
  <Dialog :open="open" @update:open="$emit('update:open', $event)">
    <DialogContent class="overflow-hidden p-0">
      <slot />
    </DialogContent>
  </Dialog>
</template> 