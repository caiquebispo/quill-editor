<div wire:ignore>
    <div id="{{ $quillId }}" style="height: {{ $config['height'] }};"></div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
    <script>

        document.addEventListener('livewire:navigated', () => initQuillEditor());
        document.addEventListener('DOMContentLoaded', () => initQuillEditor());
        document.addEventListener('open-editor-modal', () => { setTimeout(() => initQuillEditor(), 50)});

        function initQuillEditor() {
            const element = document.getElementById('{{ $quillId }}');
            if (!element || element.querySelector('.ql-editor')) {
                return;
            }

            const quill = new Quill('#{{ $quillId }}', {
                theme: '{{ $config['theme'] }}',
                placeholder: '{{ $config['placeholder'] }}',
                readOnly: {{ $config['readOnly'] ? 'true' : 'false' }},
                modules: @json($config['modules']),
                formats: @json($config['formats']),
            });

            const initialContent = @json($content);
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            // Atualiza Livewire
            let timeout;
            quill.on('text-change', function (delta, oldDelta, source) {
                if (source === 'user') {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                    @this.set('{{ $modelName }}', quill.root.innerHTML);
                    }, 300);
                }
            });
            window.addEventListener('clear-quill-{{ $quillId }}', () => quill.setText(''));
            window.addEventListener('toggle-quill-{{ $quillId }}', e => quill.enable(!e.detail.disabled));
            window.addEventListener('focus-quill-{{ $quillId }}', () => quill.focus());
            window.addEventListener('select-all-quill-{{ $quillId }}', () => quill.setSelection(0, quill.getLength()));
        }
    </script>

@endpush
