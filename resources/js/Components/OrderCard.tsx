import { useForm } from "@inertiajs/react";
import { Button } from "flowbite-react";
import { FormEventHandler } from "react";

export default function OrderCard({
    id,
    productName,
    imgUrl,
    quantity,
    retailPrice,
}: {
    id: number;
    productName: string;
    imgUrl: string;
    quantity: number;
    retailPrice: number;
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
        delete: destroy,
    } = useForm({
        quantity: quantity,
    });

    const submitUpdate: FormEventHandler = (e) => {
        e.preventDefault();

        if (quantity == data.quantity) {
            return null;
        }

        put(route("orders.update", id));
    };

    const submitDestroyOrder: FormEventHandler = (e) => {
        e.preventDefault();

        destroy(route("orders.destroy", id));
    };

    return (
        <div className="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
            <div className="space-y-4 lg:flex lg:items-center lg:justify-between lg:gap-6 lg:space-y-0">
                <a href="#" className="shrink-0 lg:order-1">
                    <img
                        className="block w-20 h-20"
                        src={imgUrl}
                        alt={"order " + id}
                    />
                </a>

                <label htmlFor="counter-input" className="sr-only">
                    Elegir cantidad:
                </label>
                <div className="flex items-center justify-between lg:order-3 lg:justify-end">
                    <div className="flex items-center">
                        <form onSubmit={submitUpdate}>
                            <Button
                                type="submit"
                                color={"transparent"}
                                disabled={processing}
                                onClick={() =>
                                    data.quantity > 1
                                        ? setData("quantity", data.quantity - 1)
                                        : null
                                }
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
                            </Button>
                            <input
                                type="text"
                                id="counter-input"
                                data-input-counter
                                className="w-10 text-sm font-medium text-center text-gray-900 bg-transparent border-0 shrink-0 focus:outline-none focus:ring-0 dark:text-white"
                                placeholder=""
                                value={data.quantity}
                                required
                            />
                            <Button
                                type="submit"
                                color={"transparent"}
                                disabled={processing}
                                onClick={() =>
                                    setData("quantity", data.quantity + 1)
                                }
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
                            </Button>
                            <div className="mt-2 text-sm text-red-600 dark:text-red-500">
                                {errors.quantity && (
                                    <span>{errors.quantity}</span>
                                )}
                            </div>
                        </form>
                    </div>
                    <div className="text-gray-900 dark:text-gray-400 text-end lg:order-4 lg:w-32">
                        Subtotal:
                        <p className="text-base font-bold text-gray-900 dark:text-white">
                            ${(retailPrice * quantity).toFixed(2)}
                        </p>
                    </div>
                </div>

                <div className="flex-1 w-full min-w-0 space-y-4 lg:order-2 lg:max-w-md">
                    <div className="flex flex-col gap-2">
                        <a
                            href="#"
                            className="text-base font-medium text-gray-900 hover:underline dark:text-white"
                        >
                            {productName}
                        </a>
                        <div className="flex text-base">
                            <span className="text-gray-900 dark:text-gray-400">
                                Precio individual:
                            </span>
                            &nbsp;
                            <span className="text-gray-900 dark:text-white">
                                ${retailPrice.toFixed(2)}
                            </span>
                        </div>
                    </div>

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
                            Añadir a favoritos
                        </button>

                        <form onSubmit={submitDestroyOrder}>
                            <Button
                                className="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500"
                                type="submit"
                                color={"transparent"}
                                disabled={processing}
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
                                Eliminar
                            </Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}
