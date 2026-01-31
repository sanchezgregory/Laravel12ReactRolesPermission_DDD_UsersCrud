import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

// Shadcn
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

interface User {
    id: number;
    name: string;
    email: string;
}

interface CreatePageProps {
    users: User[];
}

const breadcrumbs = [
    {
        title: 'Inicio',
        href: '/backoffice/dashboard',
    },
    {
        title: 'Cupones',
        href: '/backoffice/coupons',
    },
    {
        title: 'Crear Cupón',
        href: null,
    },
];

interface CouponFormData {
    [key: string]: any;
    code: string;
    discount_percentage: string;
    expires_at: string;
    max_uses_per_user: string;
    allowed_users_type: string;
    selected_users: number[];
    active: boolean;
}

export default function Create({ users }: CreatePageProps) {
    const { data, setData, post, processing, errors } = useForm({
        code: '',
        discount_percentage: '25',
        expires_at: '',
        max_uses_per_user: '1',
        allowed_users_type: 'all',
        selected_users: [] as number[],
        active: true,
    }) as any;

    const handleUserToggle = (cityId: number) => {
        const current = data.selected_users as number[];
        if (current.includes(cityId)) {
            setData('selected_users', current.filter((id) => id !== cityId));
        } else {
            setData('selected_users', [...current, cityId]);
        }
    };

    const generateRandomCode = () => {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 6; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        setData('code', result);
    };

    const handleClearSelection = () => {
        setData(data => ({
            ...data,
            selected_users: [],
            allowed_users_type: 'all'
        }));
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('backoffice.coupons.store'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Crear Cupón" />

            <div className="w-full max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
                <Card>
                    <form onSubmit={submit}>
                        <CardHeader>
                            <CardTitle>Crear Nuevo Cupón</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Code */}
                            {/* Code */}
                            <div className="space-y-2">
                                <Label htmlFor="code">Código (Opcional, 6 carac. mayúsculas)</Label>
                                <div className="flex gap-2">
                                    <Input
                                        id="code"
                                        value={data.code}
                                        onChange={(e) => setData('code', e.target.value.toUpperCase())}
                                        maxLength={6}
                                        placeholder="Dejar vacío para autogenerar"
                                        className="flex-1"
                                    />
                                    <Button type="button" variant="outline" onClick={generateRandomCode}>
                                        Generar
                                    </Button>
                                </div>
                                {errors.code && <p className="text-red-500 text-sm">{errors.code}</p>}
                            </div>

                            {/* Discount */}
                            <div className="space-y-2">
                                <Label htmlFor="discount">Porcentaje de Descuento</Label>
                                <Select
                                    value={data.discount_percentage}
                                    onValueChange={(val) => setData('discount_percentage', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar descuento" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="25">25%</SelectItem>
                                        <SelectItem value="50">50%</SelectItem>
                                        <SelectItem value="75">75%</SelectItem>
                                        <SelectItem value="100">100%</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.discount_percentage && <p className="text-red-500 text-sm">{errors.discount_percentage}</p>}
                            </div>

                            {/* Expires At */}
                            <div className="space-y-2">
                                <Label htmlFor="expires_at">Expira el</Label>
                                <Input
                                    id="expires_at"
                                    type="date"
                                    min={new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split('T')[0]}
                                    value={data.expires_at}
                                    onChange={(e) => setData('expires_at', e.target.value)}
                                    onClick={(e) => {
                                        try {
                                            if ('showPicker' in e.currentTarget) {
                                                (e.currentTarget as any).showPicker();
                                            }
                                        } catch (error) {
                                            // Fallback or ignore
                                        }
                                    }}
                                />
                                {errors.expires_at && <p className="text-red-500 text-sm">{errors.expires_at}</p>}
                            </div>

                            {/* Max Uses */}
                            <div className="space-y-2">
                                <Label htmlFor="max_uses">Usos Máximos por Usuario</Label>
                                <Select
                                    value={data.max_uses_per_user}
                                    onValueChange={(val) => setData('max_uses_per_user', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar límite" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">1 vez</SelectItem>
                                        <SelectItem value="2">2 veces</SelectItem>
                                        <SelectItem value="3">3 veces</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.max_uses_per_user && <p className="text-red-500 text-sm">{errors.max_uses_per_user}</p>}
                            </div>

                            {/* Active */}
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="active"
                                    checked={data.active}
                                    onCheckedChange={(checked) => setData('active', checked === true)}
                                />
                                <Label htmlFor="active">Activo</Label>
                            </div>

                            {/* Target Audience */}
                            <div className="space-y-2">
                                <Label htmlFor="allowed_users_type">Audiencia Objetivo</Label>
                                <Select
                                    value={data.allowed_users_type}
                                    onValueChange={(val) => setData('allowed_users_type', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccionar objetivo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Todos los Usuarios</SelectItem>
                                        <SelectItem value="new_users">Solo Nuevos Usuarios</SelectItem>
                                        <SelectItem value="selected">Usuarios Seleccionados</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.allowed_users_type && <p className="text-red-500 text-sm">{errors.allowed_users_type}</p>}
                            </div>

                            {/* Selected Users */}
                            {data.allowed_users_type === 'selected' && (
                                <div className="space-y-2">
                                    <div className="flex justify-between items-center">
                                        <Label>Seleccionar Usuarios</Label>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={handleClearSelection}
                                        >
                                            Limpiar Selección
                                        </Button>
                                    </div>
                                    <div className="space-y-2 rounded-md border p-4 max-h-60 overflow-y-auto">
                                        {users.map((user) => (
                                            <div key={user.id} className="flex items-center space-x-2">
                                                <Checkbox
                                                    id={`user-${user.id}`}
                                                    checked={data.selected_users.includes(user.id)}
                                                    onCheckedChange={() => handleUserToggle(user.id)}
                                                />
                                                <Label htmlFor={`user-${user.id}`} className="font-normal cursor-pointer">
                                                    {user.name} ({user.email})
                                                </Label>
                                            </div>
                                        ))}
                                    </div>
                                    {errors.selected_users && <p className="text-red-500 text-sm">{errors.selected_users}</p>}
                                </div>
                            )}

                        </CardContent>
                        <CardFooter>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Creando...' : 'Crear Cupón'}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}
