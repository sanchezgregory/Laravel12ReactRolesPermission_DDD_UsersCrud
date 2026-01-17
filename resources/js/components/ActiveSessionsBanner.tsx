import { SharedData } from "@/types";
import { usePage } from "@inertiajs/react";
import { CalendarDays, AlertCircle } from "lucide-react";
import { Link } from "@inertiajs/react";

export default function ActiveSessionsBanner() {
    const { auth } = usePage<SharedData>().props;

    if (!auth.user || !auth.active_sessions || auth.active_sessions.length === 0) {
        return null;
    }

    const sessionCount = auth.active_sessions.length;

    return (
        <div className="w-full bg-blue-50 border-b border-blue-100 dark:bg-blue-900/10 dark:border-blue-800">
            <div className="container mx-auto px-4 py-3">
                <div className="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
                    <div className="flex items-center gap-2 text-blue-700 dark:text-blue-300">
                        <AlertCircle className="h-4 w-4" />
                        <span className="font-medium">
                            Tienes {sessionCount} {sessionCount === 1 ? 'sesión activa' : 'sesiones activas'} pendiente{sessionCount !== 1 ? 's' : ''}.
                        </span>
                    </div>

                    <div className="flex gap-2 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0 scrollbar-hide">
                        {auth.active_sessions.slice(0, 3).map(session => (
                            <Link
                                key={session.id}
                                href={route('mediators.show', session.mediator_id)}
                                className="flex items-center gap-2 whitespace-nowrap rounded-full bg-white px-3 py-1 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors"
                            >
                                <CalendarDays className="h-3 w-3 text-muted-foreground" />
                                <span className="font-medium text-xs">{session.mediator_name}</span>
                            </Link>
                        ))}
                        {sessionCount > 3 && (
                            <span className="flex items-center px-2 text-xs text-muted-foreground">
                                +{sessionCount - 3} más
                            </span>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
