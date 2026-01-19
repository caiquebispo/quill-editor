## Livewire Quill Editor - Notes

<p align="center">
  <a href="https://packagist.org/packages/caiquebispo/quill-editor"><img src="http://poser.pugx.org/caiquebispo/quill-editor/v" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/caiquebispo/quill-editor"><img src="http://poser.pugx.org/caiquebispo/quill-editor/downloads" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/caiquebispo/quill-editor"><img src="http://poser.pugx.org/caiquebispo/quill-editor/v/unstable" alt="Latest Unstable Version"></a>
  <a href="https://packagist.org/packages/caiquebispo/quill-editor"><img src="http://poser.pugx.org/caiquebispo/quill-editor/license" alt="License"></a>
  <a href="https://packagist.org/packages/caiquebispo/quill-editor"><img src="http://poser.pugx.org/caiquebispo/quill-editor/require/php" alt="PHP Version Require"></a>
</p>

Fixes included:
- Idempotent initialization to prevent duplicate toolbars when Livewire morphs/updates
- Uses `wire:ignore.self` to scope DOM changes
- Responsive/mobile config processing with optional simplified toolbar

Basic usage:
```blade
<x-quill-editor :config="[
  'height' => '240px',
  'modules' => ['toolbar' => [['bold','italic','underline'], [{'header':1},{'header':2}], ['link']]],
]" />
```
# Quill Editor Livewire Component

Livewire component for rich text editing powered by [Quill.js](https://quilljs.com/).  
Supports editing with headings, bold, italic, lists, links, images, videos, alignment, colors, and more, fully integrated with Laravel and Livewire for seamless form and CMS workflows.

**Core Features:**
- ✅ **Multiple instances** support on the same page
- ✅ **Pre-loaded content** from database for editing
- ✅ **Improved event handling** for better reliability
- ✅ **Enhanced cleanup** and memory management
- ✅ **Fully responsive** design with mobile-specific configurations
- ✅ **Dynamic configuration** via array with support for all Quill.js options
- ✅ **Mobile-optimized** toolbar and interface
- ✅ **Customizable publishing** of configuration

**New Features (v1.2+):**
- 📊 **Character Counter** - Real-time character counting with optional limit
- 💾 **Auto-save** - Automatic draft saving to localStorage
- ⛶ **Full-screen Mode** - Expand editor to full screen (ESC to exit)
- 🖼️ **Image Upload** - Upload images directly from editor with Livewire
- 📐 **Image Resize** - Resize images with drag handles and preset buttons

---

## Requirements

- PHP >=8.2
- Livewire >= ^3.5.0|^4.0.0

## Installation

```bash
composer require caiquebispo/quill-editor
```

In your main layout file (for example, `resources/views/layouts/app.blade.php`), include:
```blade
<head>
    ...
    @stack('styles')
</head>
<body>
    ...
    @stack('scripts')
</body>
```

---

## Publishing assets and configuration

For publishing the configuration and assets, run the following commands:

```bash
php artisan vendor:publish --provider="CaiqueBispo\QuillEditor\Provider\QuillEditorServiceProvider" --tag=config

php artisan vendor:publish --provider="CaiqueBispo\QuillEditor\Provider\QuillEditorServiceProvider" --tag=assets
```

This will create the `config/quill.php` file and copy the necessary assets to your public directory.

### Configuration Options

The published configuration file (`config/quill.php`) includes the following options:

```php
return [
    // Theme: 'snow' or 'bubble'
    'theme' => 'snow',
    
    // Placeholder text
    'placeholder' => 'Digite seu texto aqui...',
    
    // Editor dimensions
    'height' => '300px',
    'width' => '100%',
    
    // Read-only mode
    'readOnly' => false,
    
    // Mobile-specific configurations
    'mobile' => [
        'simplifyToolbar' => true,
        'height' => '200px',
    ],
    
    // Quill.js modules configuration
    'modules' => [...],
    
    // Allowed formats
    'formats' => [...],
];
```

---

## New Features Configuration

### 📊 Character Counter

Display a real-time character count with optional limit:

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="[
        'showCharacterCount' => true,  // Enable counter
        'characterLimit' => 500,       // Optional: set max characters (null = no limit)
    ]" 
/>
```

When the limit is exceeded, the counter turns red to alert the user.

---

### 💾 Auto-save (Draft)

Automatically save content to localStorage at regular intervals:

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="[
        'autoSave' => [
            'enabled' => true,           // Enable auto-save
            'interval' => 30000,         // Save every 30 seconds (in ms)
            'showIndicator' => true,     // Show "Saved" indicator
            'key' => 'my-custom-key',    // Optional: custom localStorage key
        ],
    ]" 
/>
```

**Programmatic Draft Control:**
```php
// In your Livewire component
public function restoreDraft(): void
{
    $this->dispatch('restoreDraft');
}

public function clearDraft(): void
{
    $this->dispatch('clearDraft');
}
```

---

### ⛶ Full-screen Mode

Enable a full-screen button to expand the editor:

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="[
        'fullScreen' => [
            'enabled' => true,
        ],
    ]" 
/>
```

- Click the **⛶** button (top-right) to enter full-screen
- Press **ESC** to exit full-screen mode

---

### 🖼️ Image Upload

Enable direct image uploads with Livewire file handling:

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="[
        'imageUpload' => [
            'enabled' => true,
            'maxSize' => 2048,                                    // Max size in KB (2MB)
            'acceptedFormats' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],
            'disk' => 'public',                                   // Storage disk
            'path' => 'quill-images',                             // Storage path
        ],
    ]" 
/>
```

**Requirements:**
1. Run `php artisan storage:link` to create the public storage symlink
2. Ensure your storage disk is properly configured

**How it works:**
1. Click the image button 🖼️ in the toolbar
2. Select an image from your computer
3. The image is uploaded via Livewire and inserted into the editor

---

### 📐 Image Resize

When Image Upload is enabled, images can be resized interactively:

**How to use:**
1. Click on any image in the editor to select it
2. Use the **corner handles** to drag and resize (maintains aspect ratio)
3. Use the **preset buttons** (25%, 50%, 75%, 100%) for quick sizing
4. Click outside the image to deselect

---

### Complete Example (All Features)

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="[
        'height' => '400px',
        'placeholder' => 'Start writing...',
        
        // Character Counter
        'showCharacterCount' => true,
        'characterLimit' => 1000,
        
        // Auto-save
        'autoSave' => [
            'enabled' => true,
            'interval' => 15000,
            'showIndicator' => true,
        ],
        
        // Full-screen
        'fullScreen' => [
            'enabled' => true,
        ],
        
        // Image Upload + Resize
        'imageUpload' => [
            'enabled' => true,
            'maxSize' => 2048,
            'acceptedFormats' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],
        ],
    ]" 
/>
```

---

## Usage

### Basic Usage

Create a Livewire component to use the Quill editor:

```bash
php artisan make:livewire EditorComponent
```

### Advanced Usage

#### Dynamic Configuration

You can pass configuration options directly to the component:

```php
<livewire:quill-editor 
    :content="$meuConteudo" 
    :config="[
        'theme' => 'bubble',
        'height' => '400px',
        'placeholder' => 'Escreva aqui...',
        'modules' => [
            'toolbar' => [
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
            ]
        ]
    ]" 
/>
```

#### Individual Configuration Parameters

You can also pass individual configuration parameters:

```php
<livewire:quill-editor 
    :content="$meuConteudo" 
    :theme="'bubble'" 
    :height="'400px'" 
    :placeholder="'Escreva aqui...'" 
    :read-only="false" 
/>
```

#### Mobile-Specific Configuration

The component supports mobile-specific configurations:

```php
<livewire:quill-editor 
    :content="$meuConteudo" 
    :config="[
        'theme' => 'snow',
        'height' => '400px',
        'mobile' => [
            'height' => '200px',
            'simplifyToolbar' => true,
            'toolbar' => [
                ['bold', 'italic'],
                ['link']
            ]
        ]
    ]" 
/>
```

#### 📁 `app/Livewire/EditorComponent.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

class EditorComponent extends Component
{
    public string $content = '';

    #[On('quillUpdated')]
    public function quillUpdated(string $content, string $editorId): void
    {
        $this->content = $content;
    }
    
    public function save(): void
    {
        // Save your content to database
        // Example: Post::create(['content' => $this->content]);
        
        session()->flash('message', 'Content saved successfully!');
    }

    public function render(): View
    {
        return view('livewire.editor-component');
    }
}
```

#### `resources/views/livewire/editor-component.blade.php`

```blade
<div>
    <livewire:quill-editor :content="$content" />
    
    <div class="mt-4">
        <button wire:click="save" class="btn btn-primary">
            Save Content
        </button>
    </div>
    
    @if (session()->has('message'))
        <div class="alert alert-success mt-2">
            {{ session('message') }}
        </div>
    @endif
</div>
```

### Loading Existing Content

To load existing content from your database:

```php
<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\On;

class PostEditor extends Component
{
    public Post $post;
    public string $content = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->content = $post->content ?? '';
    }

    #[On('quillUpdated')]
    public function quillUpdated(string $content, string $editorId): void
    {
        $this->content = $content;
    }

    public function save(): void
    {
        $this->post->update([
            'content' => $this->content
        ]);
        
        session()->flash('message', 'Post updated successfully!');
    }

    public function render()
    {
        return view('livewire.post-editor');
    }
}
```

```blade
<div>
    <h2>Editing: {{ $post->title }}</h2>
    
    <livewire:quill-editor 
        :content="$content" 
        :config="['height' => '400px']" />
    
    <button wire:click="save" class="btn btn-success mt-3">
        Update Post
    </button>
</div>
```

### Multiple Instances

You can now use multiple Quill editors on the same page:

```blade
<div class="grid grid-cols-2 gap-6">
    {{-- Main Content Editor --}}
    <div>
        <h3>Main Content</h3>
        <livewire:quill-editor 
            :content="$post->content" 
            :config="['height' => '300px', 'placeholder' => 'Write your main content...']" />
    </div>
    
    {{-- Excerpt Editor --}}
    <div>
        <h3>Excerpt</h3>
        <livewire:quill-editor 
            :content="$post->excerpt" 
            :config="['height' => '150px', 'placeholder' => 'Write a short excerpt...']" />
    </div>
</div>
```

---

## Configuration

You can customize the editor by passing a configuration array to the component:

```blade
<livewire:quill-editor
    :content="$content"
    :config="[
        'theme' => 'bubble',
        'placeholder' => 'Digite seu texto...',
        'height' => '400px',
        'readOnly' => false,
        'modules' => [
            'toolbar' => [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [['header' => 1], ['header' => 2]],
                [['list' => 'ordered'], ['list' => 'bullet']],
                ['link', 'image', 'video'],
                ['clean']
            ]
        ],
        'formats' => [
            'bold', 'italic', 'underline', 'strike',
            'blockquote', 'code-block', 'header',
            'list', 'bullet', 'link', 'image', 'video'
        ]
    ]"
/>
```

### Available Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `theme` | string | `'snow'` | Quill theme (`'snow'` or `'bubble'`) |
| `placeholder` | string | `'Digite aqui...'` | Placeholder text |
| `height` | string | `'200px'` | Editor height |
| `readOnly` | boolean | `false` | Read-only mode |
| `modules` | array | `[]` | Quill modules configuration |
| `formats` | array | `[]` | Allowed formats |

---

## Component Methods

The Quill Editor component provides several useful methods:

### Programmatic Control

```php
// In your Livewire component
public function clearEditor(): void
{
    $this->dispatch('clearEditor');
}

public function focusEditor(): void
{
    $this->dispatch('focusEditor');
}

public function toggleEditor(bool $disabled = true): void
{
    $this->dispatch('toggleEditor', disabled: $disabled);
}

public function selectAllEditor(): void
{
    $this->dispatch('selectAllEditor');
}
```

### Content Methods

```php
// Access the editor component directly
$editor = $this->getChild('quill-editor');

// Get plain text (without HTML tags)
$plainText = $editor->getPlainText();

// Get word count
$wordCount = $editor->getWordCount();

// Check if editor is empty
$isEmpty = $editor->isEmpty();

// Set content programmatically
$editor->setContent('<p>New content here</p>');
```

---

## Events

The component dispatches the following events:

### `quillUpdated`
Fired when the editor content changes:

```php
#[On('quillUpdated')]
public function handleContentUpdate(string $content, string $editorId): void
{
    // Handle the updated content
    $this->content = $content;
}
```

---

## Advanced Usage

### Custom Toolbar Configuration

```blade
<livewire:quill-editor
    :content="$content"
    :config="[
        'modules' => [
            'toolbar' => [
                [['size' => ['small', false, 'large', 'huge']]],
                [['header' => [1, 2, 3, 4, 5, 6, false]]],
                ['bold', 'italic', 'underline', 'strike'],
                [['color' => []], ['background' => []]],
                [['script' => 'sub'], ['script' => 'super']],
                [['header' => 1], ['header' => 2], 'blockquote', 'code-block'],
                [['list' => 'ordered'], ['list' => 'bullet'], ['indent' => '-1'], ['indent' => '+1']],
                [['direction' => 'rtl'], ['align' => []]],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ]
        ]
    ]"
/>
```

### Image Upload Integration

```php
// In your Livewire component
public function uploadImage($image)
{
    $path = $image->store('images', 'public');
    $url = Storage::url($path);
    
    // Inject image into editor
    $this->dispatch('insertImage', url: $url);
}
```

---

## Troubleshooting

### Common Issues

1. **Multiple instances not working**: Ensure each instance has a unique ID
2. **Content not loading**: Check that the content is properly passed to the `:content` attribute
3. **Styles not loading**: Make sure `@stack('styles')` is in your layout's `<head>`
4. **Scripts not working**: Ensure `@stack('scripts')` is before the closing `</body>` tag

### Debug Mode

Enable debug mode to see console logs:

```blade
<livewire:quill-editor 
    :content="$content" 
    :config="['debug' => true]" />
```

---

## License

MIT © [Caique Bispo](https://github.com/caiquebispo)
