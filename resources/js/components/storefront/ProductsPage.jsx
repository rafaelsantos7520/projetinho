import React, { useState } from 'react';
import Header from './Header';
import Footer from './Footer';
import ProductCard from './ProductCard';

function ProductsPage({ data }) {
    const {
        baseUrl = '/',
        productsUrl = '/produtos',
        logoUrl = null,
        categories = [],
        products = [],
        pagination = {},
        filters = {},
        selectedCategory = null,
    } = data || {};

    const [filtersOpen, setFiltersOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState(filters.q || '');

    const buildUrl = (params) => {
        const url = new URL(productsUrl);
        Object.entries(params).forEach(([key, value]) => {
            if (value) url.searchParams.set(key, value);
        });
        return url.toString();
    };

    const handleSearch = (e) => {
        e.preventDefault();
        window.location.href = buildUrl({ ...filters, q: searchQuery, category: filters.category });
    };

    const handleSortChange = (e) => {
        window.location.href = e.target.value;
    };

    const sortOptions = [
        { value: 'newest', label: 'Mais Recentes' },
        { value: 'price_asc', label: 'Menor Preço' },
        { value: 'price_desc', label: 'Maior Preço' },
        { value: 'rating_desc', label: 'Melhor Avaliação' },
        { value: 'name_asc', label: 'Nome A-Z' },
    ];

    const pageTitle = selectedCategory?.name
        || (filters.q ? `Resultados para "${filters.q}"` : 'Todos os Produtos');

    return (
        <div className="min-h-screen bg-white">
            <Header baseUrl={baseUrl} productsUrl={productsUrl} logoUrl={logoUrl} categories={categories} />

            {/* Breadcrumb */}
            <div className="bg-white border-b border-slate-100">
                <div className="max-w-7xl mx-auto px-4 py-4">
                    <nav className="flex items-center gap-2 text-sm text-slate-500">
                        <a href={baseUrl} className="hover:text-indigo-600 transition-colors">Início</a>
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                        <span className="text-slate-900 font-medium">{pageTitle}</span>
                    </nav>
                </div>
            </div>

            <main className="bg-gradient-to-b from-slate-50 via-white to-slate-50">
                <div className="max-w-7xl mx-auto px-4 py-8">
                    <div className="flex flex-col lg:flex-row gap-8">
                        {/* Sidebar Filters (Desktop) */}
                        <aside className="hidden lg:block w-72 shrink-0">
                            <div className="sticky top-28 space-y-6">
                                {/* Categories */}
                                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                                    <h3 className="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                        <svg className="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        Categorias
                                    </h3>
                                    <ul className="space-y-2">
                                        <li>
                                            <a
                                                href={buildUrl({ q: filters.q, sort: filters.sort })}
                                                className={`flex items-center justify-between py-2 px-3 rounded-lg transition-colors ${!filters.category ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600 hover:bg-slate-50'}`}
                                            >
                                                <span>Todas</span>
                                                <span className="text-xs text-slate-400">{pagination.total}</span>
                                            </a>
                                        </li>
                                        {categories.map((cat) => (
                                            <li key={cat.id}>
                                                <a
                                                    href={buildUrl({ category: cat.slug, q: filters.q, sort: filters.sort })}
                                                    className={`flex items-center justify-between py-2 px-3 rounded-lg transition-colors ${filters.category === cat.slug ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600 hover:bg-slate-50'}`}
                                                >
                                                    <span>{cat.name}</span>
                                                    <span className="text-xs text-slate-400">{cat.products_count || 0}</span>
                                                </a>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Clear Filters */}
                                {(filters.category || filters.min_rating || filters.q) && (
                                    <a
                                        href={productsUrl}
                                        className="block w-full text-center py-3 px-4 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors"
                                    >
                                        Limpar Filtros
                                    </a>
                                )}
                            </div>
                        </aside>

                        {/* Main Content */}
                        <div className="flex-1 min-w-0">
                            {/* Header */}
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                <div>
                                    <h1 className="text-2xl font-bold text-slate-900">{pageTitle}</h1>
                                    <p className="text-slate-500 text-sm mt-1">
                                        {pagination.total} produto{pagination.total !== 1 ? 's' : ''} encontrado{pagination.total !== 1 ? 's' : ''}
                                    </p>
                                </div>

                                <div className="flex items-center gap-3">
                                    <button
                                        onClick={() => setFiltersOpen(true)}
                                        className="lg:hidden p-2 text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                    </button>

                                    <label className="text-sm text-slate-500">Ordenar:</label>
                                    <select
                                        onChange={handleSortChange}
                                        value={buildUrl({ ...filters, sort: filters.sort })}
                                        className="bg-white border border-slate-200 rounded-lg py-2 px-4 text-sm font-medium text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer"
                                    >
                                        {sortOptions.map((opt) => (
                                            <option key={opt.value} value={buildUrl({ ...filters, sort: opt.value })}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            {/* Active Filters */}
                            {(filters.category || filters.q) && (
                                <div className="flex flex-wrap gap-2 mb-6">
                                    {filters.q && (
                                        <a
                                            href={buildUrl({ category: filters.category, sort: filters.sort })}
                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 text-sm font-medium rounded-full hover:bg-indigo-100 transition-colors"
                                        >
                                            Busca: {filters.q}
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    )}
                                    {filters.category && selectedCategory && (
                                        <a
                                            href={buildUrl({ q: filters.q, sort: filters.sort })}
                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 text-sm font-medium rounded-full hover:bg-indigo-100 transition-colors"
                                        >
                                            {selectedCategory.name}
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    )}
                                </div>
                            )}

                            {/* Products Grid */}
                            {products.length === 0 ? (
                                <div className="bg-white rounded-3xl p-16 text-center border border-dashed border-slate-200">
                                    <div className="mx-auto h-20 w-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6">
                                        <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-xl font-bold text-slate-900 mb-2">Nenhum produto encontrado</h3>
                                    <p className="text-slate-500 mb-6">Tente ajustar os filtros ou realizar uma nova busca.</p>
                                    <a
                                        href={productsUrl}
                                        className="inline-block px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors"
                                    >
                                        Ver Todos os Produtos
                                    </a>
                                </div>
                            ) : (
                                <>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                        {products.map((product) => (
                                            <ProductCard key={product.id} product={product} />
                                        ))}
                                    </div>

                                    {/* Pagination */}
                                    {pagination.lastPage > 1 && (
                                        <div className="mt-12 flex justify-center">
                                            <div className="flex items-center gap-2">
                                                {pagination.currentPage > 1 && (
                                                    <a
                                                        href={buildUrl({ ...filters, page: pagination.currentPage - 1 })}
                                                        className="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                                                    >
                                                        Anterior
                                                    </a>
                                                )}

                                                <span className="px-4 py-2 text-sm text-slate-600">
                                                    Página {pagination.currentPage} de {pagination.lastPage}
                                                </span>

                                                {pagination.currentPage < pagination.lastPage && (
                                                    <a
                                                        href={buildUrl({ ...filters, page: pagination.currentPage + 1 })}
                                                        className="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors"
                                                    >
                                                        Próxima
                                                    </a>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </div>

                {/* Mobile Filters Drawer */}
                {filtersOpen && (
                    <>
                        <div
                            className="lg:hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
                            onClick={() => setFiltersOpen(false)}
                        />
                        <div className="lg:hidden fixed right-0 top-0 bottom-0 z-50 w-80 max-w-full bg-white shadow-2xl overflow-y-auto">
                            <div className="p-6">
                                <div className="flex items-center justify-between mb-6">
                                    <h2 className="text-lg font-bold text-slate-900">Filtros</h2>
                                    <button onClick={() => setFiltersOpen(false)} className="p-2 text-slate-400 hover:text-slate-600">
                                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div className="mb-6">
                                    <h3 className="font-bold text-slate-900 mb-3">Categorias</h3>
                                    <div className="space-y-1">
                                        <a
                                            href={buildUrl({ q: filters.q, sort: filters.sort })}
                                            className={`block py-2 px-3 rounded-lg ${!filters.category ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600'}`}
                                        >
                                            Todas as Categorias
                                        </a>
                                        {categories.map((cat) => (
                                            <a
                                                key={cat.id}
                                                href={buildUrl({ category: cat.slug, q: filters.q, sort: filters.sort })}
                                                className={`block py-2 px-3 rounded-lg ${filters.category === cat.slug ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-slate-600'}`}
                                            >
                                                {cat.name}
                                            </a>
                                        ))}
                                    </div>
                                </div>

                                {(filters.category || filters.q) && (
                                    <a
                                        href={productsUrl}
                                        className="block w-full text-center py-3 px-4 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors"
                                    >
                                        Limpar Todos os Filtros
                                    </a>
                                )}
                            </div>
                        </div>
                    </>
                )}
            </main>

            <Footer baseUrl={baseUrl} productsUrl={productsUrl} logoUrl={logoUrl} />
        </div>
    );
}

export default ProductsPage;
