import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { Order, PageProps } from "@/types";
import ShowableOrderCard from "@/Components/ShowableOrderCard";

export default function Index({
    auth,
    orders,
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
