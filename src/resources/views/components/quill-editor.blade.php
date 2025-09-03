<div>
    <div wire:ignore class="quill-editor-container">
        <div id="{{ $quillId }}" class="quill-editor" style="height: {{ $config['height'] ?? '200px' }}; width: {{ $config['width'] ?? '100%' }};"></div>
    </div>

    <style>
        .quill-editor-container {
            width: 100%;
            max-width: {{ $config['width'] ?? '100%' }};
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
        .ql-editor p, .ql-editor span, .ql-editor div, .ql-editor * {
            color: #333333 !important;
        }
        @media (max-width: 768px) {
            .quill-editor-container .quill-editor {
                height: {{ $config['mobile']['height'] ?? $config['height'] ?? '200px' }} !important;
            }
            .ql-toolbar button {
                padding: 3px 5px;
            }
            .ql-container {
                font-size: 14px;
            }
        }
    </style>

@once
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    @endpush
@endonce

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
        <script>

            window.quillInstances = window.quillInstances || {};
            
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
                                [{ 'header': 1 }, { 'header': 2 }],
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

                if (window.quillInstances[editorId]) {
                    try {
                        if (typeof window.quillInstances[editorId].destroy === 'function') {
                            window.quillInstances[editorId].destroy();
                        }
                    } catch (e) {
                        console.warn('Erro ao destruir instância anterior:', e);
                    }
                    delete window.quillInstances[editorId];
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
                    quill.on('text-change', function (delta, oldDelta, source) {
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
                    });

                    console.log(`Quill ${editorId} inicializado com sucesso`);

                } catch (error) {
                    console.error(`Erro ao inicializar Quill ${editorId}:`, error);
                }
            }


            document.addEventListener('livewire:navigated', () => {

                Object.keys(window.quillInstances || {}).forEach(editorId => {
                    const element = document.getElementById(editorId);
                    if (element && !element.querySelector('.ql-editor')) {

                        const config = element.dataset.quillConfig ? JSON.parse(element.dataset.quillConfig) : {};
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
                            const config = element.dataset.quillConfig ? JSON.parse(element.dataset.quillConfig) : {};
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
                            if (window.quillInstances[editorId] && typeof window.quillInstances[editorId].destroy === 'function') {
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeQuillEditor{{ str_replace('-', '_', $quillId) }}();
        });

        document.addEventListener('livewire:morph-added', function(event) {
            const editorElement = event.detail.el.querySelector('#{{ $quillId }}');
            if (editorElement) {
                console.log('Re-inicializando editor após morph: {{ $quillId }}');
                setTimeout(() => {
                    initializeQuillEditor{{ str_replace('-', '_', $quillId) }}();
                }, 100);
            }
        });

        document.addEventListener('livewire:update', function() {
            const element = document.getElementById('{{ $quillId }}');
            if (element && !element.querySelector('.ql-editor')) {
                console.log('Re-inicializando editor após update: {{ $quillId }}');
                setTimeout(() => {
                    initializeQuillEditor{{ str_replace('-', '_', $quillId) }}();
                }, 150);
            }
        });

        function initializeQuillEditor{{ str_replace('-', '_', $quillId) }}() {

            const editorConfig = {
                theme: '{{ $config['theme'] ?? 'snow' }}',
                placeholder: '{{ $config['placeholder'] ?? 'Digite aqui...' }}',
                readOnly: {{ ($config['readOnly'] ?? false) ? 'true' : 'false' }},
                modules: @json($config['modules'] ?? []),
                formats: @json($config['formats'] ?? [])
            };

            const editorId = '{{ $quillId }}';
            const initialContent = @json($content ?? '');


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
        }
    </script>
@endpush
</div>
