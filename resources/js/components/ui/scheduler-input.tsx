import { Input } from "@/components/ui/input";
import * as React from "react";
import { forwardRef } from "react";

// Helper to format a Date object to "YYYY-MM-DDTHH:mm" using Local time components
export function dateToLocalInputString(date: Date) {
    const pad = (num: number) => num.toString().padStart(2, '0');
    return (
        date.getFullYear() +
        '-' +
        pad(date.getMonth() + 1) +
        '-' +
        pad(date.getDate()) +
        'T' +
        pad(date.getHours()) +
        ':' +
        pad(date.getMinutes())
    );
}

// Helper to format a Date object to "YYYY-MM-DDTHH:mm" using UTC components
// Use this when you want to display a DB UTC value "as is" without timezone conversion
export function dateToUtcInputString(date: Date) {
    const pad = (num: number) => num.toString().padStart(2, '0');
    return (
        date.getUTCFullYear() +
        '-' +
        pad(date.getUTCMonth() + 1) +
        '-' +
        pad(date.getUTCDate()) +
        'T' +
        pad(date.getUTCHours()) +
        ':' +
        pad(date.getUTCMinutes())
    );
}

export interface SchedulerInputProps extends React.ComponentProps<"input"> {
    minHoursFromNow?: number;
}

const SchedulerInput = forwardRef<HTMLInputElement, SchedulerInputProps>(
    ({ className, minHoursFromNow = 2, onClick, min, ...props }, ref) => {

        // Calculate dynamic min if not provided manually
        const dynamicMin = min !== undefined
            ? min
            : dateToLocalInputString(new Date(Date.now() + minHoursFromNow * 60 * 60 * 1000));

        return (
            <Input
                type="datetime-local"
                className={className}
                ref={ref}
                min={dynamicMin}
                onClick={(e) => {
                    try {
                        (e.target as HTMLInputElement).showPicker();
                    } catch (error) {
                        // Fallback or ignore
                    }
                    if (onClick) onClick(e);
                }}
                {...props}
            />
        );
    }
);

SchedulerInput.displayName = "SchedulerInput";

export { SchedulerInput };
