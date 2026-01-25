import { useState, useEffect, useRef, useCallback } from 'react';

/**
 * Hook para detectar quando elemento entra na viewport
 */
export function useInView(options = {}) {
    const [isInView, setIsInView] = useState(false);
    const [hasAnimated, setHasAnimated] = useState(false);
    const ref = useRef(null);

    useEffect(() => {
        const element = ref.current;
        if (!element) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsInView(true);
                    if (options.once !== false) {
                        setHasAnimated(true);
                    }
                } else if (options.once === false) {
                    setIsInView(false);
                }
            },
            {
                threshold: options.threshold || 0.1,
                rootMargin: options.rootMargin || '0px',
            }
        );

        observer.observe(element);
        return () => observer.disconnect();
    }, [options.threshold, options.rootMargin, options.once]);

    return [ref, hasAnimated ? true : isInView];
}

/**
 * Hook para animação de contagem de números
 */
export function useCountUp(end, duration = 2000, start = 0) {
    const [count, setCount] = useState(start);
    const [isRunning, setIsRunning] = useState(false);

    const startCount = useCallback(() => {
        if (isRunning) return;
        setIsRunning(true);

        const startTime = Date.now();
        const diff = end - start;

        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function (ease-out)
            const eased = 1 - Math.pow(1 - progress, 3);
            setCount(Math.floor(start + diff * eased));

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                setCount(end);
                setIsRunning(false);
            }
        };

        requestAnimationFrame(animate);
    }, [end, duration, start, isRunning]);

    return [count, startCount];
}

/**
 * Hook para staggered animations (delay entre elementos)
 */
export function useStaggeredAnimation(itemCount, baseDelay = 100) {
    const [visibleItems, setVisibleItems] = useState([]);

    const triggerAnimation = useCallback(() => {
        setVisibleItems([]);
        for (let i = 0; i < itemCount; i++) {
            setTimeout(() => {
                setVisibleItems(prev => [...prev, i]);
            }, i * baseDelay);
        }
    }, [itemCount, baseDelay]);

    return [visibleItems, triggerAnimation];
}

/**
 * Hook para parallax simples
 */
export function useParallax(speed = 0.5) {
    const [offset, setOffset] = useState(0);
    const ref = useRef(null);

    useEffect(() => {
        const handleScroll = () => {
            if (!ref.current) return;
            const rect = ref.current.getBoundingClientRect();
            const scrolled = window.innerHeight - rect.top;
            setOffset(scrolled * speed);
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        return () => window.removeEventListener('scroll', handleScroll);
    }, [speed]);

    return [ref, offset];
}

/**
 * Hook para hover com delay
 */
export function useHoverDelay(enterDelay = 0, leaveDelay = 150) {
    const [isHovered, setIsHovered] = useState(false);
    const timeoutRef = useRef(null);

    const handlers = {
        onMouseEnter: () => {
            clearTimeout(timeoutRef.current);
            timeoutRef.current = setTimeout(() => setIsHovered(true), enterDelay);
        },
        onMouseLeave: () => {
            clearTimeout(timeoutRef.current);
            timeoutRef.current = setTimeout(() => setIsHovered(false), leaveDelay);
        },
    };

    useEffect(() => {
        return () => clearTimeout(timeoutRef.current);
    }, []);

    return [isHovered, handlers];
}

/**
 * Hook para typed text effect
 */
export function useTypedText(text, speed = 50, startDelay = 0) {
    const [displayText, setDisplayText] = useState('');
    const [isComplete, setIsComplete] = useState(false);

    const start = useCallback(() => {
        setDisplayText('');
        setIsComplete(false);

        setTimeout(() => {
            let i = 0;
            const interval = setInterval(() => {
                if (i < text.length) {
                    setDisplayText(text.substring(0, i + 1));
                    i++;
                } else {
                    setIsComplete(true);
                    clearInterval(interval);
                }
            }, speed);
        }, startDelay);
    }, [text, speed, startDelay]);

    return [displayText, isComplete, start];
}

/**
 * Classes de animação CSS
 */
export const animations = {
    fadeIn: 'animate-fadeIn',
    fadeInUp: 'animate-fadeInUp',
    fadeInDown: 'animate-fadeInDown',
    fadeInLeft: 'animate-fadeInLeft',
    fadeInRight: 'animate-fadeInRight',
    scaleIn: 'animate-scaleIn',
    slideInUp: 'animate-slideInUp',
    bounce: 'animate-bounce',
    pulse: 'animate-pulse',
    spin: 'animate-spin',
};

/**
 * Estilos para transições
 */
export const transitions = {
    fast: 'transition-all duration-150 ease-out',
    normal: 'transition-all duration-300 ease-out',
    slow: 'transition-all duration-500 ease-out',
    spring: 'transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]',
};
