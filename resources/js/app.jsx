import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import TestPage from './components/TestPage';
import StorefrontApp from './components/storefront/StorefrontApp';
import ProductsPage from './components/storefront/ProductsPage';
import ProductDetailPage from './components/storefront/ProductDetailPage';

// Mount Storefront Home
const storefrontRoot = document.getElementById('storefront-root');
if (storefrontRoot) {
    const dataElement = document.getElementById('storefront-data');
    const data = dataElement ? JSON.parse(dataElement.textContent) : {};

    const root = createRoot(storefrontRoot);
    root.render(<StorefrontApp data={data} />);
}

// Mount Products Page
const productsRoot = document.getElementById('products-root');
if (productsRoot) {
    const dataElement = document.getElementById('products-data');
    const data = dataElement ? JSON.parse(dataElement.textContent) : {};

    const root = createRoot(productsRoot);
    root.render(<ProductsPage data={data} />);
}

// Mount Product Detail Page
const productDetailRoot = document.getElementById('product-detail-root');
if (productDetailRoot) {
    const dataElement = document.getElementById('product-detail-data');
    const data = dataElement ? JSON.parse(dataElement.textContent) : {};

    const root = createRoot(productDetailRoot);
    root.render(<ProductDetailPage data={data} />);
}

// Mount Test Page (for demo)
const reactRoot = document.getElementById('react-root');
if (reactRoot) {
    const root = createRoot(reactRoot);
    root.render(<TestPage />);
}
