import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { Cart, PageProps } from "@/types";
import { Button } from "flowbite-react";
import { useEffect } from "react";

export default function Show({ auth, cart }: PageProps<{ cart: Cart }>) {
    useEffect(() => {
        console.log(cart);
    }, []);

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
                                                <div className="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                                                    <div className="space-y-4 lg:flex lg:items-center lg:justify-between lg:gap-6 lg:space-y-0">
                                                        <a
                                                            href="#"
                                                            className="shrink-0 lg:order-1"
                                                        >
                                                            <img
                                                                className="block w-20 h-20"
                                                                src={order.variant.product.thumbnail_url}
                                                                alt={"order " + order.id}
                                                            />
                                                        </a>

                                                        <label
                                                            htmlFor="counter-input"
                                                            className="sr-only"
                                                        >
                                                            Elegir cantidad:
                                                        </label>
                                                        <div className="flex items-center justify-between lg:order-3 lg:justify-end">
                                                            <div className="flex items-center">
                                                                <button
                                                                    onClick={() => {
                                                                        alert('Decrement')
                                                                    }}
                                                                    type="button"
                                                                    id="decrement-button"
                                                                    data-input-counter-decrement="counter-input"
                                                                    className="inline-flex items-center justify-center w-5 h-5 bg-gray-100 border border-gray-300 rounded-md shrink-0 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                                                >
                                                                    <svg
                                                                        className="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none"
                                                                        viewBox="0 0 18 2"
                                                                    >
                                                                        <path
                                                                            stroke="currentColor"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M1 1h16"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                                <input
                                                                    type="text"
                                                                    id="counter-input"
                                                                    data-input-counter
                                                                    className="w-10 text-sm font-medium text-center text-gray-900 bg-transparent border-0 shrink-0 focus:outline-none focus:ring-0 dark:text-white"
                                                                    placeholder=""
                                                                    value={order.quantity}
                                                                    required
                                                                />
                                                                <button
                                                                    onClick={() => {
                                                                        alert('Increment')
                                                                    }}
                                                                    type="button"
                                                                    id="increment-button"
                                                                    data-input-counter-increment="counter-input"
                                                                    className="inline-flex items-center justify-center w-5 h-5 bg-gray-100 border border-gray-300 rounded-md shrink-0 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                                                >
                                                                    <svg
                                                                        className="h-2.5 w-2.5 text-gray-900 dark:text-white"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none"
                                                                        viewBox="0 0 18 18"
                                                                    >
                                                                        <path
                                                                            stroke="currentColor"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 1v16M1 9h16"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <div className="text-end lg:order-4 lg:w-32">
                                                                <p className="text-base font-bold text-gray-900 dark:text-white">
                                                                    ${(order.variant.retail_price * order.quantity).toFixed(2)}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div className="flex-1 w-full min-w-0 space-y-4 lg:order-2 lg:max-w-md">
                                                            <a
                                                                href="#"
                                                                className="text-base font-medium text-gray-900 hover:underline dark:text-white"
                                                            >
                                                                {order.variant.product.name}
                                                            </a>

                                                            <div className="flex items-center gap-4">
                                                                <button
                                                                    type="button"
                                                                    className="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white"
                                                                >
                                                                    <svg
                                                                        className="me-1.5 h-5 w-5"
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
                                                                            d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z"
                                                                        />
                                                                    </svg>
                                                                    Add to
                                                                    Favorites
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    className="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500"
                                                                >
                                                                    <svg
                                                                        className="me-1.5 h-5 w-5"
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
                                                                            d="M6 18 17.94 6M18 18 6.06 6"
                                                                        />
                                                                    </svg>
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="flex-1 max-w-4xl mx-auto mt-6 space-y-6 lg:mt-0 lg:w-full">
                                        <div className="p-4 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                                            <p className="text-xl font-semibold text-gray-900 dark:text-white">
                                                Order summary
                                            </p>

                                            <div className="space-y-4">
                                                <div className="space-y-2">
                                                    <dl className="flex items-center justify-between gap-4">
                                                        <dt className="text-base font-normal text-gray-500 dark:text-gray-400">
                                                            Precio original
                                                        </dt>
                                                        <dd className="text-base font-medium text-gray-900 dark:text-white">
                                                            $0.00
                                                        </dd>
                                                    </dl>

                                                    <dl className="flex items-center justify-between gap-4">
                                                        <dt className="text-base font-normal text-gray-500 dark:text-gray-400">
                                                            Ahorros
                                                        </dt>
                                                        <dd className="text-base font-medium text-green-600">
                                                            -$0.00
                                                        </dd>
                                                    </dl>

                                                    <dl className="flex items-center justify-between gap-4">
                                                        <dt className="text-base font-normal text-gray-500 dark:text-gray-400">
                                                            Envío
                                                        </dt>
                                                        <dd className="text-base font-medium text-gray-900 dark:text-white">
                                                            $0.00
                                                        </dd>
                                                    </dl>

                                                    <dl className="flex items-center justify-between gap-4">
                                                        <dt className="text-base font-normal text-gray-500 dark:text-gray-400">
                                                            Impuestos (IVA)
                                                        </dt>
                                                        <dd className="text-base font-medium text-gray-900 dark:text-white">
                                                            $0.00
                                                        </dd>
                                                    </dl>
                                                </div>

                                                <dl className="flex items-center justify-between gap-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                    <dt className="text-base font-bold text-gray-900 dark:text-white">
                                                        Total
                                                    </dt>
                                                    <dd className="text-base font-bold text-gray-900 dark:text-white">
                                                        $8,191.00
                                                    </dd>
                                                </dl>
                                            </div>

                                            <Button color="blue" className="flex w-full items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white">Proceder al pago</Button>

                                            <div className="flex items-center justify-center gap-2">
                                                <span className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                    {" "}
                                                    ó{" "}
                                                </span>
                                                <Link href={route('products.index')} className='inline-flex items-center gap-2 text-sm font-medium text-blue-700 underline hover:no-underline dark:text-blue-500'>
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
                                                        ¿Tienes un vale o una tarjeta regalo?{" "}
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
