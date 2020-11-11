<?php
use System\Uteis;
use System\MasterBusiness;
use System\ValidationFields;

class EmpresaBusiness extends MasterBusiness
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
        return self::$pgn->getInfoPgn();
    }

    public static function createEmpresa($post, $files = '')
    {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'empresa');
        
        $id_anuncio = $id;
        $dados = array();
        if (isset($files)) {
            foreach ($files as $id => $img) {
                $dados['imagem'] = $img;
                $dados['imagem']['empresa'] = $id_anuncio;
                parent::create($dados['imagem'], 'empresa_imagem');
            }
        }
        $dados['empresa'] = $id_anuncio;

        return $dados;
    }

    public static function updateEmpresa($post, $files = '')
    {
        parent::update($post, 'empresa', 'id');

        $dados = array();
        $dados['created_at'] = date('Y-m-d H:i:s');
        $dados['empresa'] = $post['id'];

        if (isset($files)) {
            foreach ($files as $id => $img) {
                $dados['imagem'] = $img;

                $load = self::loadImagemByName($dados['imagem']['imagem']);
                $dados['imagem']['id'] = $load['id'];
                if (empty($load['id'])) {
                    parent::create($dados['imagem'], 'empresa_imagem');
                } else {
                    parent::update($dados['imagem'], 'empresa_imagem');
                }
            }
        }
        return $dados;
    }

    public static function removeImagemByName($nome)
    {
        $img = self::loadImagemByName($nome);

        if (count($img) > 0) {
            parent::remove($img['id'], 'empresa_imagem', 'id');
        }
    }

    public static function removeEmpresa($id)
    {
        $arrMult = self::findAllImagemByEmpresa('true', array('id' => $id));

        if (count($arrMult)) {
            foreach ($arrMult as $id => $arr) {
                self::removeFile($arr['imagem'], 'imagem');
            }
        }
        parent::remove($id, 'empresa_imagem', 'empresa');
        parent::remove($id, 'empresa', 'id');
    }

    public static function removeFile($nome, $pasta = '')
    {
        if ($pasta) {
            $pasta = DIRECTORY_SEPARATOR . $pasta;
        }
        $file = '../uploads' . $pasta . DIRECTORY_SEPARATOR . $nome;
        if (!empty($file)) {
            $dimensoes = Uteis::rtnDimensoes(TRUE);
            foreach ($dimensoes as $arq) {
                $file = '../uploads' . $pasta . DIRECTORY_SEPARATOR . $arq . $nome;
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
    }

    public static function removeFileBd($id, $tipo)
    {
        if (!empty($id)) {
            $file = self::loadImagem($id);

            if (count($file) > 0) {
                parent::remove($id, 'empresa_imagem', 'id');
                self::removeFile($file['imagem'], $tipo, 'empresa_' . $file['empresa']);
                return $file['empresa'];
            }
        }
    }

    public static function loadImagem($id)
    {
        $query = "SELECT * FROM empresa_imagem WHERE id = '" . $id . "'";
        return parent::fetchArray($query);
    }

    public static function loadImagemByName($nome)
    {
        $query = "SELECT * FROM empresa_imagem WHERE imagem = '" . $nome . "'";
        $cs = parent::fetchArray($query);
        return $cs;
    }

    public static function findAllImagemByEmpresa($modo = '', $arr = '', $campo = 'a.id', $ord = 'ASC', $pagina = 0)
    {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        if (!empty($arr['empresa'])) {
            $query .= "AND a.empresa = '" . $arr['empresa'] . "' ";
        }
        if (!empty($arr['id'])) {
            $query .= "AND a.id = '" . $arr['id'] . "' ";
        }
        if ($arr['itens']) {
            $n_de_resultados = $arr['itens'];
        }
        if (!empty($query)) {
            $query = 'WHERE ' . strstr($query, ' ');
        }

        $query = "SELECT * FROM empresa_imagem a " . $query . " ORDER BY " . $campo . ' ' . $ord;

        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function validateEmpresa($post)
    {
        $arrMsg = array();
        $vld = new ValidationFields();
        $vld->add_text_field('Descrição', $post['descricao'], 'text', 'y');
        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    public static function validateImagem($file)
    {
        $vl = Uteis::validateImagem($file['tmp_name'], '1024000'); //5MB
        switch ($vl) {
            case 2:
                $msg = 'O campo <strong>Imagem</strong> est&aacute; vazio.';
                break;
            case 3:
                $msg = 'O campo <strong>Imagem</strong> possui um imagem inv&aacute;lido.';
                break;
            case 4:
                $msg = 'Tamanho da <strong>Imagem</strong> maior que o suportado pelo servidor.';
                break;
        }
        return $msg;
    }

    public static function loadEmpresa($id = '')
    {
        if ($id) {
            $query = "WHERE id = '" . $id . "' ";
        }
        $query = "SELECT *, date_format(created_at, '%d/%m/%Y') AS created_at_formatada, date_format(created_at, '%H:%i') AS hora_cadastro_formatada, date_format(updated_at, '%d/%m/%Y')as updated_at_formatada, date_format(updated_at, '%H:%i') as hora_atualizada_formatada FROM empresa " . $query . " LIMIT 1";
        return parent::fetchArray($query);
    }

    public static function lastEmpresa($ativo = '')
    {
        if (!empty($ativo)) {
            $qry = " WHERE ativo = '" . $ativo . "' ";
        }
        $query = "SELECT *, date_format(created_at, '%d/%m/%Y') AS created_at_formatada, date_format(created_at, '%H:%i') AS hora_cadastro_formatada, date_format(updated_at, '%d/%m/%Y')as updated_at_formatada, date_format(updated_at, '%H:%i') as hora_atualizada_formatada FROM empresa " . $qry . " ORDER BY id DESC LIMIT 1";
        return parent::fetchArray($query);
    }

    public static function lastImagemEmpresa()
    {
        $query = "SELECT * FROM empresa_imagem ORDER BY id DESC LIMIT 1";
        return parent::fetchArray($query);
    }

    public static function updateTituloImagem($post)
    {
        parent::update($post, 'empresa_imagem', 'id');
        return true;
    }

    public static function loadFromTabelaByCampo($tabela, $campo = 'id', $value, $ativo = '', $dateSql = true)
    {
        if ($ativo) {
            $active = " AND a.ativo = '" . $ativo . "' ";
        }
        if ($dateSql === true) {
            $qry .= ", " . Uteis::dtSql();
        }
        $query = "SELECT a.*,
				$sqry
				date_format(a.created_at, '%d') AS dia,
				date_format(a.created_at, '%m') AS mes,
				date_format(a.created_at, '%Y') AS ano $qry FROM $tabela a WHERE a.$campo = '" . $value . "'" . $active;
        $cs = parent::fetchArray($query);
        return $cs;
    }
}
