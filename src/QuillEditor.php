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

    public function mount(
        string $content = '',
        array  $config = [],
        string $modelName = 'content'
    ): void {
        $this->content    = $content;
        $this->modelName  = $modelName;
        $this->quillId    = 'quill-editor-' . Str::uuid()->toString();

        $defaultConfig = config('quill', []);

        $this->config = $this->deepMerge($defaultConfig, $config);
    }
    public function updatedContent(string $value): void
    {
        $this->dispatch('quillUpdated', content: $value);
    }

    #[On('refreshEditor')]
    public function refreshEditor(): void
    {
        $this->dispatch("refresh-quill-{$this->quillId}", content: $this->content);
    }

    #[On('clearEditor')]
    public function clearEditor(): void
    {
        $this->content = '';
        $this->dispatch("clear-quill-{$this->quillId}");
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->refreshEditor();
    }

    #[On('toggleEditor')]
    public function toggleEditor(bool $disabled = false): void
    {
        $this->dispatch("toggle-quill-{$this->quillId}", disabled: $disabled);
    }

    #[On('focusEditor')]
    public function focusEditor(): void
    {
        $this->dispatch("focus-quill-{$this->quillId}");
    }

    #[On('selectAllEditor')]
    public function selectAllEditor(): void
    {
        $this->dispatch("select-all-quill-{$this->quillId}");
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
