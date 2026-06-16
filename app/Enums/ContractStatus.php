<?php

namespace App\Enums;

/**
 * Status possíveis de um contrato — fonte única dos valores válidos.
 */
enum ContractStatus: string
{
    case Rascunho = 'rascunho';
    case Ativo = 'ativo';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';

    /**
     * Rótulo amigável (PT-BR) para exibição.
     */
    public function label(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::Ativo => 'Ativo',
            self::Concluido => 'Concluído',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * Variante do componente x-badge para cada status.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Rascunho => 'neutral',
            self::Ativo => 'info',
            self::Concluido => 'success',
            self::Cancelado => 'danger',
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
