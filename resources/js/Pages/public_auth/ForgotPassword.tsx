import React from "react";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

type PageProps = { status?: string | null };

export default function ForgotPassword() {
    const { status } = usePage<PageProps>().props;
    const { data, setData, post, processing, errors } = useForm<{ email: string }>({ email: "" });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post("/forgot-password");
    }

    return (
        <>
            <Head title="Recuperar contraseña" />
            <div className="min-h-[calc(100vh-2rem)] flex items-center justify-center p-6">
                <Card className="w-full max-w-md rounded-2xl">
                    <CardHeader>
                        <CardTitle>Recuperar contraseña</CardTitle>
                        <CardDescription>
                            Te enviaremos un email con el enlace para restablecerla.
                        </CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-4">
                        {status && <p className="text-sm">{status}</p>}

                        <form onSubmit={submit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    autoComplete="email"
                                    value={data.email}
                                    onChange={(e) => setData("email", e.target.value)}
                                    required
                                />
                                {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? "Enviando…" : "Enviar enlace"}
                            </Button>
                        </form>

                        <div className="text-sm">
                            <Link href="/login" className="underline underline-offset-4">
                                Volver al login
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
