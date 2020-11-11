<?php

namespace System;

class UploadArquivo {

    private static $path;
    private static $pathSeparator = DIRECTORY_SEPARATOR;
    private static $arrArquivo = Array ();
    private static $arrErro = Array ();
    private static $prefix;

    public static function upload ( $files, $path, $prefix = '' ) {
        self::$path = null;
        self::$arrArquivo = Array ();
        self::$arrErro = Array ();
        self::$prefix = null;

        $arrRtn = Array ();
        self::$path = $path . self::$pathSeparator;
        self::$prefix = $prefix;
        self::vrfPastaExiste ( $path );
        $files = self::formataDadosEntrada ( $files );
        $files = self::removeArquivoVazio ( $files );
        self::copiarArquivoArray ( $files );
        return self::$arrArquivo;
    }

    public static function validateArquivo ( $files, $regex, $size, $obrigatorio = 1 ) {
        $files = self::formataDadosEntrada ( $files );

        self::validaEnvioBrowser ( $files );

        if ( ! empty ( $regex ) ) {
            self::validateExtensaoArquivo ( $files, $regex );
        }

        if ( ! empty ( $size ) ) {
            self::validateTamanhoArquivo ( $files, $size );
        }

        if ( $obrigatorio ) {
            self::validateObrigatorio ( $files );
        }
        return self::$arrErro;
    }

    private static function formataDadosEntrada ( $files ) {
        if ( is_array ( $files[ 'name' ] ) ) {
            $file = self::transformeArray ( $files );
        } else {
            $file = $files;
        }
        return $file;
    }

    private static function copiarArquivo ( $arquivo, $name ) {
        $rtn = false;
        if ( ! empty ( $arquivo ) ) {
            $rtn = copy ( $arquivo, self::$path . $name );
        }
        return $rtn;
    }

    private static function removerArquivo ( $arquivo ) {
        $rtn = false;
        if ( ! empty ( $arquivo ) ) {
            $rtn = unlink ( self::$path . $arquivo );
        }
        return $rtn;
    }

    private static function copiarArquivoArray ( $files ) {
        foreach ( $files as $fl ) {
            $nomeF = self::nomeUnicoArquivo ( $fl[ 'name' ] );
            $cp = self::copiarArquivo ( $fl[ 'tmp_name' ], $nomeF );
            if ( $cp && is_file ( self::$path . $nomeF ) ) {
                $fl[ 'name' ] = self::formataNomeReal ( $fl[ 'name' ] );
                self::$arrArquivo[] = Array ( 'nomeOriginal' => $fl[ 'name' ], 'nomeFormatado' => $nomeF, 'size' => $fl[ 'size' ] );
            } else {
                self::erroCopiar ( $fl );
            }
        }
    }

    private static function formataNomeReal ( $name, $lmt = 90 ) {
        $str = '';
        $lmt = $lmt - 4; //removendo 4 caracters da extens&atilde;o do arquivo
        if ( strlen ( $name ) > ($lmt) ) {
            $ext = self::getExtensao ( $name );
            $name = substr ( $name, 0, $lmt );
            print $name;
            $str = $name . '.' . $ext;
        } else {
            $str = $name;
        }
        return $str;
    }

    private static function removeArquivoArray ( $files ) {
        if ( ! empty ( $files ) ) {
            foreach ( $files as $fl ) {
                self::removerArquivo ( $fl[ 'nomeFormatado' ] );
            }
        }
    }

    private static function erroCopiar ( $fl ) {
        //removendo todos os arquivos j&atilde; copiados
        self::removeArquivoArray ( self::$arrArquivo );
        self::$arrArquivo = false;
    }

    private static function nomeUnicoArquivo ( $nome ) {
        return uniqid ( self::$prefix ) . '.' . self::getExtensao ( $nome );
    }

    private static function getExtensao ( $nome ) {
        return strtolower ( array_pop ( explode ( '.', $nome ) ) );
    }

    private static function transformeArray ( $files ) {
        $arr = Array ();
        if ( is_array ( $files ) ) {
            foreach ( $files as $i => $at ) {
                $c = 0;
                foreach ( $files[ $i ] as $var ) {
                    $arr[ $c ++ ][ $i ] = $var;
                }
            }
        }
        return $arr;
    }

    private static function removeArquivoVazio ( $files ) {
        $arrRtn = Array ();
        foreach ( $files as $arr ) {
            if ( ! empty ( $arr[ 'name' ] ) ) {
                $arrRtn[] = $arr;
            }
        }
        return $arrRtn;
    }

    private static function validaEnvioBrowser ( $file ) {
        $arrRtn = Array ();
        foreach ( $file as $fl ) {
            if ( $fl[ 'error' ] > 0 && ! empty ( $fl[ 'name' ] ) ) {
                $msg = self::getMsgErroEnvioBrowser ( $fl[ 'name' ], $fl[ 'error' ] );
                self::addMsgErro ( $msg );
            }
        }
        return $arrRtn;
    }

    private static function getMsgErroEnvioBrowser ( $nome, $erro ) {
        $msg = '';
        switch ( $erro ) {
            case 1:
                $msg = 'O arquivo <strong>' . $nome . '</strong> &eacute; maior do que o limite de upload suportado pelo servidor.';
                break;
            case 2:
                $msg = 'O arquivo <strong>' . $nome . '</strong> ultrapassa o limite de mem&oacute;ria do servidor.';
                break;
            case 3:
                $msg = 'O upload do arquivo <strong>' . $nome . '</strong> foi feito parcialmente.';
                break;
            case 4:
                $msg = 'N&atilde;o foi feito o upload do arquivo <strong>' . $nome . '</strong>.';
                break;
        }
        return $msg;
    }

    private static function validateExtensaoArquivo ( $file, $regex ) {
        foreach ( $file as $fl ) {
            if ( ! empty ( $fl[ 'name' ] ) ) {

                $ext = self::getExtensao ( $fl[ 'name' ] );

                if ( preg_match ( $regex, $ext ) === 0 ) {
                    $msg = 'A extens&atilde;o do arquivo <strong>' . $fl[ 'name' ] . '</strong> n&atilde;o &eacute; aceita.';
                    self::addMsgErro ( $msg );
                }
            }
        }
    }

    private static function validateTamanhoArquivo ( $file, $size ) {
        foreach ( $file as $fl ) {
            if ( $fl[ 'size' ] > $size ) {
                $msg = 'O arquivo <strong>' . $fl[ 'name' ] . '</strong>, com tamanho <strong>' . self::formataTamanho ( $fl[ 'size' ] ) . '</strong>,  maior que o permitido, <strong>' . self::formataTamanho ( $size ) . '</strong>.';
                self::addMsgErro ( $msg );
            }
        }
    }

    private static function validateObrigatorio ( $file ) {
        foreach ( $file as $fl ) {
            if ( empty ( $fl[ 'name' ] ) ) {
                self::addMsgErro ( 'Est&aacute; faltando um <strong>arquivo obrigat&oacute;rio</strong>.' );
            }
        }
    }

    private static function addMsgErro ( $msg ) {
        self::$arrErro[] = $msg;
    }

    private static function formataTamanho ( $size ) {
        $sigla = array ( 'bytes', 'KB', 'MB', 'GB', 'TB', 'PB' );
        $i = 0;
        while ( $parar == false ) {
            if ( $size >= 1024 ) {
                $size = ( int ) $size / 1024;
                $i ++;
            } else {
                $parar = true;
            }
        }
        $size = number_format ( $size, '2', ',', '.' );
        $size = $size . " " . $sigla[ $i ];
        return $size;
    }

    private static function vrfPastaExiste ( $path ) {
        if ( ! is_writable ( $path ) ) {
            mkdir ( $path, '0777', true );
        }
    }

}

?>