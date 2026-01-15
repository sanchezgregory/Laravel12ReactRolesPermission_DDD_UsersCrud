import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import MediatorForm from './MediatorForm';

export default function Create() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Mediators',
            href: route('backoffice.mediators.index'),
        },
        {
            title: 'Create',
            href: route('backoffice.mediators.create'),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Mediator" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Create New Mediator</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <MediatorForm />
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
