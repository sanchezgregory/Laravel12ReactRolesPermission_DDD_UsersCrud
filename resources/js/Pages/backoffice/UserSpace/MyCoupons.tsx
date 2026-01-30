import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { format, differenceInDays } from "date-fns";
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from "@/components/ui/progress";

interface Coupon {
    id: number;
    code: string;
    discount_percentage: number;
    expires_at: string;
    max_uses_per_user: number;
    user_redemptions: number;
}

interface MyCouponsProps {
    coupons: Coupon[];
}

export default function MyCoupons({ coupons }: MyCouponsProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Home', href: '/backoffice/dashboard' },
                { title: 'My Coupons', href: null },
            ]}
        >
            <Head title="My Coupons" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <h2 className="font-semibold text-xl text-foreground leading-tight mb-6">
                        My Coupons
                    </h2>

                    {coupons.length === 0 ? (
                        <div className="bg-card text-card-foreground p-6 rounded-lg border shadow-sm text-center">
                            You don't have any coupons available at the moment.
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {coupons.map((coupon) => {
                                const expiryDate = new Date(coupon.expires_at);
                                const daysLeft = differenceInDays(expiryDate, new Date());
                                const isExpired = daysLeft < 0;
                                const isFullyUsed = coupon.user_redemptions >= coupon.max_uses_per_user;
                                const remainingUses = coupon.max_uses_per_user - coupon.user_redemptions;

                                return (
                                    <Card key={coupon.id} className={`${(isExpired || isFullyUsed) ? 'opacity-60' : ''}`}>
                                        <CardHeader>
                                            <div className="flex justify-between items-start">
                                                <CardTitle className="text-2xl font-mono tracking-wider text-primary">
                                                    {coupon.code}
                                                </CardTitle>
                                                <Badge variant={(isExpired || isFullyUsed) ? "secondary" : "default"}>
                                                    {coupon.discount_percentage}% OFF
                                                </Badge>
                                            </div>
                                            <CardDescription>
                                                {isExpired ? (
                                                    <span className="text-red-500">Expired on {format(expiryDate, 'MMM d, yyyy')}</span>
                                                ) : (
                                                    <span>Expires on {format(expiryDate, 'MMM d, yyyy')} ({daysLeft} days left)</span>
                                                )}
                                            </CardDescription>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="space-y-2">
                                                <div className="flex justify-between text-sm">
                                                    <span>Usage</span>
                                                    <span>{coupon.user_redemptions} / {coupon.max_uses_per_user}</span>
                                                </div>
                                                <Progress value={(coupon.user_redemptions / coupon.max_uses_per_user) * 100} />
                                                {isFullyUsed && <p className="text-xs text-red-500 font-medium mt-2">Maximum usage reached</p>}
                                            </div>
                                        </CardContent>
                                    </Card>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
