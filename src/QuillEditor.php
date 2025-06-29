<?php

namespace CaiqueBispo\QuillEditor;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class QuillEditor extends Component
{
    public string $content = '';
    public string $quillId;
    public array $config = [];
    public string $modelName = 'content';
    public bool $initialized = false;

    public function mount(
        string $content = '',
        array  $config = [],
        string $modelName = 'content',
        ?string $quillId = null
    ): void {
        $this->content = $content;
        $this->modelName = $modelName;

        $this->quillId = $quillId ?? 'quill-editor-' . uniqid() . '-' . Str::random(8);

        $defaultConfig = config('quill', []);
        $this->config = $this->deepMerge($defaultConfig, $config);
    }
    public function hydrate(): void
    {
        $this->initialized = false;
    }
    public function updatedContent(string $value): void
    {
        $this->dispatch('quillUpdated', content: $value, editorId: $this->quillId);

        $this->skipRender = false;
    }
    #[On('refreshEditor')]
    public function refreshEditor(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("refresh-quill-{$this->quillId}", content: $this->content);
        }
    }
    #[On('clearEditor')]
    public function clearEditor(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->content = '';
            $this->dispatch("clear-quill-{$this->quillId}");
        }
    }
    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->refreshEditor();
    }
    #[On('toggleEditor')]
    public function toggleEditor(bool $disabled = false, ?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("toggle-quill-{$this->quillId}", disabled: $disabled);
        }
    }
    #[On('focusEditor')]
    public function focusEditor(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("focus-quill-{$this->quillId}");
        }
    }
    #[On('selectAllEditor')]
    public function selectAllEditor(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("select-all-quill-{$this->quillId}");
        }
    }
    public function getPlainText(): string
    {
        return trim(strip_tags($this->content));
    }
    public function getWordCount(): int
    {
        return str_word_count($this->getPlainText());
    }
    public function isEmpty(): bool
    {
        return $this->getPlainText() === '';
    }
    public function updateEditorContent(): void
    {
        $this->dispatch("update-content-quill-{$this->quillId}", content: $this->content);
    }
    public function reinitializeEditor(): void
    {
        $this->dispatch("reinitialize-quill-{$this->quillId}");
    }
    public function getEditorConfigProperty(): string
    {
        return json_encode($this->config);
    }
    public function render(): View
    {
        return view('quill-editor::components.quill-editor');
    }
    private function deepMerge(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->deepMerge($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
