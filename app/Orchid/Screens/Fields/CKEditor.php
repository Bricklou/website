<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Fields;

use Orchid\Screen\Field;
use Orchid\Support\Facades\Dashboard;

/**
 * Class CKEditor
 *
 * @method CKEditor autofocus($value = true)
 * @method CKEditor disabled($value = true)
 * @method CKEditor form($value = true)
 * @method CKEditor formaction($value = true)
 * @method CKEditor formenctype($value = true)
 * @method CKEditor formmethod($value = true)
 * @method CKEditor formnovalidate($value = true)
 * @method CKEditor formtarget($value = true)
 * @method CKEditor name(string $value = null)
 * @method CKEditor placeholder(string $value = null)
 * @method CKEditor readonly($value = true)
 * @method CKEditor required(bool $value = true)
 * @method CKEditor tabindex($value = true)
 * @method CKEditor value($value = true)
 * @method CKEditor help(string $value = null)
 * @method CKEditor height($value = '300px')
 * @method CKEditor title(string $value = null)
 * @method CKEditor popover(string $value = null)
 * @method CKEditor toolbar(array $options)
 * @method CKEditor base64(bool $value = true)
 */
class CKEditor extends Field
{
    /**
     * @var string
     */
    protected $view = 'orchid.fields.ckeditor';

    /**
     * All attributes that are available to the field.
     *
     * @var array
     */
    protected $attributes = [
        'value' => null,
        'height' => '300px',
        'base64' => false,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'autocomplete',
        'disabled',
        'name',
        'placeholder',
        'tabindex',
        'height',
        'readonly',
        'required'
    ];
}
