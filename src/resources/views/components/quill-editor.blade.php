<div wire:ignore>
    <div id="{{ $quillId }}" style="height: {{ $config['height'] }};"></div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const element = document.getElementById('{{ $quillId }}');
            if (!element || element.classList.contains('ql-container')) return;

            const quill = new Quill('#{{ $quillId }}', {
                theme: '{{ $config['theme'] }}',
                placeholder: '{{ $config['placeholder'] }}',
                readOnly: {{ $config['readOnly'] ? 'true' : 'false' }},
                modules: @json($config['modules']),
                formats: @json($config['formats']),
            });

            @if($content)
                quill.root.innerHTML = @json($content);
            @endif

            quill.on('text-change', function(delta, oldDelta, source) {
                if (source === 'user') {
                    @this.set('{{ $modelName }}', quill.root.innerHTML);
                }
            });

            window.addEventListener('refresh-quill-{{ $quillId }}', e => {
                quill.root.innerHTML = e.detail?.content || '';
            });

            window.addEventListener('clear-quill-{{ $quillId }}', () => quill.setText(''));
            window.addEventListener('toggle-quill-{{ $quillId }}', e => quill.enable(!e.detail.disabled));
            window.addEventListener('focus-quill-{{ $quillId }}', () => quill.focus());
            window.addEventListener('select-all-quill-{{ $quillId }}', () => quill.setSelection(0, quill.getLength()));
        });
    </script>
@endpush
