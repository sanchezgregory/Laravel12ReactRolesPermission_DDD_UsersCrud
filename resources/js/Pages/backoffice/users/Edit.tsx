import AppLayout from '@/layouts/app-layout'; // <-- Usando AppLayout
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

// Importando componentes de Shadcn
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Definiendo tipos para las props
interface UserData {
    id: number;
    name: string;
    email: string;
    roles: string[];
}

interface EditPageProps {
    user: UserData;
    roles: string[];
}

export default function Edit({ user, roles: allRoles }: EditPageProps) {
    const { data, setData, put, processing } = useForm({
        name: user.name,
        password: '',
        password_confirmation: '',
        roles: user.roles || [],
    });

    const handleRoleChange = (role: string) => {
        const currentRoles = data.roles;
        if (currentRoles.includes(role)) {
            setData('roles', currentRoles.filter((r) => r !== role));
        } else {
            setData('roles', [...currentRoles, role]);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        // Asegúrate que el nombre de la ruta sea el correcto
        put(route('backoffice.users.update', user.id));
    };

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Inicio',
                    href: route('backoffice.dashboard'),
                },
                {
                    title: 'Gestión de Usuarios',
                    href: route('backoffice.users.index'),
                },
                {
                    title: 'Editar Usuario', // Página actual
                    href: null,
                },
            ]}
        >
            <Head title={`Editar ${user.name}`} />

            <div className="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
                <Card>
                    <form onSubmit={submit}>
                        <CardHeader>
                            <CardTitle>Información del Usuario</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Campo Nombre */}
                            <div className="space-y-2">
                                <Label htmlFor="name">Nombre</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                />
                            </div>

                            {/* Campo Email (deshabilitado) */}
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input id="email" value={user.email} disabled />
                                <p className="text-sm text-muted-foreground">El email no se puede modificar.</p>
                            </div>
                            
                            {/* Campo Contraseña */}
                            <div className="space-y-2">
                                <Label htmlFor="password">Nueva Contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                <p className="text-sm text-muted-foreground">Dejar en blanco para no cambiar.</p>
                            </div>
                            
                            {/* Campo Confirmar Contraseña */}
                            <div className="space-y-2">
                                <Label htmlFor="password_confirmation">Confirmar Nueva Contraseña</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                />
                            </div>
                            
                            {/* Selección de Roles */}
                            <div className="space-y-2">
                                <Label>Roles</Label>
                                <div className="space-y-2 rounded-md border p-4">
                                    {allRoles.map((role) => (
                                        <div key={role} className="flex items-center space-x-2">
                                            <Checkbox
                                                id={role}
                                                checked={data.roles.includes(role)}
                                                onCheckedChange={() => handleRoleChange(role)}
                                            />
                                            <Label htmlFor={role} className="font-normal capitalize">{role}</Label>
                                        </div>
                                    ))}
                                </div>
                            </div>

                        </CardContent>
                        <CardFooter>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Guardando...' : 'Guardar Cambios'}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}