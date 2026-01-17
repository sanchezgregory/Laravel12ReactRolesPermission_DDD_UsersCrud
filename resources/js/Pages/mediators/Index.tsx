import PublicLayout from "@/layouts/public-layout";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Head, Link } from "@inertiajs/react";
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

import ActiveSessionsBanner from "@/components/ActiveSessionsBanner";

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

    // Removed unused payment logic defined in Index previously.

    return (
        <PublicLayout>
            <Head title="Mediadores" />
            <ActiveSessionsBanner />
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

                            <CardContent className="flex flex-col justify-between space-y-6">
                                <div className="text-sm text-muted-foreground line-clamp-4 leading-relaxed">
                                    {m.bio ?? "Sin biografía disponible."}
                                </div>

                                <div className="mt-auto space-y-4">
                                    <div className="flex items-end justify-between border-t pt-4">
                                        <div>
                                            <div className="text-xs text-muted-foreground">Precio por sesión</div>
                                            <div className="text-xl font-bold text-primary">
                                                {formatPrice(m.session_price_minor, m.currency)}
                                            </div>
                                        </div>
                                    </div>

                                    <Button asChild className="w-full">
                                        <Link href={`/mediators/${m.id}`}>Agendar Sesión</Link>
                                    </Button>
                                </div>
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
