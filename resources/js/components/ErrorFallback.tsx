import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { api } from '@/services/apiService';
import { Loader2 } from 'lucide-react';
import React, { useState } from 'react';
import { redirectToDashboard } from './AppErrorBoundary';

interface ErrorFallbackProps {
    error: Error;
}
interface ErrorPayload {
    message: string;
    stack?: string;
    url: string;
    timestamp: string;
}

const ErrorFallback: React.FC<ErrorFallbackProps> = ({ error }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleErrorProcess = async () => {
        setIsModalOpen(true);

        const currentUrl = window.location.href;
        const errorPayload = {
            message: error.message,
            stack: error.stack,
            url: currentUrl,
            timestamp: new Date().toISOString(),
        } as ErrorPayload;

        try {
            await api.post('/log-error', JSON.stringify(errorPayload), {
                headers: {
                    'Content-Type': 'application/json',
                },
            });
        } catch (logError) {
            console.error('Error on logging error:', logError);
        }

        setTimeout(() => {
            redirectToDashboard();
        }, 3000);
    };

    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-red-50 p-6">
            <div className="w-full max-w-md">
                <Alert variant="destructive" className="mb-6">
                    <AlertTitle className="text-xl font-semibold">¡Ups! Something went wrong</AlertTitle>
                    <AlertDescription className="mt-2">
                        We have found an unexpected error. Please click the button to go to the dashboard.
                    </AlertDescription>
                </Alert>

                {process.env.NODE_ENV === 'development' && (
                    <div className="bg-white rounded-md p-4 mb-6 border border-red-200 overflow-x-auto">
                        <p className="font-mono text-sm text-red-800">{error.name}</p>
                        <p className="font-mono text-sm text-red-800">{error.message}</p>
                    </div>
                )}

                <Button
                    onClick={handleErrorProcess}
                    className="w-full"
                >
                    go to Dashboard
                </Button>
            </div>

            <Dialog open={isModalOpen} onOpenChange={(open) => {
                if (isModalOpen && !open) {
                    return;
                }
                setIsModalOpen(open);
            }}>
                <DialogContent className="sm:max-w-md">
                    <DialogTitle className="text-sm font-semibold text-gray-400">PLease wait ...</DialogTitle>
                    <div className="flex flex-col items-center justify-center p-6 text-center space-y-4">
                        <Loader2 className="h-12 w-12 text-blue-500 animate-spin" />
                        <DialogDescription className="text-center">
                            We are registering the error and we will redirect you to the dashboard soon...
                        </DialogDescription>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
};

export default ErrorFallback;
