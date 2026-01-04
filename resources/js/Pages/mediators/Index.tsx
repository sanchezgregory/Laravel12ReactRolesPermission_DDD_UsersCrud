import PublicLayout from "@/layouts/public-layout";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Head } from "@inertiajs/react";
import { useMemo, useState } from "react";

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
    mediators: Mediator[];
    auth?: {
        user?: {
            id: number;
            name: string;
            email: string;
            avatar?: string;
        } | null;
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

export default function MediatorsIndex({ mediators, auth }: PageProps) {
    const isLoggedIn = !!auth?.user;
    const [q, setQ] = useState("");

    const filtered = useMemo(() => {
        const query = q.trim().toLowerCase();
        if (!query) return mediators;
        return mediators.filter((m) =>
            [m.name, m.email, m.headline, m.bio].filter(Boolean).join(" ").toLowerCase().includes(query)
        );
    }, [q, mediators]);

    const [loadingId, setLoadingId] = useState<number | null>(null);

    async function pay(m: Mediator) {
        if (!isLoggedIn) {
            window.location.href = "/login";
            return;
        }

        setLoadingId(m.id);
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
                    mediator_id: m.id,
                    amount_minor: m.session_price_minor,
                    currency: m.currency,
                    topic: `Session with ${m.name}`,
                    metadata: { source: "mediators_index" },
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
            setLoadingId(null);
        }
    }

    return (
        <PublicLayout>
            <Head title="Mediadores" />

            <div className="mx-auto max-w-6xl space-y-8 px-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight text-foreground">Encuentra tu Mediador</h1>
                        <p className="mt-2 text-muted-foreground">
                            Explora profesionales, revisa su disponibilidad y agenda tu sesión de manera segura.
                        </p>
                    </div>

                    <div className="w-full sm:w-80">
                        <Input
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            placeholder="Buscar mediador..."
                            className="bg-background"
                        />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {filtered.map((m) => (
                        <Card key={m.id} className="flex flex-col justify-between overflow-hidden border-border/50 bg-card transition-all hover:border-primary/50 hover:shadow-md">
                            <CardHeader>
                                <div className="flex items-start justify-between gap-2">
                                    <div className="space-y-1">
                                        <CardTitle className="text-xl font-bold">{m.name}</CardTitle>
                                        <CardDescription className="line-clamp-2 text-sm font-medium text-foreground/80">
                                            {m.headline ?? "Mediador certificado"}
                                        </CardDescription>
                                    </div>
                                    {m.calendly_url && (
                                        <Badge variant="secondary" className="shrink-0 text-[10px] uppercase">
                                            Disponible
                                        </Badge>
                                    )}
                                </div>
                            </CardHeader>

                            <CardContent className="space-y-6">
                                <div className="text-sm text-muted-foreground line-clamp-4 leading-relaxed">
                                    {m.bio ?? "Sin biografía disponible."}
                                </div>

                                <div className="flex items-end justify-between border-t pt-4">
                                    <div>
                                        <div className="text-xs text-muted-foreground">Precio por sesión</div>
                                        <div className="text-xl font-bold text-primary">
                                            {formatPrice(m.session_price_minor, m.currency)}
                                        </div>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        className="w-full"
                                        disabled={!m.calendly_url}
                                        onClick={() => {
                                            if (m.calendly_url) window.open(m.calendly_url, "_blank", "noopener,noreferrer");
                                        }}
                                    >
                                        Ver Calendly
                                    </Button>

                                    <Button
                                        type="button"
                                        className="w-full"
                                        onClick={() => pay(m)}
                                        disabled={loadingId === m.id}
                                    >
                                        {loadingId === m.id ? "Procesando…" : "Agendar Sesión"}
                                    </Button>
                                </div>
                                <p className="text-[10px] text-muted-foreground text-center">
                                    * El agendado se habilita tras el pago.
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {filtered.length === 0 && (
                    <div className="flex h-40 flex-col items-center justify-center rounded-xl border border-dashed text-muted-foreground">
                        <p>No se encontraron mediadores.</p>
                        <Button variant="link" onClick={() => setQ("")} className="mt-2">
                            Limpiar búsqueda
                        </Button>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
