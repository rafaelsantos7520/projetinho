# Guia de estilo (UI/UX)

## Objetivos
- UI consistente e rápida de manter
- Responsivo (mobile-first)
- Acessível (WCAG 2.1 AA)
- Bom desempenho (evitar dependências pesadas no runtime)

## Layout
- Use containers com `max-w-5xl` e espaçamentos `p-4` / `py-6 sm:py-10`.
- Prefira grids responsivos: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-...`.

## Tipografia e cores
- Texto principal: `text-slate-900` / `dark:text-slate-50`
- Texto secundário: `text-slate-600` / `dark:text-slate-300`
- Borda: `border-slate-200` / `dark:border-slate-800`
- Superfícies: `bg-white` / `dark:bg-slate-900` e `bg-slate-50` / `dark:bg-slate-950`

## Interações
- Botões e links sempre com estado de foco: `focus:ring-2 focus:ring-slate-900/20`.
- Feedback de carregamento: overlay simples em submits de formulários críticos.
- Modais: fechar por botão e por `Escape`.

## Acessibilidade (checklist)
- Cada input com `label` associado.
- Elementos clicáveis com texto claro; se não houver, usar `aria-label`.
- Contraste suficiente em claro/escuro.
- Navegação por teclado: foco visível e modais fecháveis por `Escape`.

## Performance
- Imagens com `loading="lazy"` e `decoding="async"`.
- Evite JS pesado; preferir scripts pequenos e pontuais.
- Em produção, o ideal é compilar Tailwind (em vez de CDN) para reduzir CSS.

