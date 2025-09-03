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
    | Pode ser em px, %, vh, etc.
    */
    'height' => '200px',

    /*
    |--------------------------------------------------------------------------
    | Largura do editor
    |--------------------------------------------------------------------------
    | Por padrão o editor é 100% responsivo, mas você pode definir uma largura fixa
    | Pode ser em px, %, vw, etc.
    */
    'width' => '100%',

    /*
    |--------------------------------------------------------------------------
    | Modo somente leitura
    |--------------------------------------------------------------------------
    | Define se o editor permite edição ou apenas visualização
    */
    'readOnly' => false,
    
    /*
    |--------------------------------------------------------------------------
    | Configurações para dispositivos móveis
    |--------------------------------------------------------------------------
    | Configurações específicas para quando o editor é visualizado em dispositivos móveis
    */
    'mobile' => [
        'simplifyToolbar' => true, // Simplifica a barra de ferramentas em dispositivos móveis
        'height' => '200px',        // Altura específica para dispositivos móveis
    ],

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
            ['link'],
            ['clean'],
        ],

        // Histórico de desfazer/refazer
        'history' => [
            'delay' => 2000,
            'maxStack' => 500,
            'userOnly' => true,
        ],

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
        'list', 'bullet', 'ordered',
        'indent',
        'direction', 'align',
        'link', 'image', 'video',
    ],
];
