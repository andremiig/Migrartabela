<?php

namespace Migrartabela\Sanitizer;

use DateTime;

class PersonTreatment
{
    public static function nome(string $valor): string
    {
        return trim($valor);
    }

    public static function cpfCnpj(string $valor): string
    {
        // Remove tudo que não é número
        $numeros = preg_replace('/\D/', '', $valor);

        if (strlen($numeros) === 11) {
            // CPF: 123.123.123-12
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numeros);
        }

        if (strlen($numeros) === 14) {
            // CNPJ: 12.123.123/0001-12
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numeros);
        }

        return $valor; // retorna original se não for CPF nem CNPJ válido
    }

    public static function dataNascimento(string $valor): string
    {
        $data = DateTime::createFromFormat('Y-m-d', $valor);

        if ($data === false) {
            return $valor;
        }

        return $data->format('d/m/Y');
    }
    public static function endereco(string $valor): string
    {
        return trim($valor);
    }

    public static function numero(string $valor): string
    {
        return trim($valor);
    }

    public static function complemento(string $valor): string
    {
        return trim($valor);
    }

    public static function setor(string $valor): string
    {
        return strtoupper(trim($valor));
    }

    public static function cidade(?string $valor): string
    {
        // Padroniza para Gurupi se vazio ou nulo
        $valor = trim((string) $valor);
        return $valor !== '' ? $valor : 'Gurupi';
    }

    public static function estado(string $valor): string
    {
        return trim($valor);
    }

    public static function email(string $valor): string|null
    {
        $valor = trim($valor);
        return filter_var($valor, FILTER_VALIDATE_EMAIL) ? $valor : null;
    }

    public static function telefone(string $valor): string|null
    {
        //https://blog.dp6.com.br/regex-o-guia-essencial-das-express%C3%B5es-regulares-2fc1df38a481
        //Remove parênteses, traços, espaços e o '+' (deixa SÓ números). D limpa tudo que não for dígito
        $num = preg_replace('/\D/', '', $valor);

        // 2. Se começar com 55 e for um número longo (com código de país), remove o 55
        if (str_starts_with($num, '55') && strlen($num) > 11) {
            $num = substr($num, 2);
        }

        // 3. Agora o switch funciona perfeitamente com os tamanhos limpos!
        switch (strlen($num)) {
            case 11: // Celular
                return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $num);

            case 10: // Fixo
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $num);

            case 9:  // Sem DDD
                return preg_replace('/(\d{5})(\d{4})/', '$1-$2', $num);

        } 
            return null;
    }
}