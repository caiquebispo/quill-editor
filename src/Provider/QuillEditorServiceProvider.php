<?php

namespace CaiqueBispo\QuillEditor\Provider;

use CaiqueBispo\QuillEditor\QuillEditor;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;


class QuillEditorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'quill-editor');

        $this->publishes([
            __DIR__ . '/../config/quill.php' => config_path('quill.php'),
        ], 'config', true);

        $this->publishes([
            __DIR__.'/../resources/js' => public_path('vendor/quill-editor'),
        ], 'assets', true);

        Livewire::component('quill-editor', QuillEditor::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/quill.php', 'quill'
        );
    }
}
