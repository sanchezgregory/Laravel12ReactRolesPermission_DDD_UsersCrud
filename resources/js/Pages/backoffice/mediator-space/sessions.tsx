import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { SchedulerInput, dateToUtcInputString } from '@/components/ui/scheduler-input';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { CheckCircle2, Edit, Calendar } from 'lucide-react';

interface Session {
    id: number;
    topic?: string;
    client_name: string;
    email: string;
    status: string;
    created_at: string;
    scheduled_at?: string | null;
    meeting_link?: string | null;
}

interface Props {
    sessions: Session[];
}

export default function Sessions({ sessions }: Props) {
    const [editingSession, setEditingSession] = useState<Session | null>(null);
    const [showEditModal, setShowEditModal] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        session_id: 0,
        scheduled_at: '',
        meeting_link: '',
    });

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

    function handleConfirm(sessionId: number) {
        if (confirm('¿Confirmar que la fecha y hora son correctas?')) {
            post(route('backoffice.mediator.sessions.confirm', sessionId), {
                preserveScroll: true,
            });
        }
    }

    function handleEditClick(session: Session) {
        setEditingSession(session);
        // Parse date and format using UTC components to display exactly what is in DB
        const initialDate = session.scheduled_at
            ? dateToUtcInputString(new Date(session.scheduled_at))
            : '';

        setData({
            session_id: session.id,
            scheduled_at: initialDate,
            meeting_link: session.meeting_link || '',
        });
        setShowEditModal(true);
    }

    function handleEditSubmit(e: React.FormEvent) {
        e.preventDefault();
        post(route('backoffice.mediator.sessions.update-schedule'), {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingSession(null);
                reset();
            },
            preserveScroll: true,
        });
    }

    function formatDateTime(dateString: string | null | undefined) {
        if (!dateString) return '—';
        try {
            return new Date(dateString).toLocaleString('es-ES', {
                dateStyle: 'short',
                timeStyle: 'short',
            });
        } catch {
            return dateString;
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Sessions" />

            {/* Edit Schedule Modal */}
            <Dialog open={showEditModal} onOpenChange={setShowEditModal}>
                <DialogContent className="sm:max-w-md">
                    <form onSubmit={handleEditSubmit}>
                        <DialogHeader>
                            <DialogTitle>Editar Fecha y Hora</DialogTitle>
                            <DialogDescription className="pt-2">
                                Modifica la fecha y hora de la sesión. El cliente recibirá un email con los nuevos datos.
                            </DialogDescription>
                        </DialogHeader>

                        <div className="space-y-4 py-4">
                            <div className="space-y-2">
                                <Label htmlFor="edit_scheduled_at">Nueva Fecha y Hora *</Label>
                                <SchedulerInput
                                    id="edit_scheduled_at"
                                    value={data.scheduled_at}
                                    onChange={(e) => setData('scheduled_at', e.target.value)}
                                    required
                                    className="w-full"
                                />
                                {errors.scheduled_at && (
                                    <p className="text-sm text-red-600">{errors.scheduled_at}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="edit_meeting_link">Link de Reunión (Video Call)</Label>
                                <Input
                                    id="edit_meeting_link"
                                    type="url"
                                    placeholder="https://meet.google.com/..."
                                    value={data.meeting_link}
                                    onChange={(e) => setData('meeting_link', e.target.value)}
                                    className="w-full"
                                />
                                {errors.meeting_link && (
                                    <p className="text-sm text-red-600">{errors.meeting_link}</p>
                                )}
                            </div>

                            {editingSession && (
                                <div className="rounded-md bg-muted p-3 text-sm">
                                    <p className="font-medium">Cliente: {editingSession.client_name}</p>
                                    <p className="text-muted-foreground">{editingSession.email}</p>
                                </div>
                            )}
                        </div>

                        <DialogFooter className="flex-col gap-2 sm:flex-row sm:justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => {
                                    setShowEditModal(false);
                                    setEditingSession(null);
                                }}
                                disabled={processing}
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Guardando...' : 'Guardar Cambios'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

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
                                    <TableHead>Scheduled At</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {sessions.map((session) => (
                                    <TableRow key={session.id}>
                                        <TableCell className="font-medium">
                                            {session.topic || 'General Session'}
                                        </TableCell>
                                        <TableCell>
                                            <div>
                                                <div className="font-medium">{session.client_name || 'Unknown'}</div>
                                                <div className="text-xs text-muted-foreground">{session.email}</div>
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
                                                        ? 'Scheduled'
                                                        : 'Pending Schedule'
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
                                            {session.meeting_link && (
                                                <div className="mt-1 text-xs">
                                                    <a
                                                        href={session.meeting_link}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-blue-600 hover:underline flex items-center gap-1"
                                                    >
                                                        Link Reunión
                                                    </a>
                                                </div>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {session.scheduled_at && (
                                                <div className="flex justify-end gap-2">
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        onClick={() => handleEditClick(session)}
                                                    >
                                                        <Edit className="h-4 w-4 mr-1" />
                                                        Editar
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        onClick={() => handleConfirm(session.id)}
                                                    >
                                                        <CheckCircle2 className="h-4 w-4 mr-1" />
                                                        Confirmar
                                                    </Button>
                                                </div>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {sessions.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center">
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
