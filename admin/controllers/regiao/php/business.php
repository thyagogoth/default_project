<?php
use System\Uteis;
use System\MasterBusiness;
use System\ValidationFields;

class RegiaoBusiness extends MasterBusiness
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
     * Lista todos os registros cadastrados na tabela Regioes
     */
    public static function findAllRegiao($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0)
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
                            $query = "AND (a.regiao LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.regiao LIKE '" . $termo . "') ";
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

        $query = "SELECT a.id, a.ordem, a.regiao, a.slug, a.estado, a.ddd, a.ativo " . Uteis::dtSql() . " FROM regioes a " . $query . " ORDER BY " . $campo . ' ' . $ord;

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
    public static function loadFromTabelaByCampo($tabela = "regioes", $field = 'id', $value)
    {
        $query = "SELECT a.*,
			date_format(a.created_at, '%d/%m/%Y') AS created_at_formatada,
			date_format(a.created_at, '%H:%i') AS hora_cadastro_formatada,
			date_format(a.updated_at, '%d/%m/%Y')as updated_at_formatada,
			date_format(a.updated_at, '%H:%i')as hora_atualizada_formatada
			FROM $tabela a WHERE a.$field = '" . $value . "'";
        return parent::fetchArray($query);
    }

    /**
     * Validação de preenchimento de campos
     */
    public static function validateRegiao($post)
    {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Título', $post['regiao'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    /**
     * Insere o registro $post na tabela regiao
     */
    public static function createRegiao($post)
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'regioes');
        return $id;
    }

    /**
     * Atualiza o registro $id na tabela regiao com os dados contidos em $post
     */
    public static function updateRegiao($post, $usuario = '')
    {
        $tabela = 'regioes';
        if (!empty($usuario)) {
            $busca = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $busca);
        }
        parent::update($post, $tabela);
        return $post['id'];
    }

    public static function ordenaRegiao($id, $ord, $ordem)
    {
        $arrEmp = self::findAllRegiao('true', array(), 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query(" UPDATE regioes SET  ordem = '" . $varEmp['ordem'] . "'  WHERE id = '" . $id . "'");
                    parent::query("UPDATE regioes   SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE regioes SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE regioes SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllRegiao('true', array(), 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE regioes SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'  ");
            }
        }
    }

    /**
     * Remove o registro $id da tabela regiao
     * Remove as imagens cadastradas associadas à $id
     * Reordena os itens
     */
    public static function removeRegiao($id)
    {
        $item = self::loadFromTabelaByCampo('regioes', 'id', $id);
        self::reOrdenaItens('down', $item['ordem'], 'regioes');
        parent::remove($id, 'regioes');
    }

    public static function reOrdenaItens($sentido = 'down', $ord, $tabela = 'regioes')
    {
        if ($sentido == 'down') {
            $qry = "UPDATE $tabela SET ordem = (ordem - 1) WHERE ordem > " . $ord;
        } else {
            $qry = "UPDATE $tabela SET ordem = (ordem + 1) WHERE ordem > " . $ord;
        }
        $cs = parent::fetchArray($qry);
    }

    public static function verifyRegiaoEmUso($id)
    {
        // $query = "SELECT a.id FROM regioes a JOIN revenda b ON b.ddd = a.ddd AND a.id = 69";
        return parent::fetchArray($query);
    }

    public static function alternateBestPlan($regiao)
    {
        parent::fetchArray("UPDATE regioes SET best = 'N'");
        return parent::fetchArray("UPDATE regioes SET best = 'Y' WHERE id = '" . $regiao . "'");
    }
}
