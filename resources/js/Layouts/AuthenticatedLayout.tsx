import { useState, PropsWithChildren, ReactNode } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { User } from '@/types';
import { Button, DarkThemeToggle, Flowbite, Sidebar } from 'flowbite-react';
import { HiArrowSmRight, HiShoppingBag, HiUser, HiMenu, HiHome } from 'react-icons/hi';
import { Avatar } from 'flowbite-react';

export default function Authenticated({ user, header, children }: PropsWithChildren<{ user: User, header?: ReactNode }>) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav className="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div className="flex justify-between items-center h-16 w-full pl-4 mx-auto sm:px-6 lg:px-8">
                    <Link href="/" className='flex gap-2'>
                        <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <span className="self-center whitespace-nowrap text-xl font-semibold dark:text-white hidden md:block">Stockelio</span>
                    </Link>
                    <div className='flex'>
                        <div className='flex mr-2'>
                            <Flowbite>
                                <DarkThemeToggle />
                            </Flowbite>
                        </div>
                        <Avatar className="cursor-pointer" img="" status="online" statusPosition="bottom-right">
                            <div className="space-y-1 font-medium dark:text-white hidden md:block">
                                <div>{ user.name }</div>
                            </div>
                        </Avatar>
                        <Button className='block md:hidden mr-2' color="gray" onClick={() => setShowingNavigationDropdown(!showingNavigationDropdown)}>
                            <HiMenu className='text-2xl'/>
                        </Button>
                    </div>
                </div>
            </nav>

            {/* {header && (
                <header className="bg-white dark:bg-gray-800 shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )} */}

            <div className='flex flex-col md:flex-row'>
                <div className={
                    (showingNavigationDropdown) ? 'block' : 'hidden' + ' md:block'
                }>
                    <Sidebar aria-label="Sidebar with multi-level dropdown example" className='w-full md:w-56'>
                            <Sidebar.Items className='flex-grow' style={{height: "calc(100vh - 6.1rem)"}}>
                            <Sidebar.ItemGroup>
                                <Link href={route('profile.edit')}>
                                    <Sidebar.Item href="#" icon={HiUser}>
                                        Cuenta
                                    </Sidebar.Item>
                                </Link>
                                <Link href={route('dashboard')}>
                                    <Sidebar.Item icon={HiHome}>
                                        Dashboard
                                    </Sidebar.Item>
                                </Link>
                                <Link href={route('products.index')}>
                                    <Sidebar.Item icon={HiShoppingBag}>
                                        Productos
                                    </Sidebar.Item>
                                </Link>
                                <Link href={route('logout')} method="post">
                                    <Sidebar.Item icon={HiArrowSmRight}>
                                        Cerrar sesión
                                    </Sidebar.Item>
                                </Link>
                            </Sidebar.ItemGroup>
                        </Sidebar.Items>
                    </Sidebar>
                </div>

                <main className='flex-grow'>{children}</main>
            </div>
        </div>
    );
}
