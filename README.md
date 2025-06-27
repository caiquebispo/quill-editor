# Quill Editor Livewire Component

Livewire 3 component for rich text editing powered by [Quill.js](https://quilljs.com/).  
Supports editing with headings, bold, italic, lists, links, images, videos, alignment, colors, and more, fully integrated with Laravel and Livewire for seamless form and CMS workflows.

---
## Requirements

- PHP >=8.2
- Livewire >= 3
- Laravel >= 12

## Instalation

```bash
composer require caiquebispo/quill-editor
```
---
## Publishing assets and configuration

For publishing the configuration and assets, run the following commands:

```bash
php artisan vendor:publish --provider="CaiqueBispo\QuillEditor\QuillEditorServiceProvider" --tag=config

php artisan vendor:publish --provider="CaiqueBispo\QuillEditor\QuillEditorServiceProvider" --tag=assets
```

This will create the `config/quill.php` file and copy the necessary assets to your public directory.

---

## Usage

Create a Livewire component to use the Quill editor:

```bash
php artisan make:livewire EditorComponent
```

### 📁 `app/Livewire/EditorComponent.php`

```php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

class EditorComponent extends Component
{
   public $content = '';

    #[On('quillUpdated')]
    public function quillUpdated($content): void
    {
        $this->content = $content;
    }
    
    public function save(): void
    {
        dd($this->content);
    }

    public function render(): View
    {
        return view('livewire.editor-component');
    }
}
```

### `resources/views/livewire/editor-component.blade.php`

```blade
<div>
    <livewire:quill-editor wire:model.defer="content" />
</div>
```

---

## Personalization

You can customize the editor by passing a configuration array to the component.

```blade
<livewire:quill-editor
    wire:model.defer="content"
    :config="[
        'theme' => 'bubble',
        'placeholder' => 'Digite seu texto...',
        'height' => '400px',
        'modules' => [
            'toolbar' => [
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
            ]
        ]
    ]"
/>
```
---
## Additional Features

- Support for **image resize** module
- Export **plain text** with `getPlainText()`
- Word count with `getWordCount()`
- Ready for integration with forms or CMS
---

## 📃 Licença

MIT © [Caique Bispo](https://github.com/caiquebispo)
