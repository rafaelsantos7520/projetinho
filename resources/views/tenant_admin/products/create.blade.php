<x-layouts.app :title="'Admin da Loja'" :subtitle="'Novo Produto'">
    @php
        $tenantSlug = app()->bound(\App\Models\Tenant::class) ? app(\App\Models\Tenant::class)->slug : request()->route('tenant');
        
        // Tamanhos pré-definidos - cada grupo é exclusivo
        $sizesClothing = ['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG'];
        $sizesPants = ['36', '38', '40', '42', '44', '46', '48', '50', '52'];
        $sizesShoes = ['34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        
        // Cores pré-definidas
        $colors = ['Preto', 'Branco', 'Azul', 'Vermelho', 'Verde', 'Amarelo', 'Rosa', 'Roxo', 'Cinza', 'Marrom', 'Bege', 'Laranja', 'Azul Marinho', 'Vinho'];
        
        // Apenas categorias ativas
        $activeCategories = $categories->where('is_active', true);
    @endphp

    <style>
        .product-form-container { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
        .product-form-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .product-form-grid { display: flex; gap: 24px; align-items: flex-start; }
        .product-form-main { flex: 2; min-width: 0; }
        .product-form-sidebar { flex: 1; min-width: 280px; max-width: 350px; }
        .product-card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 24px; }
        .product-card-header { padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px; }
        .product-card-body { padding: 20px; }
        .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
        .form-row > div { flex: 1; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        .form-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .price-input-wrapper { position: relative; }
        .price-prefix { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-weight: 600; }
        .price-input { padding-left: 40px !important; font-weight: 500; }
        .image-slot { border: 2px dashed #d1d5db; border-radius: 12px; background: #f9fafb; cursor: pointer; aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; transition: all 0.2s; }
        .image-slot:hover { border-color: #9ca3af; background: #f3f4f6; }
        .btn-primary { width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; }
        .btn-secondary { display: block; width: 100%; padding: 12px 20px; background: white; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 600; color: #374151; text-align: center; text-decoration: none; box-sizing: border-box; }
        
        /* Size/Color chips */
        .chips-container { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { display: inline-flex; align-items: center; padding: 8px 14px; background: #f1f5f9; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; }
        .chip:hover { border-color: #3b82f6; background: #eff6ff; }
        .chip.selected { background: #3b82f6; color: white; border-color: #3b82f6; }
        .chip-group-label { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 6px; margin-top: 12px; }
        .chip-group-label:first-child { margin-top: 0; }
        
        /* Color chips with preview */
        .color-chip { position: relative; padding-left: 30px; }
        .color-dot { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.15); }
        
        /* Field error */
        .field-error { font-size: 12px; color: #dc2626; margin-top: 6px; }
        
        /* RESPONSIVE */
        @media (max-width: 900px) {
            .product-form-grid { flex-direction: column; }
            .product-form-sidebar { max-width: 100%; min-width: 100%; }
        }
    </style>

    <div class="product-form-container" x-data="productForm()">
        <!-- Header -->
        <div class="product-form-header">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">Novo Produto</h1>
                <p style="font-size: 14px; color: #64748b; margin-top: 4px;">Cadastre o produto com imagens, preço e categoria</p>
            </div>
            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="btn-secondary" style="width: auto; padding: 8px 16px;">
                ← Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('tenant_admin.products.store', ['tenant' => $tenantSlug]) }}" enctype="multipart/form-data" @submit="if(validateForm()) { submitting = true; } else { return false; }">
            @csrf
            
            <div class="product-form-grid">
                <!-- LEFT COLUMN -->
                <div class="product-form-main">
                    
                    <!-- Informações Gerais -->
                    <div class="product-card">
                        <div class="product-card-header" style="background: linear-gradient(to right, #f1f5f9, #e2e8f0);">
                            <svg style="width: 20px; height: 20px; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <span style="font-weight: 700; color: #1e293b;">Informações Gerais</span>
                                <p style="font-size: 12px; color: #64748b; margin: 2px 0 0;">Nome, categoria e destaque</p>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <div style="margin-bottom: 16px;">
                                <label class="form-label">Nome do Produto <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Ex: Camiseta Básica Algodão" required>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label class="form-label">Categoria <span style="color: #ef4444;">*</span></label>
                                    <select name="category_id" class="form-input" required>
                                        <option value="">Selecione...</option>
                                        @foreach($activeCategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('tenant_admin.categories.create', ['tenant' => $tenantSlug]) }}" style="font-size: 12px; color: #3b82f6; text-decoration: none; margin-top: 4px; display: inline-block;">+ Criar nova categoria</a>
                                </div>
                                <div style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured')) style="width: 18px; height: 18px;">
                                        <span style="font-size: 14px; color: #374151;">Produto em Destaque</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preços e Descrição -->
                    <div class="product-card">
                        <div class="product-card-header" style="background: linear-gradient(to right, #d1fae5, #a7f3d0);">
                            <svg style="width: 20px; height: 20px; color: #166534;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <span style="font-weight: 700; color: #1e293b;">Preços e Descrição</span>
                                <p style="font-size: 12px; color: #166534; margin: 2px 0 0;">Valores e informações do produto</p>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <div class="form-row">
                                <div>
                                    <label class="form-label">Preço <span style="color: #ef4444;">*</span></label>
                                    <div class="price-input-wrapper">
                                        <span class="price-prefix">R$</span>
                                        <input type="text" name="price_formatted" id="price_input" class="form-input price-input" value="{{ old('price_formatted') }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Preço Promocional <span style="font-size: 11px; color: #9ca3af;">(opcional)</span></label>
                                    <div class="price-input-wrapper">
                                        <span class="price-prefix">R$</span>
                                        <input type="text" name="promo_price_formatted" id="promo_input" value="{{ old('promo_price_formatted') }}" class="form-input price-input">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Descrição <span style="font-size: 11px; color: #9ca3af;">(opcional)</span></label>
                                <textarea name="description" rows="4" class="form-input" style="resize: none;">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tamanho e Cor -->
                    <div class="product-card">
                        <div class="product-card-header" style="background: linear-gradient(to right, #fef3c7, #fde68a);">
                            <svg style="width: 20px; height: 20px; color: #92400e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <div>
                                <span style="font-weight: 700; color: #1e293b;">Tamanho e Cor</span>
                                <p style="font-size: 12px; color: #92400e; margin: 2px 0 0;">Campos obrigatórios</p>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <!-- Tamanho -->
                            <div style="margin-bottom: 20px;">
                                <label class="form-label">Tamanho <span style="color: #ef4444;">*</span></label>
                                <input type="hidden" name="size" :value="selectedSize" required>
                                
                                <p class="chip-group-label">Roupa</p>
                                <div class="chips-container">
                                    @foreach($sizesClothing as $size)
                                        <div class="chip" 
                                             :class="{ 'selected': selectedSize === '{{ $size }}' }" 
                                             @click="selectSize('{{ $size }}', 'clothing')">
                                            {{ $size }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <p class="chip-group-label">Calça/Bermuda</p>
                                <div class="chips-container">
                                    @foreach($sizesPants as $size)
                                        <div class="chip" 
                                             :class="{ 'selected': selectedSize === '{{ $size }}' }" 
                                             @click="selectSize('{{ $size }}', 'pants')">
                                            {{ $size }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <p class="chip-group-label">Calçado</p>
                                <div class="chips-container">
                                    @foreach($sizesShoes as $size)
                                        <div class="chip" 
                                             :class="{ 'selected': selectedSize === '{{ $size }}' }" 
                                             @click="selectSize('{{ $size }}', 'shoes')">
                                            {{ $size }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div style="margin-top: 12px;">
                                    <input type="text" placeholder="Ou digite um tamanho personalizado..." class="form-input" style="max-width: 250px;" 
                                           @input="selectedSize = $event.target.value; sizeGroup = 'custom'"
                                           :value="sizeGroup === 'custom' ? selectedSize : ''">
                                </div>
                                
                                <p x-show="sizeError" class="field-error" x-text="sizeError"></p>
                            </div>
                            
                            <!-- Cor -->
                            <div>
                                <label class="form-label">Cor <span style="color: #ef4444;">*</span></label>
                                <input type="hidden" name="color" :value="selectedColor" required>
                                
                                <div class="chips-container">
                                    @foreach($colors as $color)
                                        <div class="chip color-chip" 
                                             :class="{ 'selected': selectedColor === '{{ $color }}' }" 
                                             @click="selectedColor = selectedColor === '{{ $color }}' ? '' : '{{ $color }}'">
                                            <span class="color-dot" :style="'background-color: ' + (colorMap['{{ $color }}'] || '#ccc')"></span>
                                            {{ $color }}
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div style="margin-top: 12px;">
                                    <input type="text" placeholder="Ou digite uma cor personalizada..." class="form-input" style="max-width: 250px;" 
                                           @input="selectedColor = $event.target.value"
                                           :value="!colorList.includes(selectedColor) ? selectedColor : ''">
                                </div>
                                
                                <p x-show="colorError" class="field-error" x-text="colorError"></p>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- RIGHT COLUMN (Sidebar) -->
                <div class="product-form-sidebar">
                    
                    <!-- Imagens -->
                    <div class="product-card">
                        <div class="product-card-header" style="background: linear-gradient(to right, #dbeafe, #bfdbfe);">
                            <svg style="width: 20px; height: 20px; color: #1e40af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div>
                                <span style="font-weight: 700; color: #1e293b;">Imagens</span>
                                <p style="font-size: 12px; color: #1e40af; margin: 2px 0 0;">Até 3 imagens • Defina a principal</p>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <!-- Main Image (Slot 0) -->
                            <div class="image-slot" style="margin-bottom: 12px;" data-image-slot="create-0">
                                <img data-image-preview="create-0" style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" />
                                
                                <div data-image-placeholder="create-0" style="text-align: center;">
                                    <svg style="width: 40px; height: 40px; color: #9ca3af; margin-bottom: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p style="font-size: 13px; font-weight: 500; color: #374151; margin: 0;">Imagem Principal</p>
                                </div>
                                <div data-image-overlay="create-0" style="display: none; position: absolute; bottom: 8px; left: 8px; right: 8px; z-index: 10;">
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" data-replace="create-0" style="flex: 1; padding: 8px; background: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">Trocar</button>
                                        <button type="button" data-remove="create-0" style="flex: 1; padding: 8px; background: #ef4444; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Remover</button>
                                    </div>
                                </div>
                                <input type="file" name="images[0]" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" style="display: none;" data-file-input="create-0" />
                            </div>

                            <!-- Additional Images -->
                            <div style="display: flex; gap: 12px;">
                                @for ($slot = 1; $slot < 3; $slot++)
                                    <div class="image-slot" style="flex: 1;" data-image-slot="create-{{ $slot }}">
                                        <img data-image-preview="create-{{ $slot }}" style="display: none; position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 10px;" />
                                        
                                        <div data-image-placeholder="create-{{ $slot }}" style="text-align: center;">
                                            <svg style="width: 24px; height: 24px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </div>
                                        <div data-image-overlay="create-{{ $slot }}" style="display: none; position: absolute; bottom: 4px; left: 4px; right: 4px; z-index: 10;">
                                            <button type="button" data-replace="create-{{ $slot }}" style="width: 100%; padding: 4px; background: white; border: none; border-radius: 4px; font-size: 10px; font-weight: 600; cursor: pointer; margin-bottom: 2px;">Trocar</button>
                                            <button type="button" data-remove="create-{{ $slot }}" style="width: 100%; padding: 4px; background: #ef4444; color: white; border: none; border-radius: 4px; font-size: 10px; font-weight: 600; cursor: pointer;">Remover</button>
                                        </div>
                                        <input type="file" name="images[{{ $slot }}]" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" style="display: none;" data-file-input="create-{{ $slot }}" />
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="product-card" x-show="selectedSize || selectedColor">
                        <div class="product-card-header" style="background: #f0fdf4;">
                            <span style="font-weight: 700; color: #166534;">Resumo</span>
                        </div>
                        <div class="product-card-body" style="padding: 12px 16px;">
                            <div x-show="selectedSize" style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb;">
                                <span style="font-size: 13px; color: #64748b;">Tamanho</span>
                                <span style="font-size: 13px; font-weight: 600; color: #1e293b;" x-text="selectedSize"></span>
                            </div>
                            <div x-show="selectedColor" style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0;">
                                <span style="font-size: 13px; color: #64748b;">Cor</span>
                                <span style="display: flex; align-items: center; gap: 6px;">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1);" :style="'background-color: ' + (colorMap[selectedColor] || '#ccc')"></span>
                                    <span style="font-size: 13px; font-weight: 600; color: #1e293b;" x-text="selectedColor"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="product-card">
                        <div class="product-card-header" style="background: #f9fafb;">
                            <span style="font-weight: 700; color: #1e293b;">Ações</span>
                        </div>
                        <div class="product-card-body">
                            <button type="submit" class="btn-primary" :disabled="submitting" :style="submitting ? 'opacity: 0.7; cursor: not-allowed;' : ''" style="display: flex; justify-content: center; align-items: center; margin-bottom: 12px;">
                                <span x-show="!submitting">Salvar Produto</span>
                                <span x-show="submitting" style="display: flex; align-items: center; gap: 8px;">
                                    Salvando...
                                </span>
                            </button>
                            <a href="{{ route('tenant_admin.products.index', ['tenant' => $tenantSlug]) }}" class="btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        function productForm() {
            return {
                submitting: false,
                selectedSize: '{{ old('size') }}',
                selectedColor: '{{ old('color') }}',
                sizeGroup: '',
                sizeError: '',
                colorError: '',
                colorList: @json($colors),
                colorMap: {
                    'Preto': '#1a1a1a', 'Branco': '#ffffff', 'Azul': '#3b82f6', 'Vermelho': '#ef4444',
                    'Verde': '#22c55e', 'Amarelo': '#eab308', 'Rosa': '#ec4899', 'Roxo': '#a855f7',
                    'Cinza': '#6b7280', 'Marrom': '#92400e', 'Bege': '#d4b896', 'Laranja': '#f97316',
                    'Azul Marinho': '#1e3a5f', 'Vinho': '#722f37'
                },
                
                selectSize(size, group) {
                    if (this.selectedSize === size) {
                        this.selectedSize = '';
                        this.sizeGroup = '';
                    } else {
                        this.selectedSize = size;
                        this.sizeGroup = group;
                    }
                    this.sizeError = '';
                },
                
                validateForm() {
                    this.sizeError = '';
                    this.colorError = '';
                    let valid = true;
                    
                    if (!this.selectedSize.trim()) {
                        this.sizeError = 'Selecione um tamanho';
                        valid = false;
                    }
                    
                    if (!this.selectedColor.trim()) {
                        this.colorError = 'Selecione uma cor';
                        valid = false;
                    }
                    
                    return valid;
                }
            };
        }

        // Máscara de dinheiro
        function formatMoney(value) {
            let numbers = value.replace(/\D/g, '');
            if (!numbers) return '';
            let cents = parseInt(numbers) || 0;
            let reais = (cents / 100).toFixed(2);
            let parts = reais.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return parts.join(',');
        }

        function applyMoneyMask(input) {
            input.addEventListener('input', function(e) {
                e.target.value = formatMoney(e.target.value);
            });
        }

        document.querySelectorAll('#price_input, #promo_input').forEach(applyMoneyMask);

        // Image upload
        (function () {
            function setState(key, hasImage) {
                const preview = document.querySelector(`[data-image-preview="${key}"]`);
                const placeholder = document.querySelector(`[data-image-placeholder="${key}"]`);
                const overlay = document.querySelector(`[data-image-overlay="${key}"]`);
                if (!preview || !placeholder || !overlay) return;
                
                preview.style.display = hasImage ? 'block' : 'none';
                placeholder.style.display = hasImage ? 'none' : 'block';
                overlay.style.display = hasImage ? 'block' : 'none';
            }

            function wire(key) {
                const box = document.querySelector(`[data-image-slot="${key}"]`);
                const input = document.querySelector(`[data-file-input="${key}"]`);
                const replaceBtn = document.querySelector(`[data-replace="${key}"]`);
                const removeBtn = document.querySelector(`[data-remove="${key}"]`);
                const preview = document.querySelector(`[data-image-preview="${key}"]`);

                if (!box || !input || !preview) return;

                box.addEventListener('click', (e) => {
                    if (e.target.tagName === 'BUTTON') return;
                    input.click();
                });
                
                if (replaceBtn) replaceBtn.addEventListener('click', (e) => { e.stopPropagation(); input.click(); });

                if (removeBtn) removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    input.value = '';
                    preview.removeAttribute('src');
                    setState(key, false);
                });

                input.addEventListener('change', () => {
                    const file = input.files && input.files[0];
                    if (!file) return;
                    preview.src = URL.createObjectURL(file);
                    setState(key, true);
                });
            }

            ['create-0', 'create-1', 'create-2'].forEach(wire);
        })();
    </script>
</x-layouts.app>
