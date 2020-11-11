<?php
namespace System;

class Sql
{

    public function __construct($servidor = '', $usuario = '', $senha = '', $bd = '')
    { //Conecta servidor SQL
        $this->conexao = @mysqli_connect($servidor, $usuario, $senha) or die('Erro 1');
        $this->db_bd = $bd;
        @mysqli_select_db($this->conexao, $bd) or die('Erro 2');
        mysqli_query($this->conexao, "SET NAMES 'utf8'");
        mysqli_query($this->conexao, 'SET character_set_connection=utf8');
        mysqli_query($this->conexao, 'SET character_set_client=utf8');
        mysqli_query($this->conexao, 'SET character_set_results=utf8');
        return $this->conexao;
    }

    public function createSQL($VARS, $tabela)
    {
        $cols = $valor = '';
        foreach ($VARS as $id => $val) {
            if ($val != '') {
                $cols .= "$id, ";
                $valor .= "'" . mysqli_real_escape_string($this->conexao, $val) . "', ";
            }
        }
        $cols = trim($cols);
        $valor = trim($valor);
        $cols = substr($cols, 0, strlen($cols) - 1);
        $valor = substr($valor, 0, strlen($valor) - 1);
        $this->query("INSERT INTO $tabela($cols) VALUES($valor)");
    }

    public function updateSQL($VARS, $tabela, $primary)
    {
        $valor = null;
        foreach ($VARS as $id => $val) {
            if ($id != '') {
                $valor .= "$id='" . mysqli_real_escape_string($this->conexao, $val) . "', ";
            }
        }
        $valor = trim($valor);
        $valor = substr($valor, 0, strlen($valor) - 1);
        $this->query("UPDATE $tabela SET $valor WHERE $primary='" . $VARS[$primary] . "'");
    }

    public function loadSQL($table, $primaryField, $primaryValue)
    {
        $consulta = $this->query("SELECT * FROM $table WHERE $primaryField='$primaryValue'");
        $valor = $this->fetch_array($consulta);
        return $valor;
    }

    public function removeSQL($table, $primaryField, $primaryValue)
    {
        $this->query("DELETE FROM $table WHERE $primaryField='$primaryValue'");
    }

    public function erro($erro, $coderro)
    {
        switch ($erro) {
            case 'conexao':
                $msg = 'Falha na conex&atilde;o com o host.<br>Aguarde e tente novamente mais tarde. <strong>' . $coderro . '</strong>';
                break;
            case 'consulta':
                $msg = 'Erro de acesso ao BD: <strong>' . $coderro . '</strong>';
                break;
            case 'selectDb':
                $msg = 'Conex&atilde;o &agrave; tabela. <strong>' . $coderro . '</strong>';
                break;
        }
        $html = '<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <title>Que coisa, não???</title>
            <meta name="description" content="General Error">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
            <meta name="apple-mobile-web-app-capable" content="yes" />
            <meta name="msapplication-tap-highlight" content="no">
            <link rel="stylesheet" media="screen, print" href="' . SERVER . '/assets/css/vendors.bundle.css">
            <link rel="stylesheet" media="screen, print" href="' . SERVER . '/assets/css/app.bundle.css">
            <link rel="apple-touch-icon" sizes="180x180" href="' . SERVER . '/assets/img/favicon/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="' . SERVER . '/assets/img/favicon/favicon-32x32.png">
            <link rel="mask-icon" href="' . SERVER . '/assets/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        </head>

        <body class="mod-bg-1 ">
            <div class="page-wrapper">
                <div class="page-inner">
                    <div class="page-content-wrapper">
                        <main id="js-page-content" role="main" class="page-content">
                            <div class="h-alt-hf d-flex flex-column align-items-center justify-content-center text-center">
                                <div class="alert alert-danger bg-white pt-4 pr-5 pb-3 pl-5" id="demo-alert">
                                    <h1 class="fs-xxl fw-700 color-fusion-500 d-flex align-items-center justify-content-center m-0">
                                        <span class="color-danger-700">root@root:~$ Errar é humano; Esquecer é divino.</span>
                                    </h1>
                                    <h3 class="fw-500 mb-0 mt-3">
                                        Você encontrou um erro técnico.
                                    </h3>
                                    <p class="container container-sm mb-0 mt-1">
                                        ' . $msg . '
                                    </p>
                                    <div class="mt-4">
                                        <a href="' . SERVER . '" class="btn btn-primary">
                                            <span class="fw-700">Retornar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </main>
                        <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->

                    </div>
                </div>
            </div>
        </body>
        </html>';
        echo $html;
        exit;
    }

    public function query($query)
    {
        $this->ultimaquery = @mysqli_query($this->conexao, $query) or die("Ocorreu o seguinte erro: " . mysqli_error($this->conexao));
        return $this->ultimaquery ? $this->ultimaquery : 0;
    }

    public function rows($query = '')
    { //Total de linhas
        $consulta = !empty($query) ? $query : $this->ultimaquery;
        return @mysqli_num_rows($consulta);
    }

    public function fetch_object($consulta = '')
    { //Fetch object
        $consulta = !empty($consulta) ? $consulta : $this->ultimaquery;
        $object = @mysqli_fetch_object($consulta);
        return $object;
    }

    public function fetch_array($consulta = '')
    { //Fetch array
        $consulta = !empty($consulta) ? $consulta : $this->ultimaquery;
        $arr = @mysqli_fetch_array($consulta);
        return $arr;
    }

    public function close()
    {
        @mysqli_close($this->conexao);
    }

    //EXTRAS
    public function getId()
    {
        return @mysqli_insert_id($this->conexao);
    }

    public function affected_rows()
    {
        return @mysqli_affected_rows($this->conexao);
    }
}
