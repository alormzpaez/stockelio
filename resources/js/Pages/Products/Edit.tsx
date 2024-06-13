import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { PageProps, Product } from "@/types";
import {
    Button,
    FileInput,
    Label,
    TextInput,
    Textarea,
    Toast,
} from "flowbite-react";
import { FormEventHandler } from "react";
import { HiFire } from "react-icons/hi";

export default function Edit({
    auth,
    product,
    flash,
}: PageProps<{ product: Product }>) {
    const { data, setData, post, processing, errors, progress } = useForm<{
        _method: string;
        description: string;
        files: FileList | Array<any>;
    }>({
        _method: "put",
        description: product.description,
        files: [],
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route("products.update", product.id));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Editar producto
                </h2>
            }
        >
            <Head title="Editar producto" />

            <div className="py-12">
                <div className="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
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
                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <div className="max-w-2xl px-4 py-8 mx-auto lg:py-16">
                            <h2 className="mb-4 text-4xl font-medium text-gray-900 dark:text-gray-100">
                                Actualizar la información del producto
                            </h2>
                            <form onSubmit={submit}>
                                <div className="grid gap-4 mb-4 sm:grid-cols-2 sm:gap-6 sm:mb-5">
                                    <div className="sm:col-span-2">
                                        <div className="block mb-2">
                                            <Label
                                                htmlFor="name"
                                                value="Nombre del producto"
                                            />
                                        </div>
                                        <TextInput
                                            type="text"
                                            id="name"
                                            disabled
                                            value={product.name}
                                        />
                                    </div>
                                    <div className="sm:col-span-2">
                                        <div className="block mb-2">
                                            <Label
                                                htmlFor="description"
                                                value="Descripción del producto"
                                            />
                                        </div>
                                        <Textarea
                                            id="description"
                                            placeholder="Escribe una descripción clara acerca de lo que trata el producto..."
                                            required
                                            rows={4}
                                            value={data.description}
                                            onChange={(e) =>
                                                setData(
                                                    "description",
                                                    e.currentTarget.value
                                                )
                                            }
                                        />
                                    </div>
                                    <div className="flex justify-between text-sm text-gray-900 sm:col-span-2 dark:text-gray-100">
                                        <strong>
                                            Número total de variantes:
                                        </strong>
                                        <span>
                                            {product.variants_count +
                                                " " +
                                                (product.variants_count != 1
                                                    ? "variantes"
                                                    : "variante")}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-sm text-gray-900 sm:col-span-2 dark:text-gray-100">
                                        <strong>Creación:</strong>
                                        <span>{product.created_at}</span>
                                    </div>
                                    <div className="flex justify-between text-sm text-gray-900 sm:col-span-2 dark:text-gray-100">
                                        <strong>Última vez actualizado:</strong>
                                        <span>{product.updated_at}</span>
                                    </div>
                                    <div className="pt-3 border-t border-gray-200 dark:border-gray-700 sm:col-span-2">
                                        <div className="block mb-2">
                                            <Label
                                                htmlFor="files"
                                                value="Imagenes del producto"
                                            />
                                        </div>
                                        <div className="flex items-center justify-center w-full">
                                            <Label
                                                htmlFor="files"
                                                className="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            >
                                                <div className="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg
                                                        className="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 20 16"
                                                    >
                                                        <path
                                                            stroke="currentColor"
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                        />
                                                    </svg>
                                                    <p className="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <span className="font-semibold text-justify">
                                                            Click para subir
                                                        </span>{" "}
                                                        ó arrastra y suelta
                                                    </p>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                                        SVG, PNG, JPG ó GIF
                                                        (MÁX. 800x400px)
                                                    </p>
                                                </div>
                                                <FileInput
                                                    id="files"
                                                    className="hidden"
                                                    multiple
                                                    onChange={(e) =>
                                                        e.currentTarget.files &&
                                                        setData(
                                                            "files",
                                                            e.currentTarget
                                                                .files
                                                        )
                                                    }
                                                />
                                            </Label>
                                        </div>
                                        <div className="flex justify-center mt-2 text-sm text-gray-900 sm:col-span-2 dark:text-gray-100">
                                            <span>
                                                Hay {data.files.length} archivos
                                                cargados.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex justify-center space-x-4">
                                    <Button color="success" type="submit">
                                        Actualizar producto
                                    </Button>
                                    <button
                                        type="button"
                                        className="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                                    >
                                        <svg
                                            className="w-5 h-5 mr-1 -ml-1"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                                <div className="mt-4 text-sm text-red-600 dark:text-red-500">
                                    {errors.description && (
                                        <span>{errors.description}</span>
                                    )}
                                    {errors.files && (
                                        <span>{errors.files}</span>
                                    )}
                                    {Object.keys(errors)
                                        .filter((key) =>
                                            key.startsWith("files.")
                                        )
                                        .map((key) => (
                                            <span>
                                                {
                                                    (
                                                        errors as {
                                                            [
                                                                key: string
                                                            ]: string;
                                                        }
                                                    )[key]
                                                }
                                            </span>
                                        ))}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
