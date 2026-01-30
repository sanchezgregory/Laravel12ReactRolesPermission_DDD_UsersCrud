import AppLayout from '@/layouts/app-layout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';
import { format } from "date-fns";

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

interface EditPageProps {
    coupon: Coupon;
    users: User[];
}

const breadcrumbs = [
    { title: 'Home', href: '/backoffice/dashboard' },
    { title: 'Coupons', href: '/backoffice/coupons' },
    { title: 'Edit Coupon', href: null },
];

interface CouponFormData {
    [key: string]: any;
    discount_percentage: string;
    expires_at: string;
    max_uses_per_user: string;
    allowed_users_type: string;
    selected_users: number[];
    active: boolean;
}

export default function Edit({ coupon, users }: EditPageProps) {
    const { data, setData, put, processing, errors } = useForm({
        discount_percentage: String(coupon.discount_percentage),
        expires_at: format(new Date(coupon.expires_at), 'yyyy-MM-dd'),
        max_uses_per_user: String(coupon.max_uses_per_user),
        allowed_users_type: coupon.allowed_users_type,
        selected_users: coupon.users.map(u => u.id),
        active: coupon.active,
    }) as any;

    const handleUserToggle = (id: number) => {
        const current = data.selected_users as number[];
        if (current.includes(id)) {
            setData('selected_users', current.filter((uid) => uid !== id));
        } else {
            setData('selected_users', [...current, id]);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        put(route('backoffice.coupons.update', coupon.id));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Coupon ${coupon.code}`} />

            <div className="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
                <Card>
                    <form onSubmit={submit}>
                        <CardHeader>
                            <CardTitle>Edit Coupon: {coupon.code}</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">

                            {/* Discount */}
                            <div className="space-y-2">
                                <Label htmlFor="discount">Discount Percentage</Label>
                                <Select
                                    value={data.discount_percentage}
                                    onValueChange={(val) => setData('discount_percentage', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select discount" />
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
                                <Label htmlFor="expires_at">Expires At</Label>
                                <Input
                                    id="expires_at"
                                    type="date"
                                    value={data.expires_at}
                                    onChange={(e) => setData('expires_at', e.target.value)}
                                />
                                {errors.expires_at && <p className="text-red-500 text-sm">{errors.expires_at}</p>}
                            </div>

                            {/* Max Uses */}
                            <div className="space-y-2">
                                <Label htmlFor="max_uses">Max Uses Per User</Label>
                                <Select
                                    value={data.max_uses_per_user}
                                    onValueChange={(val) => setData('max_uses_per_user', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select limit" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">1 time</SelectItem>
                                        <SelectItem value="2">2 times</SelectItem>
                                        <SelectItem value="3">3 times</SelectItem>
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
                                <Label htmlFor="active">Active</Label>
                            </div>

                            {/* Target Audience */}
                            <div className="space-y-2">
                                <Label htmlFor="allowed_users_type">Target Audience</Label>
                                <Select
                                    value={data.allowed_users_type}
                                    onValueChange={(val) => setData('allowed_users_type', val)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select target" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Users</SelectItem>
                                        <SelectItem value="new_users">New Users Only</SelectItem>
                                        <SelectItem value="selected">Selected Users</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.allowed_users_type && <p className="text-red-500 text-sm">{errors.allowed_users_type}</p>}
                            </div>

                            {/* Selected Users */}
                            {data.allowed_users_type === 'selected' && (
                                <div className="space-y-2">
                                    <Label>Select Users</Label>
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
                                {processing ? 'Updating...' : 'Update Coupon'}
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}
