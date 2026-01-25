import React, { useRef, useState, useEffect } from 'react';
import ProductCard from './ProductCard';

function ProductSection({ title, subtitle, badge, products, productsUrl, bgClass = '' }) {
    const [isVisible, setIsVisible] = useState(false);
    const sectionRef = useRef(null);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) setIsVisible(true);
            },
            { threshold: 0.1 }
        );
        if (sectionRef.current) observer.observe(sectionRef.current);
        return () => observer.disconnect();
    }, []);

    if (!products || products.length === 0) return null;

    return (
        <section ref={sectionRef} className={`py-16 md:py-24 ${bgClass} overflow-hidden`}>
            <div className="max-w-7xl mx-auto px-4">
                {/* Header */}
                <div className={`flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12 transition-all duration-700 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    <div>
                        {badge && (
                            <span className="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold mb-3">
                                {badge}
                            </span>
                        )}
                        <h2 className="text-3xl md:text-4xl font-bold text-slate-900">{title}</h2>
                        {subtitle && <p className="text-slate-500 mt-2 text-lg">{subtitle}</p>}
                    </div>
                    <a
                        href={productsUrl}
                        className="group inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 rounded-full text-sm font-semibold text-slate-700 hover:border-indigo-500 hover:text-indigo-600 hover:shadow-lg hover:shadow-indigo-500/10 transition-all"
                    >
                        Ver Todos
                        <svg className="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                {/* Products Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {products.map((product, index) => (
                        <ProductCard key={product.id} product={product} index={index} />
                    ))}
                </div>
            </div>
        </section>
    );
}

export default ProductSection;
