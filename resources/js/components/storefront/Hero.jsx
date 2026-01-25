import React, { useState, useEffect, useRef } from 'react';

function Hero({ banners = [], productsUrl }) {
    const [activeSlide, setActiveSlide] = useState(0);
    const [isLoaded, setIsLoaded] = useState(false);
    const [mousePosition, setMousePosition] = useState({ x: 0, y: 0 });
    const heroRef = useRef(null);

    useEffect(() => {
        setIsLoaded(true);
    }, []);

    useEffect(() => {
        if (banners.length <= 1) return;
        const timer = setInterval(() => {
            setActiveSlide((prev) => (prev + 1) % banners.length);
        }, 5000);
        return () => clearInterval(timer);
    }, [banners.length]);

    // Parallax mouse effect
    const handleMouseMove = (e) => {
        if (!heroRef.current) return;
        const rect = heroRef.current.getBoundingClientRect();
        const x = (e.clientX - rect.left - rect.width / 2) / 50;
        const y = (e.clientY - rect.top - rect.height / 2) / 50;
        setMousePosition({ x, y });
    };

    if (banners.length > 0) {
        return (
            <section className="relative h-[500px] md:h-[600px] w-full bg-slate-900 overflow-hidden">
                {banners.map((banner, index) => (
                    <div
                        key={index}
                        className={`absolute inset-0 transition-all duration-1000 ${activeSlide === index ? 'opacity-100 scale-100' : 'opacity-0 scale-105'}`}
                    >
                        <img src={banner} alt="" className="w-full h-full object-cover" />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30" />
                    </div>
                ))}

                {banners.length > 1 && (
                    <>
                        <div className="absolute inset-0 flex items-center justify-between p-4 opacity-0 hover:opacity-100 transition-opacity">
                            <button
                                onClick={() => setActiveSlide((prev) => (prev - 1 + banners.length) % banners.length)}
                                className="p-3 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md transition-all hover:scale-110"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                onClick={() => setActiveSlide((prev) => (prev + 1) % banners.length)}
                                className="p-3 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md transition-all hover:scale-110"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                            {banners.map((_, index) => (
                                <button
                                    key={index}
                                    onClick={() => setActiveSlide(index)}
                                    className={`h-2 rounded-full transition-all duration-500 ${activeSlide === index ? 'w-10 bg-white' : 'w-2 bg-white/50 hover:bg-white/70'}`}
                                />
                            ))}
                        </div>
                    </>
                )}
            </section>
        );
    }

    return (
        <section
            ref={heroRef}
            onMouseMove={handleMouseMove}
            className="relative h-[550px] lg:h-[700px] w-full bg-slate-900 overflow-hidden flex items-center"
        >
            {/* Background with parallax */}
            <div
                className="absolute inset-0 transition-transform duration-100 ease-out"
                style={{ transform: `translate(${mousePosition.x}px, ${mousePosition.y}px) scale(1.1)` }}
            >
                <img
                    src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop"
                    alt=""
                    className="w-full h-full object-cover opacity-60"
                />
            </div>

            {/* Gradient overlays */}
            <div className="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/80 to-transparent" />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent" />

            {/* Animated shapes */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className={`absolute -top-20 -right-20 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl transition-all duration-1000 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'}`} />
                <div className={`absolute bottom-0 left-1/4 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl transition-all duration-1000 delay-300 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'}`} />
                <div className={`absolute top-1/4 right-1/4 w-48 h-48 bg-pink-500/10 rounded-full blur-2xl animate-float`} />
            </div>

            <div className="relative max-w-7xl mx-auto px-4 lg:px-6 w-full">
                <div className="max-w-2xl space-y-6">
                    {/* Badge */}
                    <span className={`inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full text-sm font-semibold text-white border border-white/20 transition-all duration-700 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
                        <span className="relative flex h-2 w-2">
                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span className="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        Nova Coleção 2026
                    </span>

                    {/* Title with animated gradient */}
                    <h1 className={`text-5xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.1] transition-all duration-700 delay-100 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
                        Seu estilo,
                        <br />
                        <span className="relative">
                            <span className="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent bg-[length:200%_auto] animate-shimmer">
                                sua essência.
                            </span>
                        </span>
                    </h1>

                    <p className={`text-lg text-slate-300 max-w-xl leading-relaxed transition-all duration-700 delay-200 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
                        Descubra peças exclusivas que combinam perfeitamente com quem você é.
                        Qualidade premium e design inconfundível.
                    </p>

                    {/* CTAs */}
                    <div className={`pt-4 flex flex-wrap gap-4 transition-all duration-700 delay-300 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
                        <a
                            href={productsUrl}
                            className="group relative px-8 py-4 bg-indigo-600 text-white font-bold rounded-full overflow-hidden transition-all shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:shadow-indigo-600/40 hover:scale-105 active:scale-[0.98]"
                        >
                            <span className="relative z-10 flex items-center gap-2">
                                Ver Produtos
                                <svg className="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                            <div className="absolute inset-0 bg-gradient-to-r from-indigo-700 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity" />
                        </a>
                        <a
                            href="#sobre"
                            className="group px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold rounded-full transition-all backdrop-blur-sm active:scale-[0.98] hover:scale-105"
                        >
                            <span className="flex items-center gap-2">
                                Sobre a Marca
                                <svg className="w-4 h-4 transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </span>
                        </a>
                    </div>

                    {/* Stats */}
                    <div className={`pt-8 flex gap-8 transition-all duration-700 delay-500 ${isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'}`}>
                        <div>
                            <div className="text-3xl font-bold text-white">500+</div>
                            <div className="text-sm text-slate-400">Produtos</div>
                        </div>
                        <div className="w-px bg-white/20" />
                        <div>
                            <div className="text-3xl font-bold text-white">10k+</div>
                            <div className="text-sm text-slate-400">Clientes</div>
                        </div>
                        <div className="w-px bg-white/20" />
                        <div>
                            <div className="text-3xl font-bold text-white">4.9</div>
                            <div className="text-sm text-slate-400 flex items-center gap-1">
                                <svg className="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Avaliação
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Decorative gradient */}
            <div className="absolute bottom-0 right-0 w-1/2 h-1/2 bg-gradient-to-tl from-indigo-600/20 to-transparent pointer-events-none" />

            {/* Scroll indicator */}
            <div className={`absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/50 transition-all duration-700 delay-700 ${isLoaded ? 'opacity-100' : 'opacity-0'}`}>
                <span className="text-xs uppercase tracking-widest">Role para ver mais</span>
                <div className="w-6 h-10 rounded-full border-2 border-current flex justify-center pt-2">
                    <div className="w-1 h-2 bg-current rounded-full animate-bounce" />
                </div>
            </div>
        </section>
    );
}

export default Hero;
