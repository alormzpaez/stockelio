import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { Cart, PageProps } from "@/types";
import { Button, Toast } from "flowbite-react";
import OrderCard from "@/Components/OrderCard";
import { HiFire } from "react-icons/hi";

export default function Show({ auth, cart, flash }: PageProps<{ cart: Cart }>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Mi carrito
                </h2>
            }
        >
            <Head title="Mi carrito" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <>
                        {flash.message ? (
                            <Toast className="mb-2">
                                <div className="inline-flex items-center justify-center w-8 h-8 rounded-lg shrink-0 bg-cyan-100 text-cyan-500 dark:bg-cyan-800 dark:text-cyan-200">
                                    <HiFire className="w-5 h-5" />
                                </div>
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
                                    Mi carrito
                                </h2>

                                <div className="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                                    <div className="flex-none w-full mx-auto lg:max-w-2xl xl:max-w-4xl">
                                        <div className="space-y-6">
                                            {cart.orders.map((order, index) => (
                                                <OrderCard
                                                    id={order.id}
                                                    key={index}
                                                    productName={
                                                        order.variant.product
                                                            .name
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
                                                />
                                            ))}
                                        </div>
                                    </div>

                                    <div className="flex-1 max-w-4xl mx-auto mt-6 space-y-6 lg:mt-0 lg:w-full">
                                        <div className="p-4 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                                            <p className="text-xl font-semibold text-gray-900 dark:text-white">
                                                Resumen
                                            </p>

                                            <div className="space-y-4">
                                                <dl className="flex items-center justify-between gap-4 dark:border-gray-700">
                                                    <dt className="text-base font-bold text-gray-900 dark:text-white">
                                                        Total
                                                    </dt>
                                                    <dd className="text-base font-bold text-gray-900 dark:text-white">
                                                        ${cart.total.toFixed(2)}
                                                    </dd>
                                                </dl>
                                            </div>

                                            <Button
                                                color="blue"
                                                className="flex w-full items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white"
                                            >
                                                Proceder al pago
                                            </Button>

                                            <div className="flex items-center justify-center gap-2">
                                                <span className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                    {" "}
                                                    ó{" "}
                                                </span>
                                                <Link
                                                    href={route(
                                                        "products.index"
                                                    )}
                                                    className="inline-flex items-center gap-2 text-sm font-medium text-blue-700 underline hover:no-underline dark:text-blue-500"
                                                >
                                                    Continuar comprando
                                                    <svg
                                                        className="w-5 h-5"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke="currentColor"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 12H5m14 0-4 4m4-4-4-4"
                                                        />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>

                                        <div className="p-4 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                                            <form className="space-y-4">
                                                <div>
                                                    <label
                                                        htmlFor="voucher"
                                                        className="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                                    >
                                                        {" "}
                                                        ¿Tienes un vale o una
                                                        tarjeta regalo?{" "}
                                                    </label>
                                                    <input
                                                        type="text"
                                                        id="voucher"
                                                        className="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                                        placeholder=""
                                                        required
                                                    />
                                                </div>
                                                <button
                                                    type="submit"
                                                    className="flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                >
                                                    Aplicar código
                                                </button>
                                            </form>
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
