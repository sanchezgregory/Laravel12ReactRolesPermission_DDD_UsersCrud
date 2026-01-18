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
import { Head, Link } from '@inertiajs/react';
import { Calendar, ExternalLink } from 'lucide-react';
import { Button } from '@/components/ui/button';

interface Session {
    id: number;
    topic?: string;
    mediator_name: string;
    mediator_email: string;
    mediator_id: number;
    status: string;
    created_at: string;
    scheduled_at?: string | null;
    meeting_link?: string | null;
}

interface Props {
    sessions: Session[];
}

export default function MySessions({ sessions }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/backoffice/dashboard',
        },
        {
            title: 'Mis Sesiones',
            href: route('user.sessions'),
        },
    ];

    function formatDateTime(dateString: string | null | undefined) {
        if (!dateString) return '—';
        try {
            // Force UTC timezone to prevent local browser conversion
            return new Date(dateString).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'numeric',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'UTC',
            });
        } catch {
            return dateString;
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mis Sesiones" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Historial de Sesiones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Tema</TableHead>
                                    <TableHead>Mediador</TableHead>
                                    <TableHead>Estado</TableHead>
                                    <TableHead>Fecha Programada</TableHead>
                                    <TableHead className="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {sessions.map((session) => (
                                    <TableRow key={session.id}>
                                        <TableCell className="font-medium">
                                            {session.topic || 'Sesión General'}
                                        </TableCell>
                                        <TableCell>
                                            <div>
                                                <div className="font-medium">{session.mediator_name || 'Desconocido'}</div>
                                                <div className="text-xs text-muted-foreground">{session.mediator_email}</div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                className={`px-2 py-1 rounded-full text-xs font-semibold ${session.status === 'paid'
                                                    ? session.scheduled_at
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                                                    : 'bg-gray-100 text-gray-800'
                                                    }`}
                                            >
                                                {session.status === 'paid'
                                                    ? session.scheduled_at
                                                        ? 'Agendada'
                                                        : 'Pendiente de Agendar'
                                                    : session.status}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center gap-2">
                                                {session.scheduled_at && (
                                                    <Calendar className="h-4 w-4 text-muted-foreground" />
                                                )}
                                                <span className={session.scheduled_at ? 'font-medium' : 'text-muted-foreground'}>
                                                    {formatDateTime(session.scheduled_at)}
                                                </span>
                                            </div>
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <div className="flex justify-end gap-2">
                                                {session.status === 'paid' && !session.scheduled_at && (
                                                    <Button size="sm" asChild>
                                                        <Link href={route('mediators.show', session.mediator_id)}>
                                                            Agendar Ahora <ExternalLink className="ml-2 h-3 w-3" />
                                                        </Link>
                                                    </Button>
                                                )}
                                                {session.meeting_link && (
                                                    <Button size="sm" variant="default" className="bg-blue-600 hover:bg-blue-700 text-white" asChild>
                                                        <a href={session.meeting_link} target="_blank" rel="noopener noreferrer">
                                                            Unirse a Reunión <ExternalLink className="ml-2 h-3 w-3" />
                                                        </a>
                                                    </Button>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {sessions.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center">
                                            No se encontraron sesiones.
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
