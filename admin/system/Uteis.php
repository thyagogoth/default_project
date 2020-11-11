<?php

namespace System;

use Cocur\Slugify\Slugify;

class Uteis {

    // public static function getFileContent() {
    //     $json = file_get_contents(LAYOUT_SETTINGS_FILE);
    //     return json_decode($json, true);
    // }

    // public static function setFileContent(array $assocArray) {
    //     $json = json_encode($assocArray);
    //     if (file_put_contents(LAYOUT_SETTINGS_FILE, $json)) {
    //         return true;
    //     }
    //     return false;
    // }

    public static function getNameServer() {
        $protocol = 'http://';
        if (HTTPS_MODE == TRUE) {
            $protocol = 'https://';
        }
        $port = $_SERVER['SERVER_PORT'] !== '80' ? ":{$_SERVER['SERVER_PORT']}" : "";
        return $protocol . $_SERVER['SERVER_NAME'] . $port . str_replace('/index.php', '', $_SERVER['PHP_SELF']);
    }

    /**
     * @param $termo
     * @return string
     */
    public static function space2like($termo) {
        return addslashes(str_replace(' ', '%', '%' . $termo . '%'));
    }

    /**
     *  FORMATAÇÃO DE MÁSCARAS
     */
    public static function formataData($data, $divisorEntrada = '/', $divisor = '-') {
        $aux = '';
        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data[0] as $var) {
                    if (array_key_exists($var, $data[1])) {
                        $arrData = explode($divisorEntrada, $data[1][$var]);
                        $data[1][$var] = $arrData[2] . $divisor . $arrData[1] . $divisor . $arrData[0];
                    }
                }
            } else {
                $arrData = explode($divisorEntrada, $data);
                $aux = $arrData[2] . $divisor . $arrData[1] . $divisor . $arrData[0];
            }
        }
        return $aux;
    }

    public static function formataCPF($fcpf) {
        $fcpf = substr($fcpf, 0, 3) . '.' . substr($fcpf, 3, 3) . '.' . substr($fcpf, 6, 3) . '-' . substr($fcpf, 9, 2);
        return $fcpf;
    }

    public static function formataCNPJ($fcnpj) {
        $fcnpj = substr($fcnpj, 0, 2) . '.' . substr($fcnpj, 2, 3) . '.' . substr($fcnpj, 5, 3) . '/' . substr($fcnpj, 8, 4) . '-' . substr($fcnpj, 12, 2);
        return $fcnpj;
    }

    public static function formataTelefone($tel) {
        switch (strlen($tel)) {
            case '8':
                $tel = substr($tel, 0, 4) . ' - ' . substr($tel, 4, 4);
                break;
            case '10':
                $tel = '(' . substr($tel, 0, 2) . ') ' . substr($tel, 2, 4) . '-' . substr($tel, 6, 4);
                break;
            case '12':
                $tel = '+' . substr($tel, 0, 2) . ' (' . substr($tel, 2, 2) . ') ' . substr($tel, 4, 4) . '-' . substr($tel, 8, 4);
                break;
        }
        return $tel;
    }

    public static function formataCep($cep) {
        $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        return $cep;
    }

    public static function formatTamanhoArquivo($tamanho) {
        $sigla = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
        $i = 0;
        while ($parar == false) {
            if ($tamanho >= 1024) {
                $tamanho = $tamanho / 1024;
                $i++;
            } else {
                $parar = true;
            }
        }
        $tamanho = number_format($tamanho, '2', '.', ',');
        $tamanho = $tamanho . " " . $sigla[$i];
        return $tamanho;
    }

    public static function validateCpf($cpf) {
        $nulos = array(
            '12345678909', '11111111111', '22222222222', '33333333333',
            '44444444444', '55555555555', '66666666666', '77777777777',
            '88888888888', '99999999999', '00000000000'
        );
        $cpf = ereg_replace("[^0-9]", "", $cpf);

        if (!(ereg("[0-9]", $cpf)))
            return 0;

        if (in_array($cpf, $nulos))
            return 0;

        $acum = 0;
        for ($i = 0; $i < 9; $i++) {
            $acum += $cpf[$i] * (10 - $i);
        }
        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        if ($acum != $cpf[9]) {
            return 0;
        }
        $acum = 0;
        for ($i = 0; $i < 10; $i++) {
            $acum += $cpf[$i] * (11 - $i);
        }
        $x = $acum % 11;
        $acum = ($x > 1) ? (11 - $x) : 0;
        if ($acum != $cpf[10]) {
            return 0;
        }
        return 1;
    }

    public static function validaCnpj($cnpj) {
        $cnpj = ereg_replace("[^0-9]", "", $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        $soma = 0;
        $soma += ($cnpj[0] * 5);
        $soma += ($cnpj[1] * 4);
        $soma += ($cnpj[2] * 3);
        $soma += ($cnpj[3] * 2);
        $soma += ($cnpj[4] * 9);
        $soma += ($cnpj[5] * 8);
        $soma += ($cnpj[6] * 7);
        $soma += ($cnpj[7] * 6);
        $soma += ($cnpj[8] * 5);
        $soma += ($cnpj[9] * 4);
        $soma += ($cnpj[10] * 3);
        $soma += ($cnpj[11] * 2);

        $d1 = $soma % 11;
        $d1 = $d1 < 2 ? 0 : 11 - $d1;

        $soma = 0;
        $soma += ($cnpj[0] * 6);
        $soma += ($cnpj[1] * 5);
        $soma += ($cnpj[2] * 4);
        $soma += ($cnpj[3] * 3);
        $soma += ($cnpj[4] * 2);
        $soma += ($cnpj[5] * 9);
        $soma += ($cnpj[6] * 8);
        $soma += ($cnpj[7] * 7);
        $soma += ($cnpj[8] * 6);
        $soma += ($cnpj[9] * 5);
        $soma += ($cnpj[10] * 4);
        $soma += ($cnpj[11] * 3);
        $soma += ($cnpj[12] * 2);

        $d2 = $soma % 11;
        $d2 = $d2 < 2 ? 0 : 11 - $d2;

        if ($cnpj[12] == $d1 && $cnpj[13] == $d2) {
            return true;
        } else {
            return false;
        }
    }

    public static function dataDif($data1, $data2, $intervalo) {
        switch ($intervalo) {
            case 'y':
                $Q = 86400 * 365;
                break; //ano
            case 'm':
                $Q = 2592000;
                break; //mes
            case 'd':
                $Q = 86400;
                break; //dia
            case 'h':
                $Q = 3600;
                break; //hora
            case 'n':
                $Q = 60;
                break; //minuto
            default:
                $Q = 1;
                break; //segundo
        }
        return round((strtotime($data2) - strtotime($data1)) / $Q);
    }

    public static function calculaIntervaloData($data_inicial, $data_final) {
        $time_inicial = self::geraTimestamp($data_inicial);
        $time_final = self::geraTimestamp($data_final);
        $diferenca = $time_final - $time_inicial;
        $result = floor($diferenca / (60 * 60 * 24));
        return $result;
    }

    public static function validateHour($hour, $type = '2') {
        $rtn = 1;
        $hour = self::removeCaracter($hour);
        $hora1 = (int) substr($hour, 0, 1);
        $horaA = (int) substr($hour, 0, 2);
        $hora3 = (int) substr($hour, 2, 1);
        $hotaB = (int) substr($hour, 2, 2);
        switch ($type) {
            case 1:
                if (($hora1 > 2) || ($horaA > 23)) {
                    $rtn = 0;
                }
                break;
            case 2:
                if (($hora1 > 2) || ($horaA > 23) || ($hora3 > 6) || ($horaB > 59)) {
                    $rtn = 0;
                }
                break;
            default:
                $rtn = 0;
                break;
        }
        return $rtn;
    }

    public static function unhtmlentities($string) {
        $trans_tbl1 = get_html_translation_table(HTML_ENTITIES);
        foreach ($trans_tbl1 as $ascii => $htmlentitie) {
            $trans_tbl2[$ascii] = '&#' . ord($ascii) . ';';
        }
        $trans_tbl1 = array_flip($trans_tbl1);
        $trans_tbl2 = array_flip($trans_tbl2);
        return strtr(strtr($string, $trans_tbl1), $trans_tbl2);
    }

    public static function findAllEstados($i = 0) {
        if ($i === 0) {
            $uf = array("AC" => "AC", "AL" => "AL", "AM" => "AM", "AP" => "AP", "BA" => "BA", "CE" => "CE", "DF" => "DF", "ES" => "ES", "GO" => "GO", "MA" => "MA", "MG" => "MG", "MS" => "MS", "MT" => "MT", "PA" => "PA", "PB" => "PB", "PE" => "PE", "PI" => "PI", "PR" => "PR", "RJ" => "RJ", "RN" => "RN", "RO" => "RO", "RR" => "RR", "RS" => "RS", "SC" => "SC", "SE" => "SE", "SP" => "SP", "TO" => "TO");
        } else if ($i === 1) {
            $uf = array(
                array('id' => 'AC', 'uf' => 'AC', 'descricao' => 'Acre'),
                array('id' => 'AL', 'uf' => 'AL', 'descricao' => 'Alagoas'),
                array('id' => 'AM', 'uf' => 'AM', 'descricao' => 'Amazonas'),
                array('id' => 'AP', 'uf' => 'AP', 'descricao' => 'Amapá'),
                array('id' => 'BA', 'uf' => 'BA', 'descricao' => 'Bahia'),
                array('id' => 'CE', 'uf' => 'CE', 'descricao' => 'Ceará'),
                array('id' => 'DF', 'uf' => 'DF', 'descricao' => 'Distrito Federal'),
                array('id' => 'ES', 'uf' => 'ES', 'descricao' => 'Espírito Santo'),
                array('id' => 'GO', 'uf' => 'GO', 'descricao' => 'Goiás'),
                array('id' => 'MA', 'uf' => 'MA', 'descricao' => 'Maranhão'),
                array('id' => 'MG', 'uf' => 'MG', 'descricao' => 'Minas Gerais'),
                array('id' => 'MS', 'uf' => 'MS', 'descricao' => 'Mato Grosso do Sul'),
                array('id' => 'MT', 'uf' => 'MT', 'descricao' => 'Mato Grosso'),
                array('id' => 'PA', 'uf' => 'PA', 'descricao' => 'Pará'),
                array('id' => 'PB', 'uf' => 'PB', 'descricao' => 'Paraiba'),
                array('id' => 'PE', 'uf' => 'PE', 'descricao' => 'Pernambuco'),
                array('id' => 'PI', 'uf' => 'PI', 'descricao' => 'Piauí'),
                array('id' => 'PR', 'uf' => 'PR', 'descricao' => 'Paraná'),
                array('id' => 'RJ', 'uf' => 'RJ', 'descricao' => 'Rio de Janeiro'),
                array('id' => 'RN', 'uf' => 'RN', 'descricao' => 'Rio Grande do Norte'),
                array('id' => 'RO', 'uf' => 'RO', 'descricao' => 'Rondônia'),
                array('id' => 'RR', 'uf' => 'RR', 'descricao' => 'Roraima'),
                array('id' => 'RS', 'uf' => 'RS', 'descricao' => 'Rio Grande do Sul'),
                array('id' => 'SC', 'uf' => 'SC', 'descricao' => 'Santa Catarina'),
                array('id' => 'SE', 'uf' => 'SE', 'descricao' => 'Sergipe'),
                array('id' => 'SP', 'uf' => 'SP', 'descricao' => 'São Paulo'),
                array('id' => 'TO', 'uf' => 'TO', 'descricao' => 'Tocantins')
            );
        }
        return $uf;
    }

    public static function loadEstado($id) {
        $arrEstado = self::findAllEstados(1);
        foreach ($arrEstado as $var) {
            if (strtolower($var['id']) == strtolower($id)) {
                return $var;
            }
        }
    }

    public static function findAllDiasSemana($i = 0) {
        switch ($i) {
            case 0:
                return array(0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 5 => 'Sábado');
                break;
            case 1:
                return array('domingo' => 'Domingo', 'segunda' => 'Segunda', 'terça' => 'Terça', 'quarta' => 'Quarta', 'quinta' => 'Quinta', 'sexta' => 'Sexta', 'sabado' => 'Sábado');
                break;
            case 2:
                return array(0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado');
                break;
            case 3:
                return array('domingo' => 'Domingo', 'segunda' => 'Segunda-feira', 'terça' => 'Terça-feira', 'quarta' => 'Quarta-feira', 'quinta' => 'Quinta-feira', 'sexta' => 'Sexta-feira', 'sabado' => 'Sábado');
                break;
        }
    }

    public static function loadDiaSemana($id, $i = 0) {
        $arr = self::findAllDiasSemana($i);
        return $arr[$id];
    }

    public static function findAllMes($i = 0, $idioma = 'pt_BR') {
        switch ($idioma) {
            case 'pt_BR':
                switch ($i) {
                    case 0:
                        return array(1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
                        break;
                    case 1:
                        return array('jan' => 'Janeiro', 'fev' => 'Fevereiro', 'mar' => 'Março', 'abr' => 'Abril', 'maio' => 'Maio', 'jun' => 'Junho', 'jul' => 'Julho', 'ago' => 'Agosto', 'set' => 'Setembro', 'out' => 'Outubro', 'nov' => 'Novembro', 'dez' => 'Dezembro');
                        break;
                    case 2:
                        return array(1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez');
                        break;
                }
                break;
            case 'en_US':
                switch ($i) {
                    case 0:
                        return array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
                        break;
                    case 1:
                        return array('jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December');
                        break;
                    case 2:
                        return array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
                        break;
                }
                break;
            case 'es_ES':
                switch ($i) {
                    case 0:
                        return array(1 => 'Enero ', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
                        break;
                    case 1:
                        return array('ene' => 'Enero', 'feb' => 'Febrero', 'mar' => 'Marzo', 'abr' => 'Abril', 'may' => 'Mayo', 'jun' => 'June', 'jul' => 'Julio', 'ago' => 'Agosto', 'sep' => 'Septiembre', 'oct' => 'Octubre', 'nov' => 'Noviembre', 'dec' => 'Diciembre');
                        break;
                    case 2:
                        return array(1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
                        break;
                }
                break;
        }
    }

    public static function loadMes($id, $i = 0, $idioma = 'pt_BR') {
        $arr = self::findAllMes($i, $idioma);
        return $arr[(int) $id];
    }

    public static function corrigeLink($link, $n_caracteres) {
        if (strlen($link) > $n_caracteres) {
            $carc = round(($n_caracteres / 2) + 5);
            $link = substr($link, 0, $carc) . "..." . substr($link, strlen($link) - $carc, strlen($link));
        }
        return $link;
    }

    public static function validateImagem($source, $tam = '2097152') {
        if (!empty($source)) {
            $img = getimagesize($source);
            if ($img[2] != 1 && $img[2] != 2 && $img[2] != 3) {
                return 3;
            } else if (filesize($source) > $tam) {
                return 4;
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }

    public static function getArrayFormularioFormatado($array) {
        $arr = array();
        if (is_array($array)) {
            foreach ($array as $i => $at) {
                $c = 0;
                foreach ($array[$i] as $var) {
                    $arr[$c++][$i] = $var;
                }
            }
        }
        return $arr;
    }

    public static function getSaudacao() {
        $hora = date('H');
        $saud = 'Olá';
        if ($hora >= 6 && $hora < 12) {
            $saud = 'Bom dia';
        } else if ($hora >= 12 && $hora < 18) {
            $saud = 'Boa tarde';
        } else if ($hora >= 18 || $hora < 6) {
            $saud = 'Boa noite';
        }
        return $saud;
    }

    public static function vrfMaiorData($data1, $data2) {
        if (strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$2/$1/$3", $data1)) > strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$2/$1/$3", $data2))) {
            return true;
        } else {
            return false;
        }
    }

    public static function vrfPeriodoData($data1, $data2, $dataCompara = '') {
        $data = strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$2/$1/$3", (empty($dataCompara) ? date('d/m/Y') : $dataCompara)));
        $rtn = false;
        if ($data >= strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$2/$1/$3", $data1))) {
            if ($data <= strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$2/$1/$3", $data2))) {
                $rtn = true;
            }
        }
        return $rtn;
    }

    public static function getIdAleatorio($n = 6, $sensitive = false, $alpha = true) {

        if ($alpha) {
            if ($sensitive) {
                $caracteres = 'abcdefghijklmnpqrstuvwxy0123456789';
            } else {
                $caracteres = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ0123456789';
            }
        } else {
            $caracteres = '0123456789';
        }

        $max = strlen($caracteres) - 1;
        $password = null;
        for ($i = 0; $i < $n; $i++) {
            $password .= $caracteres[
                    mt_rand(0, $max)];
        }
        return $password;
    }

    public static function getExtensaoArquivo($file) {
        $rtn = false;
        if (is_array($file)) {
            $rtn = strtolower(trim(array_pop(explode('.', $file['name']))));
        } else {
            $rtn = strtolower(trim(array_pop(explode('.', $file))));
        }
        return $rtn;
    }

    public static function redirect($action = '', $msg = '', $tempo = 10) {
        $protocol = 'http://';
        if (HTTPS_MODE == TRUE) {
            $protocol = 'https://';
        }
        header('Location: ' . $protocol . $_SERVER["HTTP_HOST"] . str_replace('index.php', '', $_SERVER['PHP_SELF']) . $action);
        exit();
    }

    public static function createFile($file, $id = '', $tipo = 'arquivo') {
        $rtn = false;
        if (!empty($file['name'])) {
            $path = ($tipo == 'arquivo' ? 'arquivo' : 'imagem') . '_sistema' . DIRECTORY_SEPARATOR;

            $ext = strtolower(trim(array_pop(explode('.', $file['name']))));
            $nome = uniqid((empty($id) ? $tipo : $id) . '_') . '.' . $ext;

            if (!file_exists($path)) {
                mkdir($path, 0777);
            }

            if (!is_writable($path)) {
                chmod($path, 0777);
            }

            if (is_writable($path)) {
                $aux = copy($file['tmp_name'], $path . $nome);
                if ($aux) {
                    $rtn = $nome;
                }
            }
        }

        return $rtn;
    }

    public static function copyFile($old, $tipo = 'arquivo', $new = '') {
        $rtn = false;
        $tipo = ($tipo == 'arquivo' ? 'arquivo' : 'imagem');
        $path = $tipo . '_sistema' . DIRECTORY_SEPARATOR;
        $old = $path . $old;
        if (file_exists($old)) {
            if (!file_exists($path)) {
                mkdir($path, 0777);
            }

            if (!is_writable($path)) {
                chmod($path, 0777);
            }

            if (is_writable($path)) {
                $ext = Uteis::getExtensaoArquivo($old);
                $new = uniqid((empty($new) ? $tipo : $new) . '_') . '.' . $ext;
                $aux = copy($old, $path . $new);
                if ($aux) {
                    $rtn = $new;
                }
            }
        }

        return $rtn;
    }

    public static function removeCaracter($arr, $fields = '') {
        if (is_array($arr)) {
            foreach ($fields as $field) {
                if (!empty($arr[$field])) {
                    $arr[$field] = preg_replace('/[-()\s\.]/', '', $arr[$field]);
                }
            }
        } else {
            $arr = preg_replace('/[-()\s\.]/', '', $arr);
        }

        return $arr;
    }

    public static function dtSql($fields = [], $pre = 'a.') {
        $rtn = ', DATE_FORMAT(' . $pre . 'created_at, "%Y-%m-%d") AS created_at,
		DATE_FORMAT(' . $pre . 'created_at, "%H:%i") AS hora_cadastro,
        DATE_FORMAT(' . $pre . 'created_at, "%d/%m/%Y") AS created_at_formatada,
		DATE_FORMAT(' . $pre . 'created_at, "%H:%i") AS hora_cadastro_formatada,
		DATE_FORMAT(' . $pre . 'updated_at, "%d/%m/%Y") AS updated_at_formatada,
		DATE_FORMAT(' . $pre . 'updated_at, "%H:%i") AS hora_atualizacao_formatada ';
        if (count($fields) > 0) {
            foreach ($fields as $campo) {
                $rtn .= ', DATE_FORMAT(' . $pre . $campo . ', "%d/%m/%Y às %H:%i") AS ' . $campo . '_formatada ';
            }
        }
        return $rtn;
    }

    public static function formata_data_extenso($data, $modo = 1, $idioma = 'pt_BR') {
        if ($data) {
            $mes = date('m', strtotime($data));
        } else {
            $mes = date('m');
            $data = date('Y-m-d');
        }
        switch ($idioma) {
            case 'pt_BR':
            default:
                $meses = array('01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro');
                $mesesAbrev = array('01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez');
                $dias = array(0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado');
                $juncao = ' de ';
                break;
            case 'en_US':
                $meses = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'Juny', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
                $mesesAbrev = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec');
                $dias = array(0 => 'Sunday ', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday');
                $juncao = ' ';
                break;
                break;
        }

        $dia_da_semana = $dias[date('w', strtotime($data))];
        $dia_do_mes = date('d', strtotime($data));
        $mes_extenso = $meses[$mes];
        $ano = date('Y', strtotime($data));

        switch ($modo) {
            case 1:
                $rtn = $dia_da_semana . ', ' . $dia_do_mes . $juncao . $mes_extenso . $juncao . $ano;
                break;
            case 2:
                $rtn = $dia_da_semana . ' - ' . $dia_do_mes . '/' . $mes_extenso;
                break;
            case 3:
                $rtn = $dia_do_mes . '/' . $mes . ' - ' . $dia_da_semana;
                break;
            case 4:
                $rtn = date('d', strtotime($data)) . $juncao . $meses[$mes] . $juncao . $ano;
                break;
            case 5:
                $rtn = date('d', strtotime($data)) . $juncao . $mesesAbrev[$mes] . $juncao . $ano;
                break;
        }
        return $rtn;
    }

    public static function limitaString($texto, $numMaxCaract, $reticent = true) {
        if (strlen($texto) < $numMaxCaract) {
            $textoCortado = $texto;
        } else {
            $textoCortado = mb_substr($texto, 0, $numMaxCaract, 'UTF-8');
            $ultimoEspacio = strripos($textoCortado, " ");

            if ($reticent === true) {
                if ($ultimoEspacio !== false) {
                    $textoCortadoTmp = mb_substr($textoCortado, 0, $ultimoEspacio, 'UTF-8');
                    if (substr($textoCortado, $ultimoEspacio)) {
                        $textoCortadoTmp .= '...';
                    }
                    $textoCortado = $textoCortadoTmp;
                } elseif (substr($texto, $numMaxCaract)) {
                    $textoCortado .= '...';
                }
            }
        }

        return $textoCortado;
    }

    public static function cortaPalavra($texto, $n = 50) {
        $texto = self::unhtmlentities($texto);
        return strlen($texto) > $n ? substr($texto, 0, ($n - 0)) : $texto;
    }

    function abreviaString($string) {
        $explode = explode(" ", $string);
        if (count($explode) > 2) {
            for ($i = 0; $i <= count($explode); $i++) {
                if ($i == 1) {
                    $sobrenome = self::cortaPalavra($explode[1], 1);
                    $nome .= $sobrenome . '. ';
                } else {
                    $nome .= ' ' . $explode[$i] . ' ';
                }
            }
        } else {
            $nome = $string;
        }
        return trim($nome);
    }

    public static function removeAcentos($palavra = '') {
        $palavra = ereg_replace("[^.|/:(a-zA-Z0-9_)]-", "", strtr($palavra, utf8_decode("áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ!"), "aaaaeeiooouucAAAAEEIOOOUUC"));
        return $palavra;
    }

    public static function substituiAcentos($string = '') {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?', ' + ', ' - ', ' º ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', ' mais ', ' menos ', ' graus ');
        return str_replace($a, $b, $string);
    }

    public static function titulolink($string, $complemento = NULL) {
        $string = preg_replace('/[\t\n]/', ' ', $string);
        $string = preg_replace('/\s{2,}/', ' ', $string);
        $list = array(
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
            '/' => '-',
            ' ' => '-',
            '.' => '-',
        );

        $string = strtr($string, $list);
        $string = preg_replace('/-{2,}/', '-', $string);
        $string = strtolower($string);

        if (!empty($complemento)) {
            return $string . $complemento;
        } else {
            return $string;
        }
    }

    public static function download($arquivo = '') {
        set_time_limit(0);
        if (file_exists($arquivo)) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($arquivo));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Expires: 0');
            readfile($arquivo);
        } else {
            echo 'Arquivo não encontrado.';
        }
    }

    public static function tratar_nome($nome) {
        $nome = explode(" ", strtolower($nome)); // Separa o nome por espaços
        for ($i = 0; $i < count($nome); $i++) {
            if ($nome[$i] == "de" or $nome[$i] == "da" or $nome[$i] == "e" or $nome[$i] == "dos" or $nome[$i] == "do") {
                $saida .= $nome[$i] . ' '; // Se a palavra estiver dentro das complementares mostrar toda em minÃƒÃ‚Âºsculo
            } else {
                $saida .= ucfirst($nome[$i]) . ' '; // Se for um nome, mostrar a primeira letra maiÃƒÃ‚Âºscula
            }
        }
        return $saida;
    }

    function validateEmail($email) {
        if (!ereg('^([a-zA-Z0-9.-_])*([@])([a-z0-9]).([a-z]{2,3})', $email)) {
            $rtn = false;
        } else {
            $dominio = explode('@', $email);
            if (!checkdnsrr($dominio[1], 'A')) {
                $rtn = false;
            } else {
                $rtn = true;
            }
        }
        return $rtn;
    }

    public static function getVimeo($video) {
        if (!empty($video)) {
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video.php"));
            return $hash[0];
        }
    }

    public static function getYoutube($video) {
        if (!empty($video)) {
            $hash = file_get_contents("http://youtube.com/get_video_info?video_id=$video");
            parse_str($hash, $arr);
            return $arr;
        }
    }

    public static function retorna_dados_video($link) {
        $youtube = preg_match('/(v=)([^&]+)/', $link, $matches);
        if (!$youtube) {
            $vimeo = preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $link, $matches);
            $retorna = self::getVimeo($matches[1]);
            $rtn['id'] = $retorna['id'];
            $rtn['thumb'] = $retorna['thumbnail_small'];
            $rtn['capa'] = $retorna['thumbnail_large'];
            $rtn['embed'] = 'http://player.vimeo.com/video/' . $retorna['id'];
            $rtn['status'] = 'ok';
            $rtn['reason'] = '';
        }
        if ($youtube) {
            $retorna = self::getYoutube($matches[2]);
            $rtn['id'] = $retorna['video_id'];
            $rtn['thumb'] = $retorna['thumbnail_url'];
            $rtn['capa'] = $retorna['iurl'];
            $rtn['embed'] = 'http://www.youtube.com/embed/' . $retorna['video_id'] . '?autoplay=0';
            $rtn['status'] = $retorna['status'];
            $rtn['reason'] = $retorna['reason'];
        }
        return $rtn;
    }

    public static function operaCalculoData($data, $formato, $tipo_operacao, $quantidade, $campo_operacao) {
        switch ($formato) {
            case 'eu':
                $split = explode('-', $data);
                $thisyear = $split[0];
                $thismonth = $split[1];
                $thisday = $split[2];
                if ($tipo_operacao == 'add') {
                    switch ($campo_operacao) {
                        case 'ano':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday, $thisyear + $quantidade);
                            break;
                        case 'mes':
                            $nextdate = mktime(0, 0, 0, $thismonth + $quantidade, $thisday, $thisyear);
                            break;
                        case 'dia':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday + $quantidade, $thisyear);
                            break;
                        default:
                            return FALSE;
                            break;
                    }
                } else {
                    switch ($campo_operacao) {
                        case 'ano':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday, $thisyear - $quantidade);
                            break;
                        case 'mes':
                            $nextdate = mktime(0, 0, 0, $thismonth - $quantidade, $thisday, $thisyear);
                            break;
                        case 'dia':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday - $quantidade, $thisyear);
                            break;
                        default:
                            return FALSE;
                            break;
                    }
                }
                $data_formatada = strftime("%Y-%m-%d", $nextdate);
                return $data_formatada;
                break;
            case 'br':
                $split = explode('/', $data);
                $thisyear = $split[2];
                $thismonth = $split[1];
                $thisday = $split[0];
                if ($tipo_operacao == 'add') {
                    switch ($campo_operacao) {
                        case 'ano':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday, $thisyear + $quantidade);
                            break;
                        case 'mes':
                            $nextdate = mktime(0, 0, 0, $thismonth + $quantidade, $thisday, $thisyear);
                            break;
                        case 'dia':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday + $quantidade, $thisyear);
                            break;
                        default:
                            return FALSE;
                            break;
                    }
                } else {
                    switch ($campo_operacao) {
                        case 'ano':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday, $thisyear - $quantidade);
                            break;
                        case 'mes':
                            $nextdate = mktime(0, 0, 0, $thismonth - $quantidade, $thisday, $thisyear);
                            break;
                        case 'dia':
                            $nextdate = mktime(0, 0, 0, $thismonth, $thisday - $quantidade, $thisyear);
                            break;
                        default:
                            return FALSE;
                            break;
                    }
                }
                $data_formatada = strftime("%d/%m/%Y", $nextdate);
                return $data_formatada;
                break;
            default:
                break;
        }
    }

    public static function getcoordenadas($address) {
        $address = urlencode(utf8_encode($address));
        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false');
        $output = json_decode($geocode);
        $lat = $output->results[0]->geometry->location->lat;
        $long = $output->results[0]->geometry->location->lng;

        return $lat . '|' . $long;
    }

    public static function parametroUrl($param = 'pg', $url = '') {
        if (empty($url)) {
            $url = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
        }
        $explode_url = explode('/', $url);
        $as = array_search($param, $explode_url);
        if (!empty($as)) {
            $rtn = $explode_url[$as + 1];
            return $rtn;
        }
    }

    public static function busca_endereco($cep) {
        $xml = simplexml_load_file('http://viacep.com.br/ws/' . $cep . '/xml/');

        if ($xml) {
            foreach ($xml as $id => $field) {
                $arr[$id] = (string) $xml->$id;
            }
            return $arr;
        } else {
            return false;
        }
    }

    public static function time_ago($time) {
        $now = strtotime(date('Y-m-d H:i:s'));
        $time = strtotime($time);
        $diff = $now - $time;

        $seconds = $diff;
        $minutes = round($diff / 60);
        $hours = round($diff / 3600);
        $days = round($diff / 86400);
        $weeks = round($diff / 604800);
        $months = round($diff / 2419200);
        $years = round($diff / 29030400);

        if ($seconds <= 60)
            return "1 min atrás";
        else if ($minutes <= 60)
            return $minutes == 1 ? '1 min atrás' : $minutes . ' min atrás';
        else if ($hours <= 24)
            return $hours == 1 ? '1 hrs atrás' : $hours . ' hrs atrás';
        else if ($days <= 7)
            return $days == 1 ? '1 dia atras' : $days . ' dias atrás';
        else if ($weeks <= 4)
            return $weeks == 1 ? '1 semana atrás' : $weeks . ' semanas atrás';
        else if ($months <= 12)
            return $months == 1 ? '1 mês atrás' : $months . ' meses atrás';
        else
            return $years == 1 ? 'um ano atrás' : $years . ' anos atrás';
    }

    public static function createFileOriginal($file, $pasta, $nome = '') {
        $rtn = false;
        if (!empty($file['name'])) {
            $explode = explode('.', $file['name']['arquivo']);
            $ext = strtolower(trim($explode[1]));
            if (empty($nome)) {
                $nome = self::titulolink($explode[0]) . '.' . $ext;
            } else {
                $nome = self::titulolink($nome) . '.' . $ext;
            }

            if (!file_exists($pasta)) {
                mkdir($pasta, 777);
            }

            if (!is_writable($pasta)) {
                chmod($pasta, 777);
            }
            if (is_writable($pasta)) {
                $pasta = $pasta . DIRECTORY_SEPARATOR;
                $aux = copy($file['tmp_name']['arquivo'], $pasta . $nome);
                if ($aux) {
                    $rtn = $nome;
                }
            }
        }

        return $rtn;
    }

    function checkIDWords($string) {
        return preg_match('/^[a-zA-Z0-9\d\.\-_]+$/', $string);
    }

    public static function getNomeStatus($id, $integrador = 'PagSeguro') {
        switch ($integrador) {
            case 'PagSeguro':
            default:
                $nameStatus = array(
                    'Aguardando pagamento' => 'Aguardando pagamento',
                    'Em análise' => 'Em análise',
                    'Paga' => 'Paga',
                    'Disponível' => 'Disponível',
                    'Em disputa' => 'Em disputa',
                    'Devolvida' => 'Devolvido',
                    'Cancelada' => 'Cancelada',
                    'em_transporte' => 'Em transporte',
                    'finalizada' => 'Pedido entregue',
                );
                break;
        }
        return $nameStatus[$id];
    }

    public static function status_pagseguro($id) {
        $id = (int) $id;
        $nameStatus = array(
            1 => 'Aguardando pagamento',
            2 => 'Em análise',
            3 => 'Paga',
            4 => 'Disponível',
            5 => 'Em disputa',
            6 => 'Devolvido',
            7 => 'Cancelada'
        );
        return $nameStatus[$id];
    }

    public static function getCorNomeStatus($id) {
        $nameStatus = array(
            'Aguardando pagamento' => 'label-default',
            'processing' => 'label-default',
            'authorized' => 'label-blue',
            'refunded' => 'label-warning',
            'waiting_payment' => 'label-cyan',
            'pending_refund' => 'label-danger',
            'refused' => 'label-danger',
            'not_authorized' => 'label-danger',
            'chargedback' => 'label-danger',
            'Aguardando pagamento' => 'label-cyan',
            'Em análise' => 'label-default',
            'Paga' => 'label-success',
            'Disponível' => 'label-success',
            'Em disputa' => 'label-warning',
            'Devolvida' => 'label-danger',
            'Cancelada' => 'label-danger',
            'em_transporte' => 'label-green',
            'finalizada' => 'label-slategray ',
        );
        return $nameStatus[$id];
    }

    public function redirect_operacoes($action = '', $msg = '', $tempo = 10, $p = '') {
        $tpl = $this->getTemplate('view/tpl.redirect.htm');

        $tpl->Set('msg', $msg);
        $tpl->Set('url', str_replace('index.php', '', $_SERVER['PHP_SELF']) . $action);
        $tpl->Set('time', $tempo);
        $tpl->set('tempo', $tempo);
        $tpl->set('icone', 'warning');

        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        $this->showHeader($header);
        $this->showBody($body);
        exit();
    }

    public static function formataPreco($var, $modo = 0) {
        $rtn = '';
        if (!empty($var)) {
            switch ($modo) {
                case 1:
                    $exp = explode('.', $var);
                    if (empty($exp[1])) {
                        $final = ',00';
                    } else {
                        if (strlen($exp[1]) == 1) {
                            $final = ',' . $exp[1] . '0';
                        } else {
                            $final = ',' . $exp[1];
                        }
                    }
                    break;

                default:
                    $var = str_replace(',', '.', (str_replace('.', '', $var)));
                    $exp = explode('.', $var);
                    if (empty($exp[1])) {
                        $final = '.00';
                    } else {
                        $final = '.' . $exp[1];
                    }
                    break;
            }

            if (empty($exp[0])) {
                $inicio = '0';
            } else {
                $inicio = $exp[0];
            }

            $rtn = ($inicio . $final);
        }

        return $rtn;
    }

    public static function get_cor_ativo($status) {
        $nameStatus = array(
            'Y' => 'success',
            'N' => 'danger',
            'P' => 'warning',
        );
        return $nameStatus[$status];
    }

    public static function get_cor_restrito($status) {
        $nameStatus = array(
            'Y' => 'danger',
            'N' => 'success',
        );
        return $nameStatus[$status];
    }

    public static function rtnDimensoes($original = FALSE) {
        $arr = [
            '70x98_fill_',
            '70x98_crop_',
            '75x75_fill_',
            '75x75_crop_',
            '90x80_fill_',
            '90x80_crop_',
            '138x108_fill_',
            '138x108_crop_',
            '240x140_crop_',
            '240x140_fill_',
            '250x180_crop_',
            '250x180_fill_',
            '375x248_fill_',
            '375x248_crop_',
            '455x300_fill_',
            '455x300_crop_',
            '500x500_fill_',
            '500x500_crop_',
            '770x440_crop_',
            '770x440_fill_',
            '800x600_fill_',
            '800x600_crop_',
            '1000x1000_fill_',
            '1000x1000_crop_',
        ];
        if ($original == TRUE) {
            $arr[] = '';
        }

        return $arr;
    }

    public static function ajaxLimpaTemp() {
        if (isset($_SESSION['imagens_tmp'])) {
            foreach ($_SESSION['imagens_tmp'] as $img) {
                @unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . $img);
            }
            unset($_SESSION['imagens_tmp']);
        }
    }

    /**
     * @param $pass
     * @return false|int
     */
    public static function passwordVerify($pass) {
        /*
         * A string precisa:
         *   Conter entre 8 e 16 caracteres;
         *   Letras e números;
         *   Caracteres especiais são permitidos;
         *   Exemplo de senha permitida: c3N!@#$%¨&*()_+
         */

        $regex = "/^(?=^.{8,16}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[a-z]).*$/";
        $rtn = preg_match($regex, $pass);
        return $rtn;
    }

    /**
     * @param $time
     * @param null $horario
     * @return string
     */
    public static function received_date($time, $horario = NULL) {
        $data = $time;
        $time = strtotime(str_replace('-', '/', $time));
        $diff = time() - $time;
        $seconds = $diff;
        $minutes = round($diff / 60);
        $hours = round($diff / 3600);
        $days = round($diff / 86400);
        $weeks = round($diff / 604800);
        $months = round($diff / 2419200);
        $years = round($diff / 29030400);
        if ($seconds <= 60) {
            
        } else if ($hours <= 24) {
            if ($horario) {
                $rtn = strtoupper(date('g:i a', strtotime($horario)));
            } else {
                $rtn = $hours == 1 ? 'a 1 hora' : $hours . ' horas atrás';
            }
        } else if ($days <= 7) {
            $rtn = $days == 1 ? 'a 1 dia' : $days . ' dias atrás';
        } else if ($weeks <= 4) {
            // $rtn = $weeks == 1 ? 'a 1 semana' : $weeks . ' semanas atrás';
            $aux = explode('-', $data);
            $loadMes = uteis::loadMes($aux[1]);
            $rtn = (int) $aux[1] . ' de ' . uteis::limitaString(strtolower($loadMes), 3, NULL);
        } else if ($months <= 12) {
            // $rtn = $months == 1 ? 'a 1 mes' : $months . ' meses atrás';
            $aux = explode('-', $data);
            $loadMes = uteis::loadMes($aux[1]);
            $rtn = (int) $aux[1] . ' de ' . uteis::limitaString(strtolower($loadMes), 3, NULL);
        } else {
            $rtn = $years == 1 ? 'a 1 ano' : $years . ' anos atrás';
        }
        return $rtn;
    }

    /**
     * @param $string
     * @param string $param
     * @return string
     */
    public static function slugify($string, $param = '-') {
        $slugify = new Slugify();
        return $slugify->slugify($string, $param);
    }

    /**
     * Get user data
     */
    public static function getDeviceData($HTTP_USER_AGENT) {
        $user_agents = array("iPhone", "iPad", "Android", "webOS", "BlackBerry", "iPod", "Symbian", "IsGeneric");
        $modelo = 'PC';
        foreach ($user_agents as $user_agent) {
            if (strpos($HTTP_USER_AGENT, $user_agent) !== FALSE) {
                // $mobile = TRUE;
                $modelo = $user_agent;
                break;
            }
        }

        return $modelo;
    }
}
