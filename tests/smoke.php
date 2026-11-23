<?php
// Autoloader simples (o artigo sugere require_once ou spl_autoload_register)
spl_autoload_register(function (string $classe) {
    $prefixos = [
        'Biblioteca\\Domain\\'         => __DIR__ . '/../src/Domain/',
        'Biblioteca\\Infrastructure\\' => __DIR__ . '/../src/Infrastructure/',
    ];
    foreach ($prefixos as $prefixo => $dir) {
        if (str_starts_with($classe, $prefixo)) {
            $arquivo = $dir . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
            if (is_file($arquivo)) require_once $arquivo;
        }
    }
});

// Exemplo de uso após carregar as classes
$pdo = \Biblioteca\Infrastructure\Conexao::abrir();
$livros = new \Biblioteca\Infrastructure\LivroRepository($pdo);
$autores = new \Biblioteca\Infrastructure\AutorRepository($pdo);

$autor = new \Biblioteca\Domain\Autor('Autora Exemplo', 'Brasil');
$autores->salvar($autor);

$livro = new \Biblioteca\Domain\Livro('POO e Banco de Dados', null, 2026);
$livros->salvar($livro);
$livros->associarAutor($livro->id(), $autor->id());

$recuperado = $livros->buscar($livro->id());
foreach ($livros->autoresDoLivro($recuperado->id()) as $a) $recuperado->adicionarAutor($a);
$recuperado->alterarTitulo('POO, PDO e MySQL');
$livros->salvar($recuperado);

// Transação: livro + associação de forma atômica
$pdo->beginTransaction();
try {
    $novo = new \Biblioteca\Domain\Livro('Título transacional');
    $livros->salvar($novo);
    $livros->associarAutor($novo->id(), $autor->id());
    $pdo->commit();
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
}

// Exemplares: entidade + repositório (status validado no domínio)
$exemplares = new \Biblioteca\Infrastructure\ExemplarRepository($pdo);
$copia = new \Biblioteca\Domain\Exemplar($livro->id(), 'PAT-' . uniqid());
$exemplares->salvar($copia);
$copia->alterarStatus('emprestado');
$exemplares->salvar($copia);
assert($exemplares->buscar($copia->id())->status() === 'emprestado');
assert(count($exemplares->porLivro($livro->id())) >= 1);
assert($exemplares->contarDisponiveis($livro->id()) === 0);

echo "OK\n";
