import { Notification as NotificationType } from "@/types";
import { Toast } from "flowbite-react";
import { useEffect, useState } from "react";
import { FaShippingFast } from "react-icons/fa";

export default function Notification({
    notification,
    duration = 5000,
    autoDismiss,
}: {
    notification: NotificationType;
    duration?: number;
    autoDismiss: boolean;
}) {
    const [title, setTitle] = useState("");
    const [body, setBody] = useState("");
    const [show, setShow] = useState(true);

    useEffect(() => {
        if (notification.type == "App\\Notifications\\PackageShipped") {
            setTitle(`Tu orden #${notification.order_id} ya fue enviada`);
            setBody(`Haz click para rastrear su seguimiento`);
        }

        if (autoDismiss) {
            setTimeout(() => {
                setShow(false);
            }, duration);
        }
    }, []);

    const toastStyle = `
        .toast {
            opacity: 1;
            animation: fade-out ${
                duration / 1000
            }s cubic-bezier(0.7, 0, 0.84, 0);
            animation-fill-mode: forwards;
        }

        @keyframes fade-out {
            0% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
    `;

    return (
        <>
            {autoDismiss ? (
                <>
                    {show && (
                        <>
                            <Toast className="cursor-pointer toast">
                                <div className="inline-flex items-center justify-center w-8 h-8 text-green-500 bg-green-100 rounded-lg shrink-0 dark:bg-green-800 dark:text-green-200">
                                    <FaShippingFast className="w-5 h-5" />
                                </div>
                                <div className="flex flex-col ml-3 text-sm">
                                    <div className="text-sm font-semibold text-gray-900 dark:text-white">
                                        {title}
                                    </div>
                                    <div className="font-normal">{body}</div>
                                </div>
                            </Toast>

                            <style>{toastStyle}</style>
                        </>
                    )}
                </>
            ) : (
                <div className="flex w-full px-4 py-2 border-b border-gray-100 dark:border-gray-600 min-h-14">
                    <div className="inline-flex items-center justify-center w-8 h-8 text-green-500 bg-green-100 rounded-lg shrink-0 dark:bg-green-800 dark:text-green-200">
                        <FaShippingFast className="w-5 h-5" />
                    </div>
                    <div className="w-full text-sm text-left ps-3">
                        <div className="font-semibold text-gray-900 dark:text-white">
                            {title}
                        </div>
                        <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                            {body}
                        </div>
                        <div className="text-xs text-blue-600 dark:text-blue-500">
                            a few moments ago
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
