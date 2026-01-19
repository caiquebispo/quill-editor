<?php

namespace CaiqueBispo\QuillEditor;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class QuillEditor extends Component
{
    use WithFileUploads;

    public string $content = '';

    public string $quillId;

    /** @var TemporaryUploadedFile|null */
    public $uploadedImage;

    public array $config = [];

    public string $modelName = 'content';

    public bool $initialized = false;

    public function mount(
        string $content = '',
        array $config = [],
        string $modelName = 'content',
        ?string $quillId = null,
        ?array $modules = null,
        ?array $formats = null,
        ?string $theme = null,
        ?string $placeholder = null,
        ?string $height = null,
        ?bool $readOnly = null
    ): void {
        $this->content = $content;
        $this->modelName = $modelName;

        $this->quillId = $quillId ?? 'quill-editor-'.uniqid().'-'.Str::random(8);

        $defaultConfig = config('quill', []);

        // Merge user config with default config
        $mergedConfig = $this->deepMerge($defaultConfig, $config);

        // Apply individual parameter overrides if provided
        if ($modules !== null) {
            $mergedConfig['modules'] = $this->deepMerge($mergedConfig['modules'] ?? [], $modules);
        }

        if ($formats !== null) {
            $mergedConfig['formats'] = array_unique(array_merge($mergedConfig['formats'] ?? [], $formats));
        }

        if ($theme !== null) {
            $mergedConfig['theme'] = $theme;
        }

        if ($placeholder !== null) {
            $mergedConfig['placeholder'] = $placeholder;
        }

        if ($height !== null) {
            $mergedConfig['height'] = $height;
        }

        if ($readOnly !== null) {
            $mergedConfig['readOnly'] = $readOnly;
        }

        $this->config = $mergedConfig;
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

    public function updatedUploadedImage(): void
    {
        $imageConfig = $this->config['imageUpload'] ?? [];

        if (! ($imageConfig['enabled'] ?? false)) {
            return;
        }

        // Validate file
        $maxSize = ($imageConfig['maxSize'] ?? 2048) * 1024; // Convert KB to bytes
        $acceptedFormats = $imageConfig['acceptedFormats'] ?? ['jpeg', 'jpg', 'png', 'gif', 'webp'];

        if ($this->uploadedImage->getSize() > $maxSize) {
            $this->dispatch("image-error-quill-{$this->quillId}", message: 'Arquivo muito grande.');

            return;
        }

        $extension = strtolower($this->uploadedImage->getClientOriginalExtension());
        if (! in_array($extension, $acceptedFormats)) {
            $this->dispatch("image-error-quill-{$this->quillId}", message: 'Formato não suportado.');

            return;
        }

        // Store the file
        $disk = $imageConfig['disk'] ?? 'public';
        $path = $imageConfig['path'] ?? 'quill-images';

        $storedPath = $this->uploadedImage->store($path, $disk);

        // Generate proper public URL
        if ($disk === 'public') {
            $url = asset('storage/'.$storedPath);
        } else {
            $url = Storage::disk($disk)->url($storedPath);
        }

        // Dispatch event to insert image in editor
        $this->dispatch("insert-image-quill-{$this->quillId}", url: $url);

        // Clear the uploaded file
        $this->uploadedImage = null;
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

    #[On('restoreDraft')]
    public function restoreDraft(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("restore-draft-quill-{$this->quillId}");
        }
    }

    #[On('clearDraft')]
    public function clearDraft(?string $targetId = null): void
    {
        if ($targetId === null || $targetId === $this->quillId) {
            $this->dispatch("clear-draft-quill-{$this->quillId}");
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

    public function getCharacterCount(): int
    {
        return mb_strlen($this->getPlainText());
    }

    public function isOverLimit(): bool
    {
        $limit = $this->config['characterLimit'] ?? null;

        return $limit !== null && $this->getCharacterCount() > $limit;
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
