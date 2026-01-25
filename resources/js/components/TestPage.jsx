import React, { useState } from 'react';

export default function TestPage() {
    const [count, setCount] = useState(0);

    return (
        <div className="p-8 bg-white rounded-xl shadow-lg border border-slate-200 text-center max-w-md mx-auto mt-10">
            <div className="flex justify-center mb-6">
                <div className="h-16 w-16 bg-[#61DAFB] rounded-full flex items-center justify-center animate-spin-slow">
                    <svg viewBox="-10.5 -9.45 21 18.9" fill="none" xmlns="http://www.w3.org/2000/svg" className="w-10 h-10 text-slate-900">
                        <circle cx="0" cy="0" r="2" fill="currentColor"></circle>
                        <g stroke="currentColor" strokeWidth="1" fill="none">
                            <ellipse rx="10" ry="4.5"></ellipse>
                            <ellipse rx="10" ry="4.5" transform="rotate(60)"></ellipse>
                            <ellipse rx="10" ry="4.5" transform="rotate(120)"></ellipse>
                        </g>
                    </svg>
                </div>
            </div>

            <h1 className="text-2xl font-bold text-slate-800 mb-2">React no Laravel!</h1>
            <p className="text-slate-500 mb-6">Isso é um componente React completo rodando dentro da sua aplicação.</p>

            <div className="bg-slate-50 rounded-lg p-4 mb-6">
                <p className="text-sm font-medium text-slate-600 mb-2">Contador Interativo:</p>
                <div className="text-4xl font-bold text-primary mb-2 text-indigo-600">{count}</div>
                <div className="flex justify-center gap-2">
                    <button
                        onClick={() => setCount(count - 1)}
                        className="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors font-medium text-sm"
                    >
                        - Diminuir
                    </button>
                    <button
                        onClick={() => setCount(count + 1)}
                        className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm"
                    >
                        + Aumentar
                    </button>
                </div>
            </div>

            <p className="text-xs text-slate-400">
                Arquivo: resources/js/components/TestPage.jsx
            </p>
        </div>
    );
}
