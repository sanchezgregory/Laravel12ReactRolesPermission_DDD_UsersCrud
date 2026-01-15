import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface Session {
    id: number;
    topic?: string;
    client_name: string;
    status: string;
    created_at: string;
}

interface Props {
    sessions: Session[];
}

export default function Sessions({ sessions }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/backoffice/dashboard',
        },
        {
            title: 'My Sessions',
            href: route('backoffice.mediator.sessions'),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Sessions" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>My Sessions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Topic</TableHead>
                                    <TableHead>Client</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {sessions.map((session) => (
                                    <TableRow key={session.id}>
                                        <TableCell className="font-medium">
                                            {session.topic || 'General Session'}
                                        </TableCell>
                                        <TableCell>
                                            {session.client_name || 'Unknown'}
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                className={`px-2 py-1 rounded-full text-xs font-semibold ${session.status === 'paid'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-gray-100 text-gray-800'
                                                    }`}
                                            >
                                                {session.status === 'paid' ? 'Scheduled' : session.status}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            {new Date(session.created_at).toLocaleDateString()}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {sessions.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center">
                                            No sessions found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
