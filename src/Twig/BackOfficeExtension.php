<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackOfficeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_cell_value', [$this, 'getCellValue']),
        ];
    }

    public function getCellValue(object $entity, string $field): string
    {
        // Gère la notation pointée : "utilisateur.prenom"
        $parts = explode('.', $field);
        $current = $entity;

        foreach ($parts as $part) {
            if ($current === null) {
                return '-';
            }

            // Essaie getXxx(), isXxx(), puis accès direct à la propriété
            $getter = 'get' . ucfirst($part);
            $isser = 'is' . ucfirst($part);
            $hasser = 'has' . ucfirst($part);

            if (method_exists($current, $getter)) {
                $current = $current->$getter();
            } elseif (method_exists($current, $isser)) {
                $current = $current->$isser();
            } elseif (method_exists($current, $hasser)) {
                $current = $current->$hasser();
            } elseif (property_exists($current, $part)) {
                $current = $current->$part;
            } else {
                return '-';
            }
        }

        return $this->formatValue($current);
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        if ($value instanceof \UnitEnum) {
            return match (true) {
                method_exists($value, 'value') => (string) $value->value,
                method_exists($value, 'name')  => (string) $value->name,
                default                        => (string) $value,
            };
        }

        if (is_bool($value)) {
            return $value ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>';
        }

        if (is_float($value)) {
            return number_format($value, 2, ',', ' ') . ' €';
        }

        if (is_array($value)) {
            $roles = array_filter($value, fn($r) => $r !== 'ROLE_USER');
            if (!empty($roles)) {
                return implode(' ', array_map(fn($r) => '<span class="badge badge-role bg-primary">' . str_replace('ROLE_', '', $r) . '</span>', $roles));
            }
            return '-';
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
            return '#' . ($value->getId() ?? '-');
        }

        return (string) $value;
    }
}
