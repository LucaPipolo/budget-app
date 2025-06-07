<?php

declare(strict_types=1);

namespace App\Filament\Custom\Components;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class SelectEnum extends Select
{
    /**
     * The enum class to be used for the select options.
     */
    protected string $enum;

    public static function make(string $name): static
    {
        return parent::make($name)
            ->options(null);
    }

    /**
     * Set the options for the select component based on the enum.
     *
     * @param  array|Arrayable|string|Closure|null  $options  The options for the select component.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function options(array|Arrayable|string|Closure|null $options): static
    {
        return parent::options(function (): array {
            return [
                ...collect(Str::studly($this->enum)::cases())->mapWithKeys(
                    fn (object $status) => [$status->value => $status->getLabel()]
                ),
            ];
        });
    }

    /**
     * Set the enum class for the select component.
     *
     * @param  Closure|string  $enum  The enum class.
     */
    public function enum(Closure|string $enum): static
    {
        $this->enum = $enum;

        return $this;
    }
}
