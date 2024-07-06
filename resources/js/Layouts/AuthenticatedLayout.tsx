import { useState, PropsWithChildren, useEffect } from "react";
import ApplicationLogo from "@/Components/ApplicationLogo";
import { Link, router } from "@inertiajs/react";
import { Notification as NotificationType, User } from "@/types";
import {
    Button,
    DarkThemeToggle,
    Flowbite,
    Sidebar,
} from "flowbite-react";
import {
    HiArrowSmRight,
    HiShoppingBag,
    HiUser,
    HiMenu,
    HiHome,
} from "react-icons/hi";
import { FaBoxes, FaShoppingCart } from "react-icons/fa";
import { Avatar } from "flowbite-react";
import Notification from "@/Components/Notification";
import axios from "axios";
import NotificationsDropdown from "@/Components/NotificationsDropdown";

export default function Authenticated({
    user,
    children,
}: PropsWithChildren<{ user: User }>) {
    useEffect(() => {
        (window as any).Echo.private(`App.Models.User.${user.id}`).notification(
            (notification: NotificationType) => {
                router.reload({
                    only: ["auth.user.unread_notifications_exists"],
                });

                getNotifications(null);

                setIncomingNotifications((prevNotifications) => [
                    ...prevNotifications,
                    notification,
                ]);
            }
        );

        return () => {
            (window as any).Echo.leave(`App.Models.User.${user.id}`);
        };
    }, []);

    const [nextCursor, setNextCursor] = useState(null);
    const [notifications, setNotifications] = useState<
        NotificationType[] | null
    >(null);
    const [incomingNotifications, setIncomingNotifications] = useState<
        NotificationType[]
    >([]);
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const getNotifications = (cursor: string | null) => {
        axios
            .get(`${window.location.origin}/api/v1/notifications?cursor=${cursor}`)
            .then((response) => {
                // response.data has props: data, links and meta
                setNextCursor(response.data.meta.next_cursor);

                if (cursor) {
                    setNotifications((prevNotifications) => [
                        ...(prevNotifications ?? []),
                        ...response.data.data,
                    ]);
                } else {
                    setNotifications(response.data.data);
                }
            })
            .catch((e) => {
                // console.log(e);
            });
    };

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav className="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div className="flex items-center justify-between w-full h-16 pl-4 mx-auto sm:px-6 lg:px-8">
                    <Link href="/" className="flex gap-2">
                        <ApplicationLogo className="block w-auto text-gray-800 fill-current h-9 dark:text-gray-200" />
                        <span className="self-center hidden text-xl font-semibold whitespace-nowrap dark:text-white md:block">
                            Stockelio
                        </span>
                    </Link>
                    <div className="flex">
                        <div className="flex mr-2">
                            <Flowbite>
                                <DarkThemeToggle />
                            </Flowbite>
                        </div>
                        <NotificationsDropdown 
                            unreadNotificationsExists={user.unread_notifications_exists}
                            notifications={notifications}
                            nextCursor={nextCursor}
                            onScrollEnd={() => {
                                getNotifications(nextCursor)
                            }}
                            onBeginning={() => {
                                setNotifications(null)
                                getNotifications(null)
                            }}  
                        />
                        <Avatar
                            className="cursor-pointer"
                            img=""
                            status="online"
                            statusPosition="bottom-right"
                        >
                            <div className="hidden space-y-1 font-medium dark:text-white md:block">
                                <div>{user.name}</div>
                            </div>
                        </Avatar>
                        <Button
                            className="block mr-2 md:hidden"
                            color="gray"
                            onClick={() =>
                                setShowingNavigationDropdown(
                                    !showingNavigationDropdown
                                )
                            }
                        >
                            <HiMenu className="text-2xl" />
                        </Button>
                    </div>
                </div>
            </nav>

            <div className="flex flex-col md:flex-row">
                <div
                    className={
                        showingNavigationDropdown
                            ? "block"
                            : "hidden" + " md:block"
                    }
                >
                    <Sidebar
                        aria-label="Sidebar with multi-level dropdown example"
                        className="w-full md:w-56"
                    >
                        <Sidebar.Items
                            className="flex-grow"
                            style={{ height: "calc(100vh - 6.1rem)" }}
                        >
                            <Sidebar.ItemGroup>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("profile.edit")}
                                >
                                    <Sidebar.Item href="#" icon={HiUser}>
                                        Cuenta
                                    </Sidebar.Item>
                                </Link>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("dashboard")}
                                >
                                    <Sidebar.Item icon={HiHome}>
                                        Dashboard
                                    </Sidebar.Item>
                                </Link>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("products.index")}
                                >
                                    <Sidebar.Item icon={HiShoppingBag}>
                                        Productos
                                    </Sidebar.Item>
                                </Link>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("carts.show", user.cart.id)}
                                >
                                    <Sidebar.Item icon={FaShoppingCart}>
                                        Mi carrito
                                    </Sidebar.Item>
                                </Link>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("orders.index")}
                                >
                                    <Sidebar.Item icon={FaBoxes}>
                                        Mis ordenes
                                    </Sidebar.Item>
                                </Link>
                                <Link
                                    onClick={() => {
                                        setShowingNavigationDropdown(false);
                                    }}
                                    href={route("logout")}
                                    method="post"
                                >
                                    <Sidebar.Item icon={HiArrowSmRight}>
                                        Cerrar sesión
                                    </Sidebar.Item>
                                </Link>
                            </Sidebar.ItemGroup>
                        </Sidebar.Items>
                    </Sidebar>
                </div>

                <main className="flex-grow overflow-hidden">{children}</main>

                {/* Floating and auto dismiss notifications div */}
                <div className="fixed flex-col-reverse hidden gap-2 overflow-hidden w-80 max-h-52 bottom-5 right-5 md:flex xl:max-h-72">
                    {incomingNotifications.map((notification, index) => (
                        <Notification
                            key={index}
                            autoDismiss={true}
                            notification={notification}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}
