import React, { useState } from 'react';
import Header from './Header';
import Footer from './Footer';
import ProductCard from './ProductCard';

function ProductDetailPage({ data }) {
    const {
        baseUrl = '/',
        productsUrl = '/produtos',
        logoUrl = null,
        categories = [],
        product = {},
        related = [],
    } = data || {};

    const [selectedImage, setSelectedImage] = useState(0);
    const [quantity, setQuantity] = useState(1);

    const images = product.images?.length > 0
        ? product.images
        : [{ url: product.primary_image_url || '/images/product-placeholder.png' }];

    const finalPrice = product.promo_price_cents || product.price_cents;
    const originalPrice = product.price_cents;
    const hasDiscount = product.promo_price_cents && product.promo_price_cents < originalPrice;
    const discountPercent = hasDiscount ? Math.round((1 - finalPrice / originalPrice) * 100) : 0;

    const formatPrice = (cents) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(cents / 100);
    };

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
                        <a href={productsUrl} className="hover:text-indigo-600 transition-colors">Produtos</a>
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                        <span className="text-slate-900 font-medium truncate">{product.name}</span>
                    </nav>
                </div>
            </div>

            <main className="bg-white">
                <div className="max-w-7xl mx-auto px-4 py-8 lg:py-12">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                        {/* Gallery */}
                        <div className="space-y-4">
                            {/* Main Image */}
                            <div className="relative aspect-square bg-slate-100 rounded-2xl overflow-hidden">
                                <img
                                    src={images[selectedImage]?.url || images[0]?.url}
                                    alt={product.name}
                                    className="w-full h-full object-cover"
                                />
                                {hasDiscount && (
                                    <span className="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                                        -{discountPercent}%
                                    </span>
                                )}
                            </div>

                            {/* Thumbnails */}
                            {images.length > 1 && (
                                <div className="flex gap-3 overflow-x-auto pb-2">
                                    {images.map((img, index) => (
                                        <button
                                            key={index}
                                            onClick={() => setSelectedImage(index)}
                                            className={`shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 transition-colors ${selectedImage === index ? 'border-indigo-600' : 'border-transparent hover:border-slate-300'}`}
                                        >
                                            <img src={img.url} alt="" className="w-full h-full object-cover" />
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Product Info */}
                        <div className="lg:py-4">
                            {/* Category */}
                            {product.category && (
                                <a
                                    href={product.category.url}
                                    className="inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700 mb-2"
                                >
                                    {product.category.name}
                                </a>
                            )}

                            {/* Title */}
                            <h1 className="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">
                                {product.name}
                            </h1>

                            {/* Price */}
                            <div className="flex items-baseline gap-3 mb-6">
                                <span className="text-3xl font-bold text-slate-900">
                                    {formatPrice(finalPrice)}
                                </span>
                                {hasDiscount && (
                                    <span className="text-xl text-slate-400 line-through">
                                        {formatPrice(originalPrice)}
                                    </span>
                                )}
                            </div>

                            {/* Installments */}
                            <p className="text-sm text-slate-500 mb-6">
                                ou até <span className="font-medium text-slate-700">12x de {formatPrice(Math.ceil(finalPrice / 12))}</span> sem juros
                            </p>

                            {/* Description */}
                            {product.description && (
                                <div className="prose prose-slate prose-sm max-w-none mb-8">
                                    <p className="text-slate-600 leading-relaxed">{product.description}</p>
                                </div>
                            )}

                            {/* Quantity */}
                            <div className="mb-6">
                                <label className="block text-sm font-medium text-slate-700 mb-2">Quantidade</label>
                                <div className="flex items-center gap-3">
                                    <button
                                        onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                        className="h-12 w-12 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span className="w-12 text-center text-lg font-medium text-slate-900">{quantity}</span>
                                    <button
                                        onClick={() => setQuantity(quantity + 1)}
                                        className="h-12 w-12 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex flex-col sm:flex-row gap-3 mb-8">
                                <button className="flex-1 py-4 px-8 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Adicionar ao Carrinho
                                </button>
                                <button className="py-4 px-6 border border-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Favoritar
                                </button>
                            </div>

                            {/* Features */}
                            <div className="border-t border-slate-100 pt-6 space-y-4">
                                <div className="flex items-center gap-3 text-sm text-slate-600">
                                    <div className="h-10 w-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span>Produto 100% original com garantia</span>
                                </div>
                                <div className="flex items-center gap-3 text-sm text-slate-600">
                                    <div className="h-10 w-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <span>Frete grátis para compras acima de R$ 199</span>
                                </div>
                                <div className="flex items-center gap-3 text-sm text-slate-600">
                                    <div className="h-10 w-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <span>Pagamento seguro e protegido</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Related Products */}
                    {related.length > 0 && (
                        <div className="mt-16 pt-12 border-t border-slate-100">
                            <h2 className="text-2xl font-bold text-slate-900 mb-8">Produtos Relacionados</h2>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {related.map((product) => (
                                    <ProductCard key={product.id} product={product} />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </main>

            <Footer baseUrl={baseUrl} productsUrl={productsUrl} logoUrl={logoUrl} />
        </div>
    );
}

export default ProductDetailPage;
