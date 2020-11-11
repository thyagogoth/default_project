<?php
use System\Uteis;
use System\MasterBusiness;
use System\ValidationFields;

class NoticiaBusiness extends MasterBusiness
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
     * Lista todos os registros cadastrados na tabela NOTICIA
     */
    public static function findAllNoticia($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0)
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

        $query = "SELECT a.id, a.ordem, a.titulo, a.slug, a.galeria, a.destaque, a.ativo " . Uteis::dtSql() . " FROM noticia a " . $query . " ORDER BY " . $campo . ' ' . $ord;
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
    public static function loadFromTabelaByCampo($tabela = "noticia", $field = 'id', $value)
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
    public static function validateNoticia($post, $files)
    {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Título', $post['titulo'], 'text', 'y');
        $vld->add_text_field('Descrição', $post['descricao'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        if (empty($post['id']) && count($files) <= 0) {
            $arrMsg[] = 'É importante cadastrar ao menos uma imagem na notícia';
        }
        return $arrMsg;
    }

    /**
     * Insere o registro $post na tabela noticia
     */
    public static function createNoticia($post, $files = '')
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'noticia');
        $cod_noticia = $id;
        $dados = array();
        if (isset($files)) {
            foreach ($files as $id => $img) {
                $dados = $img;
                $dados['noticia'] = $cod_noticia;
                parent::create($dados, 'noticia_imagem');
            }
        }
        return $cod_noticia;
    }

    /**
     * Atualiza o registro $id na tabela noticia com os dados contidos em $post
     */
    public static function updateNoticia($post, $files = '', $usuario = '')
    {
        $tabela = 'noticia';
        if (!empty($usuario)) {
            $busca = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $busca);
        }
        parent::update($post, $tabela);
        $dados['created_at'] = date('Y-m-d H:i:s');
        $dados['noticia'] = $post['id'];
        if (isset($files)) {
            foreach ($files as $id => $img) {
                $dados['imagem'] = $img;
                $load = self::loadImagemByName($dados['imagem']['imagem']);
                $dados['imagem']['id'] = $load['id'];
                if (empty($load['id'])) {
                    parent::create($dados['imagem'], 'noticia_imagem');
                } else {
                    parent::update($dados['imagem'], 'noticia_imagem');
                }
            }
        }
        return $post['id'];
    }

    public static function ordenaNoticia($id, $ord, $ordem)
    {
        $arrEmp = self::findAllNoticia('true', array(), 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query(" UPDATE noticia SET ordem = '" . $varEmp['ordem'] . "'  WHERE id = '" . $id . "'");
                    parent::query("UPDATE noticia SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE noticia SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE noticia SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllNoticia('true', array(), 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE noticia SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'  ");
            }
        }
    }

    /**
     * Remove o registro $id da tabela noticia
     * Remove as imagens cadastradas associadas à $id
     * Reordena os itens
     */
    public static function removeNoticia($id)
    {
        self::removeImagensNoticia($id);
        $item = self::loadFromTabelaByCampo('noticia', 'id', $id);
        self::reOrdenaItens('down', $item['ordem'], 'noticia');
        parent::remove($id, 'noticia');
    }

    public static function reOrdenaItens($sentido = 'down', $ord, $tabela = 'noticia')
    {
        if ($sentido == 'down') {
            $qry = "UPDATE $tabela SET ordem = (ordem - 1) WHERE ordem > " . $ord;
        } else {
            $qry = "UPDATE $tabela SET ordem = (ordem + 1) WHERE ordem > " . $ord;
        }
        $cs = parent::fetchArray($qry);
    }

    public static function removeImagensNoticia($id)
    {
        $img = self::findAllImagemBynoticia('true', array('noticia' => $id));
        $dimensoes = Uteis::rtnDimensoes(true);
        if (count($img) > 0) {
            foreach ($img as $item) {
                foreach ($dimensoes as $arq) {
                    $file = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'noticia_' . $id . DIRECTORY_SEPARATOR . $arq . $item['imagem'];
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
                parent::remove($item['id'], 'noticia_imagem', 'id');
            }
            @rmdir(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'noticia_' . $id);
        }
    }

    public static function loadImagemByName($nome)
    {
        $query = " SELECT* FROM noticia_imagem WHERE imagem = '" . $nome . "'";
        $cs = parent::fetchArray($query);
        return $cs;
    }

    public static function removeFile($nome, $pasta = '')
    {
        if ($pasta) {
            $pasta = DIRECTORY_SEPARATOR . $pasta;
        }
        $file = UPLOAD_DIR . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR . $nome;
        if (!empty($file)) {
            $dimensoes = Uteis::rtnDimensoes(true);
            foreach ($dimensoes as $arq) {
                $file = UPLOAD_DIR . DIRECTORY_SEPARATOR . $pasta . DIRECTORY_SEPARATOR . $arq . $nome;
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
            parent::remove($item['id'], 'noticia_imagem', 'id');
        }
    }

    public static function removeImagemByName($nome)
    {
        $img = self::loadImagemByName($nome);
        if (count($img) > 0) {
            parent::remove($img['id'], 'noticia_imagem', 'id');
        }
    }

    public static function updateTituloImagem($post)
    {
        parent::update($post, 'noticia_imagem', 'id');
        return true;
    }

    public static function findAllImagemByNoticia($modo = '', $arr = '', $campo = 'a.id', $ord = 'ASC', $pagina = 0)
    {
        $query = '';
        if (!empty($arr['id'])) {
            $query .= "AND     a.noticia = '" . $arr['id'] . "' ";
        }
        if (!empty($arr['noticia'])) {
            $query .= "AND a.noticia = '" . $arr['noticia'] . "' ";
        }
        if (!empty($query)) {
            $query = 'WHERE' . strstr($query, ' ');
        }
        $query = "SELECT *, date_format(a.created_at, '%d/%m/%Y') as data_abreviada FROM noticia_imagem a " . $query . " ORDER BY " . $campo . ' ' . $ord;
        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina);
        }
        return $cs;
    }
}
