"use client"

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Link, router } from "@inertiajs/react";
import { ColumnDef } from "@tanstack/react-table";
import { MoreHorizontal } from "lucide-react";
import { format } from "date-fns";

export type Coupon = {
    id: number
    code: string
    discount_percentage: number
    expires_at: string
    active: boolean
    max_uses_per_user: number
    redemptions_count: number
}

export const columns: ColumnDef<Coupon>[] = [
    {
        accessorKey: "code",
        header: "Code",
    },
    {
        accessorKey: "discount_percentage",
        header: "Discount (%)",
        cell: ({ row }) => <span className="font-bold">{row.getValue("discount_percentage")}%</span>
    },
    {
        accessorKey: "expires_at",
        header: "Expires At",
        cell: ({ row }) => format(new Date(row.getValue("expires_at")), "MMM d, yyyy"),
    },
    {
        accessorKey: "active",
        header: "Status",
        cell: ({ row }) => (
            <span className={`px-2 py-1 rounded-full text-xs text-white ${row.getValue("active") ? "bg-green-500" : "bg-red-500"}`}>
                {row.getValue("active") ? "Active" : "Inactive"}
            </span>
        )
    },
    {
        accessorKey: "redemptions_count",
        header: "Used Count",
    },
    {
        id: "actions",
        cell: ({ row }) => {
            const coupon = row.original

            const handleDelete = () => {
                router.delete(route('backoffice.coupons.destroy', coupon.id), {
                    preserveScroll: true,
                    onSuccess: () => console.log('Coupon deleted'),
                    onError: () => console.error('Error deleting coupon'),
                });
            }

            return (
                <AlertDialog>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem asChild>
                                <Link href={route('backoffice.coupons.edit', coupon.id)}>Edit</Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <AlertDialogTrigger asChild>
                                <DropdownMenuItem className="text-red-600 focus:text-red-600">
                                    Delete
                                </DropdownMenuItem>
                            </AlertDialogTrigger>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This action cannot be undone. This will permanently delete the coupon
                                <span className="font-medium text-foreground"> {coupon.code} </span>.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                            <AlertDialogAction onClick={handleDelete} className="bg-red-600 hover:bg-red-700">
                                Yes, delete coupon
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            )
        },
    },
]
