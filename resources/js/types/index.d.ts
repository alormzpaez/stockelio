export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    cart: Cart;
}

export interface Product {
    id: number;
    name: string;
    thumbnail_url: string|null;
    cheapest_variant: Variant|null;
    variants_count: number;
    description: string;
    variants: Variant[];
    files: File[];
    created_at: string;
    updated_at: string;
}

export interface File {
    id: number;
    product_id: number;
    url: string;
}

export interface Variant {
    id: number;
    product_id: number;
    retail_price: number;
    product: Product;
}

export interface Order {
    id: number;
    cart_id: number;
    status: string;
    quantity: number,
    variant_id: number;
    variant: Variant;
}

export interface Cart {
    id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    total: number;
    orders: Order[];
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
    flash: {
        message: string | null;
    }
};
