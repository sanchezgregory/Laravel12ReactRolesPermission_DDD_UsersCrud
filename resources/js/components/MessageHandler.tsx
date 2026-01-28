import { usePage } from '@inertiajs/react';
import { debounce } from 'lodash';
import React, { useCallback, useEffect, useMemo, useRef } from 'react';
import { toast, ToastPosition } from 'react-toastify';

// Define PageProps interface since it's not exported from @inertiajs/react
interface PageProps {
    [key: string]: unknown;
}

interface Flash {
    success?: string | null;
    error?: string | null;
}

interface ErrorBag {
    [key: string]: string[];
}

interface CustomPageProps extends PageProps {
    flash: Flash;
    errorBags?: {
        [key: string]: ErrorBag;
    };
    // No need to redeclare index signature as it's already in PageProps
}

const MessageHandler: React.FC = () => {
    const { flash = {}, errorBags = {} } = usePage<CustomPageProps>().props;

    const formatErrorMessages = useCallback((errors: string[]): JSX.Element => {
        return (
            <>
                <div className="toast-message">
                    <p className="font-semibold mb-2"> There are {errors.length} errors:</p>
                    <ul className="list-disc pl-4">
                        {errors.map((error, index) => (
                            <li key={index} className="mb-1">{error}</li>
                        ))}
                    </ul>
                </div>
            </>
        );
    }, []);

    const toastConfig = useMemo(() => ({
        position: 'top-right' as ToastPosition,
        autoClose: 7000,
        hideProgressBar: false,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
    }), []);

    const collectErrors = (): string[] => {
        const allErrors: string[] = [];

        if (flash.error) {
            allErrors.push(flash.error);
        }

        if (errorBags) {
            Object.values(errorBags).forEach(bag => {
                Object.values(bag).forEach(errors => {
                    allErrors.push(...errors);
                });
            });
        }

        return [...new Set(allErrors)].filter(Boolean);
    };

    const showToast = useCallback(
        (messages: string[], type: 'success' | 'error') => {
            const show = () => {
                if (messages.length === 0) return;

                const currentToastConfig = {
                    ...toastConfig,
                    className: type === 'error' ? 'custom-toast-error' : undefined
                };

                if (type === 'success') {
                    toast.success(messages[0], currentToastConfig);
                } else {
                    const content = messages.length === 1
                        ? messages[0]
                        : formatErrorMessages(messages);

                    toast.error(content, currentToastConfig);
                }
            };

            const debouncedShow = debounce(show, 300);
            debouncedShow();

            return debouncedShow;
        },
        [toastConfig, formatErrorMessages]
    );

    const displayedRef = useRef<{ success: string | null, errors: string | null }>({ success: null, errors: null });

    useEffect(() => {
        let debouncedFunc: ReturnType<typeof debounce> | null = null;

        // Handle Success
        if (flash?.success) {
            if (displayedRef.current.success !== flash.success) {
                debouncedFunc = showToast([flash.success], 'success');
                displayedRef.current.success = flash.success;
            }
        } else {
            displayedRef.current.success = null;
        }

        // Handle Errors
        const errorMessages = collectErrors();
        const errorsString = JSON.stringify(errorMessages);

        if (errorMessages.length > 0) {
            // Only show if the error content has changed
            if (displayedRef.current.errors !== errorsString) {
                debouncedFunc = showToast(errorMessages, 'error');
                displayedRef.current.errors = errorsString;
            }
        } else {
            displayedRef.current.errors = null;
        }

        return () => {
            if (debouncedFunc) {
                debouncedFunc.cancel();
            }
        };
    }, [flash, errorBags, showToast]);

    return null;
};

export default MessageHandler;
