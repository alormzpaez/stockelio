import { Dropdown } from "flowbite-react";
import { useEffect } from "react";
import { HiBell } from "react-icons/hi";
import { Notification as NotificationType } from "@/types";
import { FaEye, FaSpinner } from "react-icons/fa";
import Notification from "./Notification";
import { useIntersectionObserver } from "@uidotdev/usehooks";
import axios from "axios";
import { router } from "@inertiajs/react";

export default function NotificationsDropdown({
    unreadNotificationsExists,
    notifications,
    nextCursor,
    onScrollEnd,
    onBeginning,
}: {
    unreadNotificationsExists: boolean;
    notifications: NotificationType[]|null;
    nextCursor: string|null;
    onScrollEnd: Function;
    onBeginning: Function;
}) {
    const [ref, entry] = useIntersectionObserver({
        threshold: 0,
        root: null,
        rootMargin: "0px",
    });

    const markAllNotificationsAsRead = (onSuccess: Function) => {
        axios
            .put(`${window.location.origin}/api/v1/notifications`, {
                are_read: true,
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

    const handleOnClick = () => {
        markAllNotificationsAsRead(() => {
            router.reload({
                only: ["auth.user.unread_notifications_exists"],
            });
        })
    };

    useEffect(() => {
        if (entry?.isIntersecting) {
            onScrollEnd()
        }
    }, [entry?.isIntersecting]);

    return (
        <Dropdown
            label=""
            inline
            placement="bottom"
            renderTrigger={() => (
                <button
                    onClickCapture={() => {
                        onBeginning()
                    }}
                    className="mr-2 relative rounded-lg p-2.5 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                >
                    {unreadNotificationsExists ? (
                        <>
                            <HiBell className="text-xl" />
                            <div className="absolute right-1.5 top-2 block w-3 h-3 bg-red-500 border-2 border-white rounded-full dark:border-gray-900"></div>
                        </>
                    ) : (
                        <HiBell className="text-xl" />
                    )}
                </button>
            )}
        >
            <Dropdown.Header className="flex justify-center">
                <b>Mis notificaciones</b>
            </Dropdown.Header>
            <div className="justify-center overflow-auto w-72 md:w-80 max-h-96">
                {!notifications ? (
                    <div className="flex justify-center py-3">
                        <svg
                            className="w-5 h-5 mr-3 animate-spin"
                            viewBox="0 0 24 24"
                        >
                            <FaSpinner className="text-2xl" />
                        </svg>
                    </div>
                ) : notifications.length == 0 ? (
                    <div className="flex justify-center w-full py-2">
                        <span className="text-sm text-gray-900 dark:text-gray-100">
                            Sin notificaciones
                        </span>
                    </div>
                ) : (
                    <>
                        {notifications.map(
                            (notification: NotificationType, index) => (
                                <Dropdown.Item className="p-0" key={index}>
                                    <Notification
                                        autoDismiss={false}
                                        notification={notification}
                                    />
                                </Dropdown.Item>
                            )
                        )}
                        {nextCursor && (
                            <div ref={ref} className="flex justify-center py-3">
                                <svg
                                    className="w-5 h-5 mr-3 animate-spin"
                                    viewBox="0 0 24 24"
                                >
                                    <FaSpinner className="text-2xl" />
                                </svg>
                            </div>
                        )}
                    </>
                )}
            </div>
            <Dropdown.Item onClick={handleOnClick}>
                <div className="flex justify-center w-full py-1">
                    <span className="flex items-center justify-center gap-3 text-sm text-gray-900 dark:text-gray-100">
                        <FaEye className="text-2xl text-gray-500 dark:text-gray-400" />
                        Marcar todas como vistas
                    </span>
                </div>
            </Dropdown.Item>
        </Dropdown>
    );
}
