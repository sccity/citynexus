import type { Component } from 'vue';
import type { LucideIcon } from 'lucide-vue-next';
import type { PageProps } from '@inertiajs/core';

export interface Auth {
    user?: {
        id: number;
        name: string;
        email: string;
        avatar?: string;
        keycloak_roles?: Array<{
            role_name: string;
        }>;
    };
    user_permissions?: string[];
}

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

export interface NavItemBase {
    title: string;
    icon?: Component;
}

export interface NavItemLink extends NavItemBase {
    href: string;
    permission?: string;
}

export interface NavItemSection extends NavItemBase {
    section: string;
    items: NavItemLink[];
}

export type NavItem = NavItemLink | NavItemSection;

export interface Role {
    id: number;
    name: string;
    display_name: string;
}

export interface KeycloakRole {
    role_name: string;
    role_type: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    keycloak_roles?: KeycloakRole[];
    permissions?: string[];
}

export interface SharedData extends PageProps {
    auth: Auth;
    name: string;
    quote: {
        message: string;
        author: string;
    };
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Deployment {
    name: string;
    namespace: string;
    status: 'healthy' | 'warning' | 'error';
    replicas: {
        total: number;
        available: number;
        unavailable: number;
        updated: number;
    };
}

export interface KubernetesStatus {
    healthy: number;
    warning: number;
    error: number;
    total: number;
    timestamp: string;
    deployments: Deployment[];
}

export interface Dag {
    name: string;
    status: 'success' | 'running' | 'failed';
    lastRun: string;
}

export interface AirflowStatus {
    total: number;
    running: number;
    failed: number;
    dags: Dag[];
}

export interface Website {
    name: string;
    status: 'healthy' | 'error';
    latency: string;
    uptime: string;
}
