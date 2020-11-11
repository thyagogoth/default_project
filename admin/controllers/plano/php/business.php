<?php
use System\MasterBusiness;
use System\Uteis;
use System\ValidationFields;

class PlanoBusiness extends MasterBusiness
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
     * Altera o valor do campo 'ativo' para 'N" se:
     * o campo 'programado' for = 'Y' e as datas indicadas
     * nos campos data_inicio e data_termino tiverem sido superadas
     */
    public static function desativaPlanosProgramados()
    {
        $query = "UPDATE
			planos SET ativo = 'N'
			WHERE
				ativo = 'Y' AND
				programado = 'Y' AND
				IF (
					data_termino IS NOT NULL AND data_inicio IS NOT NULL,
					DATE(NOW()) > DATE_FORMAT(data_termino, '%Y-%m-%d')
				,
					0
				)";
        return parent::fetchArray($query);
    }

    /**
     * Lista todos os registros cadastrados na tabela Plano
     */
    public static function findAllPlano($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0)
    {
        self::desativaPlanosProgramados();
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
            if (isset($arr['ativo'])) {
                $query = "AND a.ativo = '" . $arr['ativo'] . "'";
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.id, a.ordem, a.titulo, a.prioridade, a.tempo_parceria, a.num_anuncios, a.fotos_anuncio, a.sedes, a.dashboard, a.leads, a.valor, a.slug, a.best, a.ativo " . Uteis::dtSql() . " FROM planos a " . $query . " ORDER BY " . $campo . ' ' . $ord;

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
    public static function loadFromTabelaByCampo($tabela = "planos", $field = 'id', $value)
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
    public static function validatePlano($post)
    {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Título', $post['titulo'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    /**
     * Insere o registro $post na tabela plano
     */
    public static function createPlano($post)
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'planos');
        return $id;
    }

    /**
     * Atualiza o registro $id na tabela plano com os dados contidos em $post
     */
    public static function updatePlano($post, $usuario = '')
    {
        $tabela = 'planos';
        if (!empty($usuario)) {
            $busca = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $busca);
        }
        parent::update($post, $tabela);
        return $post['id'];
    }

    public static function ordenaPlano($id, $ord, $ordem)
    {
        $arrEmp = self::findAllPlano('true', array(), 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query(" UPDATE planos SET  ordem = '" . $varEmp['ordem'] . "'  WHERE id = '" . $id . "'");
                    parent::query("UPDATE planos   SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE planos SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE planos SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllPlano('true', array(), 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE planos SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'  ");
            }
        }
    }

    /**
     * Remove o registro $id da tabela plano
     * Remove as imagens cadastradas associadas à $id
     * Reordena os itens
     */
    public static function removePlano($id)
    {
        $item = self::loadFromTabelaByCampo('planos', 'id', $id);
        self::reOrdenaItens('down', $item['ordem'], 'planos');
        parent::remove($id, 'planos');
    }

    public static function reOrdenaItens($sentido = 'down', $ord, $tabela = 'planos')
    {
        if ($sentido == 'down') {
            $qry = "UPDATE $tabela SET ordem = (ordem - 1) WHERE ordem > " . $ord;
        } else {
            $qry = "UPDATE $tabela SET ordem = (ordem + 1) WHERE ordem > " . $ord;
        }
        $cs = parent::fetchArray($qry);
    }

    // public static function verifyPlanoEmUso($id)
    // {
    //     $query = "SELECT a.* FROM revenda a WHERE a.plano = '" . $id . "' ";
    //     return parent::fetchArray($query);
    // }

    public static function alternateBestPlan($plano)
    {
        parent::fetchArray("UPDATE planos SET best = 'N'");
        return parent::fetchArray("UPDATE planos SET best = 'Y' WHERE id = '" . $plano . "'");
    }
}
