export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface Product {
    id: number;
    name: string;
    thumbnail_url: string;
    cheapest_variant: Variant|null;
    variants_count: number;
}

export interface Variant {
    id: number;
    product_id: number;
    retail_price: number;
}

export interface PaginationInfo<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};
