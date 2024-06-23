import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import DeleteUserForm from "./Partials/DeleteUserForm";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm";
import { Head } from "@inertiajs/react";
import { PageProps, Location } from "@/types";
import { Button } from "flowbite-react";
import { HiPlus } from "react-icons/hi";
import LocationCard from "@/Components/LocationCard";

export default function Edit({
    auth,
    mustVerifyEmail,
    status,
    locations,
}: PageProps<{
    mustVerifyEmail: boolean;
    status?: string;
    locations: Location[];
}>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Profile
                </h2>
            }
        >
            <Head title="Profile" />

            <div className="py-12">
                <div className="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <UpdateProfileInformationForm
                            mustVerifyEmail={mustVerifyEmail}
                            status={status}
                            className="max-w-xl"
                        />
                    </div>

                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <div className="max-w-xl">
                            <div>
                                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Mis direcciones
                                </h2>

                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Esta información es necesaria para calcular
                                    gastos de envío, de impuestos, etc. Por lo
                                    tanto, hasta no tener alguna dirección
                                    predeterminada, no será posible pedir ningún
                                    producto.
                                </p>
                            </div>
                        </div>

                        <div className="mt-6 space-y-6">
                            {locations.length == 0 ? (
                                <div className="flex justify-center py-2 text-sm text-gray-600 dark:text-gray-400">
                                    No hay direcciones todavía.
                                </div>
                            ) : (
                                <div className="grid gap-2 lg:grid-cols-2 xl:grid-cols-3">
                                    {locations.map((location, key) => (
                                        <LocationCard
                                            id={location.id}
                                            country={location.country_name}
                                            fullAddress={location.full_address}
                                            isPreferred={location.is_preferred}
                                            phone={location.phone}
                                            key={key}
                                        />
                                    ))}
                                </div>
                            )}

                            <div className="flex items-center gap-4">
                                <Button color={"blue"} disabled={false}>
                                    <HiPlus className="w-5 h-5 mr-2" />
                                    Agregar una dirección
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <UpdatePasswordForm className="max-w-xl" />
                    </div>

                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <DeleteUserForm className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
