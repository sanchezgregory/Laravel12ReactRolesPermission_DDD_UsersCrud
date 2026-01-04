import React from "react";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

type PageProps = { token: string; email: string };

type FormData = {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export default function ResetPassword() {
    const { token, email } = usePage<PageProps>().props;

    const { data, setData, post, processing, errors, reset } = useForm<FormData>({
        token,
        email,
        password: "",
        password_confirmation: "",
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post("/reset-password", {
            onFinish: () => reset("password", "password_confirmation"),
        });
    }

    return (
        <>
            <Head title="Restablecer contraseña" />
            <div className="min-h-[calc(100vh-2rem)] flex items-center justify-center p-6">
                <Card className="w-full max-w-md rounded-2xl">
                    <CardHeader>
                        <CardTitle>Restablecer contraseña</CardTitle>
                        <CardDescription>Define una nueva contraseña para tu cuenta.</CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-5">
                        <form onSubmit={submit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData("email", e.target.value)}
                                    required
                                />
                                {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="password">Nueva contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    autoComplete="new-password"
                                    value={data.password}
                                    onChange={(e) => setData("password", e.target.value)}
                                    required
                                />
                                {errors.password && <p className="text-sm text-red-600">{errors.password}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="password_confirmation">Confirmar contraseña</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    autoComplete="new-password"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData("password_confirmation", e.target.value)}
                                    required
                                />
                                {errors.password_confirmation && (
                                    <p className="text-sm text-red-600">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? "Guardando…" : "Guardar contraseña"}
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
