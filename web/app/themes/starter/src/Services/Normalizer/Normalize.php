<?php

namespace MyNamespace\Services\Normalizer;

use NumberFormatter;

class Normalize
{
    protected $currencyFormatter;

    public function unit(string $unitId, int $count = 1): string
    {
        return match ($unitId) {
            'h' => _n('heure', 'heures', $count),
            'd' => _n('jour', 'jours', $count),
            'm' => 'mois',
            'y' => _n('année', 'années', $count),
        };
    }

    public function takeDesc(string $text, int $length)
    {
        if ($length >= \strlen($text)) {
            return $text;
        }

        return \preg_replace(
            "/^(.{1,$length})(\s.*|$)/s",
            '\\1...',
            $text
        );
    }

    public function formRemoveCrlChars($value): string
    {
        return \preg_replace('/[\x{0000}-\x{0008}\x{0011}-\x{0012}]/u', '', $value);
    }

    public function formNormalizeSpaces($value): string
    {
        return \preg_replace('/[\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}\x{FEFF}\x{00A0}\x{1680}\x{180E}]/u', ' ', $value);
    }

    public function formNormalizeText($value, $character_mask = " \t\n\r\0\x0B"): string
    {
        $value = \sanitize_text_field($value);
        $value = $this->formRemoveCrlChars($value);
        $value = $this->formNormalizeSpaces($value);
        $value = \str_replace(' ', '', $value);
        $value = \trim($value, $character_mask);
        return $value;
    }

    public function phoneRaw($phone): string
    {
        $phone = $this->formNormalizeText($phone);

        if (\str_starts_with($phone, '0')) {
            $phone = \substr($phone, 1);
        } elseif (\str_starts_with($phone, '33')) {
            $phone = \substr($phone, 2);
        } elseif (\str_starts_with($phone, '+33')) {
            $phone = \substr($phone, 3);
        }

        return $phone;
    }

    public function phone($phone, $human = true)
    {
        $phone = $this->phoneRaw($phone);

        if ($human) {
            $phone = '0' . $phone;
            return chunk_split($phone, 2, '&nbsp;');
        } else {
            return '+33' . $phone;
        }
    }

    public function currency(null|int|float $input): string
    {
        if (!isset($input)) {
            return '';
        }

        if (!isset($this->currencyFormatter)) {
            $this->currencyFormatter = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
        }
        return $this->currencyFormatter->formatCurrency($input, "EUR");
    }
}
