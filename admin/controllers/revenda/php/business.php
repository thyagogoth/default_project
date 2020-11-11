<?php
use System\MasterBusiness;
use System\Uteis;
use System\ValidationFields;
// use System\Helpers\Mail;
// use System\UploadArquivo;

class RevendaBusiness extends MasterBusiness {

    public static function getPaginacao($mode = '') {
        if (isset($mode) && $mode !== 'default') {
            return self::$pgn->paginas();
        } else {
            return self::$pgn->paginasDefault();
        }
    }

    public static function getTotalPaginacao() {
        return self::$pgn->getTotal();
    }

    public static function getPaginacaoCentral() {
        $aux = parent::$pgn->getInfoPgn();
        if ($aux['paginacao']) {
            $aux['paginacao'] = preg_replace('/&(?!amp;|nbsp;)/', '&amp;', $aux['paginacao']);
        }
        return $aux;
    }

    /**
     * Lista todos os registros cadastrados na tabela NOTICIA
     */
    public static function findAllRevenda($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        if (is_array($arr)) {
            if (isset($arr['busca'])) {
                $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
                $i = 1;
                foreach ($termos as $item) {
                    if (strlen($item) > 2) {
                        $termo = Uteis::space2like($item);
                        if ($i < 2) {
                            $query = "AND (a.titulo LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.titulo LIKE '" . $termo . "') ";
                        }
                    }
                    $i++;
                }
            }

            if (isset($arr['no_cod'])) {
                if (is_array($arr['no_cod'])) {
                    foreach ($arr['no_cod'] as $noCod) {
                        $query .= "AND a.id <> '" . $noCod . "' ";
                    }
                } else {
                    $query .= "AND a.id <> '" . $arr['no_cod'] . "' ";
                }
            }

            if (isset($arr['item'])) {
                $n_de_resultados = $arr['item'];
            }
        }

        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.* " . Uteis::dtSql() . " FROM revenda a " . $query . " ORDER BY " . $campo . ' ' . $ord;
        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    /**
     * Select registro da tabela $tabela mediante parâmetro $value
     */
    public static function loadFromTabelaByCampo($tabela = "revenda", $field = 'id', $value) {
        if ($tabela == "revenda") {
            $subQuery = "";
            $join = "LEFT JOIN contratos c ON c.revenda = a.id LEFT JOIN planos p ON p.id = c.plano";
            // $subQuery = "p.titulo as nome_plano, ";
            // $join = "JOIN planos p ON p.id = a.plano";
        }
        $query = "SELECT a.*, {$subQuery}
			date_format(a.created_at, '%d/%m/%Y') AS created_at_formatada,
			date_format(a.created_at, '%H:%i') AS hora_cadastro_formatada,
			date_format(a.updated_at, '%d/%m/%Y') AS updated_at_formatada,
			date_format(a.updated_at, '%H:%i') AS hora_atualizada_formatada
            FROM {$tabela} a {$join} WHERE a.$field = '" . $value . "'";
            
        return parent::fetchArray($query);
    }

    /**
     * Validação de preenchimento de campos
     */
    public static function validateRevenda($post, $file) {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Título', $post['nome'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    /**
     * Insere o registro $post na tabela revenda
     */
    public static function createRevenda($post, $logotipo = '') {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'revenda');
        return $id;
    }

    /**
     * Atualiza o registro $id na tabela revenda com os dados contidos em $post
     */
    public static function updateRevenda($post, $logotipo = '', $usuario = '') {
        $tabela = 'revenda';
        if (!empty($usuario)) {
            $busca = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $busca);
        }
        parent::update($post, $tabela);
        return $post['id'];
    }

    public static function ordenaRevenda($id, $ord, $ordem) {
        $arrEmp = self::findAllRevenda('true', array(), 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query(" UPDATE revenda SET  ordem = '" . $varEmp['ordem'] . "'  WHERE id = '" . $id . "'");
                    parent::query("UPDATE revenda   SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE revenda SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE revenda SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllRevenda('true', array(), 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE revenda SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'  ");
            }
        }
    }

    /**
     * Remove o registro $id da tabela revenda
     * Remove as imagens cadastradas associadas à $id
     */
    public static function removeRevenda($id) {
        $item = self::loadFromTabelaByCampo('revenda', 'id', $id);
        parent::remove($id, 'revenda');
    }

    public static function removeFile($nome, $pasta = '') {
        if ($pasta) {
            $pasta = DIRECTORY_SEPARATOR . $pasta;
        }
        $file = UPLOAD_DIR . $pasta . DIRECTORY_SEPARATOR . $nome;
        if (!empty($file)) {
            $dimensoes = Uteis::rtnDimensoes(TRUE);
            foreach ($dimensoes as $arq) {
                $file = UPLOAD_DIR . $pasta . DIRECTORY_SEPARATOR . $arq . $nome;
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
            parent::remove($item['id'], 'revenda', 'id');
        }
    }

}
