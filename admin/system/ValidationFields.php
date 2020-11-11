<?php
namespace System;

class ValidationFields {

    var $fields = array ();
    var $messages = array ();
    var $check_4html = false;
    var $language;

    function __construct() {

    }

    function ValidationFields ( $lg = 'pt_BR' ) {
        $this -> language = $lg;
        $this -> create_msg ();
    }

    function validation () {
        $status = 0;
        foreach ( $this -> fields as $key => $val ) {
            switch ( $val[ 'type' ] ) {
                case "email":
                    if ( ! $this -> check_email ( $val[ 'value' ], $key, $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "number":
                    if ( ! $this -> check_num_val ( $val[ 'value' ], $key, $val[ 'length' ], $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "decimal":
                    if ( ! $this -> check_decimal ( $val[ 'value' ], $key, $val[ 'decimals' ], $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "date":
                    if ( ! $this -> check_date ( $val[ 'value' ], $key, $val[ 'version' ], $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "url":
                    if ( ! $this -> check_url ( $val[ 'value' ], $key, $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "text":
                    if ( ! $this -> check_text ( $val[ 'value' ], $key, $val[ 'length' ], $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "senha":
                    if ( ! $this -> check_senha ( $val[ 'senha1' ], $val[ 'senha2' ], $val[ 'required' ] ) ) {
                        $status ++;
                    }
                    break;
                case "checkbox":
                case "radio":
                    if ( ! $this -> check_check_box ( $val[ 'value' ], $key, $val[ 'element' ] ) ) {
                        $status ++;
                    }
            }
            if ( $this -> check_4html ) {
                if ( ! $this -> check_html_tags ( $val[ 'value' ], $key ) ) {
                    $status ++;
                }
            }
        }
        if ( $status == 0 ) {
            return true;
        } else {
            //$this->messages[] = $this->error_text(0);
            return false;
        }
    }

    function check_senha ( $senha1, $senha2, $valReq ) {
        if ( $senha1 != $senha2 ) {
            $field = "Senhas";
            $this -> messages[] = $this -> error_text ( 16, $field );
            return false;
        } else {
            return true;
        }
    }

    function add_text_field ( $name, $val, $type = "text", $required = "y", $length = 0 ) {
        $this -> fields[ $name ][ 'value' ] = trim ( $val );
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'required' ] = $required;
        $this -> fields[ $name ][ 'length' ] = $length;
    }

    function add_num_field ( $name, $val, $type = "number", $required = "y", $decimals = 0, $length = 0 ) {
        $this -> fields[ $name ][ 'value' ] = trim ( $val );
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'required' ] = $required;
        $this -> fields[ $name ][ 'decimals' ] = $decimals;
        $this -> fields[ $name ][ 'length' ] = $length;
    }

    function add_link_field ( $name, $val, $type = "email", $required = "y" ) {
        $this -> fields[ $name ][ 'value' ] = trim ( $val );
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'required' ] = $required;
    }

    function add_senha_field ( $name, $senha1, $senha2, $type = "senha", $required = "y" ) {
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'required' ] = $required;
        $this -> fields[ $name ][ 'senha1' ] = $senha1;
        $this -> fields[ $name ][ 'senha2' ] = $senha2;
    }

    function add_date_field ( $name, $val, $type = "date", $version = "us", $required = "y" ) {
        $this -> fields[ $name ][ 'value' ] = trim ( $val );
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'version' ] = $version;
        $this -> fields[ $name ][ 'required' ] = $required;
    }

    function add_check_box ( $name, $element_name, $type = "checkbox", $required_value = "" ) {
        $this -> fields[ $name ][ 'value' ] = $required_value;
        $this -> fields[ $name ][ 'type' ] = $type;
        $this -> fields[ $name ][ 'element' ] = $element_name;
    }

    function check_url ( $url_val, $field, $req = "y" ) {
        if ( $url_val == "" ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true;
            }
        } else {
            $url_pattern = "http\:\/\/[[:alnum:]\-\.]+(\.[[:alpha:]]{2,4})+";
            $url_pattern .= "(\/[\w\-]+)*"; // folders like /val_1/45/
            $url_pattern .= "((\/[\w\-\.]+\.[[:alnum:]]{2,4})?"; // filename like index.html
            $url_pattern .= "|"; // end with filename or ?
            $url_pattern .= "\/?)"; // trailing slash or not
            $error_count = 0;
            if ( strpos ( $url_val, "?" ) ) {
                $url_parts = explode ( "?", $url_val );
                if ( ! preg_match ( "/^" . $url_pattern . "$/", $url_parts[ 0 ] ) ) {
                    $error_count ++;
                }
                if ( ! preg_match ( "/^(&?[\w\-]+=\w*)+$/", $url_parts[ 1 ] ) ) {
                    $error_count ++;
                }
            } else {
                if ( ! preg_match ( "/^" . $url_pattern . "$/", $url_val ) ) {
                    $error_count ++;
                }
            }
            if ( $error_count > 0 ) {
                $this -> messages[] = $this -> error_text ( 14, $field );
                return false;
            } else {
                return true;
            }
        }
    }

    function check_num_val ( $num_val, $field, $num_len = 0, $req = "n" ) {
        if ( $num_val == "" ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true;
            }
        } else {
            $pattern = ($num_len == 0) ? "/^\-?[0-9]*$/" : "/^\-?[0-9]{0," . $num_len . "}$/";
            if ( preg_match ( $pattern, $num_val ) ) {
                return true;
            } else {
                $this -> messages[] = $this -> error_text ( 12, $field );
                return false;
            }
        }
    }

    function check_text ( $text_val, $field, $text_len = 0, $req = "y" ) {
        if ( empty ( $text_val ) ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true; // in case only the text length is validated
            }
        } else {
            if ( $text_len > 0 ) {
                if ( strlen ( $text_val ) > $text_len ) {
                    $this -> messages[] = $this -> error_text ( 13, $field );
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    function check_check_box ( $req_value, $field, $element ) {
        if ( empty ( $_REQUEST[ 'form' ][ $element ] ) ) {
            $this -> messages[] = $this -> error_text ( 12, $field );
            return false;
        } else {
            if ( ! empty ( $req_value ) ) {
                if ( $req_value != $_REQUEST[ $element ] ) {
                    $this -> messages[] = $this -> error_text ( 12, $field );
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    function check_decimal ( $dec_val, $field, $decimals = 2, $req = "n" ) {
        if ( $dec_val == "" ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true;
            }
        } else {
            $pattern = "/^[-]*[0-9][0-9]*\,[0-9]{" . $decimals . "}$/";
            if ( preg_match ( $pattern, $dec_val ) ) {
                return true;
            } else {
                $this -> messages[] = $this -> error_text ( 12, $field );
                return false;
            }
        }
    }

    function check_date ( $date, $field, $version = "us", $req = "n" ) {
        if ( $date == "" ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true;
            }
        } else {
            $date_parts = explode ( "/", $date );
            $month = $date_parts[ 1 ];
            if ( $version == 'eu' ) {
                $pattern = "/^(0?[1-9]|[1-2][0-9]|3[0-1])[\/](0?[1-9]|1[0-2])[\/](19|20)[0-9]{2}$/";
                $day = $date_parts[ 0 ];
                $year = $date_parts[ 2 ];
            } else if ( $version == 'iso' ) {
                $pattern = "/^(19|20)[0-9]{2}[\/](0?[1-9]|1[0-2])[\/](0?[1-9]|[1-2][0-9]|3[0-1])$/";
                $day = $date_parts[ 2 ];
                $year = $date_parts[ 0 ];
            } else if ( $version == 'us' ) {
                $pattern = "/^(0?[1-9]|1[0-2])[\/](0?[1-9]|[1-2][0-9]|3[0-1])[\/](19|20)[0-9]{2}$/";
                $month = $date_parts[ 0 ];
                $day = $date_parts[ 1 ];
                $year = $date_parts[ 2 ];
            }
            if ( preg_match ( $pattern, $date ) && checkdate ( intval ( $month ), intval ( $day ), $year ) ) {
                return true;
            } else {
                $this -> messages[] = $this -> error_text ( 10, $field );
                return false;
            }
        }
    }

    function check_email ( $mail_address, $field, $req = "y" ) {
        if ( $mail_address == "" ) {
            if ( $req == "y" ) {
                $this -> messages[] = $this -> error_text ( 1, $field );
                return false;
            } else {
                return true;
            }
        } else {
            if ( preg_match ( "/^[0-9a-z]+(([\.\-_])[0-9a-z]+)*@[0-9a-z]+(([\.\-])[0-9a-z-]+)*\.[a-z]{2,4}$/i", $mail_address ) ) {
                return true;
            } else {
                $this -> messages[] = $this -> error_text ( 11, $field );
                return false;
            }
        }
    }

    function check_html_tags ( $value, $field ) {
        if ( preg_match ( "/<[a-z]+(\s[a-z]{2,}=['\"]?(.*)['\"]?)+(\s?\/)?>(<\/[a-z]>)?/i", $value ) ) {
            $this -> messages[] = $this -> error_text ( 15, $field );
            return false;
        } else {
            return true;
        }
    }

    function create_msg ( $break_elem = "<br />" ) {
        $the_msg = array ();
        ksort ( $this -> messages ); // modified in 1.35
        reset ( $this -> messages );
        foreach ( $this -> messages as $value ) {
            $the_msg[] = $value;
        }
        return $the_msg;
    }

    function error_text ( $num, $fieldname = "" ) {
        $fieldname = str_replace ( '_', ' ', $fieldname );
        switch ( $this -> language ) {
            case 'en-us':
            case 'en_US':
            case 'us':
                $msg[ 0 ] = 'Please correct the following error(s):';
                $msg[ 1 ] = 'The field <strong>' . $fieldname . '</strong> is empty.';
                $msg[ 10 ] = 'The date in field <strong>' . $fieldname . '</strong> is not valid.';
                $msg[ 11 ] = 'The e-mail address in field <strong>' . $fieldname . '</strong> is not valid.';
                $msg[ 12 ] = 'The value in field <strong>' . $fieldname . '</strong> is not valid.';
                $msg[ 13 ] = 'The text in field <strong>' . $fieldname . '</strong> is too long.';
                $msg[ 14 ] = 'The url in field <strong>' . $fieldname . '</strong> is not valid.';
                $msg[ 15 ] = 'There is html code in field <strong>' . $fieldname . '</strong>, this is not allowed.';
                $msg[ 16 ] = '<strong>' . $fieldname . '</strong> does not matches.';
                break;
            case "es-es":
            case "es_ES":
            case "es":
                $msg[ 0 ] = 'Por favor corrija los siguientes errores:';
                $msg[ 1 ] = 'El campo <strong>' . $fieldname . '</strong> está vacío.';
                $msg[ 10 ] = 'La fecha del campo <strong>' . $fieldname . '</strong> no es válida.';
                $msg[ 11 ] = 'La dirección de correo electrónico del campo <strong>' . $fieldname . '</strong> no es válida.';
                $msg[ 12 ] = 'El valor en el campo <strong>' . $fieldname . '</strong> no es válido.';
                $msg[ 13 ] = 'El texto en el campo <strong>' . $fieldname . '</strong> es demasiado largo.';
                $msg[ 14 ] = 'La URL en el campo <strong>' . $fieldname . '</strong> no es válida.';
                $msg[ 15 ] = 'Hay código HTML en el campo <strong>' . $fieldname . '</strong>, esto no está permitido.';
                $msg[ 16 ] = 'As <strong>' . $fieldname . '</strong> no coinciden.';
                break;
            default:
                $msg[ 0 ] = 'Por favor, verifique as seguintes informações:';
                $msg[ 1 ] = 'O campo <strong>' . $fieldname . '</strong> é obrigatório.';
                $msg[ 10 ] = 'A data no campo <strong>' . $fieldname . '</strong> é inválida.';
                $msg[ 11 ] = 'O <strong>' . $fieldname . '</strong> é inválido.';
                $msg[ 12 ] = 'O valor no campo <strong>' . $fieldname . '</strong> é inválido.';
                $msg[ 13 ] = 'O texto no campo <strong>' . $fieldname . '</strong> É muito longo.';
                $msg[ 14 ] = 'A url no campo <strong>' . $fieldname . '</strong> é inválido.';
                $msg[ 15 ] = 'Há código HTML no campo <strong>' . $fieldname . '</strong>, que não são permitidos.';
                $msg[ 16 ] = 'As <strong>' . $fieldname . '</strong> não conferem.';
                break;
        }
        return $msg[ $num ];
    }

}

?>
