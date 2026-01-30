import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { DataTable } from '@/pages/backoffice/users/data-table';
import { Head, Link } from '@inertiajs/react';
import { columns, Coupon } from './columns';

interface IndexPageProps {
    coupons: {
        data: Coupon[];
    };
}

export default function Index({ coupons }: IndexPageProps) {
    const couponsData = coupons.data;
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
        </AppLayout>
    );
}
