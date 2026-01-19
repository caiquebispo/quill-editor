<div>
    <div wire:ignore class="quill-editor-container">
        <div id="{{ $quillId }}" class="quill-editor"
            style="height: {{ $config["height"] ?? "200px" }}; width: {{ $config["width"] ?? "100%" }};"></div>

        {{-- Character Counter (inside wire:ignore to prevent reset) --}}
        @if($config['showCharacterCount'] ?? false)
        <div class="quill-character-counter" id="{{ $quillId }}-counter-wrapper">
            <span id="{{ $quillId }}-char-count">0</span>
            @if($config['characterLimit'] ?? null)
                / <span class="quill-char-limit">{{ $config['characterLimit'] }}</span>
            @endif
            <span class="quill-char-label">caracteres</span>
        </div>
        @endif
    </div>

    {{-- Hidden File Input for Image Upload --}}
    @if($config['imageUpload']['enabled'] ?? false)
    <input 
        type="file" 
        id="{{ $quillId }}-image-upload"
        wire:model="uploadedImage"
        accept="image/*"
        class="hidden"
        style="display: none;"
    />
    @endif

    <style>
        .quill-editor-container {
            width: 100%;
            max-width: {{ $config["width"] ?? "100%" }};
        }

        .quill-editor {
            width: 100%;
        }

        .ql-container {
            font-size: 16px;
        }

        .ql-editor {
            color: #333333 !important;
        }

        .ql-editor p,
        .ql-editor span,
        .ql-editor div,
        .ql-editor * {
            color: #333333 !important;
        }

        @media (max-width: 768px) {
            .quill-editor-container .quill-editor {
                height: {{ $config["mobile"]["height"] ?? ($config["height"] ?? "200px") }} !important;
            }

            .ql-toolbar button {
                padding: 3px 5px;
            }

            .ql-container {
                font-size: 14px;
            }
        }

        /* Character Counter Styles */
        .quill-character-counter {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            font-size: 12px;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }

        .quill-character-counter.over-limit {
            color: #dc2626;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .quill-character-counter.over-limit #{{ $quillId }}-char-count {
            font-weight: 600;
        }

        /* Auto-save Indicator Styles */
        .quill-autosave-indicator {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 10px;
            font-size: 11px;
            color: #059669;
            background: #d1fae5;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 10;
            animation: fadeIn 0.3s ease;
        }

        .quill-autosave-indicator.fade-out {
            animation: fadeOut 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .quill-editor-container {
            position: relative;
        }

        /* Full-screen Mode Styles */
        .quill-fullscreen-btn {
            position: absolute;
            top: 8px;
            right: 40px;
            width: 28px;
            height: 28px;
            padding: 4px;
            font-size: 14px;
            color: #6b7280;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quill-fullscreen-btn:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .quill-editor-container.quill-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
            background: white;
            border-radius: 0;
        }

        .quill-editor-container.quill-fullscreen .quill-editor {
            height: 100% !important;
        }

        .quill-editor-container.quill-fullscreen .ql-container {
            height: calc(100vh - 42px) !important;
        }

        .quill-editor-container.quill-fullscreen .quill-fullscreen-btn {
            top: 8px;
            right: 16px;
        }

        .quill-editor-container.quill-fullscreen .quill-character-counter {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 0;
        }
    </style>

    @once
        @push("styles")
            <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
            <style>
                /* Image Resize Module Styles */
                .ql-editor img {
                    cursor: pointer;
                }
                .ql-editor img.resizing {
                    outline: 2px solid #3b82f6;
                }
                .image-resize-container {
                    position: relative;
                    display: inline-block;
                }
                .image-resize-handle {
                    position: absolute;
                    width: 10px;
                    height: 10px;
                    background: #3b82f6;
                    border: 1px solid white;
                    border-radius: 2px;
                }
                .image-resize-handle.nw { top: -5px; left: -5px; cursor: nw-resize; }
                .image-resize-handle.ne { top: -5px; right: -5px; cursor: ne-resize; }
                .image-resize-handle.sw { bottom: -5px; left: -5px; cursor: sw-resize; }
                .image-resize-handle.se { bottom: -5px; right: -5px; cursor: se-resize; }
                .image-resize-toolbar {
                    position: absolute;
                    bottom: -35px;
                    left: 50%;
                    transform: translateX(-50%);
                    display: flex;
                    gap: 4px;
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 4px;
                    padding: 4px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    z-index: 100;
                }
                .image-resize-toolbar button {
                    padding: 4px 8px;
                    font-size: 11px;
                    border: 1px solid #e5e7eb;
                    border-radius: 3px;
                    background: #f9fafb;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .image-resize-toolbar button:hover {
                    background: #e5e7eb;
                }
                .image-resize-toolbar button.active {
                    background: #3b82f6;
                    color: white;
                    border-color: #3b82f6;
                }
            </style>
        @endpush
    @endonce

    @once
        @push("scripts")
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
            <script>
                window.quillInstances = window.quillInstances || {};

                // Character Counter Update Function
                function updateCharacterCount(editorId, quill, config) {
                    const charCountEl = document.getElementById(editorId + '-char-count');
                    const counterWrapper = document.getElementById(editorId + '-counter-wrapper');

                    if (!charCountEl || !counterWrapper) return;

                    const text = quill.getText().trim();
                    const charCount = text.length;
                    charCountEl.textContent = charCount;

                    // Check if over limit and add/remove class
                    const limit = config.characterLimit;
                    if (limit && charCount > limit) {
                        counterWrapper.classList.add('over-limit');
                    } else {
                        counterWrapper.classList.remove('over-limit');
                    }
                }

                // Auto-save Indicator Function
                function showAutoSaveIndicator(editorId, message) {
                    const container = document.getElementById(editorId)?.closest('.quill-editor-container');
                    if (!container) return;

                    // Remove existing indicator
                    const existing = container.querySelector('.quill-autosave-indicator');
                    if (existing) existing.remove();

                    // Create new indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'quill-autosave-indicator';
                    indicator.innerHTML = `<span>✓</span> ${message}`;
                    container.appendChild(indicator);

                    // Auto-remove after 2 seconds
                    setTimeout(() => {
                        indicator.classList.add('fade-out');
                        setTimeout(() => indicator.remove(), 300);
                    }, 2000);
                }

                // Image Resize Manager Class
                class ImageResizeManager {
                    constructor(quill, editorId) {
                        this.quill = quill;
                        this.editorId = editorId;
                        this.selectedImage = null;
                        this.overlay = null;
                        this.init();
                    }

                    init() {
                        this.quill.root.addEventListener('click', (e) => {
                            if (e.target.tagName === 'IMG') {
                                this.selectImage(e.target);
                            } else {
                                this.deselectImage();
                            }
                        });

                        document.addEventListener('click', (e) => {
                            if (this.selectedImage && !this.quill.root.contains(e.target) && 
                                !this.overlay?.contains(e.target)) {
                                this.deselectImage();
                            }
                        });
                    }

                    selectImage(img) {
                        this.deselectImage();
                        this.selectedImage = img;
                        img.classList.add('resizing');
                        this.createOverlay(img);
                    }

                    deselectImage() {
                        if (this.selectedImage) {
                            this.selectedImage.classList.remove('resizing');
                            this.selectedImage = null;
                        }
                        if (this.overlay) {
                            this.overlay.remove();
                            this.overlay = null;
                        }
                    }

                    createOverlay(img) {
                        this.overlay = document.createElement('div');
                        this.overlay.className = 'image-resize-container';
                        this.overlay.style.position = 'absolute';
                        
                        const rect = img.getBoundingClientRect();
                        const editorRect = this.quill.root.getBoundingClientRect();
                        
                        this.overlay.style.left = (rect.left - editorRect.left + this.quill.root.scrollLeft) + 'px';
                        this.overlay.style.top = (rect.top - editorRect.top + this.quill.root.scrollTop) + 'px';
                        this.overlay.style.width = rect.width + 'px';
                        this.overlay.style.height = rect.height + 'px';

                        // Add resize handles
                        ['nw', 'ne', 'sw', 'se'].forEach(pos => {
                            const handle = document.createElement('div');
                            handle.className = `image-resize-handle ${pos}`;
                            handle.addEventListener('mousedown', (e) => this.startResize(e, pos));
                            this.overlay.appendChild(handle);
                        });

                        // Add toolbar with preset sizes
                        const toolbar = document.createElement('div');
                        toolbar.className = 'image-resize-toolbar';
                        
                        const sizes = [
                            { label: '25%', value: 0.25 },
                            { label: '50%', value: 0.5 },
                            { label: '75%', value: 0.75 },
                            { label: '100%', value: 1 },
                        ];
                        
                        sizes.forEach(size => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = size.label;
                            btn.addEventListener('click', () => this.resizeToPercent(size.value));
                            toolbar.appendChild(btn);
                        });

                        this.overlay.appendChild(toolbar);
                        this.quill.root.appendChild(this.overlay);
                    }

                    startResize(e, position) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const startX = e.clientX;
                        const startY = e.clientY;
                        const startWidth = this.selectedImage.offsetWidth;
                        const startHeight = this.selectedImage.offsetHeight;
                        const aspectRatio = startWidth / startHeight;

                        const onMouseMove = (moveEvent) => {
                            let newWidth, newHeight;
                            const deltaX = moveEvent.clientX - startX;
                            const deltaY = moveEvent.clientY - startY;

                            if (position.includes('e')) {
                                newWidth = Math.max(50, startWidth + deltaX);
                            } else {
                                newWidth = Math.max(50, startWidth - deltaX);
                            }

                            newHeight = newWidth / aspectRatio;

                            this.selectedImage.style.width = newWidth + 'px';
                            this.selectedImage.style.height = 'auto';
                            this.selectedImage.setAttribute('width', Math.round(newWidth));
                            
                            this.updateOverlayPosition();
                        };

                        const onMouseUp = () => {
                            document.removeEventListener('mousemove', onMouseMove);
                            document.removeEventListener('mouseup', onMouseUp);
                        };

                        document.addEventListener('mousemove', onMouseMove);
                        document.addEventListener('mouseup', onMouseUp);
                    }

                    resizeToPercent(percent) {
                        if (!this.selectedImage) return;
                        
                        const editorWidth = this.quill.root.offsetWidth - 30;
                        const newWidth = editorWidth * percent;
                        
                        this.selectedImage.style.width = newWidth + 'px';
                        this.selectedImage.style.height = 'auto';
                        this.selectedImage.setAttribute('width', Math.round(newWidth));
                        
                        this.updateOverlayPosition();
                    }

                    updateOverlayPosition() {
                        if (!this.overlay || !this.selectedImage) return;
                        
                        const rect = this.selectedImage.getBoundingClientRect();
                        const editorRect = this.quill.root.getBoundingClientRect();
                        
                        this.overlay.style.left = (rect.left - editorRect.left + this.quill.root.scrollLeft) + 'px';
                        this.overlay.style.top = (rect.top - editorRect.top + this.quill.root.scrollTop) + 'px';
                        this.overlay.style.width = rect.width + 'px';
                        this.overlay.style.height = rect.height + 'px';
                    }
                }

                // Função para processar configurações avançadas do Quill
                function processQuillConfig(config) {
                    // Clone para não modificar o objeto original
                    const processedConfig = JSON.parse(JSON.stringify(config));

                    // Garantir que as configurações básicas existam
                    processedConfig.theme = processedConfig.theme || 'snow';
                    processedConfig.placeholder = processedConfig.placeholder || 'Digite aqui...';
                    processedConfig.modules = processedConfig.modules || {};
                    processedConfig.formats = processedConfig.formats || [];

                    // Configurações responsivas
                    const isMobile = window.innerWidth < 768;
                    if (isMobile && processedConfig.mobile) {
                        // Aplicar configurações específicas para dispositivos móveis
                        if (processedConfig.mobile.simplifyToolbar !== false) {
                            // Simplificar a barra de ferramentas em dispositivos móveis
                            if (processedConfig.modules.toolbar && Array.isArray(processedConfig.modules.toolbar)) {
                                // Manter apenas as ferramentas essenciais em dispositivos móveis
                                const essentialTools = [
                                    ['bold', 'italic', 'underline'],
                                    [{
                                        'header': 1
                                    }, {
                                        'header': 2
                                    }],
                                    ['link']
                                ];

                                // Verificar se o usuário definiu uma barra de ferramentas móvel específica
                                if (processedConfig.mobile.toolbar) {
                                    processedConfig.modules.toolbar = processedConfig.mobile.toolbar;
                                } else if (processedConfig.mobileToolbar) {
                                    processedConfig.modules.toolbar = processedConfig.mobileToolbar;
                                    delete processedConfig.mobileToolbar;
                                } else {
                                    processedConfig.modules.toolbar = essentialTools;
                                }
                            }
                        }

                        // Aplicar outras configurações móveis se existirem
                        if (processedConfig.mobile.theme) {
                            processedConfig.theme = processedConfig.mobile.theme;
                        }

                        if (processedConfig.mobile.placeholder) {
                            processedConfig.placeholder = processedConfig.mobile.placeholder;
                        }
                    }

                    // Remover configurações móveis do objeto principal para não interferir na inicialização do Quill
                    delete processedConfig.mobile;
                    delete processedConfig.mobileToolbar;

                    return processedConfig;
                }

                function initSpecificQuillEditor(editorId, config, initialContent) {
                    const element = document.getElementById(editorId);

                    if (!element) {
                        console.warn(`Elemento ${editorId} não encontrado`);
                        return;
                    }

                    // Idempotent re-init guard: if already initialized and editor exists, skip
                    if (window.quillInstances[editorId] && element.querySelector('.ql-editor')) {
                        return;
                    }

                    // Clean any previous children before a new init
                    if (window.quillInstances[editorId]) {
                        try {
                            window.quillInstances[editorId] = null;
                            delete window.quillInstances[editorId];
                        } catch {}
                    }

                    element.innerHTML = '';

                    try {
                        // Processando configurações avançadas
                        const processedConfig = processQuillConfig(config);

                        // Garantir que os formatos de lista estejam incluídos
                        if (!processedConfig.formats || !processedConfig.formats.includes('list')) {
                            processedConfig.formats = [...(processedConfig.formats || []), 'list', 'bullet', 'ordered'];
                        }

                        // Inicializando o editor com as configurações processadas
                        const quill = new Quill(`#${editorId}`, processedConfig);

                        window.quillInstances[editorId] = quill;

                        // Aplicando conteúdo inicial
                        if (initialContent && initialContent.trim().length > 0) {
                            setTimeout(() => {
                                if (initialContent.startsWith('<') || initialContent.includes('</')) {
                                    quill.root.innerHTML = initialContent;
                                } else {
                                    quill.setText(initialContent);
                                }
                            }, 100);
                        }


                        let timeout;
                        quill.on('text-change', function(delta, oldDelta, source) {
                            if (source === 'user') {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {

                                    const wireElement = element.closest('[wire\\:id]');
                                    if (wireElement && window.Livewire) {
                                        const component = window.Livewire.find(wireElement.getAttribute('wire:id'));
                                        if (component) {
                                            component.set('content', quill.root.innerHTML);
                                        }
                                    }
                                }, 300);
                            }

                            // Character Counter Update
                            updateCharacterCount(editorId, quill, config);
                        });

                        // Initial character count on load
                        setTimeout(() => updateCharacterCount(editorId, quill, config), 150);

                        // Auto-save Feature
                        if (config.autoSave?.enabled) {
                            const saveKey = config.autoSave.key || 'quill-draft-' + editorId;
                            const interval = config.autoSave.interval || 30000;
                            const showIndicator = config.autoSave.showIndicator !== false;

                            // Restore draft if exists and no initial content
                            const savedDraft = localStorage.getItem(saveKey);
                            if (savedDraft && (!initialContent || initialContent.trim().length === 0)) {
                                setTimeout(() => {
                                    quill.root.innerHTML = savedDraft;
                                    if (showIndicator) {
                                        showAutoSaveIndicator(editorId, 'Draft restaurado');
                                    }
                                }, 200);
                            }

                            // Set up auto-save interval
                            setInterval(() => {
                                const content = quill.root.innerHTML;
                                if (content.trim().length > 0 && content !== '<p><br></p>') {
                                    localStorage.setItem(saveKey, content);
                                    if (showIndicator) {
                                        showAutoSaveIndicator(editorId, 'Salvo automaticamente');
                                    }
                                }
                            }, interval);
                        }

                        // Full-screen Mode Feature
                        if (config.fullScreen?.enabled) {
                            const container = element.closest('.quill-editor-container');
                            if (container && !container.querySelector('.quill-fullscreen-btn')) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'quill-fullscreen-btn';
                                btn.title = 'Tela cheia (ESC para sair)';
                                btn.innerHTML = '⛶';
                                
                                btn.addEventListener('click', () => {
                                    const isFullscreen = container.classList.toggle('quill-fullscreen');
                                    btn.innerHTML = isFullscreen ? '✕' : '⛶';
                                    btn.title = isFullscreen ? 'Sair da tela cheia' : 'Tela cheia';
                                    
                                    // Focus editor after toggle
                                    quill.focus();
                                });
                                
                                container.appendChild(btn);
                                
                                // ESC key to exit fullscreen
                                document.addEventListener('keydown', (e) => {
                                    if (e.key === 'Escape' && container.classList.contains('quill-fullscreen')) {
                                        container.classList.remove('quill-fullscreen');
                                        btn.innerHTML = '⛶';
                                        btn.title = 'Tela cheia';
                                    }
                                });
                            }
                        }

                        // Image Upload Feature
                        if (config.imageUpload?.enabled) {
                            const imageInput = document.getElementById(editorId + '-image-upload');
                            
                            if (imageInput) {
                                // Override default image handler to use our file input
                                const toolbar = quill.getModule('toolbar');
                                if (toolbar) {
                                    toolbar.addHandler('image', () => {
                                        imageInput.click();
                                    });
                                }
                            }

                            // Initialize Image Resize Manager
                            new ImageResizeManager(quill, editorId);
                        }

                        console.log(`Quill ${editorId} inicializado com sucesso`);

                    } catch (error) {
                        console.error(`Erro ao inicializar Quill ${editorId}:`, error);
                    }
                }


                document.addEventListener('livewire:navigated', () => {

                    Object.keys(window.quillInstances || {}).forEach(editorId => {
                        const element = document.getElementById(editorId);
                        if (element && !element.querySelector('.ql-editor')) {

                            const config = element.dataset.quillConfig ? JSON.parse(element.dataset.quillConfig) :
                                {};
                            const content = element.dataset.initialContent || '';
                            initSpecificQuillEditor(editorId, config, content);
                        }
                    });
                });

                // Adicionar evento de redimensionamento para responsividade
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        // Reinicializar editores quando a janela for redimensionada
                        // para aplicar configurações responsivas
                        Object.keys(window.quillInstances || {}).forEach(editorId => {
                            const element = document.getElementById(editorId);
                            if (element) {
                                const config = element.dataset.quillConfig ? JSON.parse(element.dataset
                                    .quillConfig) : {};
                                const content = window.quillInstances[editorId].root.innerHTML;
                                initSpecificQuillEditor(editorId, config, content);
                            }
                        });
                    }, 300); // Debounce para evitar múltiplas reinicializações
                });


                document.addEventListener('livewire:before-dom-update', () => {

                    Object.keys(window.quillInstances || {}).forEach(editorId => {
                        if (!document.getElementById(editorId)) {
                            try {
                                if (window.quillInstances[editorId] && typeof window.quillInstances[editorId]
                                    .destroy === 'function') {
                                    window.quillInstances[editorId].destroy();
                                }
                                delete window.quillInstances[editorId];
                                console.log(`Cleanup do editor ${editorId} realizado`);
                            } catch (e) {
                                console.warn(`Erro no cleanup do editor ${editorId}:`, e);
                            }
                        }
                    });
                });
            </script>
        @endpush
    @endonce

    @push("scripts")
        <script>
            // Robust boot that waits for Quill and element presence
            (function() {
                const boot = () => initializeQuillEditor{{ str_replace("-", "_", $quillId) }}();
                const tryInit = () => {
                    const el = document.getElementById('{{ $quillId }}');
                    if (window.Quill && el) {
                        boot();
                        return true;
                    }
                    return false;
                };
                const start = () => {
                    if (tryInit()) return;
                    let attempts = 0;
                    const timer = setInterval(() => {
                        attempts++;
                        if (tryInit() || attempts > 50) clearInterval(timer);
                    }, 60);
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', start);
                } else {
                    start();
                }
                window.addEventListener('livewire:init', start, {
                    once: true
                });
            })();

            // Avoid aggressive re-inits that cause duplicate toolbars

            function initializeQuillEditor{{ str_replace("-", "_", $quillId) }}() {

                const editorConfig = @json($config);

                const editorId = '{{ $quillId }}';
                const initialContent = @json($content ?? "");


                const element = document.getElementById(editorId);
                if (element) {
                    element.dataset.quillConfig = JSON.stringify(editorConfig);
                    element.dataset.initialContent = initialContent || '';
                }

                initSpecificQuillEditor(editorId, editorConfig, initialContent);

                // Event listeners específicos para esta instância
                window.addEventListener('refresh-quill-{{ $quillId }}', function(e) {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        const content = e.detail?.content || '';
                        if (content.startsWith('<') || content.includes('</')) {
                            quill.root.innerHTML = content;
                        } else {
                            quill.setText(content);
                        }
                    }
                });

                window.addEventListener('update-content-quill-{{ $quillId }}', function(e) {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        const content = e.detail?.content || '';
                        if (content.startsWith('<') || content.includes('</')) {
                            quill.root.innerHTML = content;
                        } else {
                            quill.setText(content);
                        }
                    }
                });

                window.addEventListener('clear-quill-{{ $quillId }}', function() {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        quill.setText('');
                    }
                });

                window.addEventListener('toggle-quill-{{ $quillId }}', function(e) {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        quill.enable(!e.detail.disabled);
                    }
                });

                window.addEventListener('focus-quill-{{ $quillId }}', function() {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        quill.focus();
                    }
                });

                window.addEventListener('select-all-quill-{{ $quillId }}', function() {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill) {
                        quill.setSelection(0, quill.getLength());
                    }
                });

                // Auto-save Draft Event Listeners
                window.addEventListener('restore-draft-quill-{{ $quillId }}', function() {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    const config = @json($config);
                    const saveKey = config.autoSave?.key || 'quill-draft-{{ $quillId }}';
                    const savedDraft = localStorage.getItem(saveKey);
                    if (quill && savedDraft) {
                        quill.root.innerHTML = savedDraft;
                        showAutoSaveIndicator('{{ $quillId }}', 'Draft restaurado');
                    }
                });

                window.addEventListener('clear-draft-quill-{{ $quillId }}', function() {
                    const config = @json($config);
                    const saveKey = config.autoSave?.key || 'quill-draft-{{ $quillId }}';
                    localStorage.removeItem(saveKey);
                    showAutoSaveIndicator('{{ $quillId }}', 'Draft removido');
                });

                // Image Upload Event Listeners
                window.addEventListener('insert-image-quill-{{ $quillId }}', function(e) {
                    const quill = window.quillInstances['{{ $quillId }}'];
                    if (quill && e.detail?.url) {
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', e.detail.url);
                        quill.setSelection(range.index + 1);
                    }
                });

                window.addEventListener('image-error-quill-{{ $quillId }}', function(e) {
                    const message = e.detail?.message || 'Erro ao fazer upload da imagem.';
                    // Show error indicator similar to auto-save
                    const container = document.getElementById('{{ $quillId }}')?.closest('.quill-editor-container');
                    if (container) {
                        const indicator = document.createElement('div');
                        indicator.className = 'quill-autosave-indicator';
                        indicator.style.color = '#dc2626';
                        indicator.style.background = '#fef2f2';
                        indicator.innerHTML = `<span>✕</span> ${message}`;
                        container.appendChild(indicator);
                        setTimeout(() => {
                            indicator.classList.add('fade-out');
                            setTimeout(() => indicator.remove(), 300);
                        }, 3000);
                    }
                });
            }
        </script>
    @endpush
</div>
