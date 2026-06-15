<?php

namespace App\Enums;

/**
 * Status possíveis de uma viagem — fonte única dos valores válidos.
 */
enum TripStatus: string
{
    case Agendada = 'agendada';
    case EmAndamento = 'em_andamento';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    /**
     * Rótulo amigável (PT-BR) para exibição.
     */
    public function label(): string
    {
        return match ($this) {
            self::Agendada => 'Agendada',
            self::EmAndamento => 'Em andamento',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }

    /**
     * Variante do componente x-badge para cada status.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Agendada => 'info',
            self::EmAndamento => 'warning',
            self::Concluida => 'success',
            self::Cancelada => 'danger',
        };
    }

    /**
     * Opções [valor => rótulo] para selects.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
