import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

// Importando componentes de Shadcn
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Definiendo tipos para las props
interface CreatePageProps {
    roles: string[];
}
const breadcrumbs = [
    {
        title: 'Inicio',
        href: route('backoffice.dashboard'),
    },
    {
        title: 'Gestión de Usuarios',
        href: route('backoffice.users.index'),
    },
    {
        title: 'Crear Nuevo Usuario', // Página actual
        href: null,
    },
]

export default function Create({ roles: allRoles }: CreatePageProps) {
    // useForm con el estado inicial para un nuevo usuario
    const { data, setData, post, processing } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: [], // Empieza sin roles seleccionados
    });

    // Manejador para los checkboxes de roles
    const handleRoleChange = (role: string) => {
        const currentRoles = data.roles;
        if (currentRoles.includes(role)) {
            setData('roles', currentRoles.filter((r) => r !== role));
        } else {
            setData('roles', [...currentRoles, role]);
        }
    };

    // Manejador para el envío del formulario
    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('backoffice.users.store'));
    };

    return (
        <AppLayout
            breadcrumbs={breadcrumbs}
        >
            <Head title="Crear Usuario" />

            <div className="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
                <Card>
                    <form onSubmit={submit}>
                        <CardHeader>
                            <CardTitle>Crear Nuevo Usuario</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Campo Nombre */}
                            <div className="space-y-2">
                                <Label htmlFor="name">Nombre</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    autoFocus
                                />
                            </div>

                            {/* Campo Email */}
                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                />
                            </div>
                            
                            {/* Campo Contraseña */}
                            <div className="space-y-2">
                                <Label htmlFor="password">Contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                           </div>
                            
                            {/* Campo Confirmar Contraseña */}
                            <div className="space-y-2">
                                <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
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
                                {processing ? 'Creando...' : 'Crear Usuario'}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}