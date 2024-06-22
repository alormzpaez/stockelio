import { router } from "@inertiajs/react";
import { Badge } from "flowbite-react";
import { FaClock } from "react-icons/fa";

export default function ShowableOrderCard({
    id,
    variantName,
    imgUrl,
    quantity,
    retailPrice,
    status,
}: {
    id: number;
    variantName: string;
    imgUrl: string | null;
    quantity: number;
    retailPrice: number;
    status: string;
}) {
    const onClick = () => {
        router.visit(route("orders.show", id))
    }

    return (
        <div onClick={onClick} className="p-4 bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer dark:border-gray-700 dark:bg-gray-800 md:p-6 hover:bg-gray-100 hover:dark:bg-gray-700">
            <div className="p-2 space-y-4">
                <div className="flex items-center gap-6">
                    <div className="">
                        <img
                            className="block w-20 h-20"
                            src={imgUrl ?? ""}
                            alt={"order " + id}
                        />
                    </div>

                    <div
                        className="flex-1 min-w-0 font-medium text-gray-900 dark:text-white"
                    >
                        {variantName}
                    </div>
                </div>

                <div className="flex items-center justify-between gap-4">
                    <div className="flex flex-col gap-1">
                        <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                            <span className="font-medium text-gray-900 dark:text-white">
                                ID de Orden:
                            </span>{" "}
                            {id}
                        </p>
                        <p className="text-sm font-normal text-gray-500 dark:text-gray-400">
                            <span className="font-medium text-gray-900 dark:text-white">
                                Precio individual:
                            </span>{" "}
                            ${retailPrice.toFixed(2)}
                        </p>
                    </div>

                    <div className="flex items-center justify-end gap-4">
                        <p className="text-base font-normal text-gray-900 dark:text-white">
                            x{quantity}
                        </p>

                        <div className="flex flex-col items-end">
                            <span className="text-sm font-normal text-gray-500 dark:text-gray-400">
                                Total:
                            </span>
                            <p className="text-xl font-bold leading-tight text-gray-900 dark:text-white">
                                ${(retailPrice * quantity).toFixed(2)}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="flex items-center gap-2">
                    <span className="font-medium text-gray-900 dark:text-white">
                        Estatus:
                    </span>
                    {status == "pending" ? (
                        <Badge
                            size={"xs"}
                            className="w-fit"
                            icon={FaClock}
                            color="warning"
                        >
                            Pendiente
                        </Badge>
                    ) : null}
                </div>
            </div>
        </div>
    );
}
