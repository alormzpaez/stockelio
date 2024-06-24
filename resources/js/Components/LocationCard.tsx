import { router, useForm } from "@inertiajs/react";
import { Button, Card, Dropdown } from "flowbite-react";
import { FormEventHandler, useState } from "react";
import { HiDotsVertical, HiStar } from "react-icons/hi";
import Modal from "./Modal";

export default function LocationCard({
    id,
    fullAddress,
    country,
    phone,
    isPreferred,
}: {
    id: number;
    fullAddress: string;
    country: string;
    phone: string;
    isPreferred: boolean;
}) {
    const [confirmingLocationDeletion, setConfirmingLocationDeletion] =
        useState(false);

    const {
        data,
        setData,
        put,
        errors,
        delete: destroy,
        processing,
    } = useForm({
        is_preferred: isPreferred,
    });

    const confirmLocationDeletion = () => {
        setConfirmingLocationDeletion(true);
    };

    const closeModal = () => {
        setConfirmingLocationDeletion(false);
    };

    const submitUpdatePreferredLocation: FormEventHandler = (e) => {
        e.preventDefault();

        put(route("locations.update", id), {
            preserveScroll: true,
        });
    };

    const submitDestroy: FormEventHandler = (e) => {
        e.preventDefault();

        destroy(route("locations.destroy", id), {
            preserveScroll: true,
            onSuccess: () => closeModal()
        });
    };

    return (
        <Card>
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

            <div className="flex justify-between">
                <div className="flex flex-col gap-2">
                    <span className="text-base text-gray-900 dark:text-white">
                        {fullAddress}
                    </span>
                    <span className="text-base text-gray-900 dark:text-white">
                        {country}
                    </span>
                    <span className="text-base text-gray-900 dark:text-white">
                        Tel: {phone}
                    </span>
                    {isPreferred && (
                        <span className="flex items-center gap-2 text-base text-blue-700 dark:text-blue-500">
                            <HiStar className="text-2xl" /> Predeterminada
                        </span>
                    )}
                </div>
                <div className="flex flex-col justify-start">
                    <Dropdown
                        label=""
                        inline
                        dismissOnClick={false}
                        renderTrigger={() => (
                            <div className="p-2 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                                <HiDotsVertical className="text-2xl text-gray-900 dark:text-white" />
                            </div>
                        )}
                    >
                        <Dropdown.Item
                            onClick={() =>
                                router.visit(route("locations.edit", id))
                            }
                        >
                            Editar
                        </Dropdown.Item>
                        <Dropdown.Item>
                            <form onSubmit={submitUpdatePreferredLocation}>
                                <button
                                    type="submit"
                                    onClick={() =>
                                        setData("is_preferred", true)
                                    }
                                >
                                    Establecer como predeterminada
                                </button>
                            </form>
                        </Dropdown.Item>
                        <Dropdown.Divider />
                        <Dropdown.Item
                            onClick={confirmLocationDeletion}
                            className="font-bold text-red-700 dark:text-red-500"
                        >
                            Eliminar
                        </Dropdown.Item>
                    </Dropdown>
                </div>
            </div>
        </Card>
    );
}
