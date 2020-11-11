<?php

class Idioma {

    public static function language ( $idioma = "pt_BR", $html = '' ) {
        $arquivo = 'system' . DIRECTORY_SEPARATOR . 'idioma' . DIRECTORY_SEPARATOR . $idioma . '.lang';
        if ( ! file_exists ( $arquivo ) | filesize ( $arquivo ) <= 0 ) {
            $arquivo = 'system' . DIRECTORY_SEPARATOR . 'idioma' . DIRECTORY_SEPARATOR . 'pt_br.lang';
        }

        if ( file_exists ( $arquivo ) ) {
            $handle = fopen ( $arquivo, "r" );
            $contents = fread ( $handle, filesize ( $arquivo ) );
            $arrLista = split ( "\n", trim ( $contents ) );

            $campo = Array ();
            $campoTraducao = Array ();

            if ( count ( $arrLista ) > 0 ) {
                foreach ( $arrLista as $var ) {
                    $tmp = explode ( "#", $var );
                    $campo[] = '{' . $tmp[ 0 ] . '}';
                    $campoTraducao[] = trim ( $tmp[ 1 ] );
                }
            }

            $traducao = str_replace ( $campo, $campoTraducao, $html );
            return $traducao;
        }
    }

}