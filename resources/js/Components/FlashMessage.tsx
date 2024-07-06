import { Flash } from "@/types";
import { Toast } from "flowbite-react";
import { HiFire, HiX } from "react-icons/hi";

export default function FlashMessage({ flash }: { flash: Flash }) {
    return (
        <>
            {flash.message ? (
                <Toast className="gap-1 mb-2">
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
    );
}
