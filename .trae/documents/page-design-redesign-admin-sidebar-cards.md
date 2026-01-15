# Page Design — Redesign Admin (Sidebar + Cards)

## Diretrizes globais (desktop-first)
- **Layout:** grid 2 colunas (sidebar fixa + conteúdo fluido). Conteúdo usa cards com espaçamento consistente (24px) e radius (12px).
- **Meta:**
  - Title base: “Admin — {Seção}”
  - Description: “Painel administrativo para gerenciar {seção}.”
  - Open Graph: title/description iguais; type=website.
- **Tokens/estilo:**
  - Fundo app: #F6F7FB; cards: #FFFFFF; borda: #E8EAF0
  - Texto: #111827; secundário: #6B7280
  - Primário: #2563EB (hover #1D4ED8); Perigo: #DC2626
  - Tipografia: base 14–16px; títulos 18–24px; labels 12–13px
  - Botões: primary/secondary/ghost; estados disabled/loading; foco visível.
- **Responsivo:**
  - ≥1024px: sidebar fixa (260–280px), conteúdo em 2 colunas quando útil.
  - <1024px: sidebar colapsa (drawer); cards viram 1 coluna.

## Componentes compartilhados
- **Sidebar:** logo no topo; grupos (ex.: “Management”); itens: Produtos, Categorias, Configurações; estado ativo; colapso de grupo; contador opcional.
- **Header da página:** título + ações (ex.: “Novo”, “Salvar”); breadcrumb opcional.
- **Card:** header (título/descrição curta) + body (campos/controles) + footer (ações) quando necessário.
- **Feedback:** toast de sucesso/erro; skeleton em carregamento; modal de confirmação para ações destrutivas.

---

## Página: Admin — Produtos
### Estrutura
- **Coluna esquerda (cards):**
  1) Card “Lista de Produtos”: busca (input), filtros básicos (select), lista (linhas clicáveis).
- **Coluna direita (cards, estilo do layout enviado):**
  1) Card “Imagens do Produto” (gerenciador 0–3)
  2) Card “Informações Gerais” (campos essenciais do produto)
  3) Card “Estoque/Disponibilidade” (campos essenciais)

### Gerenciador de imagens (até 3)
- Exibe **slots** (1–3) em grid.
- Cada slot tem: preview, estado vazio (“Adicionar imagem”), e ações:
  - **Substituir:** abre seletor; faz upload; atualiza slot mantendo `sort_order`.
  - **Remover:** confirma; remove referência e opcionalmente apaga objeto no Storage.
- Botão “Adicionar outra imagem” fica visível **até** atingir 3; depois fica disabled com hint “Limite: 3”.
- Estados: uploading por slot (overlay + spinner); erro por slot (mensagem curta).

### Interações
- Selecionar item na lista abre edição na coluna direita.
- “Novo produto” limpa formulário e inicia modo criação.
- “Salvar” valida campos; mostra toast; mantém usuário na página.

---

## Página: Admin — Categorias
### Estrutura
- Conteúdo em 2 cards principais:
  1) Card “Lista de Categorias” (busca + lista)
  2) Card “Editar/Criar Categoria” (form em campos essenciais)
- Ações: “Nova categoria”, “Salvar”, “Remover” (com confirmação).

---

## Página: Admin — Configurações
### Estrutura
- Cards por seção (sem inventar novos grupos):
  - Card por grupo de chaves já existentes (ex.: “Geral”, “Catálogo”, etc. conforme dados).
- Cada card lista pares **chave → campo** (input/select/textarea conforme tipo conhecido) e um botão “Salvar alterações”.

### Interações
- Detectar alterações pendentes (dirty state) e alertar ao sair.
- Salvar com feedback e tratamento de erros por campo/linha.
