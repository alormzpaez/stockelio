import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { Location, PageProps } from "@/types";
import { Button } from "flowbite-react";
import { FormEventHandler, useState } from "react";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import Modal from "@/Components/Modal";

export default function Edit({ 
    auth,
    location,
}: PageProps<{
    location: Location
}>) {
    const [confirmingLocationDeletion, setConfirmingLocationDeletion] =
        useState(false);

    const { data, setData, put, errors, delete: destroy, processing } = useForm({
        state_name: location.state_name,
        city: location.city,
        locality: location.locality,
        address: location.address,
        zip: location.zip,
        phone: location.phone,
    });

    const confirmLocationDeletion = () => {
        console.log("Hey");

        setConfirmingLocationDeletion(true);
    };

    const closeModal = () => {
        setConfirmingLocationDeletion(false);
    };

    const submitUpdate: FormEventHandler = (e) => {
        e.preventDefault();

        put(route("locations.update", location.id));
    };

    const submitDestroy: FormEventHandler = (e) => {
        e.preventDefault();

        destroy(route("locations.destroy", location.id));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Editar dirección
                </h2>
            }
        >
            <Head title="Editar dirección" />

            <Modal
                show={confirmingLocationDeletion}
                onClose={closeModal}
                closeable
            >
                <form onSubmit={submitDestroy} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        ¿Estás seguro que quieres eliminar esta dirección?
                    </h2>

                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Este sistema no almacenará nunca más esta información. Por lo tanto, esta dirección no podrá ser recuperada.
                    </p>

                    <div className="flex justify-end mt-6">
                        <Button color={"gray"} onClick={closeModal}>Cancelar</Button>

                        <Button color={"failure"} type="submit" className="ms-3" disabled={processing}>
                            Eliminar
                        </Button>
                    </div>
                </form>
            </Modal>

            <div className="py-12">
                <div className="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                    <div className="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                        <div className="max-w-2xl px-4 py-8 mx-auto lg:py-16">
                            <h2 className="mb-4 text-4xl font-medium text-gray-900 dark:text-gray-100">
                                Editar dirección
                            </h2>
                            <form onSubmit={submitUpdate} className="flex flex-col max-w-md gap-2 mx-auto">
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

                                <div className="flex items-center gap-2 mt-4 justify-evenly">
                                    <Button
                                        type="submit"
                                        color={"success"}
                                        disabled={processing}
                                    >
                                        Actualizar dirección
                                    </Button>
                                    
                                    <Button
                                        onClick={confirmLocationDeletion}
                                        color={"failure"}
                                        disabled={processing}
                                    >
                                        Eliminar
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
