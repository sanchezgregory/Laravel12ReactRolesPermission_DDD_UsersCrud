import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { DataTable } from '@/pages/backoffice/users/data-table';
import { Head, Link } from '@inertiajs/react';
import { columns, User } from './columns';

// Hacemos que la página acepte los usuarios como prop
interface IndexPageProps {
    users: User[];
}

export default function Index({ users }: IndexPageProps) {
    
    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Inicio',
                    href: '/backoffice/dashboard',
                },
                {
                    title: 'Gestión de Usuarios',
                    href: '/backoffice/users',
                },
            ]}
        >
            <Head title="Usuarios" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="font-semibold text-xl text-foreground leading-tight">
                            Gestión de Usuarios
                        </h2>
                        <Link href={route('backoffice.users.create')}>
                            <Button>Crear Usuario</Button>
                        </Link>
                    </div>
                    
                    <div className="bg-card text-card-foreground rounded-lg border shadow-sm">
                        <div className="p-6">
                            <DataTable columns={columns} data={users} />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}