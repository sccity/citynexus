<script setup lang="ts">
import { inject, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  MessageSquare, 
  Settings, 
  Vote, 
  Building2, 
  Receipt, 
  Menu,
  User,
  LogOut 
} from 'lucide-vue-next';
import Sidebar from '@/components/ui/sidebar/Sidebar.vue';
import SidebarNav from '@/components/ui/sidebar/SidebarNav.vue';
import SidebarNavItem from '@/components/ui/sidebar/SidebarNavItem.vue';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import type { User as UserType } from '@/types';
import type { PageProps as InertiaPageProps } from '@inertiajs/core';

interface PageProps extends InertiaPageProps {
  auth: {
    user: UserType;
  };
}

const page = usePage<PageProps>();
const { toggle } = inject('sidebar', { toggle: () => {} });
const user = computed(() => page.props.auth.user);

const navigation = [
  {
    title: 'Overview',
    items: [
      {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutDashboard
      },
      {
        title: 'Admin Dashboard',
        href: '/admin-dashboard',
        icon: Settings
      }
    ]
  },
  {
    title: 'Accounting',
    items: [
      {
        title: 'Budgeting Tool',
        href: '/budgeting',
        icon: Receipt
      },
      {
        title: 'Business Licenses',
        href: '/business-licenses',
        icon: Building2
      }
    ]
  },
  {
    title: 'Other Tools',
    items: [
      {
        title: 'Quick Vote',
        href: '/quick-vote',
        icon: Vote
      },
      {
        title: 'GovTxt Config',
        href: route('govtxt-config.index'),
        icon: MessageSquare
      }
    ]
  }
];

const currentRoute = computed(() => page.url);
</script>

<template>
  <Sidebar class="bg-[#e6e6e6] border border-r-[#d1d1d1] border-b-[#d1d1d1] border-t-white border-l-white">
    <div class="flex h-10 items-center bg-gradient-to-r from-[#2b3a67] to-[#4d648d] px-3">
      <div class="flex items-center">
        <span class="font-semibold text-white text-sm tracking-wide">CityNexus</span>
      </div>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button 
            variant="ghost" 
            size="icon" 
            class="h-7 w-7 ml-auto bg-[#e6e6e6]/10 hover:bg-[#e6e6e6]/20 rounded-sm"
            @click="toggle"
          >
            <Menu class="h-4 w-4 text-white/90" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-56">
          <div class="flex items-center gap-2 p-2">
            <div class="flex flex-col space-y-1">
              <p class="text-sm font-medium leading-none">{{ user.name }}</p>
              <p class="text-xs leading-none text-muted-foreground">{{ user.email }}</p>
            </div>
          </div>
          <DropdownMenuSeparator />
          <DropdownMenuItem asChild>
            <Link :href="route('profile.edit')" class="flex items-center">
              <Settings class="mr-2 h-4 w-4" />
              <span>Settings</span>
            </Link>
          </DropdownMenuItem>
          <DropdownMenuItem asChild>
            <Link :href="route('logout')" method="post" class="flex items-center">
              <LogOut class="mr-2 h-4 w-4" />
              <span>Log out</span>
            </Link>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>

    <div class="space-y-1 p-3 bg-gradient-to-b from-[#e6e6e6] to-[#f0f0f0]">
      <div v-for="group in navigation" :key="group.title" class="mb-4">
        <h2 class="mb-2 text-[13px] font-semibold text-[#2b3a67] px-2">
          {{ group.title }}
        </h2>
        <SidebarNav class="grid grid-cols-1 gap-[2px]">
          <SidebarNavItem
            v-for="item in group.items"
            :key="item.title"
            :href="item.href"
            :icon="item.icon"
            :title="item.title"
            :active="currentRoute === item.href"
            class="hover:bg-[#acc2ef]/20 hover:text-[#2b3a67] rounded-sm text-sm font-medium text-[#4a4a4a] px-2 py-1.5 transition-colors"
            :class="{
              'bg-[#acc2ef]/30 text-[#2b3a67] font-semibold': currentRoute === item.href
            }"
          />
        </SidebarNav>
      </div>
    </div>
  </Sidebar>
</template>
