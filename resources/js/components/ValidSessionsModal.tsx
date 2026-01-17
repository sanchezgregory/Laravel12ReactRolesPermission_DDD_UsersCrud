import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { usePage, router } from "@inertiajs/react";
import { SharedData } from "@/types";
import { useState, useEffect } from "react";
import { Calendar, User } from "lucide-react";

export default function ValidSessionsModal() {
    const { auth } = usePage<SharedData>().props;
    const [open, setOpen] = useState(false);

    useEffect(() => {
        // Show only if user is logged in, has active sessions, and hasn't dismissed it this session
        const hasSeenModal = sessionStorage.getItem("seen_upcoming_sessions_modal");

        if (auth.user && auth.active_sessions?.length > 0 && !hasSeenModal) {
            // Delay slightly to appear after render
            setTimeout(() => setOpen(true), 500);
        }
    }, [auth.user, auth.active_sessions]);

    const handleClose = () => {
        setOpen(false);
        sessionStorage.setItem("seen_upcoming_sessions_modal", "true");
    };

    const handleGoToMediators = () => {
        handleClose();
        router.visit(route('mediators.index'));
    };

    if (!auth.active_sessions || auth.active_sessions.length === 0) return null;

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Tus Sesiones Activas</DialogTitle>
                    <DialogDescription>
                        Tienes sesiones pendientes de agendar o realizar.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4 py-4 max-h-[60vh] overflow-y-auto">
                    {auth.active_sessions.map((session) => (
                        <div key={session.id} className="flex items-start gap-3 rounded-lg border p-3 bg-muted/50">
                            <div className="mt-1 bg-primary/10 p-2 rounded-full">
                                <User className="h-4 w-4 text-primary" />
                            </div>
                            <div className="space-y-1">
                                <p className="font-medium text-sm leading-none">{session.mediator_name}</p>
                                <p className="text-xs text-muted-foreground">{session.topic || "Sesión de mediación"}</p>
                                <div className="flex items-center pt-1 text-xs text-muted-foreground">
                                    <Calendar className="mr-1 h-3 w-3" />
                                    <span>Pagado el {new Date(session.created_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <DialogFooter className="sm:justify-between flex-col gap-2 sm:flex-row">
                    <Button variant="secondary" onClick={handleClose}>
                        Cerrar
                    </Button>
                    <Button onClick={handleGoToMediators}>
                        Ver Mediadores
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
