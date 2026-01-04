import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

type FormData = {
    email: string;
    password: string;
    remember: boolean;
};

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm<FormData>({
        email: "",
        password: "",
        remember: true,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post("/login", {
            onFinish: () => reset("password"),
        });
    }

    return (
        <>
            <Head title="Login" />
            <div className="min-h-[calc(100vh-2rem)] flex items-center justify-center p-6">
                <Card className="w-full max-w-md rounded-2xl">
                    <CardHeader>
                        <CardTitle>Iniciar sesión</CardTitle>
                        <CardDescription>Accede para pagar y gestionar tus sesiones.</CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-5">
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

                            <div className="space-y-2">
                                <Label htmlFor="password">Contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    autoComplete="current-password"
                                    value={data.password}
                                    onChange={(e) => setData("password", e.target.value)}
                                    required
                                />
                                {errors.password && <p className="text-sm text-red-600">{errors.password}</p>}
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? "Entrando…" : "Entrar"}
                            </Button>
                        </form>

                        <div className="flex items-center justify-between text-sm">
                            <Link href="/forgot-password" className="underline underline-offset-4">
                                ¿Olvidaste tu contraseña?
                            </Link>
                            <Link href="/register" className="underline underline-offset-4">
                                Crear cuenta
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
