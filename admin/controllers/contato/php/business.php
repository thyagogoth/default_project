<?php
use System\Uteis;
use System\MasterBusiness;
use System\ValidationFields;

class ContatoBusiness extends MasterBusiness
{

    public static function getPaginacao($mode = '')
    {
        if (isset($mode) && $mode !== 'default') {
            return self::$pgn->paginas();
        } else {
            return self::$pgn->paginasDefault();
        }
    }

    public static function getTotalPaginacao()
    {
        return self::$pgn->getTotal();
    }

    public static function getPaginacaoCentral()
    {
        $aux = parent::$pgn->getInfoPgn();
        if ($aux['paginacao']) {
            $aux['paginacao'] = preg_replace('/&(?!amp;|nbsp;)/', '&amp;', $aux['paginacao']);
        }
        return $aux;
    }

    /**
     * Lista todos os registros cadastrados na tabela contato_assunto
     */
    public static function findAllAssunto($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0)
    {
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
                            $query = "AND (a.assunto LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.assunto LIKE '" . $termo . "') ";
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

        $query = "SELECT a.id, a.ordem, a.assunto, a.slug, a.email, a.ativo " . Uteis::dtSql() . " FROM contato_assunto a " . $query . " ORDER BY " . $campo . ' ' . $ord;
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
    public static function loadFromTabelaByCampo($tabela = "contato_assunto", $field = 'id', $value)
    {
        if ($tabela == "contato_mensagem") {
            $fields = "b.assunto as nome_assunto, b.email as email_assunto, ";
            $join = "LEFT JOIN contato_assunto b ON b.id = a.assunto";
        }
        $query = "SELECT a.*, $fields
			date_format(a.created_at, '%d/%m/%Y') AS created_at_formatada,
			date_format(a.created_at, '%H:%i') AS hora_cadastro_formatada,
			date_format(a.updated_at, '%d/%m/%Y')as updated_at_formatada,
			date_format(a.updated_at, '%H:%i')as hora_atualizada_formatada
            FROM $tabela a $join WHERE a.$field = '" . $value . "'";
        return parent::fetchArray($query);
    }

    /**
     * Validação de preenchimento de campos
     */
    public static function validateAssunto($post)
    {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Departamento', $post['assunto'], 'text', 'y');
        $vld->add_link_field('E-mail', $post['email'], 'email', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    /**
     * Insere o registro $post na tabela contato_assunto
     */
    public static function createAssunto($post)
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        $post['id'] = parent::create($post, 'contato_assunto');
        return $post;
    }

    /**
     * Atualiza o registro $id na tabela contato_assunto com os dados contidos em $post
     */
    public static function updateAssunto($post, $usuario = '')
    {
        $tabela = 'contato_assunto';
        if (!empty($usuario)) {
            $busca = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $busca);
        }
        parent::update($post, $tabela);
        return $post['id'];
    }

    /**
     * Remove o registro $id da tabela contato_assunto
     * Reordena os itens
     */
    public static function removeAssunto($id)
    {
        $item = self::loadFromTabelaByCampo('contato_assunto', 'id', $id);
        self::reOrdenaItens('down', $item['ordem'], 'contato_assunto');
        parent::remove($id, 'contato_assunto');
    }

    /**
     * Ordenação dos assuntos cadastrados
     */
    public static function ordenaAssunto($id, $ord, $ordem)
    {
        $arrEmp = self::findAllAssunto('true', array(), 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query(" UPDATE contato_assunto SET  ordem = '" . $varEmp['ordem'] . "'  WHERE id = '" . $id . "'");
                    parent::query("UPDATE contato_assunto   SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE contato_assunto SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE contato_assunto SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllAssunto('true', array(), 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE contato_assunto SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'  ");
            }
        }
    }

    /**
     * Altera a ordem dos itens após exlusão
     */
    public static function reOrdenaItens($sentido = 'down', $ord, $tabela = 'contato_assunto')
    {
        if ($sentido == 'down') {
            $qry = "UPDATE $tabela SET ordem = (ordem - 1) WHERE ordem > " . $ord;
        } else {
            $qry = "UPDATE $tabela SET ordem = (ordem + 1) WHERE ordem > " . $ord;
        }
        $cs = parent::fetchArray($qry);
    }

    /**
     * Lista todas as mensagens recebidas
     */
    public static function findAllMensagem($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0)
    {
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
                            $query = "AND (a.nome LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.nome LIKE '" . $termo . "') ";
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

            if (isset($arr['id'])) {
                $query .= "AND a.id = '" . $arr['id'] . "' ";
            }

            if (isset($arr['origem'])) {
                $query .= "AND a.origem = '" . $arr['origem'] . "' ";
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.id, a.origem, a.nome, a.email, b.assunto, a.lida, a.important " . Uteis::dtSql() .
            " FROM contato_mensagem a JOIN contato_assunto b ON b.id = a.assunto " . $query . " ORDER BY " . $campo . ' ' . $ord;

        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function removeMensagem($id)
    {
        parent::remove($id, 'contato_mensagem');
    }

    public static function updateMensagem($post)
    {
        parent::update($post, 'contato_mensagem');
    }

    public static function createResposta($post)
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        return parent::create($post, 'contato_mensagem');
    }
}
