<script setup lang="ts">
import { ref, provide } from 'vue';
import { cn } from '@/lib/utils';

const SIDEBAR_WIDTH = 240;
const SIDEBAR_WIDTH_COLLAPSED = 64;
const SIDEBAR_COOKIE_NAME = 'sidebar-expanded';
const SIDEBAR_COOKIE_MAX_AGE = 60 * 60 * 24 * 365; // 1 year

interface Props {
  defaultOpen?: boolean;
  open?: boolean;
  onOpenChange?: (open: boolean) => void;
  collapsible?: 'icon' | 'hidden';
  side?: 'left' | 'right';
}

const props = withDefaults(defineProps<Props>(), {
  defaultOpen: true,
  collapsible: 'icon',
  side: 'left'
});

const isOpen = ref(props.defaultOpen);

provide('sidebar', {
  isOpen,
  width: SIDEBAR_WIDTH,
  widthCollapsed: SIDEBAR_WIDTH_COLLAPSED,
  toggle: () => {
    isOpen.value = !isOpen.value;
    document.cookie = `${SIDEBAR_COOKIE_NAME}=${isOpen.value}; path=/; max-age=${SIDEBAR_COOKIE_MAX_AGE}`;
  }
});
</script>

<template>
  <aside
    :class="cn(
      'relative flex flex-col gap-4 border-r bg-sidebar-background px-2 py-4 text-sidebar-foreground',
      {
        'w-[var(--sidebar-width)]': isOpen,
        'w-[var(--sidebar-width-collapsed)]': !isOpen && props.collapsible === 'icon',
        'hidden': !isOpen && props.collapsible === 'hidden',
      },
      props.side === 'right' ? 'border-l' : 'border-r'
    )"
    style="
      --sidebar-width: ${SIDEBAR_WIDTH}px;
      --sidebar-width-collapsed: ${SIDEBAR_WIDTH_COLLAPSED}px;
    "
  >
    <slot />
  </aside>
</template>
