import React from 'react';
import Header from './Header';
import Hero from './Hero';
import FeaturesStrip from './FeaturesStrip';
import CategoriesSection from './CategoriesSection';
import ProductSection from './ProductSection';
import Newsletter from './Newsletter';
import Footer from './Footer';

function StorefrontApp({ data }) {
    const {
        baseUrl = '/',
        productsUrl = '/produtos',
        logoUrl = null,
        banners = [],
        categories = [],
        featured = [],
        promos = [],
        newest = [],
    } = data || {};

    return (
        <div className="min-h-screen bg-white">
            <Header
                baseUrl={baseUrl}
                productsUrl={productsUrl}
                logoUrl={logoUrl}
                categories={categories}
            />

            <main>
                <Hero banners={banners} productsUrl={productsUrl} />

                <FeaturesStrip />

                {categories.length > 0 && (
                    <CategoriesSection categories={categories} productsUrl={productsUrl} />
                )}

                {featured.length > 0 && (
                    <ProductSection
                        title="Produtos em Destaque"
                        badge="⭐ Destaques"
                        products={featured}
                        productsUrl={productsUrl}
                        bgClass="bg-slate-50"
                    />
                )}

                {promos.length > 0 && (
                    <ProductSection
                        title="Ofertas Especiais"
                        subtitle="Aproveite descontos por tempo limitado!"
                        badge="🔥 Promoção"
                        products={promos}
                        productsUrl={productsUrl}
                    />
                )}

                {newest.length > 0 && (
                    <ProductSection
                        title="Novidades"
                        subtitle="Acabou de chegar na loja"
                        badge="✨ Novo"
                        products={newest}
                        productsUrl={productsUrl}
                        bgClass="bg-slate-50"
                    />
                )}

                <Newsletter />
            </main>

            <Footer baseUrl={baseUrl} productsUrl={productsUrl} logoUrl={logoUrl} />
        </div>
    );
}

export default StorefrontApp;
