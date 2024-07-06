import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps } from "@/types";
import { Head } from "@inertiajs/react";
import { ReactElement } from "react";

function Show() {
    return (
        <>
            <Head title="Conversación" />

            <div className="py-12">
                <div className="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <div className="max-w-2xl px-4 py-8 mx-auto lg:py-16">
                            <h2 className="mb-4 text-4xl font-medium text-gray-900 dark:text-gray-100">
                                Ver conversación
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

Show.layout = (page: ReactElement<PageProps>) => (
    <AuthenticatedLayout user={page.props.auth.user} children={page} />
);

export default Show