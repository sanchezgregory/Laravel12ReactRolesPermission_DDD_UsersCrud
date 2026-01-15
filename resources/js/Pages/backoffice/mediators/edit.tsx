import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import MediatorForm from './MediatorForm';

interface Props {
    mediator: any;
}

export default function Edit({ mediator }: Props) {
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
            title: 'Edit',
            href: route('backoffice.mediators.edit', mediator.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Mediator" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Mediator: {mediator.name}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <MediatorForm mediator={mediator} isEditing />
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
