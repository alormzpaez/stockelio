import { Button, Card as FlowbiteCard } from "flowbite-react";
import { MouseEvent } from "react";

export default function Card({ name, imgUrl, variantsCount, price, onClick }: {
    name: string,
    imgUrl: string,
    variantsCount: number,
    price: number,
    onClick?: (event: MouseEvent<HTMLDivElement>) => void;
}) {
    return (
        <FlowbiteCard
            className="max-w-xs overflow-hidden cursor-pointer"
            imgAlt={name}
            imgSrc={imgUrl}
            onClick={onClick}
        >
            <div className='flex'>
                <h5 className="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    { name }
                </h5>
            </div>
            <div className="flex items-center">
                <span className='text-gray-900 dark:text-gray-100'>
                    Con <span className="rounded bg-cyan-100 px-2.5 py-0.5 text-sm font-semibold text-cyan-800 dark:bg-cyan-200 dark:text-cyan-800">
                        { variantsCount }
                    </span> variantes
                </span>
            </div>
            <div className="flex items-center justify-between gap-2">
                <span className="text-gray-900 dark:text-gray-100">
                    Desde <span className="text-3xl font-bold text-gray-900 dark:text-white">${price.toFixed(2)}</span>
                </span>
                <Button color="green" disabled={variantsCount == 0}>Leer más</Button>
            </div>
        </FlowbiteCard>
    );
}
