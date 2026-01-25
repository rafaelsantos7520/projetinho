import React, { useState, useRef, useEffect } from 'react';

function ProductCard({ product, index = 0 }) {
    const [isVisible, setIsVisible] = useState(false);
    const [imageLoaded, setImageLoaded] = useState(false);
    const [isHovered, setIsHovered] = useState(false);
    const cardRef = useRef(null);

    const finalPrice = product.promo_price_cents || product.price_cents;
    const originalPrice = product.price_cents;
    const hasDiscount = product.promo_price_cents && product.promo_price_cents < originalPrice;
    const discountPercent = hasDiscount
        ? Math.round((1 - finalPrice / originalPrice) * 100)
        : 0;

    const formatPrice = (cents) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(cents / 100);
    };

    // Intersection Observer for animation on scroll
    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                }
            },
            { threshold: 0.1 }
        );

        if (cardRef.current) {
            observer.observe(cardRef.current);
        }

        return () => observer.disconnect();
    }, []);

    return (
        <div
            ref={cardRef}
            className={`group relative bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 hover:border-indigo-200 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}
            style={{ transitionDelay: `${index * 100}ms` }}
            onMouseEnter={() => setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
        >
            {/* Image Container */}
            <a href={product.url} className="block relative aspect-square overflow-hidden">
                {/* Skeleton loader */}
                {!imageLoaded && (
                    <div className="absolute inset-0 skeleton" />
                )}

                <img
                    src={product.primary_image_url || '/images/product-placeholder.png'}
                    alt={product.name}
                    loading="lazy"
                    className={`h-full w-full object-cover transition-all duration-700 ${imageLoaded ? 'opacity-100' : 'opacity-0'} ${isHovered ? 'scale-110' : 'scale-100'}`}
                    onLoad={() => setImageLoaded(true)}
                    onError={(e) => {
                        e.target.src = '/images/product-placeholder.png';
                        setImageLoaded(true);
                    }}
                />

                {/* Discount Badge */}
                {discountPercent > 0 && (
                    <span className={`absolute top-3 left-3 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg transition-transform duration-300 ${isHovered ? 'scale-110' : 'scale-100'}`}>
                        -{discountPercent}%
                    </span>
                )}

                {/* Overlay with actions */}
                <div className={`absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent transition-opacity duration-300 ${isHovered ? 'opacity-100' : 'opacity-0'}`}>
                    <div className="absolute bottom-4 left-4 right-4 flex gap-2">
                        <span className={`flex-1 text-center py-2.5 bg-white rounded-xl text-sm font-semibold text-slate-900 transition-all duration-300 ${isHovered ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'}`}>
                            Ver Detalhes
                        </span>
                    </div>
                </div>

                {/* Quick actions */}
                <div className={`absolute top-3 right-3 flex flex-col gap-2 transition-all duration-300 ${isHovered ? 'translate-x-0 opacity-100' : 'translate-x-4 opacity-0'}`}>
                    <button
                        className="h-10 w-10 rounded-full bg-white shadow-lg flex items-center justify-center text-slate-600 hover:text-red-500 hover:scale-110 transition-all"
                        onClick={(e) => { e.preventDefault(); }}
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                    <button
                        className="h-10 w-10 rounded-full bg-white shadow-lg flex items-center justify-center text-slate-600 hover:text-indigo-600 hover:scale-110 transition-all delay-75"
                        onClick={(e) => { e.preventDefault(); }}
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                </div>
            </a>

            {/* Product Info */}
            <div className="p-4">
                <a href={product.url} className="block group/link">
                    <p className={`text-xs font-medium text-indigo-600 mb-1 transition-all duration-300 ${isHovered ? 'translate-x-1' : 'translate-x-0'}`}>
                        {product.category?.name || 'Geral'}
                    </p>
                    <h3 className="font-semibold text-slate-900 truncate group-hover/link:text-indigo-600 transition-colors">
                        {product.name}
                    </h3>
                </a>

                <div className="mt-3 flex items-end justify-between">
                    <div className="space-y-0.5">
                        <div className="flex items-baseline gap-2">
                            <span className={`text-xl font-bold transition-all duration-300 ${hasDiscount ? 'text-red-600' : 'text-slate-900'}`}>
                                {formatPrice(finalPrice)}
                            </span>
                            {hasDiscount && (
                                <span className="text-sm text-slate-400 line-through">
                                    {formatPrice(originalPrice)}
                                </span>
                            )}
                        </div>
                        <p className="text-xs text-slate-500">
                            ou 3x de {formatPrice(Math.ceil(finalPrice / 3))}
                        </p>
                    </div>

                    <button
                        className={`h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 ${isHovered ? 'bg-indigo-600 text-white scale-110 shadow-lg shadow-indigo-500/30' : 'bg-slate-100 text-slate-600'}`}
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Shine effect on hover */}
            <div className={`absolute inset-0 pointer-events-none transition-opacity duration-500 ${isHovered ? 'opacity-100' : 'opacity-0'}`}>
                <div className="absolute -inset-full top-0 block w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 animate-shimmer" />
            </div>
        </div>
    );
}

export default ProductCard;
