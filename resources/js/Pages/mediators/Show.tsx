import PublicLayout from "@/layouts/public-layout";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Head, router, usePage, useForm } from "@inertiajs/react";
import { useState, useEffect } from "react";
import { CheckCircle2, Calendar } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { SchedulerInput, dateToUtcInputString, dateToLocalInputString } from "@/components/ui/scheduler-input";

import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from "@/components/ui/dialog";

type Mediator = {
    id: number;
    name: string;
    email: string;
    session_price_minor: number;
    currency: string;
    calendly_url?: string | null;
    headline?: string | null;
    bio?: string | null;
};

type PageProps = {
    mediator: Mediator;
    current_session?: {
        id: number;
        mediator_id: number;
        scheduled_at?: string | null;
        [key: string]: any;
    } | null;
    other_active_session?: {
        id: number;
        mediator_id: number;
        mediator_name: string;
        created_at: string;
    } | null;
    auth?: {
        user?: {
            id: number;
            name: string;
            email: string;
            avatar?: string;
        } | null;
    };
    flash?: {
        success?: string;
    };
    errors: {
        error?: string; // Backend error
        [key: string]: any;
    };
};

function formatPrice(amountMinor: number, currency: string) {
    const amount = amountMinor / 100;
    try {
        return new Intl.NumberFormat("es-ES", { style: "currency", currency }).format(amount);
    } catch {
        return `${amount.toFixed(2)} ${currency}`;
    }
}

export default function MediatorShow({ mediator, auth, current_session, other_active_session, errors: serverErrors }: PageProps) {
    const { flash } = usePage<PageProps>().props;
    const isLoggedIn = !!auth?.user;
    const [loading, setLoading] = useState(false);
    const [calendlyUrl, setCalendlyUrl] = useState<string | null>(mediator.calendly_url ?? null);

    // Determine if we show schedule (paid session exists)
    const hasActivePayment = !!current_session;
    const isAlreadyScheduled = !!current_session?.scheduled_at;

    const [showSchedule, setShowSchedule] = useState(hasActivePayment);

    // Modal for submitting scheduled session
    const [showScheduleModal, setShowScheduleModal] = useState(false);

    // If backend returns the specific "already scheduled" error, treat as already scheduled locally for UI
    const hasAlreadyScheduledError = serverErrors.error === "No se encontró una sesión pagada pendiente de agendar.";
    const isReadOnly = isAlreadyScheduled || hasAlreadyScheduledError;

    const { data, setData, post, processing, errors, reset } = useForm({
        mediator_id: mediator.id,
        scheduled_at: '',
        notes: '',
    });

    useEffect(() => {
        if (hasActivePayment) {
            setShowSchedule(true);
        }

        const params = new URLSearchParams(window.location.search);
        const urlFromParams = params.get("calendly_url");
        if (urlFromParams) {
            setCalendlyUrl(urlFromParams);
            setShowSchedule(true);
        }

        // Pre-fill date if already scheduled
        if (current_session?.scheduled_at) {
            const date = new Date(current_session.scheduled_at);
            // Use the shared utility to format UTC "as is"
            setData('scheduled_at', dateToUtcInputString(date));
        }

        // If we got the specific error, and we have opened the modal implicitly or explicit user action,
        // we might want to ensure the modal is open or at least state matches.
        if (hasAlreadyScheduledError) {
            setShowScheduleModal(true);
            // If we don't have the date from current_session (e.g. race condition), we can't guess it easily
            // unless we re-fetch, but typically current_session should be updated on reload.
            // If Inertia reload didn't update current_session yet, we might be stuck. 
            // But ShowController logic updates current_session every request.
        }

    }, [hasActivePayment, current_session, hasAlreadyScheduledError]);

    function handlePay() {
        setLoading(true);
        router.post(route('payments.checkout'), {
            mediator_id: mediator.id,
            amount_minor: mediator.session_price_minor,
            currency: mediator.currency,
            method: 'stripe',
            topic: `Session with ${mediator.name}`,
            metadata: { source: 'mediator_show' },
        }, {
            onFinish: () => setLoading(false),
            onError: (errors) => {
                console.error("Payment Error:", errors);
                alert("Hubo un error al iniciar el pago. Por favor intenta nuevamente.");
            }
        });
    }

    function handleSubmitSchedule(e: React.FormEvent) {
        e.preventDefault();
        post(route('payments.submit-schedule'), {
            onSuccess: () => {
                setShowScheduleModal(false);
                reset();
            },
        });
    }

    const [showWarning, setShowWarning] = useState(!!(other_active_session));

    return (
        <PublicLayout>
            <Head title={`Mediador - ${mediator.name}`} />

            {/* Warning modal for other active sessions */}
            <Dialog open={showWarning} onOpenChange={setShowWarning}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Sesión Activa Detectada</DialogTitle>
                        <DialogDescription className="pt-2">
                            Ya tienes una sesión pagada y activa con <span className="font-semibold text-foreground">{other_active_session?.mediator_name}</span>.
                        </DialogDescription>
                        <DialogDescription>
                            Para continuar, por favor selecciona la agenda de tu sesión pendiente.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter className="flex-col gap-2 sm:flex-row sm:justify-end mt-4">
                        <Button variant="outline" onClick={() => setShowWarning(false)}>
                            Cerrar
                        </Button>
                        {other_active_session && (
                            <Button
                                onClick={() => router.visit(route('mediators.show', other_active_session.mediator_id))}
                                className="w-full sm:w-auto"
                            >
                                Ir a la agenda de {other_active_session.mediator_name}
                            </Button>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Schedule submission modal */}
            <Dialog open={showScheduleModal} onOpenChange={setShowScheduleModal}>
                <DialogContent className="sm:max-w-md">
                    <form onSubmit={handleSubmitSchedule}>
                        <DialogHeader>
                            <DialogTitle>
                                {isReadOnly ? "Sesión Agendada" : "Registrar Sesión Agendada"}
                            </DialogTitle>
                            <DialogDescription className="pt-2">
                                {isReadOnly
                                    ? "Esta sesión ya ha sido agendada."
                                    : "Ingresa la fecha y hora que seleccionaste en Calendly."}
                            </DialogDescription>
                        </DialogHeader>

                        {/* Display Backend Error explicitly in Red if present */}
                        {hasAlreadyScheduledError && (
                            <div className="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400">
                                {serverErrors.error}
                            </div>
                        )}

                        <div className="space-y-4 py-4">
                            <div className="space-y-2">
                                <Label htmlFor="scheduled_at">Fecha y Hora {isReadOnly ? '' : '*'}</Label>
                                <SchedulerInput
                                    id="scheduled_at"
                                    value={data.scheduled_at}
                                    onChange={(e) => setData('scheduled_at', e.target.value)}
                                    readOnly={isReadOnly}
                                    disabled={isReadOnly}
                                    className="w-full"
                                    // min is handled automatically by default (2 hours), but we disable it if readOnly
                                    min={isReadOnly ? undefined : undefined}
                                />
                                {errors.scheduled_at && (
                                    <p className="text-sm text-red-600">{errors.scheduled_at}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="notes">Notas (opcional)</Label>
                                <Textarea
                                    id="notes"
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    placeholder="Agrega cualquier comentario adicional..."
                                    rows={3}
                                    maxLength={500}
                                    disabled={isReadOnly}
                                    className="w-full resize-none"
                                />
                                {errors.notes && (
                                    <p className="text-sm text-red-600">{errors.notes}</p>
                                )}
                            </div>
                        </div>

                        <DialogFooter className="flex-col gap-2 sm:flex-row sm:justify-end">
                            {isReadOnly && (
                                <Button
                                    type="button"
                                    onClick={() => router.visit(route('user.sessions'))}
                                >
                                    Ir a mis sesiones
                                </Button>
                            )}
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setShowScheduleModal(false)}
                                disabled={processing}
                            >
                                {isReadOnly ? "Cerrar" : "Cancelar"}
                            </Button>
                            {!isReadOnly && (
                                <Button type="submit" disabled={processing}>
                                    {processing ? "Enviando..." : "Confirmar Sesión"}
                                </Button>
                            )}
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <div className="mx-auto max-w-4xl space-y-8 px-4 py-8">
                {flash?.success && (
                    <div className="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-300">
                        <div className="flex items-center gap-3">
                            <CheckCircle2 className="h-5 w-5" />
                            <p className="font-medium">{flash.success}</p>
                        </div>
                    </div>
                )}

                <Card className="overflow-hidden border-border/50 bg-card shadow-lg">
                    <CardHeader className="border-b bg-muted/20 px-6 py-8">
                        <div className="flex flex-col items-center gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div className="space-y-2 text-center sm:text-left">
                                <Badge variant="outline" className="mb-2">Mediador Certificado</Badge>
                                <CardTitle className="text-3xl font-bold sm:text-4xl">{mediator.name}</CardTitle>
                                <CardDescription className="text-lg font-medium text-foreground/80">
                                    {mediator.headline ?? "Profesional en resolución de conflictos"}
                                </CardDescription>
                            </div>
                            {calendlyUrl && (
                                <div className="flex flex-col items-center gap-1">
                                    <Badge variant="secondary" className="px-3 py-1 text-xs uppercase tracking-wider">
                                        Disponible
                                    </Badge>
                                </div>
                            )}
                        </div>
                    </CardHeader>

                    <CardContent className="grid gap-8 p-6 sm:grid-cols-3 sm:p-8">
                        <div className="sm:col-span-2 space-y-6">
                            <div>
                                <h3 className="mb-3 text-lg font-semibold">Sobre mí</h3>
                                <div className="text-muted-foreground leading-relaxed whitespace-pre-line">
                                    {mediator.bio ?? "Sin biografía disponible."}
                                </div>
                            </div>
                        </div>

                        <div className="sm:col-span-1">
                            <div className="rounded-xl border bg-card p-6 shadow-sm">
                                <div className="mb-6 space-y-1">
                                    <div className="text-sm text-muted-foreground">Valor por sesión</div>
                                    <div className="text-3xl font-bold text-primary">
                                        {formatPrice(mediator.session_price_minor, mediator.currency)}
                                    </div>
                                </div>

                                <div className="space-y-3">
                                    {showSchedule ? (
                                        <>
                                            <div className="flex items-center gap-2 rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                                                <CheckCircle2 className="size-4" />
                                                <span className="font-medium">Pago realizado</span>
                                            </div>
                                            <Button
                                                type="button"
                                                className="w-full text-lg"
                                                size="lg"
                                                disabled={isReadOnly}
                                                onClick={() => {
                                                    if (calendlyUrl) window.open(calendlyUrl, "_blank", "noopener,noreferrer");
                                                }}
                                            >
                                                {isReadOnly ? "Sesión ya agendada" : "Agendar Sesión"}
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                className="w-full"
                                                size="lg"
                                                onClick={() => {
                                                    // Only calculate default if we don't have a value (or override logic)
                                                    // But logic says: if isReadOnly, we use stored date.
                                                    if (!isReadOnly && !data.scheduled_at) {
                                                        const now = new Date();
                                                        const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
                                                        setData('scheduled_at', dateToLocalInputString(oneHourLater));
                                                    }
                                                    setShowScheduleModal(true);
                                                }}
                                            >
                                                <Calendar className="mr-2 h-4 w-4" />
                                                {isReadOnly ? "Ver fecha agendada" : "Ya agendé mi sesión"}
                                            </Button>
                                        </>
                                    ) : (
                                        <>
                                            {isLoggedIn ? (
                                                <Button
                                                    type="button"
                                                    className="w-full text-lg"
                                                    size="lg"
                                                    onClick={handlePay}
                                                    disabled={loading}
                                                >
                                                    {loading ? "Procesando..." : "Pagar Sesión"}
                                                </Button>
                                            ) : (
                                                <Button
                                                    type="button"
                                                    variant="secondary"
                                                    className="w-full text-lg"
                                                    size="lg"
                                                    onClick={() => router.get(route('login'))}
                                                >
                                                    Inicie sesión
                                                </Button>
                                            )}
                                            <p className="text-xs text-muted-foreground text-center px-2">
                                                Realiza el pago seguro para habilitar el agendamiento.
                                            </p>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card >
            </div >
        </PublicLayout >
    );
}
