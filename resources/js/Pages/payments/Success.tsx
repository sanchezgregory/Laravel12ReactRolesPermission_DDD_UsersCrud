import React from "react";
import { Head, Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";

export default function PaymentsSuccess() {
    return (
        <>
            <Head title="Pago exitoso" />
            <div className="mx-auto max-w-xl p-6">
                <Card className="rounded-2xl">
                    <CardHeader>
                        <CardTitle>Pago exitoso</CardTitle>
                        <CardDescription>
                            Recibimos tu pago. En breve se confirmará por webhook y se habilitará el agendado.
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
