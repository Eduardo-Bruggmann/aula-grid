# AulaGrid

O AulaGrid é um sistema de apontamento e alocação de professores. Ele relaciona professores, especialidades, disponibilidades, turmas, unidades curriculares, unidades SENAI e períodos para apoiar a montagem da grade acadêmica.

Seu principal objetivo é automatizar a alocação de professores, respeitando restrições de disponibilidade, qualificação e carga de trabalho. Cada geração fica registrada de forma independente, com cobertura, alocações produzidas e conflitos que ainda precisam de tratamento.

## Objetivo do projeto

O sistema foi construído para:

- centralizar os cadastros necessários ao planejamento acadêmico;
- identificar pendências de configuração antes da geração da grade;
- gerar alocações automaticamente a partir das regras cadastradas;
- registrar cada execução do motor para consulta posterior;
- apresentar o percentual de cobertura e os períodos não atendidos;
- apoiar a tomada de decisão no planejamento acadêmico.

## Funcionalidades principais

- CRUD de unidades SENAI;
- CRUD de especialidades;
- CRUD de unidades curriculares;
- CRUD de professores;
- vínculo de especialidades aos professores, com percentual de aderência;
- configuração da disponibilidade de cada professor por período;
- CRUD de turmas, incluindo unidade, unidade curricular, carga de períodos e situação ativa;
- dashboard operacional com indicadores, pendências e cadastros recentes;
- identificação de professores sem especialidade ou disponibilidade e de turmas sem professor ativo compatível;
- geração automática de alocações;
- histórico paginado das execuções;
- visualização das alocações geradas e de sua pontuação;
- visualização dos conflitos em linguagem compreensível;
- cálculo de cobertura por execução.

## Tecnologias utilizadas

As versões abaixo são as declaradas nos arquivos de dependências do projeto:

- PHP 8.3 ou superior compatível com `^8.3`;
- Laravel `^13.8`;
- Blade;
- Tailwind CSS `^4.0`;
- Vite `^8.0`;
- PostgreSQL;
- PHPUnit `^12.5.12`;
- Composer;
- Node.js e npm.

## Arquitetura

Os CRUDs e a interface seguem as convenções do Laravel: rotas web, controllers, Form Requests para validação, models Eloquent e views Blade. A persistência fica em `app/Models`; não há, no estado atual do repositório, uma pasta dedicada `app/Infrastructure`.

O módulo de alocação possui uma separação inspirada em Clean Architecture:

```text
app/
├── Application/Allocation/
│   ├── DTOs/
│   └── UseCases/
├── Domain/Allocation/
│   ├── DTOs/
│   ├── Enums/
│   ├── Services/
│   └── ValueObjects/
├── Http/Controllers/
└── Models/

resources/views/
├── allocation-runs/
└── demais módulos Blade
```

- **Domain:** contém as regras, resultados de validação, códigos de conflito e serviços do domínio de alocação.
- **Application:** contém o caso de uso que coordena uma geração e o DTO entregue à camada HTTP.
- **Models:** implementam a persistência e os relacionamentos por meio do Eloquent.
- **Http:** recebe as requisições através de controllers e das rotas definidas em `routes/web.php`.
- **Views:** apresentam dashboard, cadastros, histórico e resultados com Blade e Tailwind CSS.

As principais classes do fluxo são:

- `AllocationValidator`: valida atividade, especialidade, disponibilidade, choque de horário e limites diário e semanal.
- `CandidateFinder`: consulta professores ativos com especialidade e disponibilidade compatíveis e mantém apenas os aprovados pelo validador.
- `AllocationScorer`: calcula a pontuação dos candidatos válidos e os ordena, com desempate pelo identificador do professor.
- `AllocationEngine`: ordena as turmas por dificuldade, percorre suas necessidades, escolhe o melhor candidato disponível e reúne as necessidades não resolvidas.
- `GenerateAllocationUseCase`: cria a execução, chama o motor, persiste conflitos, calcula o status final e atualiza os totais e a cobertura dentro do fluxo transacional.

## Principais entidades

- `SchoolUnit`: representa uma unidade SENAI à qual professores e turmas podem pertencer.
- `Specialty`: representa uma área de qualificação e agrupa professores e unidades curriculares compatíveis.
- `Subject`: representa uma unidade curricular vinculada a uma especialidade.
- `Teacher`: armazena matrícula, dados pessoais, unidade, situação e limites de carga do professor.
- `SchoolClass`: representa uma turma, sua unidade curricular, unidade SENAI, situação e quantidade de períodos exigida.
- `Period`: representa uma combinação ordenada de dia da semana e turno.
- `TeacherSpecialty`: materializa o vínculo entre professor e especialidade, incluindo a pontuação de aderência.
- `TeacherAvailability`: registra se um professor está disponível em determinado período.
- `AllocationRun`: registra uma execução independente, seu status, cobertura, totais e horários de início e fim.
- `Allocation`: registra o professor escolhido para uma turma e período dentro de uma execução.
- `AllocationConflict`: registra um período que não pôde ser atendido, com código interno, descrição e situação.

O banco também possui `ConflictSuggestion`, estrutura auxiliar para possíveis sugestões associadas a um conflito, embora ela não participe do fluxo atual de geração exibido pela interface.

## Regras de negócio

O motor implementa as seguintes regras:

- somente professores ativos podem ser candidatos;
- somente turmas ativas participam da geração;
- a unidade curricular da turma deve possuir uma especialidade definida;
- o professor deve possuir a especialidade exigida;
- o professor deve estar marcado como disponível no período;
- o professor não pode estar em duas turmas no mesmo período e na mesma execução;
- uma turma não pode receber duas alocações no mesmo período e na mesma execução;
- o total de períodos do professor deve ficar abaixo de `max_weekly_periods`;
- o total do professor em um mesmo dia deve ficar abaixo de `max_daily_periods`;
- a unidade do professor influencia a pontuação, mas não impede uma alocação em outra unidade;
- cada turma solicita a quantidade definida em `required_periods`;
- quando a carga exigida não pode ser completada, são registrados conflitos;
- cada geração cria uma `AllocationRun` independente;
- alocações e conflitos pertencem exclusivamente à execução que os gerou.

Não existe uma regra fixa separada chamada “no máximo dois turnos por dia”. O limite é representado por `Teacher.max_daily_periods`, cujo valor padrão na migration e nos seeders é `2`, mas os formulários permitem configurá-lo entre 1 e 3. O `AllocationValidator` conta as alocações no mesmo dia da semana e aplica o valor configurado para cada professor.

## Como funciona a busca heurística

O sistema usa uma abordagem heurística do tipo *greedy* (gulosa); ele não explora por força bruta todas as combinações possíveis.

1. O `AllocationEngine` carrega os períodos por `sort_order` e somente as turmas ativas.
2. As turmas são ordenadas por dificuldade: maior quantidade de períodos exigidos primeiro, depois menor quantidade de professores ativos vinculados à especialidade e, por fim, pelo identificador.
3. Para cada necessidade de uma turma, o `CandidateFinder` busca professores potencialmente compatíveis.
4. A consulta exige professor ativo, especialidade correspondente e disponibilidade positiva no período.
5. O `AllocationValidator` elimina candidatos que causariam choque de professor ou turma, ou ultrapassariam os limites diário e semanal.
6. O `AllocationScorer` pontua os candidatos válidos com os critérios e pesos existentes no código:
   - aderência da especialidade: 50%;
   - capacidade semanal restante: 30%;
   - capacidade diária restante: 15%;
   - professor e turma na mesma unidade: 5%.
7. Os candidatos são ordenados pela maior pontuação, com desempate pelo menor identificador do professor.
8. O motor escolhe o melhor candidato naquele momento e persiste a alocação.
9. A nova alocação passa a compor o estado da execução, de modo que as decisões seguintes considerem cargas e horários já ocupados.
10. Quando a carga de uma turma não pode ser completada, o motor cria necessidades não resolvidas, que o caso de uso transforma em `AllocationConflict`.
11. Ao final, a cobertura é calculada por `alocações criadas / períodos solicitados × 100` e o resultado é persistido. Quando nenhuma turma ativa solicita períodos, o código retorna 100% para evitar divisão por zero, mantendo os totais em zero.

Essa estratégia procura uma solução válida e de boa qualidade por decisões progressivas. Ela é mais simples e rápida que uma busca exaustiva, mas não garante o ótimo global. A qualidade depende da ordenação das turmas, das regras de validação e dos critérios de pontuação.

## Fluxo de uma geração

```text
AllocationRunController::store
→ GenerateAllocationUseCase
→ AllocationEngine
→ CandidateFinder
→ AllocationValidator
→ AllocationScorer
→ persistência de Allocation
→ persistência de AllocationConflict
→ atualização de AllocationRun
→ redirecionamento para a apresentação do resultado
```

A `AllocationRun` é criada como `running` antes da transação que executa o motor. Em caso de exceção, as alterações parciais da transação são revertidas e a execução é mantida no histórico como `failed`.

## Status das execuções

Os status são armazenados como strings; não existe um enum específico para eles no código atual.

- `pending`: valor padrão definido na migration de `allocation_runs`.
- `running`: geração criada e ainda em processamento.
- `completed`: geração concluída sem conflitos.
- `completed_with_conflicts`: geração concluída com períodos não atendidos.
- `failed`: geração interrompida por uma exceção.

## Requisitos

- PHP compatível com a restrição `^8.3` do projeto;
- Composer;
- PostgreSQL;
- extensão PDO para PostgreSQL (`pdo_pgsql`) habilitada no PHP usado pela aplicação;
- Node.js;
- npm.

## Instalação e configuração

1. Clone o repositório e acesse a pasta:

   ```bash
   git clone <url-do-repositorio>
   cd aula-grid
   ```

2. Instale as dependências PHP:

   ```bash
   composer install
   ```

3. Crie o arquivo de ambiente e a chave da aplicação:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   No Windows PowerShell, a cópia pode ser feita com:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Crie o banco PostgreSQL e configure o `.env` sem versionar credenciais:

   ```dotenv
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=aula_grid
   DB_USERNAME=...
   DB_PASSWORD=...
   ```

5. Execute migrations e seeders:

   ```bash
   php artisan migrate --seed
   ```

6. Instale e compile as dependências do frontend:

   ```bash
   npm install
   npm run build
   ```

7. Inicie a aplicação:

   ```bash
   php artisan serve
   ```

O `.env.example` define sessão, cache e fila com driver de banco de dados. Para usar esses drivers, as respectivas tabelas de infraestrutura do Laravel também precisam existir. Em um ambiente local simples, podem ser utilizados `SESSION_DRIVER=file`, `CACHE_STORE=file` e `QUEUE_CONNECTION=sync`.

## Execução em desenvolvimento

Normalmente são usados dois terminais:

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

A aplicação fica disponível, por padrão, em [http://127.0.0.1:8000](http://127.0.0.1:8000).

O projeto também oferece `composer run dev`, que inicia servidor, listener da fila, visualização de logs e Vite em conjunto.

## Banco de dados

Aplicar migrations pendentes:

```bash
php artisan migrate
```

Executar os seeders:

```bash
php artisan db:seed
```

Como o `DatabaseSeeder` já chama os seeders de unidades, especialidades, unidades curriculares, períodos, professores, vínculos, turmas e disponibilidades, a preparação inicial pode ser feita com:

```bash
php artisan migrate --seed
```

Para recriar completamente um banco de desenvolvimento:

```bash
php artisan migrate:fresh --seed
```

> **Atenção:** `migrate:fresh` remove todas as tabelas e apaga os dados do banco selecionado. Use-o somente em um ambiente de desenvolvimento descartável e após conferir a conexão ativa.

## Testes

Executar toda a suíte:

```bash
php artisan test
```

Executar apenas o caso de uso de geração:

```bash
php artisan test tests/Feature/Application/Allocation/GenerateAllocationUseCaseTest.php
```

Executar os testes HTTP do módulo:

```bash
php artisan test tests/Feature/Http/Controllers/AllocationRunControllerTest.php
```

Também existem testes específicos para `AllocationEngine`, `CandidateFinder`, `AllocationValidator` e `AllocationScorer` em `tests/Feature/Domain/Allocation`.

O `phpunit.xml` define o ambiente de testes com SQLite em memória, cache e sessão em memória e fila síncrona. Portanto, a suíte não usa o banco PostgreSQL configurado para desenvolvimento.

## Uso básico

Uma ordem recomendada de preparação e uso é:

1. cadastrar as unidades SENAI;
2. cadastrar as especialidades;
3. cadastrar as unidades curriculares e associá-las às especialidades;
4. cadastrar os professores;
5. vincular especialidades aos professores e informar a aderência;
6. configurar a disponibilidade de cada professor;
7. cadastrar as turmas e a quantidade de períodos exigida;
8. revisar indicadores e pendências na dashboard;
9. acessar **Alocações** no menu principal;
10. gerar uma nova execução;
11. consultar cobertura, quantidade preenchida, alocações e conflitos.

## Limitações e possíveis evoluções

O motor atual é síncrono e heurístico. As possibilidades abaixo são evoluções futuras, não funcionalidades já disponíveis:

- comparação entre execuções;
- filtros por unidade;
- exportação de resultados;
- explicações mais detalhadas e sugestões para conflitos;
- estratégias alternativas e configuráveis de pontuação;
- técnicas de otimização global;
- processamento assíncrono para bases maiores.

## Licença e contexto

O `composer.json` declara a licença MIT. O repositório não contém, no estado documentado, um arquivo de licença separado com o texto integral.
