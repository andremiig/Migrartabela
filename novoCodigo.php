<?php

// =========================================================================
// 1. INCLUSÃO DE CONFIGURAÇÕES LOCAIS
// Este script pressupõe que seu arquivo config_local.php (IGNORADO PELO GIT) 
// define as seguintes constantes: HOST_ORIGEM, USUARIO_ORIGEM, SENHA_ORIGEM, DB_ORIGEM,
// HOST_DESTINO, USUARIO_DESTINO, SENHA_DESTINO, DB_DESTINO.
// =========================================================================

// --- Configurações de Conexão de Exemplo (Para teste local) ---
// Se você não incluiu o config_local.php, defina as credenciais aqui:
if (!defined('HOST_ORIGEM')) { define('HOST_ORIGEM', 'localhost'); }
if (!defined('USUARIO_ORIGEM')) { define('USUARIO_ORIGEM', 'root'); } 
if (!defined('SENHA_ORIGEM')) { define('SENHA_ORIGEM', 'sua_senha_mysql'); } 
if (!defined('DB_ORIGEM')) { define('DB_ORIGEM', 'banco_origem'); } 

if (!defined('HOST_DESTINO')) { define('HOST_DESTINO', 'localhost'); }
if (!defined('USUARIO_DESTINO')) { define('USUARIO_DESTINO', 'root'); }
if (!defined('SENHA_DESTINO')) { define('SENHA_DESTINO', 'sua_senha_mysql'); }
if (!defined('DB_DESTINO')) { define('DB_DESTINO', 'banco_destino'); } 
// -------------------------------------------------------------

// =========================================================================
// 2. CONFIGURAÇÕES DA MIGRAÇÃO
// =========================================================================

$id_para_migrar = 10; // <<--- DEFINA O ID_ORIGEM A SER MIGRADO AQUI

$tabela_origem = 'veiculos_origem';
$tabela_destino = 'veiculos_destino';

// Colunas que você quer migrar. Devem existir em AMBAS as tabelas.
$colunas_para_migrar = ['id_origem', 'marca', 'modelo', 'ano', 'data_cadastro']; 
$cols = implode(', ', $colunas_para_migrar);

echo "<h1>Migração de Dados (Local)</h1>";
echo "<h3>Migrando registros com ID de Origem: **{$id_para_migrar}**</h3>";
echo "<p>De: `{$tabela_origem}` ({DB_ORIGEM})</p>";
echo "<p>Para: `{$tabela_destino}` ({DB_DESTINO})</p>";
echo "<hr>";


// =========================================================================
// 3. CONEXÃO COM OS BANCOS DE DADOS
// =========================================================================

// Conexão com o Banco de Origem (A)
$conn_origem = new mysqli(HOST_ORIGEM, USUARIO_ORIGEM, SENHA_ORIGEM, DB_ORIGEM);
if ($conn_origem->connect_error) {
    die("Falha na conexão de ORIGEM: " . $conn_origem->connect_error);
}

// Conexão com o Banco de Destino (B)
$conn_destino = new mysqli(HOST_DESTINO, USUARIO_DESTINO, SENHA_DESTINO, DB_DESTINO);
if ($conn_destino->connect_error) {
    die("Falha na conexão de DESTINO: " . $conn_destino->connect_error);
}

echo "<p style='color: green;'>✅ Conexões estabelecidas com sucesso.</p>";


// =========================================================================
// 4. ETAPA 1: SELECIONAR E MIGRAR DADOS
// =========================================================================

$sql_select = "SELECT {$cols} FROM {$tabela_origem} WHERE id_origem = {$id_para_migrar}";
$resultado = $conn_origem->query($sql_select);

if (!$resultado) {
    die("<p style='color: red;'>❌ Erro na consulta de seleção: " . $conn_origem->error . "</p>");
}

$linhas_migradas = 0;

if ($resultado->num_rows > 0) {
    echo "<h3>Iniciando Migração de {$resultado->num_rows} linhas...</h3>";

    // Loop pelas linhas encontradas na tabela de Origem
    while ($linha = $resultado->fetch_assoc()) {
        
        // Constrói os valores para a query de INSERT no Destino
        $valores = [];
        foreach ($colunas_para_migrar as $coluna) {
            // Garante que os valores são escapados para a inserção
            $valor_escapado = $conn_destino->real_escape_string($linha[$coluna]);
            $valores[] = "'{$valor_escapado}'";
        }
        $vals = implode(', ', $valores);

        $sql_insert = "INSERT INTO {$tabela_destino} ({$cols}) VALUES ({$vals})";
        
        if ($conn_destino->query($sql_insert) === TRUE) {
            $linhas_migradas++;
        } else {
            echo "<p style='color: orange;'>⚠️ Erro ao inserir linha: " . $conn_destino->error . "</p>";
        }
    }
    
    echo "<p style='color: green;'>✅ Migração concluída. Total de {$linhas_migradas} linhas inseridas na tabela `{$tabela_destino}`.</p>";

} else {
    echo "<p>ℹ️ Nenhuma linha encontrada com `id_origem` = {$id_para_migrar} para migrar.</p>";
}


// =========================================================================
// 5. ETAPA 2: LIMPEZA (DELETAR DADOS NA ORIGEM)
// Esta etapa só será executada se a migração foi bem-sucedida.
// =========================================================================

if ($linhas_migradas > 0 && $linhas_migradas == $resultado->num_rows) {
    echo "<hr><h3>Iniciando Limpeza na Tabela de Origem...</h3>";

    $sql_delete = "DELETE FROM {$tabela_origem} WHERE id_origem = {$id_para_migrar}";
    
    if ($conn_origem->query($sql_delete) === TRUE) {
        echo "<p style='color: green;'>✅ Limpeza concluída. Total de {$conn_origem->affected_rows} linhas DELETADAS da tabela `{$tabela_origem}`.</p>";
    } else {
        echo "<p style='color: red;'>❌ ERRO CRÍTICO ao deletar linhas: " . $conn_origem->error . "</p>";
    }
} else if ($linhas_migradas > 0) {
    // Caso tenha dado algum erro na inserção de algumas linhas
     echo "<p style='color: orange;'>⚠️ Limpeza não executada! Houve falha na migração de algumas linhas. Verifique erros e rode novamente.</p>";
}

// =========================================================================
// 6. FINALIZAÇÃO
// =========================================================================

$conn_origem->close();
$conn_destino->close();

echo "<hr><p>Script finalizado.</p>";
?>