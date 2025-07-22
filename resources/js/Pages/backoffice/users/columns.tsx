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
} from "@/components/ui/alert-dialog"; // <-- Importa AlertDialog
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Link, router } from "@inertiajs/react"; // <-- Importa el router de Inertia
import { ColumnDef } from "@tanstack/react-table";
import { MoreHorizontal } from "lucide-react";

// ... tu tipo User ...
export type User = {
    id: number
    name: string
    email: string
    roles: string[]
}


export const columns: ColumnDef<User>[] = [
  // ... tus columnas de name, email, roles ...
  {
    accessorKey: "name",
    header: "Nombre",
  },
  {
    accessorKey: "email",
    header: "Email",
  },
  {
    accessorKey: "roles",
    header: "Roles",
    cell: ({ row }) => {
      const roles = row.getValue("roles") as string[]
      return <div className="space-x-1">{roles.map(role => <span key={role} className="px-2 py-1 text-xs font-semibold text-white bg-gray-600 rounded-full">{role}</span>)}</div>
    },
  },
  {
    id: "actions",
    cell: ({ row }) => {
      const user = row.original

      const handleDelete = () => {
        router.delete(route('backoffice.users.destroy', user.id), {
            preserveScroll: true,
            // Opcional: puedes añadir un toast de notificación aquí
            onSuccess: () => console.log('Usuario eliminado'), 
            onError: () => console.error('Error al eliminar usuario'),
        });
      }

      return (
        <AlertDialog> {/* El modal envuelve todo el menú de acciones */}
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="h-8 w-8 p-0">
                <span className="sr-only">Open menu</span>
                <MoreHorizontal className="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Acciones</DropdownMenuLabel>
              <DropdownMenuItem asChild>
                <Link href={route('backoffice.users.edit', user.id)}>Editar</Link>
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <AlertDialogTrigger asChild>
                {/* Este item ahora abre el modal en lugar de ejecutar una acción directa */}
                <DropdownMenuItem className="text-red-600 focus:text-red-600">
                    Eliminar
                </DropdownMenuItem>
              </AlertDialogTrigger>
            </DropdownMenuContent>
          </DropdownMenu>

          {/* Aquí defines el contenido del modal de confirmación */}
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>¿Estás absolutamente seguro?</AlertDialogTitle>
              <AlertDialogDescription>
                Esta acción no se puede deshacer. Esto eliminará permanentemente al usuario 
                <span className="font-medium text-foreground"> {user.name} </span>
                y borrará sus datos de nuestros servidores.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel>Cancelar</AlertDialogCancel>
              {/* El botón de acción ejecuta la función de borrado */}
              <AlertDialogAction onClick={handleDelete} className="bg-red-600 hover:bg-red-700">
                Sí, eliminar usuario
              </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      )
    },
  },
]