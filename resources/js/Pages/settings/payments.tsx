import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetFooter } from '@/components/ui/sheet';
import InputError from '@/components/input-error';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { Loader2, Settings2, ExternalLink } from 'lucide-react';
import axios from 'axios';
import { Transition } from '@headlessui/react';

interface Mediator {
    id: number;
    name: string;
    email: string;
    custom_fee_percent: number | null;
    providers_data: Record<string, any>;
}

interface Props {
    globalFee: number;
    mediators: Mediator[];
}

export default function Payments({ globalFee, mediators }: Props) {
    return (
        <AppLayout breadcrumbs={[{ title: 'Payment Settings', href: '/backoffice/settings/payments' }]}>
            <Head title="Payment Settings" />
            <SettingsLayout>
                <div className="space-y-8">
                    <HeadingSmall title="Payment Configuration" description="Manage platform fees and mediator payment accounts." />

                    <GlobalSettingsCard globalFee={globalFee} />

                    <MediatorsList mediators={mediators} defaultGlobalFee={globalFee} />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}

function GlobalSettingsCard({ globalFee }: { globalFee: number }) {
    const { data, setData, put, processing, errors, recentlySuccessful } = useForm({
        percent: globalFee,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('settings.payments.global'));
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Global Platform Fee</CardTitle>
                <CardDescription>
                    This percentage will be applied to all mediators who do not have a custom fee configured.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="flex max-w-sm items-end gap-4">
                    <div className="grid w-full gap-1.5">
                        <Label htmlFor="global-percent">Percentage (%)</Label>
                        <Input
                            id="global-percent"
                            type="number"
                            min="0"
                            max="100"
                            value={data.percent}
                            onChange={(e) => setData('percent', parseInt(e.target.value) || 0)}
                        />
                        <InputError message={errors.percent} />
                    </div>
                    <div className="flex flex-col gap-2">
                        <Button type="submit" disabled={processing}>
                            {processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                            Save
                        </Button>
                        <Transition
                            show={recentlySuccessful}
                            enter="transition ease-in-out"
                            enterFrom="opacity-0"
                            leave="transition ease-in-out"
                            leaveTo="opacity-0"
                        >
                            <p className="text-xs text-green-600 text-center">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}

function MediatorsList({ mediators, defaultGlobalFee }: { mediators: Mediator[], defaultGlobalFee: number }) {
    const [selectedMediator, setSelectedMediator] = useState<Mediator | null>(null);

    return (
        <Card>
            <CardHeader>
                <CardTitle>Mediator Configurations</CardTitle>
                <CardDescription>
                    Override fees and configure payment provider accounts for individual mediators.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Mediator</TableHead>
                            <TableHead>Fee config</TableHead>
                            <TableHead>Providers Configured</TableHead>
                            <TableHead className="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {mediators.map((mediator) => (
                            <TableRow key={mediator.id}>
                                <TableCell>
                                    <div className="font-medium">{mediator.name}</div>
                                    <div className="text-sm text-muted-foreground">{mediator.email}</div>
                                </TableCell>
                                <TableCell>
                                    {mediator.custom_fee_percent !== null ? (
                                        <Badge variant="outline" className="border-blue-500 text-blue-500">
                                            Custom: {mediator.custom_fee_percent}%
                                        </Badge>
                                    ) : (
                                        <Badge variant="secondary">Global: {defaultGlobalFee}%</Badge>
                                    )}
                                </TableCell>
                                <TableCell>
                                    <div className="flex gap-2">
                                        {mediator.providers_data?.stripe?.account_id ? (
                                            <Badge className="bg-indigo-600 hover:bg-indigo-700">Stripe</Badge>
                                        ) : null}
                                        {mediator.providers_data?.paypal?.email ? (
                                            <Badge className="bg-blue-600 hover:bg-blue-700">PayPal</Badge>
                                        ) : null}
                                        {!mediator.providers_data?.stripe && !mediator.providers_data?.paypal && (
                                            <span className="text-sm text-muted-foreground">None</span>
                                        )}
                                    </div>
                                </TableCell>
                                <TableCell className="text-right">
                                    <Button variant="ghost" size="icon" onClick={() => setSelectedMediator(mediator)}>
                                        <Settings2 className="h-4 w-4" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        ))}
                        {mediators.length === 0 && (
                            <TableRow>
                                <TableCell colSpan={4} className="text-center text-muted-foreground">
                                    No mediators found.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </CardContent>

            <MediatorEditSheet
                mediator={selectedMediator}
                isOpen={!!selectedMediator}
                onClose={() => setSelectedMediator(null)}
            />
        </Card>
    );
}

function MediatorEditSheet({ mediator, isOpen, onClose }: { mediator: Mediator | null, isOpen: boolean, onClose: () => void }) {
    if (!mediator) return null;

    // We init form with key=mediator.id to force re-render/reset when mediator changes
    return (
        <Sheet open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <SheetContent className="overflow-y-auto sm:max-w-md">
                <SheetHeader>
                    <SheetTitle>Edit {mediator.name}</SheetTitle>
                    <SheetDescription>
                        Configure specific financial settings for this mediator.
                    </SheetDescription>
                </SheetHeader>

                <MediatorEditForm mediator={mediator} onSuccess={onClose} />
            </SheetContent>
        </Sheet>
    );
}

function MediatorEditForm({ mediator, onSuccess }: { mediator: Mediator, onSuccess: () => void }) {
    const { data, setData, put, processing, errors, transform } = useForm({
        custom_fee_percent: mediator.custom_fee_percent?.toString() || '',
        stripe_account_id: mediator.providers_data?.stripe?.account_id || '',
        paypal_email: mediator.providers_data?.paypal?.email || '',
    });

    const [isCreatingStripe, setIsCreatingStripe] = useState(false);
    const [onboardingUrl, setOnboardingUrl] = useState<string | null>(null);

    const handleCreateStripeAccount = async (e: React.MouseEvent) => {
        e.preventDefault();
        setIsCreatingStripe(true);
        try {
            const response = await axios.post(route('backoffice.settings.payments.stripe.create'), {
                mediator_id: mediator.id,
                email: mediator.email
            });

            const { account_id, url } = response.data;
            setData('stripe_account_id', account_id);
            setOnboardingUrl(url);
        } catch (error) {
            console.error('Error creating Stripe account:', error);
            // Optionally set an error in the UI, but log is fine for now
        } finally {
            setIsCreatingStripe(false);
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        transform((data) => ({
            custom_fee_percent: data.custom_fee_percent === '' ? null : parseInt(data.custom_fee_percent),
            providers_data: {
                stripe: data.stripe_account_id ? { account_id: data.stripe_account_id } : null,
                paypal: data.paypal_email ? { email: data.paypal_email } : null,
            }
        }));

        put(route('settings.payments.mediator', mediator.id), {
            onSuccess: () => {
                onSuccess();
            }
        });
    };

    return (
        <form onSubmit={submit} className="space-y-6 pt-6">
            <div className="space-y-4">
                <h3 className="font-medium text-sm text-foreground">Fee Configuration</h3>
                <div className="grid gap-2">
                    <Label htmlFor="custom_fee">Custom Platform Fee (%)</Label>
                    <Input
                        id="custom_fee"
                        type="number"
                        placeholder="Leave empty to use global fee"
                        value={data.custom_fee_percent}
                        onChange={e => setData('custom_fee_percent', e.target.value)}
                    />
                    <p className="text-xs text-muted-foreground">
                        If set, overrides the global platform fee for this mediator only.
                    </p>
                    <InputError message={errors.custom_fee_percent} />
                </div>
            </div>

            <Separator />

            <div className="space-y-4">
                <h3 className="font-medium text-sm text-foreground">Payment Providers</h3>

                <div className="grid gap-2">
                    <Label htmlFor="stripe_id" className="flex items-center justify-between">
                        Stripe Account ID
                        <Badge variant="outline" className="text-xs font-normal">Provider: Stripe</Badge>
                    </Label>
                    <Input
                        id="stripe_id"
                        placeholder="acct_..."
                        value={data.stripe_account_id}
                        onChange={e => setData('stripe_account_id', e.target.value)}
                    />
                    <p className="text-xs text-muted-foreground">
                        The connected Stripe account ID (starts with acct_).
                    </p>
                    <InputError message={errors['providers_data.stripe.account_id' as keyof typeof errors]} />
                </div>

                <div className="flex justify-end gap-2">
                    {!data.stripe_account_id && !onboardingUrl && (
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            onClick={handleCreateStripeAccount}
                            disabled={isCreatingStripe}
                        >
                            {isCreatingStripe && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                            Create Stripe Account
                        </Button>
                    )}
                </div>

                {onboardingUrl && (
                    <div className="rounded-md bg-muted p-3">
                        <p className="mb-1 text-sm font-medium">Onboarding Link check (Send to Mediator):</p>
                        <a
                            href={onboardingUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-1 break-all text-sm text-blue-600 hover:underline"
                        >
                            {onboardingUrl}
                            <ExternalLink className="h-3 w-3" />
                        </a>
                    </div>
                )}

                <div className="grid gap-2">
                    <Label htmlFor="paypal_email" className="flex items-center justify-between">
                        PayPal Email
                        <Badge variant="outline" className="text-xs font-normal">Provider: PayPal</Badge>
                    </Label>
                    <Input
                        id="paypal_email"
                        type="email"
                        placeholder="mediator@example.com"
                        value={data.paypal_email}
                        onChange={e => setData('paypal_email', e.target.value)}
                    />
                    <InputError message={errors['providers_data.paypal.email' as keyof typeof errors]} />
                </div>
            </div>

            <SheetFooter className="mt-8">
                <Button type="submit" disabled={processing}>
                    {processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                    Save Changes
                </Button>
            </SheetFooter>
        </form>
    );
}
