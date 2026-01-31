import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface Payment {
    id: number;
    client_name: string;
    amount_total: number;
    currency: string;
    status: string;
    created_at: string;
    metadata?: {
        coupon_code?: string;
        original_amount?: number;
        discount_amount?: number;
        [key: string]: any;
    };
}

interface Props {
    payments: Payment[];
}

export default function Payments({ payments }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/backoffice/dashboard',
        },
        {
            title: 'My Payments',
            href: route('backoffice.mediator.payments'),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Payments" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>My Payments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Client</TableHead>
                                    <TableHead>Amount</TableHead>
                                    <TableHead>Coupon</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {payments.map((payment) => (
                                    <TableRow key={payment.id}>
                                        <TableCell className="font-medium">
                                            {payment.client_name || 'Unknown'}
                                        </TableCell>
                                        <TableCell>
                                            {(payment.amount_total / 100).toFixed(2)} {payment.currency}
                                        </TableCell>
                                        <TableCell>
                                            {payment.metadata?.coupon_code ? (
                                                <div className="flex flex-col">
                                                    <span className="font-bold text-xs">{payment.metadata.coupon_code}</span>
                                                    {payment.metadata.original_amount && payment.metadata.discount_amount ? (
                                                        <span className="text-[10px] text-muted-foreground">
                                                            {Math.round((payment.metadata.discount_amount / payment.metadata.original_amount) * 100)}% OFF
                                                        </span>
                                                    ) : null}
                                                </div>
                                            ) : (
                                                <span className="text-muted-foreground">-</span>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                className={`px-2 py-1 rounded-full text-xs font-semibold ${payment.status === 'paid'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800'
                                                    }`}
                                            >
                                                {payment.status}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            {new Date(payment.created_at).toLocaleDateString()}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {payments.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center">
                                            No payments found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
