<?php
namespace Biblioteca\Infrastructure;

use Biblioteca\Domain\Exemplar;

final class ExemplarRepository extends AbstractRepository {
    protected function tabela(): string { return 'exemplares'; }
    protected function colunas(): array { return ['livro_id','codigo_patrimonio','status']; }

    protected function hidratar(array $linha): Exemplar {
        $exemplar = new Exemplar((int)$linha['livro_id'], $linha['codigo_patrimonio'], $linha['status']);
        $exemplar->atribuirId((int)$linha['id']);
        return $exemplar;
    }

    protected function extrair(object $entidade): array {
        if (!$entidade instanceof Exemplar) throw new \InvalidArgumentException('Esperado Exemplar');
        return ['livro_id' => $entidade->livroId(),
            'codigo_patrimonio' => $entidade->codigoPatrimonio(),
            'status' => $entidade->status()];
    }

    protected function identidade(object $entidade): ?int {
        if (!$entidade instanceof Exemplar) throw new \InvalidArgumentException('Esperado Exemplar');
        return $entidade->id();
    }

    protected function definirIdentidade(object $entidade, int $id): void {
        if (!$entidade instanceof Exemplar) throw new \InvalidArgumentException('Esperado Exemplar');
        $entidade->atribuirId($id);
    }

    /** @return Exemplar[] */
    public function porLivro(int $livroId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM exemplares WHERE livro_id = :id ORDER BY id');
        $stmt->execute(['id' => $livroId]);
        return array_map(fn(array $r) => $this->hidratar($r), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function contarDisponiveis(int $livroId): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM exemplares WHERE livro_id = :id AND status = 'disponivel'");
        $stmt->execute(['id' => $livroId]);
        return (int)$stmt->fetchColumn();
    }
}
