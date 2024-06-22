import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { Order, PageProps } from "@/types";
import ShowableOrderCard from "@/Components/ShowableOrderCard";
import { Toast } from "flowbite-react";
import { HiFire, HiX } from "react-icons/hi";

export default function Index({
    auth,
    orders,
    flash,
}: PageProps<{ orders: Order[] }>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mis ordenes
                </h2>
            }
        >
            <Head title="Mis ordenes" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <>
                        {flash.message ? (
                            <Toast className="mb-2">
                                {flash.type == "error" ? (
                                    <div className="inline-flex items-center justify-center w-8 h-8 text-red-500 bg-red-100 rounded-lg shrink-0 dark:bg-red-800 dark:text-red-200">
                                        <HiX className="w-5 h-5" />
                                    </div>
                                ) : (
                                    <div className="inline-flex items-center justify-center w-8 h-8 rounded-lg shrink-0 bg-cyan-100 text-cyan-500 dark:bg-cyan-800 dark:text-cyan-200">
                                        <HiFire className="w-5 h-5" />
                                    </div>
                                )}
                                <div className="ml-3 text-sm font-normal">
                                    {flash.message}
                                </div>
                                <Toast.Toggle />
                            </Toast>
                        ) : null}
                    </>

                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <section className="py-8 antialiased bg-white dark:bg-gray-800 md:py-16 md:px-6">
                            <div className="max-w-screen-xl px-4 mx-auto 2xl:px-0">
                                <h2 className="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                                    Mis ordenes pendientes
                                </h2>

                                <div className="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                                    <div className="flex-none w-full mx-auto lg:max-w-2xl xl:max-w-4xl">
                                        <div className="space-y-6">
                                            {orders.map((order, index) => (
                                                <ShowableOrderCard
                                                    id={order.id}
                                                    key={index}
                                                    variantName={
                                                        order.variant.name
                                                    }
                                                    imgUrl={
                                                        order.variant.product
                                                            .thumbnail_url
                                                    }
                                                    quantity={order.quantity}
                                                    retailPrice={
                                                        order.variant
                                                            .retail_price
                                                    }
                                                    status={order.status}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
