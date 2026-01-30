import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type User } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Briefcase, Calendar, Folder, LayoutGrid, Users, DollarSign, Ticket } from 'lucide-react';
import AppLogo from './app-logo';

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    const { auth } = usePage().props as unknown as { auth: { user: User & { roles: string[] } } };
    const isAdmin = auth.user.roles.includes('admin');
    const isMediator = auth.user.roles.includes('mediator');

    let mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/backoffice/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'My Coupons',
            href: '/backoffice/my-coupons',
            icon: Ticket,
        },
        {
            title: 'Mediadores',
            href: '/mediators',
            icon: Users,
        },
    ];

    if (isAdmin) {
        mainNavItems = [
            ...mainNavItems,
            {
                title: 'Users',
                href: '/backoffice/users',
                icon: Users,
            },
            {
                title: 'Mediators',
                href: '/backoffice/mediators',
                icon: Briefcase,
            },
            {
                title: 'Coupons Manager',
                href: '/backoffice/coupons',
                icon: Ticket,
            },
        ];
    }

    if (isMediator) {
        mainNavItems = [
            ...mainNavItems,
            {
                title: 'My Clients',
                href: '/backoffice/my-clients',
                icon: Users,
            },
            {
                title: 'My Payments',
                href: '/backoffice/my-payments',
                icon: DollarSign,
            },
            {
                title: 'My Sessions',
                href: '/backoffice/my-sessions',
                icon: Calendar,
            },
        ];
    }

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
