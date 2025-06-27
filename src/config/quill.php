<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tema visual do editor
    |--------------------------------------------------------------------------
    | Opções disponíveis:
    | - snow: tema padrão com barra de ferramentas clara
    | - bubble: tema minimalista que mostra ferramentas em tooltip
    */
    'theme' => 'snow',

    /*
    |--------------------------------------------------------------------------
    | Placeholder exibido quando o campo está vazio
    |--------------------------------------------------------------------------
    */
    'placeholder' => 'Digite seu texto aqui...',

    /*
    |--------------------------------------------------------------------------
    | Altura do editor
    |--------------------------------------------------------------------------
    | Este valor será aplicado diretamente no estilo do container
    */
    'height' => '300px',

    /*
    |--------------------------------------------------------------------------
    | Modo somente leitura
    |--------------------------------------------------------------------------
    */
    'readOnly' => false,

    /*
    |--------------------------------------------------------------------------
    | Módulos do Quill.js
    |--------------------------------------------------------------------------
    | Define os módulos ativos e suas opções.
    | Mais informações: https://quilljs.com/docs/modules/
    */
    'modules' => [

        'toolbar' => [
            [
                ['font' => []],
                ['size' => ['small', false, 'large', 'huge']],
            ],
            [
                'bold', 'italic', 'underline', 'strike',
            ],
            [
                ['color' => []], ['background' => []],
            ],
            [
                ['script' => 'sub'], ['script' => 'super'],
            ],
            [
                'blockquote', 'code-block',
            ],
            [
                ['header' => 1], ['header' => 2], ['header' => 3], ['header' => 4], ['header' => 5], ['header' => 6], ['header' => false],
            ],
            [
                ['list' => 'ordered'], ['list' => 'bullet'],
                ['indent' => '-1'], ['indent' => '+1'],
            ],
            [
                ['direction' => 'rtl'],
                ['align' => []],
            ],
            ['link', 'image', 'video'],
            ['clean'],
        ],

        // Histórico de desfazer/refazer
        'history' => [
            'delay' => 2000,
            'maxStack' => 500,
            'userOnly' => true,
        ],

        // Permite redimensionamento de imagens (requer quill-image-resize-module)
        'imageResize' => true,

        // Configurações do clipboard
        'clipboard' => [
            'matchVisual' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formatos permitidos
    |--------------------------------------------------------------------------
    | Define quais formatações o editor aceitará e aplicará.
    | Docs: https://quilljs.com/docs/formats/
    */
    'formats' => [
        'header', 'font', 'size',
        'bold', 'italic', 'underline', 'strike',
        'color', 'background',
        'script',
        'blockquote', 'code-block',
        'list', 'bullet', 'indent',
        'direction', 'align',
        'link', 'image', 'video',
        'clean',
    ],
];
