import { Notification as NotificationType } from "@/types";
import { router } from "@inertiajs/react";
import axios from "axios";
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
            setTitle(`Tu orden #${notification.data.order_id} ya fue enviada`);
            setBody(`Haz click para rastrear su seguimiento`);
        }

        if (autoDismiss) {
            setTimeout(() => {
                setShow(false);
            }, duration);
        }
    }, [notification]);

    const handleOnClick = () => {
        if (!notification.read_at) {
            markNotificationAsRead(() => {
                redirect()
            })
        } else {
            redirect()
        }
    };

    const redirect = () => {
        if (notification.type == "App\\Notifications\\PackageShipped") {
            router.visit(route("orders.show", notification.data.order_id))
        }
    }

    const markNotificationAsRead = (onSuccess: Function) => {
        axios
            .put(`${window.location.origin}/api/v1/notifications/${notification.id}`, {
                is_read: true,
            })
            .then((response) => {
                if (response.status == 200) {
                    onSuccess()
                }
            })
            .catch((e) => {
                // console.log(e);
            });
    }

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
                            <Toast className="toast">
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
                <div
                    onClick={handleOnClick}
                    className={
                        "flex w-full px-4 py-2 border-b border-gray-100 dark:border-gray-600 min-h-14 " +
                        (!notification.read_at && " bg-gray-50 hover:bg-gray-100 dark:bg-slate-700 dark:hover:bg-gray-600")
                    }
                >
                    <div className="relative inline-flex items-center justify-center w-8 h-8 text-green-500 bg-green-100 rounded-lg shrink-0 dark:bg-green-800 dark:text-green-200">
                        <FaShippingFast className="w-5 h-5" />
                        {!notification.read_at && (
                            <div className="absolute block w-3 h-3 bg-red-500 border-2 border-white rounded-full -right-0.5 -top-0.5 dark:border-gray-900"></div>
                        )}
                    </div>
                    <div className="w-full text-sm text-left ps-3">
                        <div className="font-semibold text-gray-900 dark:text-white">
                            {title}
                        </div>
                        <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                            {body}
                        </div>
                        <div className="text-xs text-blue-600 dark:text-blue-500">
                            {notification.created_at}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
