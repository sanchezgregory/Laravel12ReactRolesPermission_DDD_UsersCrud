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
    available_coupons?: { code: string, discount_percentage: number, expires_at: string }[];
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
    available_payment_methods: string[];
}; // Close PageProps

function formatPrice(amountMinor: number, currency: string) {
    const amount = amountMinor / 100;
    try {
        return new Intl.NumberFormat("es-ES", { style: "currency", currency }).format(amount);
    } catch {
        return `${amount.toFixed(2)} ${currency}`;
    }
}

export default function MediatorShow({ mediator, auth, current_session, other_active_session, available_payment_methods, available_coupons = [], errors: serverErrors }: PageProps) {
    const { flash } = usePage<PageProps>().props;
    const isLoggedIn = !!auth?.user;
    const [loading, setLoading] = useState(false);
    const [calendlyUrl, setCalendlyUrl] = useState<string | null>(mediator.calendly_url ?? null);

    // Determines if payment modal should be shown
    const [showPaymentModal, setShowPaymentModal] = useState(false);

    // Determine if we show schedule (paid session exists)
    const hasActivePayment = !!current_session;
    const isAlreadyScheduled = !!current_session?.scheduled_at;

    const [showSchedule, setShowSchedule] = useState(hasActivePayment);

    // Coupon State
    const [couponCode, setCouponCode] = useState("");
    const [couponResult, setCouponResult] = useState<{ valid: boolean, coupon: { code: string, discount_percentage: number }, message: string } | null>(null);
    const [couponError, setCouponError] = useState<string | null>(null);
    const [validatingCoupon, setValidatingCoupon] = useState(false);

    // Modal for submitting scheduled session
    const [showScheduleModal, setShowScheduleModal] = useState(false);

    // If backend returns the specific "already scheduled" error, treat as already scheduled locally for UI
    const hasAlreadyScheduledError = serverErrors.error === "No se encontró una sesión pagada pendiente de agendar.";
    const isReadOnly = isAlreadyScheduled || hasAlreadyScheduledError;

    // Participants State for Form
    const { data, setData, post, processing, errors, reset } = useForm({
        mediator_id: mediator.id,
        scheduled_at: '',
        notes: '',
        participants: [] as { email: string }[],
    });

    // Helper to add participant
    const addParticipant = () => {
        if (data.participants.length < 5) {
            setData('participants', [...data.participants, { email: '' }]);
        }
    };

    // Helper to remove participant
    const removeParticipant = (index: number) => {
        const newParticipants = [...data.participants];
        newParticipants.splice(index, 1);
        setData('participants', newParticipants);
    };

    // Helper to update participant email
    const updateParticipantEmail = (index: number, email: string) => {
        const newParticipants = [...data.participants];
        newParticipants[index].email = email;
        setData('participants', newParticipants);
    };


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

        if (hasAlreadyScheduledError) {
            setShowScheduleModal(true);
        }

    }, [hasActivePayment, current_session, hasAlreadyScheduledError]);

    // Function to apply a selected coupon directly
    async function applyCoupon(code: string) {
        setCouponCode(code);
        // We need to trigger validation immediately or just set it?
        // Better to validate via backend to be sure.
        // But we can simplify by setting state and calling validate.
        // We'll call validations logic but with specific code.
        await validateCouponInternal(code);
    }

    async function validateCouponInternal(code: string) {
        setValidatingCoupon(true);
        setCouponError(null);
        setCouponResult(null);

        try {
            const response = await fetch(route('coupons.validate'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                },
                body: JSON.stringify({ coupon_code: code }),
            });

            const responseData = await response.json();

            if (response.ok && responseData.valid) {
                setCouponResult(responseData);
                // IF 100% DISCOUNT, AUTO PAY logic?
                // Requirements say: "redimir... y si elighe un cupon de 100%, ya se debe marcar la sesion como pagada"
                // So if valid & 100%, we should show a specific "Redeem Free Session" button OR auto-trigger?
                // A UI step "Confirm redemption" is safer than auto-triggering on type.
            } else {
                setCouponError(responseData.message || 'Cupón inválido');
            }
        } catch (error) {
            console.error(error);
            setCouponError('Error al validar el cupón');
        } finally {
            setValidatingCoupon(false);
        }
    }

    async function handleValidateCoupon() {
        await validateCouponInternal(couponCode);
    }

    function handlePayClick() {
        // Always show modal to allow coupon selection
        setShowPaymentModal(true);
    }

    function processPayment(method: string) {
        setLoading(true);
        router.post(route('payments.checkout'), {
            mediator_id: mediator.id,
            amount_minor: mediator.session_price_minor,
            currency: mediator.currency,
            gateway: method,
            method: 'card', // Generic default
            topic: `Session with ${mediator.name}`,
            metadata: { source: 'mediator_show' },
            coupon_code: couponResult && couponResult?.valid ? couponResult.coupon.code : null, // Use result code to be safe
        }, {
            onFinish: () => {
                setLoading(false);
                setShowPaymentModal(false);
            },
            onError: (errors) => {
                console.error("Payment Error:", errors);
                alert("Hubo un error al iniciar el pago. Revisa los mensajes o intenta nuevamente.");
                setLoading(false);
            }
        });
    }

    function processFreeRedemption() {
        // For 100% coupons, we basically 'pay' with any gateway trigger but the backend handles zero amount.
        // We can reuse processPayment with a dummy gateway or just 'stripe' as it will be bypassed.
        // OR we just send 'free' as gateway?
        // Backend `GeneralSessionPaymentService` expects a valid gateway to instantiation factory initially?
        // Actually line 32 in Service: `createCheckout(array $data, string $gatewaySlug)`
        // If amount is 0, it calls `markPaid` and returns before factory.
        // So we can send 'stripe' or any valid slug so validation passes.
        // Let's use available_payment_methods[0] or 'manual'.
        const gateway = available_payment_methods.length > 0 ? available_payment_methods[0] : 'stripe';
        processPayment(gateway);
    }

    // ... handleSubmitSchedule logic ...
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
                {/* ... existing warning modal content ... */}
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

            {/* Payment Selection Modal */}
            <Dialog open={showPaymentModal} onOpenChange={setShowPaymentModal}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Monto a Pagar</DialogTitle>
                        <DialogDescription>
                            Total: <span className="font-bold text-foreground text-lg">{formatPrice(mediator.session_price_minor, mediator.currency)}</span>
                        </DialogDescription>
                    </DialogHeader>

                    {/* Coupon Section */}
                    <div className="space-y-4 pt-2">
                        {/* List Available Coupons if any */}
                        {available_coupons.length > 0 && !couponResult && (
                            <div className="space-y-2">
                                <Label className="text-xs font-semibold uppercase text-muted-foreground">Cupones Disponibles</Label>
                                <div className="grid gap-2">
                                    {available_coupons.map(coupon => (
                                        <div key={coupon.code} className="flex items-center justify-between rounded-md border p-3 bg-muted/30">
                                            <div>
                                                <p className="font-medium text-sm">{coupon.code}</p>
                                                <p className="text-xs text-muted-foreground">{coupon.discount_percentage}% OFF</p>
                                            </div>
                                            <Button size="sm" variant="outline" onClick={() => applyCoupon(coupon.code)}>Usar</Button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div className="space-y-2">
                            <Label>¿Tienes un código?</Label>
                            <div className="flex gap-2">
                                <Input
                                    placeholder="Código de cupón"
                                    value={couponCode}
                                    onChange={(e) => setCouponCode(e.target.value.toUpperCase())}
                                    disabled={!!couponResult || validatingCoupon}
                                />
                                {couponResult ? (
                                    <Button
                                        variant="ghost"
                                        onClick={() => {
                                            setCouponResult(null);
                                            setCouponCode("");
                                        }}
                                    >
                                        Quitar
                                    </Button>
                                ) : (
                                    <Button
                                        variant="secondary"
                                        onClick={handleValidateCoupon}
                                        disabled={!couponCode || validatingCoupon}
                                    >
                                        {validatingCoupon ? "Validando..." : "Aplicar"}
                                    </Button>
                                )}
                            </div>
                            {couponError && <p className="text-sm text-red-500">{couponError}</p>}
                            {couponResult && (
                                <div className="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400 space-y-1">
                                    <p className="font-semibold flex items-center gap-2">
                                        <CheckCircle2 className="w-4 h-4" />
                                        <span>¡Cupón aplicado!</span>
                                    </p>
                                    <div className="flex justify-between items-center pt-1 border-t border-green-200 dark:border-green-800 mt-2">
                                        <span>Total con descuento ({couponResult.coupon.discount_percentage}% OFF):</span>
                                        <span className="font-bold text-lg">
                                            {formatPrice(Math.round(mediator.session_price_minor * (1 - couponResult.coupon.discount_percentage / 100)), mediator.currency)}
                                        </span>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="space-y-4 py-4 pt-0">
                        {/* If 100% discount, show FREE button only */}
                        {couponResult && couponResult.coupon.discount_percentage === 100 ? (
                            <Button
                                className="w-full text-lg h-14 bg-green-600 hover:bg-green-700 text-white"
                                onClick={processFreeRedemption}
                                disabled={loading}
                            >
                                {loading ? "Procesando..." : "Confirmar Sesión Gratis"}
                            </Button>
                        ) : (
                            <>
                                <Label className="text-xs font-semibold uppercase text-muted-foreground mt-4 block">Método de Pago</Label>
                                {available_payment_methods.map((method: string) => (
                                    <Button
                                        key={method}
                                        variant="outline"
                                        className="w-full justify-start h-14"
                                        onClick={() => processPayment(method)}
                                        disabled={loading}
                                    >
                                        {method === 'stripe' && (
                                            <>
                                                <span className="font-semibold text-lg">Tarjeta de Crédito / Débito (Stripe)</span>
                                            </>
                                        )}
                                        {method === 'mercadopago' && (
                                            <>
                                                <span className="font-semibold text-lg">Mercado Pago / Efectivo</span>
                                            </>
                                        )}
                                        {!['stripe', 'mercadopago'].includes(method) && method}
                                    </Button>
                                ))}
                            </>
                        )}
                    </div>
                </DialogContent>
            </Dialog>

            {/* Schedule submission modal */}
            <Dialog open={showScheduleModal} onOpenChange={setShowScheduleModal}>
                {/* ... existing schedule modal content ... */}
                <DialogContent className="sm:max-w-lg">
                    <form onSubmit={handleSubmitSchedule}>
                        <DialogHeader>
                            <DialogTitle>
                                {isReadOnly ? "Sesión Agendada" : "Registrar Sesión Agendada"}
                            </DialogTitle>
                            <DialogDescription className="pt-2">
                                {isReadOnly
                                    ? "Esta sesión ya ha sido agendada."
                                    : "Confirma los detalles de tu sesión y agrega participantes."}
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
                                <Label htmlFor="scheduled_at">Fecha y Hora (Seleccionada en Calendly) {isReadOnly ? '' : '*'}</Label>
                                <SchedulerInput
                                    id="scheduled_at"
                                    value={data.scheduled_at}
                                    onChange={(e) => setData('scheduled_at', e.target.value)}
                                    readOnly={isReadOnly}
                                    disabled={isReadOnly}
                                    className="w-full"
                                    min={isReadOnly ? undefined : undefined}
                                />
                                {errors.scheduled_at && (
                                    <p className="text-sm text-red-600">{errors.scheduled_at}</p>
                                )}
                            </div>

                            {/* Participants Section */}
                            <div className="space-y-3 border-t pt-4">
                                <div className="flex items-center justify-between">
                                    <Label>Participantes (Emails)</Label>
                                    {!isReadOnly && data.participants.length < 5 && (
                                        <Button type="button" variant="ghost" size="sm" onClick={addParticipant} className="h-8 text-xs">
                                            + Agregar Email
                                        </Button>
                                    )}
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Agrega hasta 5 correos electrónicos de las personas que asistirán.
                                </p>

                                {data.participants.map((participant, index) => (
                                    <div key={index} className="flex gap-2">
                                        <Input
                                            placeholder={`Email participante ${index + 1}`}
                                            value={participant.email}
                                            onChange={(e) => updateParticipantEmail(index, e.target.value)}
                                            type="email"
                                            disabled={isReadOnly}
                                            required
                                        />
                                        {!isReadOnly && (
                                            <Button type="button" variant="ghost" size="icon" onClick={() => removeParticipant(index)}>
                                                <span className="sr-only">Eliminar</span>
                                                <span aria-hidden="true" className="text-lg text-red-500">×</span>
                                            </Button>
                                        )}
                                        {/* Error for specific index if needed, but errors bag usually returns array format like 'participants.0.email' */}
                                        {errors[`participants.${index}.email`] && (
                                            <p className="text-xs text-red-600">{errors[`participants.${index}.email`]}</p>
                                        )}
                                    </div>
                                ))}
                                {data.participants.length === 0 && !isReadOnly && (
                                    <Button type="button" variant="outline" size="sm" onClick={addParticipant} className="w-full border-dashed">
                                        Agregar Participante
                                    </Button>
                                )}
                            </div>

                            <div className="space-y-2 border-t pt-4">
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
                {/* ... existing header and card structure ... */}
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
                        {/* ... */}
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
                                            {/* ... existing schedule buttons ... */}
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
                                                    onClick={handlePayClick} // Use new handler
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
