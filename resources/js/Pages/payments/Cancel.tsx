import React from "react";
import { Head, Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";

export default function PaymentsCancel() {
    return (
        <>
            <Head title="Pago cancelado" />
            <div className="mx-auto max-w-xl p-6">
                <Card className="rounded-2xl">
                    <CardHeader>
                        <CardTitle>Pago cancelado</CardTitle>
                        <CardDescription>
                            No se completó el pago. Puedes intentarlo de nuevo cuando quieras.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <Button asChild className="w-full">
                            <Link href="/mediators">Volver a mediadores</Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
