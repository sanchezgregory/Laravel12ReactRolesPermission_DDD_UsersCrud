import PublicLayout from "@/layouts/public-layout";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Head } from "@inertiajs/react";
import { useState, useEffect } from "react";
import { CheckCircle2 } from "lucide-react";

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
};

function formatPrice(amountMinor: number, currency: string) {
    const amount = amountMinor / 100;
    try {
        return new Intl.NumberFormat("es-ES", { style: "currency", currency }).format(amount);
    } catch {
        return `${amount.toFixed(2)} ${currency}`;
    }
}

export default function MediatorShow({ mediator, auth }: PageProps) {
    const isLoggedIn = !!auth?.user;
    const [loading, setLoading] = useState(false);
    const [hasPaid, setHasPaid] = useState(false);

    // Check query param for payment success
    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        if (params.get("success") === "true" || params.get("payment_success") === "1") {
            setHasPaid(true);
        }
    }, []);

    async function pay() {
        if (!isLoggedIn) {
            window.location.href = "/login";
            return;
        }

        setLoading(true);
        try {
            const res = await fetch("/payments/checkout", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? "",
                },
                body: JSON.stringify({
                    method: "stripe",
                    mediator_id: mediator.id,
                    amount_minor: mediator.session_price_minor,
                    currency: mediator.currency,
                    topic: `Session with ${mediator.name}`,
                    metadata: { source: "mediator_show" }, // Changed source
                }),
            });

            if (!res.ok) {
                const txt = await res.text();
                throw new Error(txt || "Error creando checkout.");
            }

            const data = await res.json();
            if (!data.redirect_url) throw new Error("No se recibió redirect_url del backend.");

            window.location.href = data.redirect_url;
        } catch (e: any) {
            alert(e?.message ?? "Error inesperado al iniciar el pago.");
        } finally {
            setLoading(false);
        }
    }

    return (
        <PublicLayout>
            <Head title={`Mediador - ${mediator.name}`} />

            <div className="mx-auto max-w-4xl space-y-8 px-4 py-8">
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
                            {mediator.calendly_url && (
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
                                    {hasPaid ? (
                                        <>
                                            <div className="flex items-center gap-2 rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                                                <CheckCircle2 className="size-4" />
                                                <span className="font-medium">Pago realizado</span>
                                            </div>
                                            <Button
                                                type="button"
                                                className="w-full text-lg"
                                                size="lg"
                                                disabled={!mediator.calendly_url}
                                                onClick={() => {
                                                    if (mediator.calendly_url) window.open(mediator.calendly_url, "_blank", "noopener,noreferrer");
                                                }}
                                            >
                                                Agendar en Calendly
                                            </Button>
                                        </>
                                    ) : (
                                        <>
                                            <Button
                                                type="button"
                                                className="w-full text-lg"
                                                size="lg"
                                                onClick={pay}
                                                disabled={loading}
                                            >
                                                {loading ? "Procesando..." : "Pagar Sesión"}
                                            </Button>
                                            <p className="text-xs text-muted-foreground text-center px-2">
                                                Realiza el pago seguro para habilitar el agendamiento.
                                            </p>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </PublicLayout>
    );
}
