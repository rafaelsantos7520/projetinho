import React, { useRef, useState, useEffect } from 'react';

function Newsletter() {
    const [email, setEmail] = useState('');
    const [status, setStatus] = useState('idle'); // idle, loading, success, error
    const [isVisible, setIsVisible] = useState(false);
    const sectionRef = useRef(null);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) setIsVisible(true);
            },
            { threshold: 0.2 }
        );
        if (sectionRef.current) observer.observe(sectionRef.current);
        return () => observer.disconnect();
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!email) return;

        setStatus('loading');

        // Simulate API call
        setTimeout(() => {
            setStatus('success');
            setEmail('');
            setTimeout(() => setStatus('idle'), 5000);
        }, 1500);
    };

    return (
        <section
            ref={sectionRef}
            className="relative py-20 md:py-28 overflow-hidden"
        >
            {/* Background */}
            <div className="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600" />

            {/* Animated shapes */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                <div className={`absolute -top-20 -left-20 w-80 h-80 bg-white/10 rounded-full blur-3xl transition-all duration-1000 ${isVisible ? 'opacity-100 scale-100' : 'opacity-0 scale-50'}`} />
                <div className={`absolute -bottom-20 -right-20 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl transition-all duration-1000 delay-300 ${isVisible ? 'opacity-100 scale-100' : 'opacity-0 scale-50'}`} />
                <div className="absolute top-1/2 left-1/4 w-4 h-4 bg-white/30 rounded-full animate-float" />
                <div className="absolute top-1/3 right-1/4 w-3 h-3 bg-white/20 rounded-full animate-float delay-500" />
                <div className="absolute bottom-1/4 left-1/3 w-2 h-2 bg-white/40 rounded-full animate-float delay-300" />
            </div>

            <div className="relative max-w-4xl mx-auto px-4 text-center">
                {/* Icon */}
                <div className={`mx-auto mb-6 h-16 w-16 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center transition-all duration-700 ${isVisible ? 'opacity-100 translate-y-0 rotate-0' : 'opacity-0 translate-y-8 rotate-12'}`}>
                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>

                {/* Title */}
                <h2 className={`text-3xl md:text-5xl font-bold text-white mb-4 transition-all duration-700 delay-100 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    Fique por dentro das novidades
                </h2>
                <p className={`text-lg text-white/80 mb-10 max-w-xl mx-auto transition-all duration-700 delay-200 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    Cadastre-se e receba ofertas exclusivas, lançamentos e promoções diretamente no seu e-mail.
                </p>

                {/* Form */}
                <form
                    onSubmit={handleSubmit}
                    className={`flex flex-col sm:flex-row gap-3 max-w-lg mx-auto transition-all duration-700 delay-300 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}
                >
                    <div className="relative flex-1">
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="Seu melhor e-mail"
                            disabled={status === 'loading' || status === 'success'}
                            className="w-full px-6 py-4 bg-white/10 backdrop-blur-md text-white placeholder-white/60 rounded-full border border-white/20 focus:border-white/50 focus:bg-white/20 outline-none transition-all disabled:opacity-50"
                        />
                        {status === 'success' && (
                            <div className="absolute right-4 top-1/2 -translate-y-1/2">
                                <svg className="w-6 h-6 text-green-400 animate-scaleIn" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        )}
                    </div>
                    <button
                        type="submit"
                        disabled={status === 'loading' || status === 'success'}
                        className="relative px-8 py-4 bg-white text-indigo-600 font-bold rounded-full hover:bg-white/90 hover:scale-105 active:scale-[0.98] transition-all disabled:opacity-80 disabled:hover:scale-100 overflow-hidden group"
                    >
                        <span className={`flex items-center justify-center gap-2 transition-all ${status === 'loading' ? 'opacity-0' : 'opacity-100'}`}>
                            {status === 'success' ? 'Inscrito!' : 'Inscrever'}
                            {status === 'idle' && (
                                <svg className="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            )}
                        </span>
                        {status === 'loading' && (
                            <div className="absolute inset-0 flex items-center justify-center">
                                <div className="w-5 h-5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin" />
                            </div>
                        )}
                    </button>
                </form>

                {/* Trust badges */}
                <div className={`flex items-center justify-center gap-6 mt-10 text-white/60 text-sm transition-all duration-700 delay-500 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
                    <div className="flex items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Dados protegidos</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span>Sem spam</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Ofertas exclusivas</span>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default Newsletter;
