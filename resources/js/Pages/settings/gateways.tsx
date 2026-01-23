import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Switch } from '@headlessui/react';
import { useState } from 'react';
import HeadingSmall from '@/components/heading-small';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { cn } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payment Gateways',
        href: '/settings/gateways',
    },
];

type Gateway = {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
};

export default function Gateways({ gateways }: { gateways: Gateway[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Payment Gateways" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Payment Gateways" description="Manage active payment gateways for checkout." />

                    <div className="space-y-4">
                        {gateways.map((gateway) => (
                            <GatewayToggle key={gateway.id} gateway={gateway} />
                        ))}
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}

function GatewayToggle({ gateway }: { gateway: Gateway }) {
    const [loading, setLoading] = useState(false);

    const toggle = () => {
        if (loading) return;
        setLoading(true);
        router.put(
            route('backoffice.settings.gateways.update', gateway.slug),
            {
                is_active: !gateway.is_active,
            },
            {
                preserveScroll: true,
                onFinish: () => setLoading(false),
            }
        );
    };

    return (
        <div className="flex items-center justify-between rounded-lg border p-4">
            <div className="space-y-0.5">
                <div className="text-base font-medium">{gateway.name}</div>
                <div className="text-sm text-muted-foreground">
                    {gateway.is_active ? 'Enabled' : 'Disabled'}
                </div>
            </div>
            <Switch
                checked={!!gateway.is_active}
                onChange={toggle}
                disabled={loading}
                className={cn(
                    !!gateway.is_active ? 'bg-primary' : 'bg-input',
                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background'
                )}
            >
                <span
                    className={cn(
                        !!gateway.is_active ? 'translate-x-5' : 'translate-x-0',
                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-background shadow-lg ring-0 transition duration-200 ease-in-out'
                    )}
                />
            </Switch>
        </div>
    );
}
