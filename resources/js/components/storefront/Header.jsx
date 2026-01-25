import React, { useState, useEffect } from 'react';

function Header({ baseUrl, productsUrl, logoUrl, categories = [] }) {
    const [isScrolled, setIsScrolled] = useState(false);
    const [searchOpen, setSearchOpen] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');

    useEffect(() => {
        const handleScroll = () => setIsScrolled(window.scrollY > 20);
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const handleSearch = (e) => {
        e.preventDefault();
        if (searchQuery.trim()) {
            window.location.href = `${productsUrl}?q=${encodeURIComponent(searchQuery)}`;
        }
    };

    return (
        <header className={`sticky top-0 z-50 transition-all duration-300 ${isScrolled ? 'bg-white shadow-md' : 'bg-white/95 backdrop-blur-lg'} border-b border-slate-200`}>
            {/* Top Bar */}
            <div className="bg-slate-900 text-white text-xs py-2">
                <div className="max-w-7xl mx-auto px-4 flex justify-between items-center">
                    <span className="hidden sm:block">Enviamos para todo o Brasil 🇧🇷</span>
                    <span className="flex items-center gap-2 mx-auto sm:mx-0">
                        <span className="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Frete grátis acima de R$ 199
                    </span>
                </div>
            </div>

            {/* Main Header */}
            <div className="max-w-7xl mx-auto px-4">
                <div className="h-16 md:h-20 flex items-center justify-between gap-4">
                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                        className="lg:hidden p-2 -ml-2 text-slate-600 hover:text-slate-900"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {/* Logo */}
                    <a href={baseUrl} className="shrink-0 flex items-center gap-3 group">
                        {logoUrl ? (
                            <img src={logoUrl} alt="Logo" className="h-10 md:h-12 w-auto object-contain" />
                        ) : (
                            <div className="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg transition-transform group-hover:scale-105">
                                L
                            </div>
                        )}
                    </a>

                    {/* Desktop Search */}
                    <form onSubmit={handleSearch} className="hidden lg:flex flex-1 max-w-xl mx-8">
                        <div className="relative w-full flex">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="O que você está procurando?"
                                className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-l-full py-3 pl-5 pr-4 text-sm transition-all outline-none"
                            />
                            <button
                                type="submit"
                                className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 rounded-r-full transition-colors flex items-center"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {/* Actions */}
                    <div className="flex items-center gap-2 md:gap-4">
                        <button
                            onClick={() => setSearchOpen(!searchOpen)}
                            className="lg:hidden p-2 text-slate-600 hover:text-slate-900"
                        >
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <a href="#" className="hidden md:flex items-center gap-2 text-sm text-slate-600 hover:text-indigo-600 transition-colors">
                            <div className="p-2 bg-slate-100 rounded-full">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span className="hidden xl:block font-medium">Entrar</span>
                        </a>

                        <a href="#" className="relative p-2 text-slate-600 hover:text-indigo-600 transition-colors group">
                            <div className="p-2 bg-slate-100 rounded-full group-hover:bg-indigo-50 transition-colors">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <span className="absolute top-0 right-0 h-5 w-5 bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-white">
                                0
                            </span>
                        </a>
                    </div>
                </div>

                {/* Desktop Navigation */}
                <nav className="hidden lg:flex items-center gap-8 py-3 border-t border-slate-100">
                    <a href={baseUrl} className="text-sm font-semibold text-slate-900 hover:text-indigo-600 transition-colors">
                        Início
                    </a>
                    <a href={productsUrl} className="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">
                        Todos os Produtos
                    </a>
                    {categories.slice(0, 4).map((cat) => (
                        <a
                            key={cat.id}
                            href={cat.url}
                            className="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors"
                        >
                            {cat.name}
                        </a>
                    ))}
                    <div className="flex-1" />
                    <a href="#" className="text-sm font-medium text-red-600 hover:text-red-700 flex items-center gap-1">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        </svg>
                        Ofertas
                    </a>
                </nav>
            </div>

            {/* Mobile Search Overlay */}
            {searchOpen && (
                <div className="lg:hidden absolute top-full left-0 w-full bg-white border-b border-slate-200 p-4 shadow-lg">
                    <form onSubmit={handleSearch} className="relative">
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="O que você procura?"
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg py-3 pl-10 pr-4 text-sm focus:outline-none focus:border-indigo-500"
                            autoFocus
                        />
                        <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </form>
                </div>
            )}

            {/* Mobile Menu Overlay */}
            {mobileMenuOpen && (
                <div className="lg:hidden absolute top-full left-0 w-full bg-white border-b border-slate-200 shadow-xl max-h-[80vh] overflow-y-auto">
                    <div className="p-4 space-y-1">
                        <a href={baseUrl} className="block p-3 rounded-lg hover:bg-slate-50 font-semibold text-slate-800">
                            Início
                        </a>
                        <a href={productsUrl} className="block p-3 rounded-lg hover:bg-slate-50 font-semibold text-slate-800">
                            Todos os Produtos
                        </a>
                        <div className="border-t border-slate-100 my-2" />
                        {categories.map((cat) => (
                            <a
                                key={cat.id}
                                href={cat.url}
                                className="block p-3 rounded-lg hover:bg-slate-50 text-slate-600"
                            >
                                {cat.name}
                            </a>
                        ))}
                    </div>
                </div>
            )}
        </header>
    );
}

export default Header;
