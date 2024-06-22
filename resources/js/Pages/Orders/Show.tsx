import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import { Order, PageProps } from "@/types";
import { Button } from "flowbite-react";
import { FaArrowLeft } from "react-icons/fa";

export default function Index({ auth, order }: PageProps<{ order: Order }>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mi orden
                </h2>
            }
        >
            <Head title="Mi orden" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div className="flex justify-between gap-4 p-6 text-4xl font-medium text-gray-900 dark:text-gray-100">
                            <Button
                                color="gray"
                                onClick={() => {
                                    router.visit(route("orders.index"));
                                }}
                            >
                                <div className="flex items-center gap-2">
                                    <FaArrowLeft className="mr-2" />
                                    Volver a mis ordenes
                                </div>
                            </Button>
                        </div>
                        <hr className="h-px bg-gray-200 border-0 dark:bg-gray-700"></hr>
                        <section className="py-8 antialiased bg-white dark:bg-gray-800 md:py-12">
                            <div className="max-w-screen-xl px-4 mx-auto">
                                <h2 className="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                                    Seguimiento de la orden #{order.id}
                                </h2>

                                <div className="mt-6 sm:mt-8 lg:flex lg:gap-8">
                                    <div className="w-full overflow-hidden border border-gray-200 divide-y divide-gray-200 rounded-lg h-fit dark:divide-gray-700 dark:border-gray-700 lg:max-w-xl xl:max-w-2xl">
                                        <div className="p-6 space-y-4">
                                            <div className="flex items-center gap-6">
                                                <div className="h-14 w-14 shrink-0">
                                                    <img
                                                        className="w-full h-full"
                                                        src={
                                                            order.variant
                                                                .product
                                                                .thumbnail_url ??
                                                            ""
                                                        }
                                                        alt="variant image"
                                                    />
                                                </div>

                                                <div className="flex-1 min-w-0 font-medium text-gray-900 dark:text-white">
                                                    {order.variant.name}
                                                </div>
                                            </div>

                                            <div className="flex items-center justify-between gap-4">
                                                <div className="flex flex-col gap-1">
                                                    <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        <span className="font-medium text-gray-900 dark:text-white">
                                                            ID de variante:
                                                        </span>{" "}
                                                        {order.variant_id}
                                                    </p>
                                                    <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        <span className="font-medium text-gray-900 dark:text-white">
                                                            Precio individual:
                                                        </span>{" "}
                                                        $
                                                        {order.variant.retail_price.toFixed(
                                                            2
                                                        )}
                                                    </p>
                                                </div>

                                                <div className="flex items-center justify-end gap-4">
                                                    <p className="text-base font-normal text-gray-900 dark:text-white">
                                                        x{order.quantity}
                                                    </p>

                                                    <p className="text-xl font-bold leading-tight text-gray-900 dark:text-white">
                                                        $
                                                        {(
                                                            order.variant
                                                                .retail_price *
                                                            order.quantity
                                                        ).toFixed(2)}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="p-6 space-y-4 bg-gray-50 dark:bg-gray-800">
                                            <div className="space-y-2">
                                                <dl className="flex items-center justify-between gap-4">
                                                    <dt className="font-normal text-gray-500 dark:text-gray-400">
                                                        Precio original
                                                    </dt>
                                                    <dd className="font-medium text-gray-900 dark:text-white">
                                                        $
                                                        {(
                                                            order.variant
                                                                .retail_price *
                                                            order.quantity
                                                        ).toFixed(2)}
                                                    </dd>
                                                </dl>

                                                <dl className="flex items-center justify-between gap-4">
                                                    <dt className="font-normal text-gray-500 dark:text-gray-400">
                                                        Ahorros
                                                    </dt>
                                                    <dd className="text-base font-medium text-green-500">
                                                        -$0.00
                                                    </dd>
                                                </dl>

                                                <dl className="flex items-center justify-between gap-4">
                                                    <dt className="font-normal text-gray-500 dark:text-gray-400">
                                                        Envío
                                                    </dt>
                                                    <dd className="font-medium text-gray-900 dark:text-white">
                                                        $99.99
                                                    </dd>
                                                </dl>

                                                <dl className="flex items-center justify-between gap-4">
                                                    <dt className="font-normal text-gray-500 dark:text-gray-400">
                                                        Impuestos (IVA)
                                                    </dt>
                                                    <dd className="font-medium text-gray-900 dark:text-white">
                                                        $99.99
                                                    </dd>
                                                </dl>
                                            </div>

                                            <dl className="flex items-center justify-between gap-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                <dt className="text-lg font-bold text-gray-900 dark:text-white">
                                                    Total
                                                </dt>
                                                <dd className="text-lg font-bold text-gray-900 dark:text-white">
                                                    $7,191.00
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>

                                    <div className="mt-6 grow sm:mt-8 lg:mt-0">
                                        <div className="p-6 space-y-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                            <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
                                                Historial de la orden
                                            </h3>

                                            <ol className="relative border-gray-200 ms-3 border-s dark:border-gray-700">
                                                <li className="mb-10 ms-6">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3 ring-8 ring-white dark:bg-gray-700 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <h4 className="mb-0.5 text-base font-semibold text-gray-900 dark:text-white">
                                                        Estimated delivery in 24
                                                        Nov 2023
                                                    </h4>
                                                    <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        Products delivered
                                                    </p>
                                                </li>

                                                <li className="mb-10 ms-6">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3 ring-8 ring-white dark:bg-gray-700 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <h4 className="mb-0.5 text-base font-semibold text-gray-900 dark:text-white">
                                                        Today
                                                    </h4>
                                                    <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        Products being delivered
                                                    </p>
                                                </li>

                                                <li className="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 11.917 9.724 16.5 19 7.5"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <h4 className="mb-0.5 font-semibold">
                                                        23 Nov 2023, 15:15
                                                    </h4>
                                                    <p className="text-sm">
                                                        Products in the
                                                        courier's warehouse
                                                    </p>
                                                </li>

                                                <li className="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 11.917 9.724 16.5 19 7.5"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <h4 className="mb-0.5 text-base font-semibold">
                                                        22 Nov 2023, 12:27
                                                    </h4>
                                                    <p className="text-sm">
                                                        Products delivered to
                                                        the courier - DHL
                                                        Express
                                                    </p>
                                                </li>

                                                <li className="mb-10 ms-6 text-primary-700 dark:text-primary-500">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 11.917 9.724 16.5 19 7.5"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <h4 className="mb-0.5 font-semibold">
                                                        19 Nov 2023, 10:47
                                                    </h4>
                                                    <p className="text-sm">
                                                        Payment accepted - VISA
                                                        Credit Card
                                                    </p>
                                                </li>

                                                <li className="ms-6 text-primary-700 dark:text-primary-500">
                                                    <span className="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 bg-primary-100 ring-8 ring-white dark:bg-primary-900 dark:ring-gray-800">
                                                        <svg
                                                            className="w-4 h-4"
                                                            aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke="currentColor"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M5 11.917 9.724 16.5 19 7.5"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <div>
                                                        <h4 className="mb-0.5 font-semibold">
                                                            19 Nov 2023, 10:45
                                                        </h4>
                                                        <a
                                                            href="#"
                                                            className="text-sm font-medium hover:underline"
                                                        >
                                                            Order placed -
                                                            Receipt #647563
                                                        </a>
                                                    </div>
                                                </li>
                                            </ol>

                                            <div className="gap-4 sm:flex sm:items-center">
                                                <button
                                                    type="button"
                                                    className="w-full rounded-lg  border border-gray-200 bg-white px-5  py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                                                >
                                                    Cancel the order
                                                </button>

                                                <a
                                                    href="#"
                                                    className="mt-4 flex w-full items-center justify-center rounded-lg bg-primary-700  px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300  dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 sm:mt-0"
                                                >
                                                    Order details
                                                </a>
                                            </div>
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
