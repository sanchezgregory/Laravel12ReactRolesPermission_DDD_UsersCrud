import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import AppLayout from '@/layouts/app-layout';
import { DataTable } from '@/pages/backoffice/users/data-table';
import { Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { EditCouponForm } from './EditCouponForm';
import { Coupon, getColumns } from './columns';

interface User {
    id: number;
    name: string;
    email: string;
}

interface IndexPageProps {
    coupons: {
        data: Coupon[];
    };
    users: User[];
}

export default function Index({ coupons, users }: IndexPageProps) {
    const couponsData = coupons.data;
    const [editingCoupon, setEditingCoupon] = useState<Coupon | null>(null);

    const columns = useMemo(() => getColumns({
        onEdit: (coupon) => setEditingCoupon(coupon)
    }), []);

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Home',
                    href: '/backoffice/dashboard',
                },
                {
                    title: 'Coupons',
                    href: '/backoffice/coupons',
                },
            ]}
        >
            <Head title="Coupons" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="font-semibold text-xl text-foreground leading-tight">
                            Coupons Management
                        </h2>
                        <Link href={route('backoffice.coupons.create')}>
                            <Button>Create Coupon</Button>
                        </Link>
                    </div>

                    <div className="bg-card text-card-foreground rounded-lg border shadow-sm">
                        <div className="p-6">
                            <DataTable columns={columns} data={couponsData} />
                        </div>
                    </div>
                </div>
            </div>

            <Dialog open={!!editingCoupon} onOpenChange={(open) => !open && setEditingCoupon(null)}>
                <DialogContent className="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Edit Coupon</DialogTitle>
                    </DialogHeader>
                    {editingCoupon && (
                        <EditCouponForm
                            coupon={editingCoupon}
                            users={users}
                            onSuccess={() => setEditingCoupon(null)}
                            onCancel={() => setEditingCoupon(null)}
                        />
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
