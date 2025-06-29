<div wire:ignore>
    <div id="{{ $quillId }}" style="height: {{ $config['height'] ?? '200px' }};"></div>
</div>

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

            function initSpecificQuillEditor(editorId, config, initialContent) {
                const element = document.getElementById(editorId);

                if (!element) {
                    console.warn(`Elemento ${editorId} não encontrado`);
                    return;
                }

                if (window.quillInstances[editorId]) {
                    try {
                        window.quillInstances[editorId].destroy?.();
                    } catch (e) {
                        console.warn('Erro ao destruir instância anterior:', e);
                    }
                    delete window.quillInstances[editorId];
                }

                element.innerHTML = '';

                try {

                    const quill = new Quill(`#${editorId}`, config);

                    window.quillInstances[editorId] = quill;

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

                                const component = window.Livewire.find(element.closest('[wire\\:id]')?.getAttribute('wire:id'));
                                if (component) {
                                    component.set('content', quill.root.innerHTML);
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

            document.addEventListener('livewire:before-dom-update', () => {

                Object.keys(window.quillInstances || {}).forEach(editorId => {
                    if (!document.getElementById(editorId)) {
                        try {
                            window.quillInstances[editorId]?.destroy?.();
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

            const editorConfig = {
                theme: '{{ $config['theme'] ?? 'snow' }}',
                placeholder: '{{ $config['placeholder'] ?? 'Digite aqui...' }}',
                readOnly: {{ ($config['readOnly'] ?? false) ? 'true' : 'false' }},
                modules: @json($config['modules'] ?? []),
                formats: @json($config['formats'] ?? []),
            };

            const editorId = '{{ $quillId }}';
            const initialContent = @json($content);

            const element = document.getElementById(editorId);
            if (element) {
                element.dataset.quillConfig = JSON.stringify(editorConfig);
                element.dataset.initialContent = initialContent || '';
            }

            initSpecificQuillEditor(editorId, editorConfig, initialContent);

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
        });
    </script>
@endpush
