import { Button } from '@/components/ui/button';
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useForm } from '@inertiajs/react';
import { format } from "date-fns";
import { FormEventHandler } from 'react';

// Types
interface User {
    id: number;
    name: string;
    email: string;
}

interface Coupon {
    id: number;
    code: string;
    discount_percentage: number;
    expires_at: string;
    max_uses_per_user: number;
    allowed_users_type: string;
    active: boolean;
    users: User[];
}

interface EditCouponFormProps {
    coupon: Coupon;
    users: User[];
    onSuccess: () => void;
    onCancel: () => void;
}

export function EditCouponForm({ coupon, users, onSuccess, onCancel }: EditCouponFormProps) {
    const { data, setData, put, processing, errors } = useForm({
        discount_percentage: String(coupon.discount_percentage),
        expires_at: format(new Date(coupon.expires_at), 'yyyy-MM-dd'),
        max_uses_per_user: String(coupon.max_uses_per_user),
        allowed_users_type: coupon.allowed_users_type,
        selected_users: coupon.users ? coupon.users.map(u => u.id) : [],
        active: coupon.active,
    });

    const handleUserToggle = (id: number) => {
        const current = data.selected_users as number[];
        if (current.includes(id)) {
            setData('selected_users', current.filter((uid) => uid !== id));
        } else {
            setData('selected_users', [...current, id]);
        }
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
        put(route('backoffice.coupons.update', coupon.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <form onSubmit={submit} className="space-y-6">
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

            <div className="flex justify-end gap-2">
                <Button type="button" variant="outline" onClick={onCancel}>
                    Cancel
                </Button>
                <Button type="submit" disabled={processing}>
                    {processing ? 'Actualizando...' : 'Actualizar Cupón'}
                </Button>
            </div>
        </form>
    );
}
