# DESAFIO ADIPLIX
_Migração de Base de Dados_

- **Autor:** Samuel Pires
- **Contato:** samuel.adiplix@gmail.com / (63) 99214-0309
- **Data de Criação:** 09/05/2026
- **Data de Atualização:** 26/05/2026

---

⚠️ Utilize o VS Code para visualizar este arquivo (Comando para entrar no modo de visualização: Ctrl + Shift + V)

---

## Cenário

Migrar os dados de cadastro de pessoa, planos e contratos do Sistema Origem (**Base de Dados:** `sistema_proprio`) realizando os devidos tratamentos para que os dados fiquem em conformidade com as Regras de Negócio do Sistema Adiplix (**Base de Dados:** `nando690_exclusivesis_sistema_proprio`).

O desafio levará em consideração a capacidade de interpretação e análise dos dados.

---

## Avaliação Geral

- Nível de interpretação e análise
- Entendimento do código desenvolvido e explicação
- Dados no Adiplix conforme padrão esperado
- Não será levado em consideração dados visuais
- Não será levado em consideração a tecnologia utilizada para migração dos dados (Linguagem de Programação, Padrão de Código, etc.)
- Não será levado em consideração consultas, pesquisas ou outro meio tecnológico para obtenção do conhecimento necessário para alcançar o objetivo do desafio

> ⚠️ **Importante:**
>
> É esperado que o programador seja capaz de realizar o mapeamento da base de dados de origem e destino para identificar corretamente os dados de uma determinada tabela/coluna e realizar a migração para o local correto no destino.
>
> Cada sistema possui sua própria lógica e forma de relacionamento dos dados, sendo necessário identificar e seguir o padrão correspondente.

---

# Regras de Negócio

## Módulo: Plano (1)

### 1.1
Faça um levantamento de todos os planos vinculados aos contratos no Sistema Origem.

### 1.2
Para identificar os planos que devem ser migrados, utilize o filtro levando em consideração a descrição do plano e valor.

Exemplo:

```sql
GROUP BY plano, valor
```

> É importante selecionar os casos onde existem clientes que utilizam o mesmo plano, porém com valores diferentes.

### 1.3
Faça a importação dos planos para o Adiplix utilizando o payload padrão como base de preenchimento das colunas.

> 💡 **Dica:** Consultar arquivo `adiplix_payload_plano.php`

---

## Módulo: Pessoa (2)

### 2.1
`pessoa.nome`

- Necessário realizar tratamento para remover os espaços do início e do fim.

### 2.2
`pessoa.cpf_cnpj`

- Fazer formatação tanto para CNPJ quanto para CPF.

Exemplos:

```txt
12.123.123/0001-12
123.123.123-12
```

### 2.3
`pessoa.data_nascimento`

- Fazer formatação no padrão brasileiro.

Exemplo:

```txt
12/12/1995
```

### 2.4
`pessoa.endereco`

- Necessário remover os espaços do início e do fim.

### 2.5
`pessoa.numero`

- Necessário remover os espaços do início e do fim.

### 2.6
`pessoa.complemento`

- Necessário remover os espaços do início e do fim.

### 2.7
`pessoa.setor`

- Necessário remover os espaços do início e do fim.
- Transformar todos os caracteres em maiúsculo (`UPPER`).

### 2.8
`pessoa.cidade`

- Necessário padronizar o nome das cidades.
- Por padrão, a cidade deve ser `Gurupi`.

### 2.9
`pessoa.estado`

- Necessário remover os espaços do início e do fim.

### 2.10
`pessoa.endereco2`

- Necessário remover os espaços do início e do fim.

### 2.11
`pessoa.numero2`

- Necessário remover os espaços do início e do fim.

### 2.12
`pessoa.complemento2`

- Necessário remover os espaços do início e do fim.

### 2.13
`pessoa.setor2`

- Necessário remover os espaços do início e do fim.
- Transformar todos os caracteres em maiúsculo (`UPPER`).

### 2.14
`pessoa.cidade2`

- Necessário padronizar o nome das cidades.
- Por padrão, a cidade deve ser `Gurupi`.

### 2.15
`pessoa.estado2`

- Necessário remover os espaços do início e do fim.

### 2.16
`pessoa_email.email`

- Importar apenas e-mails válidos.
- Remover os espaços do início e do fim.

### 2.17
`pessoa_telefone.telefone`

- Formatar conforme padrão:

```txt
(00)00000-0000
```

- Remover os espaços do início e do fim.

### 2.18
Preencher na coluna `pessoa.externo_id` no destino o respectivo `id` da pessoa migrada da origem.

> 💡 **Dica:**
>
> No Adiplix é utilizada uma tabela específica para armazenar os dados quando o associado possui mais de um endereço no Sistema Origem.
>
> Consultar os seguintes arquivos:
>
> - `adiplix_payload_pessoa.php`
> - `adiplix_payload_pessoa_endereco.php`

---

## Módulo: Contrato (3)

### 3.1
`contrato.id`

- Migrar essa informação para:
  - `contrato.Matricula`
  - `contrato.externo_id`

### 3.2
`contrato.plano` e `contrato.valor`

- Relacionar corretamente com o plano já migrado para o destino.

### 3.3
`contrato.vendedor` e `contrato.cobrador`

- Não serão levados em consideração na migração.
- O valor no destino deve ser `0`.

### 3.4
`contrato.forma_pagamento`

- Necessário realizar relacionamento para identificar o direcionamento correto conforme os dados da tabela `contrato_formapagamento` no destino.

### 3.5
`contrato.id_pessoa`

- Relacionar corretamente com a pessoa já migrada para o destino.

> ⚠️ **Importante:**
>
> No Adiplix NÃO pode existir uma mesma pessoa vinculada a mais de um contrato.
>
> Caso a pessoa já possua contrato vinculado, será necessário DUPLICAR os dados da pessoa (gerando um novo `Id`) e relacionar esse novo `Id` ao contrato.
>
> Relacionamento esperado:
>
> ```txt
> 1:1
> ```

### 3.6
`contrato.status`

- Independentemente do status, os contratos devem ser migrados com situação de carência:

```txt
contrato.Situacao = Carência
```

- Exceção:

Caso exista uma data de cancelamento na coluna `contrato.data_cancelamento` da origem:

```txt
contrato.Situacao = Cancelado
```

E:

```txt
contrato.Cancelamento = data_cancelamento
```

### 3.7
Preencher na coluna `contrato.externo_id` no destino o respectivo `id` do contrato migrado da origem.

> 💡 **Dica:** Consultar arquivo `adiplix_payload_contrato.php`

---

## Módulo: Relatório (4)

### 4.1
Ao finalizar a importação de todos os registros da origem, gerar relatórios contendo:

#### 4.1.1
Relação de e-mails inconsistentes que não foram importados da origem.

#### 4.1.2
Relação da quantidade total de clientes com mais de um endereço no destino.

#### 4.1.3
Relação da quantidade total de clientes por status no destino.

