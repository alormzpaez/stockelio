import { Card, Dropdown } from "flowbite-react";
import { HiDotsVertical, HiStar } from "react-icons/hi";

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
    return (
        <Card>
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
                        <Dropdown.Item>Editar</Dropdown.Item>
                        <Dropdown.Item>
                            Establecer como predeterminada
                        </Dropdown.Item>
                        <Dropdown.Divider />
                        <Dropdown.Item>
                            <span className="font-bold text-red-700 dark:text-red-500">
                                Eliminar
                            </span>
                        </Dropdown.Item>
                    </Dropdown>
                </div>
            </div>
        </Card>
    );
}
