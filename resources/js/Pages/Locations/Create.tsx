import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { PageProps } from "@/types";
import {
    Button,
} from "flowbite-react";
import { FormEventHandler } from "react";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";

export default function Create({ auth }: PageProps) {
    const { data, setData, post, errors, processing } =
        useForm({
            state_name: "",
            city: "",
            locality: "",
            address: "",
            zip: "",
            phone: "",
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route("locations.store"));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Crear dirección
                </h2>
            }
        >
            <Head title="Crear dirección" />

            <div className="py-12">
                <div className="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <div className="max-w-2xl px-4 py-8 mx-auto lg:py-16">
                            <h2 className="mb-4 text-4xl font-medium text-gray-900 dark:text-gray-100">
                                Crear una dirección nueva
                            </h2>
                            <form
                                onSubmit={submit}
                                className="flex flex-col max-w-md gap-2 mx-auto"
                            >
                                <div>
                                    <span className="text-gray-900 dark:text-gray-100">
                                        Por el momento, solo hay entregas a
                                        México.
                                    </span>
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="state_name"
                                        value="Estado"
                                    />

                                    <TextInput
                                        id="state_name"
                                        className="block w-full mt-1"
                                        value={data.state_name}
                                        onChange={(e) =>
                                            setData(
                                                "state_name",
                                                e.target.value
                                            )
                                        }
                                        required
                                        isFocused
                                        autoComplete="state_name"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.state_name}
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="city" value="Ciudad" />

                                    <TextInput
                                        id="city"
                                        className="block w-full mt-1"
                                        value={data.city}
                                        onChange={(e) =>
                                            setData("city", e.target.value)
                                        }
                                        required
                                        isFocused
                                        autoComplete="city"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.city}
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="locality"
                                        value="Colonia"
                                    />

                                    <TextInput
                                        id="locality"
                                        className="block w-full mt-1"
                                        value={data.locality}
                                        onChange={(e) =>
                                            setData("locality", e.target.value)
                                        }
                                        required
                                        isFocused
                                        autoComplete="locality"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.locality}
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="address"
                                        value="Dirección"
                                    />

                                    <TextInput
                                        id="address"
                                        className="block w-full mt-1"
                                        value={data.address}
                                        onChange={(e) =>
                                            setData("address", e.target.value)
                                        }
                                        required
                                        isFocused
                                        autoComplete="address"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.address}
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="zip"
                                        value="Código postal"
                                    />

                                    <TextInput
                                        id="zip"
                                        className="block w-full mt-1"
                                        value={data.zip}
                                        onChange={(e) =>
                                            setData("zip", e.target.value)
                                        }
                                        required
                                        isFocused
                                        autoComplete="zip"
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.zip}
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="phone"
                                        value="Teléfono"
                                    />

                                    <div className="flex items-center">
                                        <span className="mx-2 text-gray-900 dark:text-gray-100">
                                            {"(+52) "}
                                        </span>
                                        <TextInput
                                            id="phone"
                                            type="tel"
                                            className="block w-full mt-1"
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            required
                                            isFocused
                                            autoComplete="phone"
                                        />
                                    </div>

                                    <InputError
                                        className="mt-2"
                                        message={errors.phone}
                                    />
                                </div>

                                <div className="flex justify-center mt-4">
                                    <Button
                                        type="submit"
                                        color={"blue"}
                                        disabled={processing}
                                    >
                                        Crear dirección
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
