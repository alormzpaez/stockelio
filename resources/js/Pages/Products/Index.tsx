import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { PageProps, PaginationInfo, Product } from '@/types';
import { Pagination } from 'flowbite-react';
import Card from '@/Components/Card';

export default function Index({ auth, products }: PageProps<{ products: PaginationInfo<Product> }>) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Todos los productos</h2>}
        >
            <Head title="Todos los productos" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div className="p-6 text-4xl font-medium text-gray-900 dark:text-gray-100">Todos nuestros productos</div>
                        <hr className="h-px my-8 mt-4 bg-gray-200 border-0 dark:bg-gray-700"></hr>
                        <div className='flex flex-col items-center gap-2 px-2 sm:grid sm:grid-cols-2 sm:place-items-center lg:grid-cols-3'>
                            {
                                products.data.map((product, index) => 
                                    <Card name={product.name} price={product.cheapest_variant?.retail_price ?? 0} imgUrl={product.thumbnail_url} variantsCount={product.variants_count} key={index} onClick={() => {
                                        router.visit(route('products.show', product.id))
                                    }} />
                                )
                            }
                        </div>
                        <div className="flex justify-center my-10 sm:justify-center">
                            <Pagination
                                layout="pagination"
                                currentPage={products.current_page}
                                totalPages={products.last_page}
                                onPageChange={(page) => {
                                    router.visit(route('products.index', {
                                        page
                                    }))
                                }}
                                previousLabel=""
                                nextLabel=""
                                showIcons
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
