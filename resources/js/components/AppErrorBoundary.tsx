import ErrorFallback from '@/components/ErrorFallback';
import React from 'react';
import { ErrorBoundary } from 'react-error-boundary';

function redirectToDashboard() {
    const path = window.location.pathname;

    let dashboardPath;
    if (path.startsWith('/backoffice/')) {
        dashboardPath = '/backoffice/dashboard';
    } else if (path.startsWith('/dashboard/')) {
        dashboardPath = '/dashboard';
    } else {
        dashboardPath = '/';
    }

    window.location.href = dashboardPath;
}

export { redirectToDashboard };

const AppWithErrorBoundary: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    return (
        <ErrorBoundary
            FallbackComponent={({ error }) => (
                <ErrorFallback
                    error={error}
                />
            )}
        >
            {children}
        </ErrorBoundary>
    );
};

export default AppWithErrorBoundary;
