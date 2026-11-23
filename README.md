# biblioteca-orm

Mini-ORM didático (Data Mapper simplificado) com PHP 8.2, PDO e MySQL 8.

## Estrutura

- `src/Domain`: entidades (`Livro`, `Autor`, `Exemplar`) com suas regras de validação.
- `src/Infrastructure`: repositórios (`LivroRepository`, `AutorRepository`, `ExemplarRepository`) que estendem `AbstractRepository` e fazem o mapeamento entre objeto e tabela.
- `sql/schema.sql`: criação do banco e das tabelas.
- `tests/smoke.php`: teste de fumaça com CRUD, associação livro/autor, exemplares e transação.

## Como executar

1. Execute `sql/schema.sql` no MySQL.
2. Defina `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`.
3. Rode `php tests/smoke.php`.
