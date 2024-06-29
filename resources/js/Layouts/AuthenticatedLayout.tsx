import { useState, PropsWithChildren, useEffect } from "react";
import ApplicationLogo from "@/Components/ApplicationLogo";
import { Link } from "@inertiajs/react";
import { User } from "@/types";
import {
    Button,
    DarkThemeToggle,
    Dropdown,
    DropdownItem,
    Flowbite,
    Sidebar,
} from "flowbite-react";
import {
    HiArrowSmRight,
    HiShoppingBag,
    HiUser,
    HiMenu,
    HiHome,
    HiBell,
} from "react-icons/hi";
import { FaBoxes, FaShippingFast, FaShoppingCart } from "react-icons/fa";
import { Avatar } from "flowbite-react";

export default function Authenticated({
    user,
    children,
}: PropsWithChildren<{ user: User }>) {
    useEffect(() => {
        (window as any).Echo.private(`App.Models.User.${user.id}`).notification(
            (notification: any) => {
                setIncomingNotification(true);
            }
        );

        return () => {
            (window as any).Echo.leave(`App.Models.User.${user.id}`);
        };
    }, []);

    const [incomingNotification, setIncomingNotification] = useState(false);
    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

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
                        <Dropdown
                            label=""
                            inline
                            placement="bottom"
                            renderTrigger={() => (
                                <button className="mr-2 relative rounded-lg p-2.5 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    {incomingNotification ? (
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
                                Mis notificaciones
                            </Dropdown.Header>
                            <div className="w-72 md:w-80">
                                <DropdownItem className="flex items-start px-4 py-3 overflow-hidden border-b border-gray-100 dark:border-gray-600 min-h-14 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <div className="flex-shrink-0">
                                            <Avatar rounded img={() => (
                                                <FaShippingFast className="text-4xl"/>
                                            )} />
                                        </div>
                                        <div className="w-full text-sm text-left ps-3">
                                            <div className="font-semibold text-gray-900 dark:text-white">
                                                Tu orden #1 ya fue enviada
                                            </div>
                                            <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                                                Haz click para rastrear su seguimiento
                                            </div>
                                            <div className="text-xs text-blue-600 dark:text-blue-500">
                                                a few moments ago
                                            </div>
                                        </div>
                                </DropdownItem>
                                <DropdownItem className="flex items-start px-4 py-3 overflow-hidden border-b border-gray-100 dark:border-gray-600 min-h-14 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <div className="flex-shrink-0">
                                            <Avatar rounded img='https://flowbite.com/docs/images/people/profile-picture-5.jpg' />
                                            <div className="absolute flex items-center justify-center w-5 h-5 -mt-5 bg-blue-600 border border-white rounded-full ms-6 dark:border-gray-800">
                                                <svg
                                                    className="w-2 h-2 text-white"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor"
                                                    viewBox="0 0 18 18"
                                                >
                                                    <path d="M1 18h16a1 1 0 0 0 1-1v-6h-4.439a.99.99 0 0 0-.908.6 3.978 3.978 0 0 1-7.306 0 .99.99 0 0 0-.908-.6H0v6a1 1 0 0 0 1 1Z" />
                                                    <path d="M4.439 9a2.99 2.99 0 0 1 2.742 1.8 1.977 1.977 0 0 0 3.638 0A2.99 2.99 0 0 1 13.561 9H17.8L15.977.783A1 1 0 0 0 15 0H3a1 1 0 0 0-.977.783L.2 9h4.239Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div className="w-full text-sm text-left ps-3">
                                            <div className="font-semibold text-gray-900 dark:text-white">
                                                Tu orden #1 ya fue enviada
                                            </div>
                                            <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                                                Haz click para rastrear su seguimiento
                                            </div>
                                            <div className="text-xs text-blue-600 dark:text-blue-500">
                                                a few moments ago
                                            </div>
                                        </div>
                                </DropdownItem>
                                <DropdownItem className="flex items-start px-4 py-3 overflow-hidden border-b border-gray-100 dark:border-gray-600 min-h-14 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <div className="flex-shrink-0">
                                            <Avatar rounded img='https://flowbite.com/docs/images/people/profile-picture-5.jpg' />
                                            <div className="absolute flex items-center justify-center w-5 h-5 -mt-5 bg-blue-600 border border-white rounded-full ms-6 dark:border-gray-800">
                                                <svg
                                                    className="w-2 h-2 text-white"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor"
                                                    viewBox="0 0 18 18"
                                                >
                                                    <path d="M1 18h16a1 1 0 0 0 1-1v-6h-4.439a.99.99 0 0 0-.908.6 3.978 3.978 0 0 1-7.306 0 .99.99 0 0 0-.908-.6H0v6a1 1 0 0 0 1 1Z" />
                                                    <path d="M4.439 9a2.99 2.99 0 0 1 2.742 1.8 1.977 1.977 0 0 0 3.638 0A2.99 2.99 0 0 1 13.561 9H17.8L15.977.783A1 1 0 0 0 15 0H3a1 1 0 0 0-.977.783L.2 9h4.239Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div className="w-full text-sm text-left ps-3">
                                            <div className="font-semibold text-gray-900 dark:text-white">
                                                Tu orden #1 ya fue enviada
                                            </div>
                                            <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                                                Haz click para rastrear su seguimiento
                                            </div>
                                            <div className="text-xs text-blue-600 dark:text-blue-500">
                                                a few moments ago
                                            </div>
                                        </div>
                                </DropdownItem>
                                <DropdownItem className="flex items-start px-4 py-3 overflow-hidden border-b border-gray-100 dark:border-gray-600 min-h-14 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <div className="flex-shrink-0">
                                            <Avatar rounded img={() => (
                                                <FaShippingFast className="text-4xl"/>
                                            )} />
                                        </div>
                                        <div className="w-full text-sm text-left ps-3">
                                            <div className="font-semibold text-gray-900 dark:text-white">
                                                Tu orden #1 ya fue enviada
                                            </div>
                                            <div className="text-gray-500 mb-1.5 dark:text-gray-400">
                                                Haz click para rastrear su seguimiento
                                            </div>
                                            <div className="text-xs text-blue-600 dark:text-blue-500">
                                                a few moments ago
                                            </div>
                                        </div>
                                </DropdownItem>
                            </div>
                            <Dropdown.Item className="flex justify-center">
                                Ver todas
                            </Dropdown.Item>
                        </Dropdown>

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
            </div>
        </div>
    );
}
