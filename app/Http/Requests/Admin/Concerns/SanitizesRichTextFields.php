<?php

namespace App\Http\Requests\Admin\Concerns;

use Mews\Purifier\Facades\Purifier;

/**
 * Strips any HTML outside the Quill editor's own toolbar (see
 * resources/js/app.js) from rich-text fields before validation. These
 * fields render unescaped ({!! !!}) on the public frontend, so raw admin
 * input must never reach the database unpurified - see config/purifier.php's
 * "cms" profile for the exact allowlist.
 */
trait SanitizesRichTextFields
{
    /**
     * @param  array<int, string>  $fields
     */
    protected function sanitizeRichTextFields(array $fields): void
    {
        $merge = [];

        foreach ($fields as $field) {
            if ($this->filled($field)) {
                $merge[$field] = Purifier::clean($this->input($field), 'cms');
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
