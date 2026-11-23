<?php
namespace Biblioteca\Domain;

final class Exemplar {
    public const STATUS_VALIDOS = ['disponivel', 'emprestado', 'manutencao'];

    private ?int $id = null;
    private string $status;

    public function __construct(
        private int $livroId,
        private string $codigoPatrimonio,
        string $status = 'disponivel'
    ) {
        if ($livroId <= 0) throw new \InvalidArgumentException('Livro inválido');
        if (trim($codigoPatrimonio) === '') throw new \InvalidArgumentException('Código de patrimônio obrigatório');
        $this->alterarStatus($status);
    }

    public function id(): ?int { return $this->id; }

    public function atribuirId(int $id): void {
        if ($id <= 0 || ($this->id !== null && $this->id !== $id))
            throw new \LogicException('Identidade inválida');
        $this->id = $id;
    }

    public function livroId(): int { return $this->livroId; }
    public function codigoPatrimonio(): string { return $this->codigoPatrimonio; }
    public function status(): string { return $this->status; }

    public function alterarStatus(string $status): void {
        if (!in_array($status, self::STATUS_VALIDOS, true))
            throw new \InvalidArgumentException('Status inválido');
        $this->status = $status;
    }

    public function disponivel(): bool { return $this->status === 'disponivel'; }
}
