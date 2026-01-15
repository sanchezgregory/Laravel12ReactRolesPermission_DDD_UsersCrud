import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';

interface MediatorFormData {
    name: string;
    email: string;
    session_price_minor: number;
    currency: string;
    calendly_url: string;
    headline: string;
    bio: string;
}

interface Props {
    mediator?: MediatorFormData & { id?: number };
    isEditing?: boolean;
}

export default function MediatorForm({ mediator, isEditing = false }: Props) {
    const { data, setData, post, put, processing, errors } = useForm<Required<MediatorFormData>>({
        name: mediator?.name || '',
        email: mediator?.email || '',
        session_price_minor: mediator?.session_price_minor || 0,
        currency: mediator?.currency || 'EUR',
        calendly_url: mediator?.calendly_url || '',
        headline: mediator?.headline || '',
        bio: mediator?.bio || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEditing && mediator?.id) {
            put(route('backoffice.mediators.update', mediator.id));
        } else {
            post(route('backoffice.mediators.store'));
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="name">Name</Label>
                    <Input
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                    />
                    <InputError message={errors.name} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    <InputError message={errors.email} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="session_price_minor">Session Price (Minor Units)</Label>
                    <Input
                        id="session_price_minor"
                        type="number"
                        value={data.session_price_minor}
                        onChange={(e) => setData('session_price_minor', parseInt(e.target.value))}
                        required
                    />
                    <InputError message={errors.session_price_minor} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="currency">Currency</Label>
                    <Input
                        id="currency"
                        value={data.currency}
                        onChange={(e) => setData('currency', e.target.value)}
                        required
                        maxLength={3}
                    />
                    <InputError message={errors.currency} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="calendly_url">Calendly URL</Label>
                    <Input
                        id="calendly_url"
                        type="url"
                        value={data.calendly_url}
                        onChange={(e) => setData('calendly_url', e.target.value)}
                    />
                    <InputError message={errors.calendly_url} />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="headline">Headline</Label>
                    <Input
                        id="headline"
                        value={data.headline}
                        onChange={(e) => setData('headline', e.target.value)}
                    />
                    <InputError message={errors.headline} />
                </div>

                <div className="col-span-1 md:col-span-2 space-y-2">
                    <Label htmlFor="bio">Bio</Label>
                    <Textarea
                        id="bio"
                        value={data.bio}
                        onChange={(e) => setData('bio', e.target.value)}
                    />
                    <InputError message={errors.bio} />
                </div>
            </div>

            <div className="flex justify-end">
                <Button type="submit" disabled={processing}>
                    {isEditing ? 'Update Mediator' : 'Create Mediator'}
                </Button>
            </div>
        </form>
    );
}
