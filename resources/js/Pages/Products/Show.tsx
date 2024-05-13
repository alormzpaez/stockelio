import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import { PageProps, Product } from "@/types";
import { Button } from "flowbite-react";
import { useEffect } from "react";
import { FaArrowLeft } from "react-icons/fa";
import { FaCartShopping } from "react-icons/fa6";

export default function Show({
    auth,
    product,
}: PageProps<{ product: Product }>) {
    useEffect(() => {
        console.log(product);
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Producto
                </h2>
            }
        >
            <Head title="Producto" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div className="p-6 text-4xl font-medium text-gray-900 dark:text-gray-100">
                            <Button
                                color="gray"
                                onClick={() => {
                                    router.visit(route("products.index"));
                                }}
                            >
                                <FaArrowLeft className="mr-2" />
                                Volver a productos
                            </Button>
                        </div>
                        <hr className="h-px bg-gray-200 border-0 dark:bg-gray-700"></hr>
                        <section className="px-8 py-8 antialiased bg-white md:py-16 dark:bg-gray-800">
                            <div className="max-w-screen-xl px-4 mx-auto 2xl:px-0">
                                <div className="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                                    <div className="max-w-md mx-auto shrink-0 lg:max-w-lg">
                                        <img
                                            className="hidden w-full dark:block"
                                            src={product.thumbnail_url}
                                            alt=""
                                        />
                                    </div>

                                    <div className="mt-6 sm:mt-8 lg:mt-0">
                                        <h1 className="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                                            {product.name}
                                        </h1>
                                        <div className="mt-4 sm:items-center sm:gap-4 sm:flex">
                                            <p className="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white">
                                                <span className="text-base font-normal text-gray-500 sm:text-lg dark:text-gray-400">
                                                    Desde:&nbsp;
                                                </span>
                                                $
                                                {
                                                    product.cheapest_variant
                                                        ?.retail_price
                                                }
                                            </p>
                                            <div className="flex items-center gap-2 mt-2 sm:mt-0">
                                                <div className="flex items-center gap-1">
                                                    <svg
                                                        className="w-4 h-4 text-yellow-300"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                    </svg>
                                                    <svg
                                                        className="w-4 h-4 text-yellow-300"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                    </svg>
                                                    <svg
                                                        className="w-4 h-4 text-yellow-300"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                    </svg>
                                                    <svg
                                                        className="w-4 h-4 text-yellow-300"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                    </svg>
                                                    <svg
                                                        className="w-4 h-4 text-yellow-300"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                    </svg>
                                                </div>
                                                <p className="text-sm font-medium leading-none text-gray-500 dark:text-gray-400">
                                                    (5.0)
                                                </p>
                                                <a
                                                    href="#"
                                                    className="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white"
                                                >
                                                    345 Reviews
                                                </a>
                                            </div>
                                        </div>
                                        <div className="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8">
                                            <Button
                                                color="gray"
                                                onClick={() => {
                                                    console.log(
                                                        "Added to cart"
                                                    );
                                                }}
                                            >
                                                <FaCartShopping className="mr-2" />
                                                Agregar a carrito
                                            </Button>
                                        </div>
                                        <hr className="my-6 border-gray-200 md:my-8 dark:border-gray-700" />
                                        <p className="mb-6 text-gray-500 dark:text-gray-400">
                                            {product.description}
                                        </p>
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
