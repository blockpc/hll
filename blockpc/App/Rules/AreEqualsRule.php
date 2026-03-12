<?php

declare(strict_types=1);

namespace Blockpc\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class AreEqualsRule implements ValidationRule
{
    public function __construct(
        protected ?string $text,
        protected string $message = 'The :attribute is not equal to value.'
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($this->text)) {
            $fail('The text to compare is null.');

            return;
        }

        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        if (strcmp($this->text, $value) !== 0) {
            $fail($this->message);
        }
    }
}
