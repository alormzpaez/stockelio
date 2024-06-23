import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { PageProps, Product, Variant } from "@/types";
import { Button, Toast, Label, Select, Carousel } from "flowbite-react";
import { FormEventHandler, useState } from "react";
import { FaArrowLeft } from "react-icons/fa";
import { FaCartShopping, FaPencil } from "react-icons/fa6";
import { HiFire, HiX } from "react-icons/hi";

export default function Show({
    auth,
    product,
    flash,
    can,
}: PageProps<{ product: Product }>) {
    const [selectedVariant, setSelectedVariant] = useState<Variant | null>(
        product.cheapest_variant
    );

    const { data, setData, post, processing, errors } = useForm({
        variant_id: selectedVariant?.id,
        quantity: 1,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route("orders.store"));
    };

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

                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg md:px-4">
                        <div className="flex justify-between gap-4 p-6 text-4xl font-medium text-gray-900 dark:text-gray-100">
                            <Button
                                color="gray"
                                onClick={() => {
                                    router.visit(route("products.index"));
                                }}
                                disabled={processing}
                            >
                                <div className="flex items-center gap-2">
                                    <FaArrowLeft className="mr-2" />
                                    Volver a productos
                                </div>
                            </Button>
                            {can["update product"] && (
                                <Button
                                    color="blue"
                                    onClick={() => {
                                        router.visit(
                                            route("products.edit", product.id)
                                        );
                                    }}
                                    disabled={processing}
                                >
                                    <div className="flex items-center gap-2">
                                        <FaPencil className="mr-2" />
                                        Editar producto
                                    </div>
                                </Button>
                            )}
                        </div>
                        <hr className="h-px bg-gray-200 border-0 dark:bg-gray-700"></hr>
                        <section className="px-5 py-8 antialiased bg-white md:py-16 dark:bg-gray-800">
                            <div className="max-w-screen-xl mx-auto 2xl:px-0">
                                <div className="w-full lg:flex lg:justify-center lg:gap-4 xl:gap-16">
                                    <div className="flex items-center grow lg:flex-1">
                                        <div className="flex w-full h-56 sm:h-64 xl:h-80 2xl:h-96">
                                            <Carousel className="bg-gray-300 rounded-lg dark:bg-gray-700">
                                                {product.files.map((file) => (
                                                    <img
                                                        className="w-auto h-full"
                                                        src={file.url}
                                                        alt="..."
                                                    />
                                                ))}
                                            </Carousel>
                                        </div>
                                    </div>

                                    <div className="mt-6 lg:w-1/2 sm:mt-8 lg:mt-0">
                                        <h1 className="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                                            {selectedVariant?.name}
                                        </h1>
                                        <div className="mt-4 sm:items-center sm:gap-4 sm:flex">
                                            <p className="text-2xl font-extrabold text-gray-900 sm:text-3xl dark:text-white">
                                                <span className="text-base font-normal text-gray-500 sm:text-lg dark:text-gray-400">
                                                    A solo:&nbsp;
                                                </span>
                                                ${selectedVariant?.retail_price}
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
                                                <Link
                                                    href={""}
                                                    className="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white"
                                                >
                                                    345 Reviews
                                                </Link>
                                            </div>
                                        </div>
                                        <div className="mt-4 sm:items-center sm:gap-4 sm:flex">
                                            <p className="text-base font-extrabold text-gray-900 dark:text-white">
                                                <span className="text-base font-normal text-gray-500 dark:text-gray-400">
                                                    Id de variante:&nbsp;
                                                </span>
                                                #{selectedVariant?.id}
                                            </p>
                                        </div>
                                        <form onSubmit={submit}>
                                            <div className="flex flex-col items-stretch gap-2 mt-6 md:items-center xl:flex-row xl:gap-4">
                                                <div className="md:w-2/3">
                                                    <div className="block mb-2">
                                                        <Label
                                                            className="mb-2"
                                                            htmlFor="variants"
                                                            value="Selecciona una variante:"
                                                        />
                                                    </div>
                                                    <Select
                                                        id="variants"
                                                        required
                                                        onChange={(e) => {
                                                            let variant =
                                                                product
                                                                    .variants[
                                                                    Number(
                                                                        e
                                                                            .currentTarget
                                                                            .value
                                                                    )
                                                                ];
                                                            setData(
                                                                "variant_id",
                                                                variant.id
                                                            );
                                                            setSelectedVariant(
                                                                variant
                                                            );
                                                        }}
                                                        defaultValue={product.variants.findIndex(
                                                            (variant) =>
                                                                variant.id ==
                                                                selectedVariant?.id
                                                        )}
                                                    >
                                                        {product.variants.map(
                                                            (
                                                                variant,
                                                                index
                                                            ) => (
                                                                <option
                                                                    value={
                                                                        index
                                                                    }
                                                                    key={index}
                                                                >
                                                                    {variant.name +
                                                                        " ($" +
                                                                        variant.retail_price +
                                                                        ")"}
                                                                </option>
                                                            )
                                                        )}
                                                    </Select>
                                                </div>
                                                <div className="flex items-center justify-center">
                                                    <Label
                                                        htmlFor="variants"
                                                        value={"Cantidad:"}
                                                    />
                                                    &nbsp;
                                                    <button
                                                        type="button"
                                                        id="decrement-button"
                                                        data-input-counter-decrement="counter-input"
                                                        className="inline-flex items-center justify-center w-5 h-5 bg-gray-100 border border-gray-300 rounded-md shrink-0 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                                        onClick={() =>
                                                            data.quantity > 1
                                                                ? setData(
                                                                      "quantity",
                                                                      data.quantity -
                                                                          1
                                                                  )
                                                                : null
                                                        }
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
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
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
                                                        value={data.quantity}
                                                        required
                                                    />
                                                    <button
                                                        type="button"
                                                        id="increment-button"
                                                        data-input-counter-increment="counter-input"
                                                        className="inline-flex items-center justify-center w-5 h-5 bg-gray-100 border border-gray-300 rounded-md shrink-0 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                                        onClick={() =>
                                                            setData(
                                                                "quantity",
                                                                data.quantity +
                                                                    1
                                                            )
                                                        }
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
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth="2"
                                                                d="M9 1v16M1 9h16"
                                                            />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <Button
                                                    type="submit"
                                                    className="md:w-2/3"
                                                    color="gray"
                                                    disabled={processing}
                                                >
                                                    <FaCartShopping className="mr-2" />
                                                    Agregar a carrito
                                                </Button>
                                            </div>
                                            <div className="mt-2 text-sm text-red-600 dark:text-red-500">
                                                {errors.variant_id && (
                                                    <span>
                                                        {errors.variant_id}
                                                    </span>
                                                )}
                                                {errors.quantity && (
                                                    <span>
                                                        {errors.quantity}
                                                    </span>
                                                )}
                                            </div>
                                        </form>
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
