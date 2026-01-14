# Componentes UI (Blade + Tailwind)

Este projeto usa componentes Blade simples para padronizar UI e reduzir repetição.

## Componentes disponíveis

### x-ui.card
- Arquivo: `resources/views/components/ui/card.blade.php`
- Uso:

```blade
<x-ui.card>
    <div class="text-lg font-semibold">Título</div>
    <div class="text-sm text-slate-600 dark:text-slate-300">Conteúdo</div>
</x-ui.card>
```

### x-ui.button
- Arquivo: `resources/views/components/ui/button.blade.php`
- Variantes: `primary`, `secondary`, `danger`
- Uso:

```blade
<x-ui.button type="submit" class="w-full">Salvar</x-ui.button>
<x-ui.button variant="secondary">Cancelar</x-ui.button>
```

### x-ui.modal
- Arquivo: `resources/views/components/ui/modal.blade.php`
- Abertura/fechamento:
  - Abrir: `data-open-modal="ID_DO_MODAL"`
  - Fechar: `data-close-modal="ID_DO_MODAL"`
  - Escape fecha o modal aberto
- Uso:

```blade
<a href="#" data-open-modal="recover">Esqueci minha senha</a>

<x-ui.modal id="recover" title="Recuperar senha">
    <form class="space-y-4">
        <input type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2" />
        <x-ui.button type="submit" class="w-full">Enviar</x-ui.button>
    </form>
</x-ui.modal>
```

## Padrões
- Formulários: inputs com `label` visível (ou `sr-only` quando necessário).
- Acessibilidade: modais com `role="dialog"`, `aria-modal`, `aria-labelledby`, e botões com `aria-label` quando o texto não for suficiente.
- Tema: suporte a `dark:` com alternância por classe no `<html>`.

