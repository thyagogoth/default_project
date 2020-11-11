<?php
use System\Uteis;
use System\Helpers\Mail;
use System\UploadArquivo;
use System\MasterBusiness;
use System\ValidationFields;

class SistemaBusiness extends MasterBusiness {

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
     * Login de Usuário no Painel de controle
     */
    public static function login($login, $senha, $ativo = "", $encrypt = true) {
        $qryAtivo = NULL;
        if (!empty($ativo)) {
            $qryAtivo = " AND a.ativo = '{$ativo}' ";
        }
        $check_senha = $senha;
        if ($encrypt) {
            $check_senha = sha1($senha);
        }
        $query = "SELECT a.*,
        date_format(a.created_at, '%d/%m/%Y')as dia_formatado,
        b.nome as nome_permissao

		FROM sistema_usuario a
        JOIN sistema_permissoes b ON b.id = a.permissao
		WHERE sha1(a.email) = BINARY '" . sha1($login) . "' AND a.senha = BINARY '{$check_senha}' {$qryAtivo}";
        return parent::fetchArray($query);
    }

    /**
     * Valida o preenchimento dos requisitos para
     * cadastro/edição de usuário
     */
    public static function validateUsuario($post, $file = '') {
        $arrMsg = [];
        $vld = new ValidationFields();
        $vld->add_text_field('Nome', $post['nome'], 'text', 'y', 255);
        $vld->add_link_field('E-mail', $post['email'], 'email', 'y', 255);
        if (empty($post['id'])) {
            $vld->add_text_field('Permissão', $post['permissao'], 'text', 'y');
        }
        
        if (empty($post['id'])) {
            // É um novo cadastro
            if ($post['senha']) {
                $forcaDaSenha = Uteis::passwordVerify($post['senha']);
                if (!$forcaDaSenha) {
                    $ErrSenha = 'A <strong>senha:</strong>
					<ul class="list-unstyled">
					<li><i class="fa fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> entre 6 e 16 caracteres</li>
					<li><i class="fa fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> letras e números</li>
					<li><i class="fa fa-angle-right"></i>&nbsp;Caracteres especiais (ex: !@#$%¨&*()_+) <strong>são opcionais</strong></li>
					</ul>';
                }
                if (!empty($post['repita_senha'])) {
                    $vld->add_senha_field('Senhas', $post['senha'], $post['repita_senha'], 'senha', 'y');
                }
            } else {
                if (empty($post['senha'])) {
                    $vld->add_text_field('Senha', $post['senha'], 'text', 'y');
                } else {
                    $vld->add_senha_field('Senhas', $post['senha'], $post['repita_senha'], 'senha', 'y');
                }
            }
        } else {
            if (!empty($post['senha'])) {
                $forcaDaSenha = Uteis::passwordVerify($post['senha']);
                if (!$forcaDaSenha) {
                    $ErrSenha = 'A <strong>senha:</strong>
					<ul class="list-unstyled">
					<li><i class="fal fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> entre 6 e 16 caracteres</li>
					<li><i class="fal fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> letras e números</li>
					<li><i class="fal fa-angle-right"></i>&nbsp;Caracteres especiais (ex: !@#$%¨&*()_+) <strong>são opcionais</strong></li>
					</ul>';
                }
                $vld->add_senha_field('Senhas', $post['senha'], $post['repita_senha'], 'senha', 'y');
            }
        }

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        if (!$post['id']) {
            if (!is_writable(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios')) {
                @mkdir(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios', 0777);
            }
            $vl = UploadArquivo::validateArquivo(array(0 => $file), '/(jpg|jpeg|png|gif)$/', '2097152', 0);
        } else {
            $vl = UploadArquivo::validateArquivo(array(0 => $file), '/(jpg|jpeg|png|gif)$/', '2097152', 0);
        }

        if (count($vl) > 0) {
            foreach ($vl as $var) {
                $arrMsg[] = $var;
            }
        }
        
        if (!empty($ErrSenha)) {
            array_push($arrMsg, $ErrSenha);
        }
        
        return $arrMsg;
    }
    
    /**
     * Cria o registro de um usuário
     */
    public static function createUsuario($post, $file = '') {
        $post['created_at'] = date('Y-m-d H:i:s');
        if (!empty($file['name'])) {
            $arquivo = UploadArquivo::upload(array(0 => $file), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios', '');
            $new['avatar'] = $arquivo[0]['nomeFormatado'];
        }
        $id = parent::create($post, 'sistema_usuario');
        return $id;
    }

    /**
     * Atualiza um registro de usuário
     */
    public static function updateUsuario($post, $file = '', $usuario = '') {
        if (!empty($file['name'])) {
            self::removeAvatar('sistema_usuario', $post['id']);
            $arquivo = UploadArquivo::upload(array(0 => $file), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios', '');
            $post['avatar'] = $arquivo[0]['nomeFormatado'];
            if ($_SESSION['usuario']['id'] == $post['id']) :
                $_SESSION['usuario']['avatar'] = $post['avatar'];
            endif;
        }
        if (!empty($usuario)) {
            $tabela = 'sistema_usuario';
            $aux = self::loadFromTabelaByCampo($tabela, 'id', $post['id']);
            parent::logSql($post, $usuario, $tabela, $post);
        }
        parent::update($post, 'sistema_usuario', 'id');
    }

    /**
     * Remove o registro de um usuário
     */
    public static function removeUsuario($id) {
        self::removeAvatar('sistema_usuario', $id);
        parent::remove($id, 'sistema_usuario');
    }

    /**
     * Remove o Avatar do usuário $id
     */
    public static function removeAvatar($tabela, $id) {
        $img = self::loadFromTabelaByCampo($tabela, 'id', $id);
        if ($img['avatar']) {
            $file = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios' . DIRECTORY_SEPARATOR . $img['avatar'];
            if (is_file($file))
                @unlink($file);
        }
        return true;
    }

    /**
     * Busca todos os usuários cadastrados
     */
    public static function findAllUsuario($pagination = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        $having = '';
        if (is_array($arr)) {
            if (isset($arr['busca'])) {
                $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
                $i = 1;
                foreach ($termos as $item) {
                    if (strlen($item) > 2) {
                        $termo = Uteis::space2like($item);
                        if ($i < 2) {
                            $query = "AND (a.nome_usuario LIKE '" . $termo . "' OR a.login LIKE '" . $termo . "' OR a.email LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.nome_usuario LIKE '" . $termo . "' OR a.login LIKE '" . $termo . "' OR a.email LIKE '" . $termo . "') ";
                        }
                    }
                    $i++;
                }
            }
            if (isset($arr['no_permissao'])) {
                $query .= "AND a.permissao <> " . $arr['no_permissao'] . " ";
            }
            if (isset($arr['permissao'])) {
                $query .= "AND a.permissao = " . $arr['permissao'] . " ";
            }

            if (isset($arr['nome_permissao'])) {
                $having .= "HAVING nome_permissao = '" . $arr['nome_permissao'] . "' ";
                // COMPARA DADO COM ALIAS
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
            if (isset($arr['no_usuario'])) {
                $query .= "AND a.login <> '" . $arr['no_usuario'] . "' ";
            }

            if (isset($arr['itens'])) {
                $n_de_resultados = $arr['itens'];
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.*,
        (SELECT b.nome FROM sistema_permissoes b WHERE b.id = a.permissao) as nome_permissao
         " . Uteis::dtSql(['ultimo_login']) . " FROM sistema_usuario a " . $query . "
            " . $having . " ORDER BY " . $campo . ' ' . $ord;

        if ($pagination == 'false') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    /**
     * Valida o cadastro de Permissões
     */
    public static function validatePermissao($post) {
        $arrMsg = [];
        $vld = new ValidationFields();

        $vld->add_text_field('Nome', $post['nome'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    /**
     * Cria o registro de uma Permissão
     */
    public static function createPermissao($post, $arrAcoes = []) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'sistema_permissoes');
        foreach ($arrAcoes as $item_permitido) {
            $new['permissao'] = $id;
            $new['action'] = $item_permitido;
            parent::create($new, 'sistema_action_permissao');
        }
        return $id;
    }

    /**
     * Atualiza o registro de uma Permissão
     * e as suas respectivas ações permitidas
     */
    public static function updatePermissao($post, $arrAcoes = []) {
        parent::update($post, 'sistema_permissoes');

        self::remove($post['id'], 'sistema_action_permissao', 'permissao');
        // dd($post['id']);
        foreach ($arrAcoes as $item_permitido) {
            $new['permissao'] = $post['id'];
            $new['action'] = $item_permitido;
            parent::create($new, 'sistema_action_permissao');
        }
        return $post['id'];
    }

    /**
     * Remove o registro de uma permissão
     */
    public static function removePermissao($id) {
        parent::remove($id, 'sistema_permissoes');
        parent::remove($id, 'sistema_action_permissao', 'permissao');
    }

    /**
     * Ordena os registros de Permissões
     */
    public static function ordenaPermissao($id, $ord, $ordem) {
        $arrEmp = self::findAllPermissao('false', [], 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_permissoes SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_permissoes SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_permissoes SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_permissoes SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllPermissao('false', [], 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE sistema_permissoes SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'");
            }
        }
    }

    /**
     * Lista as permissões cadastradas no Banco de Dados
     */
    public static function findAllPermissao($pagination = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        $having = '';
        if (is_array($arr)) {
            if (isset($arr['busca'])) {
                $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
                $i = 1;
                foreach ($termos as $item) {
                    if (strlen($item) > 2) {
                        $termo = Uteis::space2like($item);
                        if ($i < 2) {
                            $query = "AND (a.nome_usuario LIKE '" . $termo . "' OR a.login LIKE '" . $termo . "' OR a.email LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.nome_usuario LIKE '" . $termo . "' OR a.login LIKE '" . $termo . "' OR a.email LIKE '" . $termo . "') ";
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
            if (isset($arr['no_id'])) {
                $query .= "AND a.id <> '" . $arr['no_id'] . "' ";
            }
            if (isset($arr['no_permissao'])) {
                $query .= "AND a.permissao <> '" . $arr['no_permissao'] . "' ";
            }

            if (isset($arr['itens'])) {
                $n_de_resultados = $arr['itens'];
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.* " . Uteis::dtSql() . " FROM sistema_permissoes a " . $query . " ORDER BY " . $campo . " " . $ord;

        if ($pagination == 'false') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    /**
     * Valida o preenchimento do cadastro de Módulos
     */
    public static function validateModulo($post) {
        $arrMsg = [];
        $vld = new ValidationFields();

        $vld->add_text_field('Nome do módulo', $post['modulo'], 'text', 'y');
        $vld->add_text_field('Label', $post['label'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        return $arrMsg;
    }

    /**
     * Cria o registro de um módulo
     */
    public static function createModulo($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'sistema_modulo');
        return $id;
    }

    /**
     * Atualiza o registro de um módulo
     */
    public static function updateModulo($post) {
        $id = parent::update($post, 'sistema_modulo');
        return $id;
    }

    /**
     * Remove o registro do módulo $id
     */
    public static function removeModulo($id) {
        $actionsByModulo = [];
        $actionsByModulo == self::findAllAcao('true', ['modulo' => $id], 'a.id', 'asc');
        foreach ($actionsByModulo as $abm) {
            parent::remove($abm['action'], 'sistema_action_permissao', 'action');
            parent::remove($abm['action'], 'sistema_menu', 'action');
        }
        parent::remove($id, 'sistema_action', 'modulo');
        parent::remove($id, 'sistema_modulo');

        return true;
    }

    /**
     * Busca todos os módulos cadastrados
     */
    public static function findAllModulo($pagination = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        $having = '';
        if (is_array($arr)) {
            if (isset($arr['busca'])) {
                $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
                $i = 1;
                foreach ($termos as $item) {
                    if (strlen($item) > 2) {
                        $termo = Uteis::space2like($item);
                        if ($i < 2) {
                            $query = "AND (a.label LIKE '" . $termo . "') ";
                        } else {
                            $query .= "OR (a.label LIKE '" . $termo . "') ";
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

            if (isset($arr['itens'])) {
                $n_de_resultados = $arr['itens'];
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.* " . Uteis::dtSql() . " FROM sistema_modulo a " . $query . " ORDER BY " . $campo . ' ' . $ord;

        if ($pagination == 'false') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    /**
     * Ordena os registros de Módulos
     */
    public static function ordenaModulo($id, $ord, $ordem) {
        $arrEmp = self::findAllModulo('false', [], 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_modulo SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_modulo SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_modulo SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_modulo SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllModulo('false', [], 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE sistema_modulo SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'");
            }
        }
    }

    /**
     * Carrega um registro da $tabela mediante field=$value
     */
    public static function loadFromTabelaByCampo($tabela = "sistema_usuario", $field = 'id', $value) {
        $query = "SELECT a.*,
			date_format(a.created_at, '%d/%m/%Y') AS created_at_formatada,
			date_format(a.created_at, '%H:%i') AS hora_cadastro_formatada,
			date_format(a.updated_at, '%d/%m/%Y')as updated_at_formatada,
			date_format(a.updated_at, '%H:%i')as hora_atualizada_formatada
            FROM $tabela a WHERE a.$field = '" . $value . "'";
        return parent::fetchArray($query);
    }

    /**
     * Valida o formulário de cadastro de Ações
     */
    public static function validateAcao($post) {
        $arrMsg = [];
        $vld = new ValidationFields();

        $vld->add_text_field('Módulo responsável', $post['modulo'], 'text', 'y');
        $vld->add_text_field('Ação', $post['action'], 'text', 'y');
        $vld->add_text_field('Máscara', $post['label'], 'text', 'y');

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        return $arrMsg;
    }

    /**
     * Cria o registro de uma ação
     */
    public static function createAcao($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'sistema_action');
        return $id;
    }

    /**
     * Atualiza o registro de uma ação
     */
    public static function updateAcao($post) {
        $id = parent::update($post, 'sistema_action');
        return $id;
    }

    /**
     * Remove o registro de uma ação
     */
    public static function removeAcao($id) {
        parent::remove($id, 'sistema_action');
        return true;
    }

    /**
     * Busca todas as ações cadastradas
     */
    public static function findAllAcao($pagination = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;

        if (is_array($arr)) {
            if (isset($arr['itens'])) {
                $n_de_resultados = $arr['itens'];
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.*, b.label as nome_modulo,
        date_format(a.created_at, '%d/%m/%Y') AS created_at_formatada,
        date_format(a.created_at, '%H:%i') AS hora_cadastro_formatada,
        date_format(a.updated_at, '%d/%m/%Y')as updated_at_formatada,
        date_format(a.updated_at, '%H:%i')as hora_atualizada_formatada
        FROM sistema_action a JOIN sistema_modulo b ON b.id = a.modulo " .
                $query . " ORDER BY " . $campo . ' ' . $ord;

        if ($pagination == 'false') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    /**
     * Ordena os registros de Ações
     */
    public static function ordenaAcao($id, $ord, $ordem) {
        $arrEmp = self::findAllAcao('false', [], 'ordem', 'asc');
        if ($ord == 'up') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem - 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_action SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_action SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        } else if ($ord == 'down') {
            foreach ($arrEmp as $varEmp) {
                if (($ordem + 1) == $varEmp['ordem']) {
                    parent::query("UPDATE sistema_action SET ordem = '" . $varEmp['ordem'] . "' WHERE id = '" . $id . "'");
                    parent::query("UPDATE sistema_action SET ordem = '" . $ordem . "' WHERE id = '" . $varEmp['id'] . "'");
                }
            }
        }

        $arrEmp = self::findAllAcao('false', [], 'ordem', 'asc');
        foreach ($arrEmp as $id => $arr) {
            if (is_numeric($id)) {
                parent::query("UPDATE sistema_modulo SET ordem = '" . ($id + 1) . "' WHERE id = '" . $arr['id'] . "'");
            }
        }
    }

    /*     * **
     * GERAÇÃO DO MENU
     * Nestable List
     */

    public static function createMenu($post) {
        if ($post['menu'] == '') {
            $post['menu'] = '0';
        }
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'sistema_menu');
        return $id;
    }

    public static function updateMenu($post) {
        $id = parent::update($post, 'sistema_menu');
        return $id;
    }

    public static function getMenuEdit($ocultar_itens = true) {
        $htm = '';
        $menu = self::getMenuWithPerm(0);
        if (count($menu) > 0) {
            $count = 1;
            $htm .= '<ol class="dd-list" style="padding-left:25px;">';
            foreach ($menu as $var) {
                $rtn = self::returnHtmlList($var, $count, $ocultar_itens);
                $htm .= $rtn['html'];
                $count = $rtn['count'];
                $count++;
            }
            $htm .= '</ol>';
        }
        return $htm;
    }

    public static function getMenuWithPerm($id) {
        $query = "SELECT a.*, a.ordem as order_by,
                c.modulo, a.icone as icone, c.ordem
                FROM sistema_menu a
                LEFT JOIN sistema_action d ON (d.id = a.action)
                LEFT JOIN sistema_modulo c ON (c.id = d.modulo)
                WHERE a.menu = " . $id . "  ORDER BY a.menu ASC, a.ordem ASC ";
        $list = parent::transformFetchToArray($query);

        if (count($list) > 0) {
            foreach ($list as $id => $var) {
                if ($var['action'] == 0 || $var['action'] == '0') {
                    $aux = self::getMenuWithPerm($var['id']);
                    if ($aux) {
                        $list[$id]['menu'] = $aux;
                    }
                }
            }
        }
        return $list;
    }

    public static function returnHtmlList($var, $count = 1, $ocultar_itens = true) {
        $html = '';
        $html2 = '';
        $html3 = '';

        if ($var['action']) {
            $hide_item = '';
            $color_oculto = NULL;
            if ($var['oculto'] == "Y") {
                $var['item'] = ucfirst($var['modulo']) . ' &raquo; ' . $var['item'];
                $color_oculto = 'hidden-item-color';

                $hide_item = 'hide';
                if ($ocultar_itens == false) {
                    $hide_item = '';
                }
            }
            $var['parent'] = isset($var['parent']) ? $var['parent'] : NULL;
            $html .= '<li class="dd-item ' . $var['parent'] . ' ' . $hide_item . '" data-id="' . $var['id'] . '" data-order="' . $var['order_by'] . '">';
            $html .= '	<div class="dd-handle ' . $color_oculto . '">';
            $html .= '		<span class="' . $var['icone'] . '"></span>&nbsp; ' . $var['item'];
            $html .= '	</div>';
            $html .= '	<div class="agrupa-botoes">';
            $html .= '		<button type="button" data-code="' . $var['id'] . '" class="edit-this-item btn btn-outline-info btn-sm btn-icon waves-effect waves-themed" data-toggle="modal" data-target="#edit-action-menu"><i class="fal fa-pencil"></i></button>';
            $html .= '		<a href="javascript:void(0);" onclick="implementsConfirm($(this))" data-src="' . SERVER . '/sistema/excluir-item-menu?excluir=' . $var['id'] . '" data-toggle="modal" data-target="#confirm-delete" class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';
            $html .= '	</div>';
            $html .= '</li>';
        } elseif (is_array($var['menu'])) {
            $hide_item = '';
            $color_oculto = NULL;
            if ($var['oculto'] == "Y") {
                $var['item'] = $var['item'];
                $color_oculto = 'hidden-item-color';
                $hide_item = 'hide';
                if ($ocultar_itens == false) {
                    $hide_item = '';
                }
            }
            $html2 .= '<li class="dd-item ' . $hide_item . '" data-id="' . $var['id'] . '" data-order="' . $var['order_by'] . '">';
            $html2 .= '	<div class="dd-handle ' . $color_oculto . '">';
            $html2 .= '		<span class="' . $var['icone'] . '"></span>&nbsp;' . $var['item'];
            $html2 .= ' </div>';
            $html2 .= '	<div class="agrupa-botoes">';
            $html2 .= '		<button type="button" data-code="' . $var['id'] . '" class="edit-this-item btn btn-outline-info btn-sm btn-icon waves-effect waves-themed" data-toggle="modal" data-target="#edit-action-menu"><i class="fal fa-pencil"></i></button>';
            $html2 .= '		<a href="javascript:void(0);" onclick="implementsConfirm($(this))" data-src="' . SERVER . '/sistema/excluir-item-menu?excluir=' . $var['id'] . '" data-toggle="modal" data-target="#confirm-delete" class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';
            $html2 .= '	</div>';
            $html2 .= '	<ol class="dd-list" style="padding-left:25px;">';

            foreach ($var['menu'] as $vari) {
                $count++;
                $rtn = self::returnHtmlList($vari, $count, $ocultar_itens);
                $html3 .= $rtn['html'];
                $count = $rtn['count'];
            }

            if ($html3) {
                $html .= $html2 . $html3;
                $html .= '	</ol>';
                $html .= '</li>';
            }
        } else {
            $hide_item = '';
            $color_oculto = NULL;
            if ($var['oculto'] == "Y") {
                $var['item'] = $var['item'];
                $color_oculto = 'hidden-item-color';
                $hide_item = 'hide';
                if ($ocultar_itens == false) {
                    $hide_item = '';
                }
            }
            $html .= '<li class="dd-item ' . $hide_item . '" data-id="' . $var['id'] . '" data-order="' . $var['order_by'] . '">';
            $html .= '	<div class="dd-handle ' . $color_oculto . '">';
            $html .= '		<span class="' . $var['icone'] . '"></span>&nbsp;' . $var['item'];
            $html .= '	</div>';
            $html .= '	<div class="agrupa-botoes">';
            $html .= '		<button type="button" data-code="' . $var['id'] . '" class="edit-this-item btn btn-outline-info btn-sm btn-icon waves-effect waves-themed" data-toggle="modal" data-target="#edit-action-menu"><i class="fal fa-pencil"></i></button>';
            $html .= '		<a href="javascript:void(0);" onclick="implementsConfirm($(this))" data-src="' . SERVER . '/sistema/excluir-item-menu?excluir=' . $var['id'] . '" data-toggle="modal" data-target="#confirm-delete" class="btn btn-outline-danger btn-sm btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';
            $html .= '	</div>';
            $html .= '</li>';
        }
        $arr = [];
        $arr['html'] = $html;
        $arr['count'] = $count;

        return $arr;
    }

    public function findAllSubItens($id) {
        $query = "SELECT * FROM sistema_menu WHERE menu = " . $id;
        $cs = parent::transformFetchToArray($query);
        return $cs;
    }

    public function findAllActionModulo() {
        $arrFinal = [];

        $query = "SELECT a.*, a.modulo as modulo FROM sistema_modulo a ORDER BY a.modulo ASC";
        $arrFinal = parent::transformFetchToArray($query);
        if (count($arrFinal) > 0) {
            foreach ($arrFinal as $id => $modulo) {
                $arrActions = SistemaBusiness::findAllAcao('false', ['permissao' => 'Y', 'modulo' => $modulo['id']], 'a.action', 'ASC');
                $arrFinal[$id]['menu'] = $arrActions;
            }
        }
        return $arrFinal;
    }

    function doHTMLCheckboxes($arrCheckbox, $arrAcoesFinal = []) {
        $htmlFinal = '';
        // dd($arrAcoesFinal);
        $htmlFinal .= '<div class="master_div">';
        foreach ($arrCheckbox as $id => $checkbox) {

            $act = (is_numeric($checkbox['action']) && $checkbox['action'] != '0' ? $checkbox['action'] : $checkbox['id']);
            $nom = ($checkbox['item'] ? $checkbox['item'] : ($checkbox['label'] ? $checkbox['label'] : $checkbox['item']));
            if (is_array($checkbox['menu'])) {
                $htmlFinal .= '<div class="mb-1 checkbox check_parent_div custom-control custom-checkbox" id="parent_' . $act . '">';
                $htmlFinal .= '     <input type="checkbox" class="check_parent custom-control-input" id="check_parent_' . $act . '">';
                $htmlFinal .= '     <label class="custom-control-label labelCheck" for="check_parent_' . $act . '">' . $nom . '</label>';

                $htmlFinal .= self::doHTMLCheckboxes($checkbox['menu'], $arrAcoesFinal);
            } else {
                if (is_array($arrAcoesFinal) && count($arrAcoesFinal) > 0) {
                    $checked = (in_array($act, $arrAcoesFinal)) ? 'checked="checked"' : '';
                }
                $oculto = " ";
                if ($checkbox['oculto'] == "Y") {
                    $oculto = " <i class='fal fa-eye-slash'></i> ";
                }
                $htmlFinal .= '<div class="mb-1 checkbox custom-control custom-checkbox">';
                $htmlFinal .= '     <input id="check_children_' . $act . '" value="' . $act . '" ' . $checked . ' class="check_children custom-control-input" name="arrAct[]" type="checkbox">';
                $htmlFinal .= '     <label for="check_children_' . $act . '" class="custom-control-label labelCheck">' . $nom . $oculto . '</label>';
            }
            $htmlFinal .= '</div>';
        }
        $htmlFinal .= '</div>';
        return $htmlFinal;
    }

    public static function findAllActionPermissao($pagination = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        if (is_array($arr)) {
            if (isset($arr['no_cod'])) {
                if (is_array($arr['no_cod'])) {
                    foreach ($arr['no_cod'] as $noCod) {
                        $query .= "AND a.id <> '" . $noCod . "' ";
                    }
                } else {
                    $query .= "AND a.id <> '" . $arr['no_cod'] . "' ";
                }
            }
            if (isset($arr['no_id'])) {
                $query .= "AND a.id <> '" . $arr['no_id'] . "' ";
            }
            if (isset($arr['no_permissao'])) {
                $query .= "AND a.permissao <> '" . $arr['no_permissao'] . "' ";
            }
            if (isset($arr['permissao'])) {
                $query .= "AND a.permissao = '" . $arr['permissao'] . "' ";
            }

            if (isset($arr['itens'])) {
                $n_de_resultados = $arr['itens'];
            }
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.* FROM sistema_action_permissao a " . $query . " ORDER BY " . $campo . " " . $ord;
        if ($pagination == 'false') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function removeActionPermissao($id, $primary) {
        parent::remove($id, 'sistema_action_permissao', $primary);
    }

    public static function verifyAction($nomeAction, $codModulo) {
        $query = "SELECT * FROM sistema_action WHERE action = '" . $nomeAction . "' AND modulo=" . $codModulo;
        return parent::fetchArray($query);
    }

    public static function findAllPermissaoByPermissao($permissao) {
        $query = "SELECT
                        a.*, b.action, b.restrito, c.modulo
                    FROM
                        sistema_action_permissao a
                    JOIN
                        sistema_action b ON b.id = a.action
                    JOIN
                        sistema_modulo c ON c.id = b.modulo
                    WHERE a.permissao = '" . $permissao . "' AND b.restrito = 'Y' ORDER BY a.id ASC";

        $cs = parent::transformFetchToArray($query);
        return $cs;
    }

    public static function removeMenu($id) {
        parent::remove($id, 'sistema_menu');
        parent::remove($id, 'sistema_menu', 'menu');
    }

    /**
     * GET the parent Items (menu: 0) from menu
     * @param type $codMenu
     * @param type $permissao
     * @return type
     */
    public static function getMenu($codMenu, $permissao) {
        $query = "SELECT a.*, 
                c.modulo, a.ordem as order_by,
                d.action AS acao, a.icone, c.ordem
                FROM sistema_menu a
                LEFT JOIN sistema_action_permissao b ON (b.action = a.action AND b.permissao = '" . $permissao . "')
                LEFT JOIN sistema_action d ON (d.id = b.action)
                LEFT JOIN sistema_modulo c ON (c.id = d.modulo)
                WHERE a.menu = '" . $codMenu . "' AND a.oculto <> 'Y' ";
        $query .= "ORDER BY a.ordem ASC";

        $list = parent::transformFetchToArray($query);

        if (count($list) > 0) {
            foreach ($list as $id => $var) {
                if (!$var['acao']) {
                    $aux = self::getMenu($var['id'], $permissao);
                    if ($aux) {
                        $list[$id]['menu'] = $aux;
                    }
                }
            }
        }
        return $list;
    }

    /**
     * Gera o HTML do Menu de acesso para o usuário logado
     */
    public static function geraMenu($id) {
        $htm = '';
        $htm2 = '';
        $menu = self::getMenu(0, $id);

        $htm = '<ul id="js-nav-menu" class="nav-menu">';
        $htm .= '	<li><a title="Página inicial" data-filter-tags="página inicial" class="waves-effect waves-themed" href="' . SERVER . '/home"><i class="fal fa-home"></i><span class="nav-link-text" data-i18n="nav.pagina_inicial"> Página Inicial </span></a></li>';

        if (count($menu) > 0) {
            foreach ($menu as $var) {
                $htm2 .= self::returnHtmlMenu($var);
            }
            $htm .= $htm2;
        }
        $htm .= '	<li><a title="Desconectar do perfil" data-filter-tags="desconectar do perfil" class="waves-effect waves-themed" data-toggle="modal" onclick="implementsConfirm($(this))" data-src="' . SERVER . '/desconectar" data-target="#confirm-exit" href="javascript:void(0);"><i class="ni ni-power"></i><span class="nav-link-text"> Desconectar </span></a></li>';
        $htm .= '<div class="filter-message js-filter-message"></div>';
        $htm .= '</ul>';

        return $htm;
    }

    public static function returnHtmlMenu($var) {
        $html = '';
        $html2 = '';
        $html3 = '';
        if ($var['acao']) {
            $icone = empty($var['icone']) ? '<i class="ni ni-tag"></i>' : '<i class = "' . $var['icone'] . '"></i>';
            $html .= '<li>
							<a title="' . $var['item'] . '" data-filter-tags="' . $var['item'] . ' ' . $var['acao'] . ' ' . $var['modulo'] . '" href = "' . SERVER . '/' . $var['modulo'] . '/' . $var['acao'] . '">
							' . $icone . ' <span class="nav-link-text" data-i18n="nav.' . str_replace('.', '', str_replace(' ', '_', str_replace('-', '_', strtolower($var['item'])))) . '">' . $var['item'] . '</span>
							</a>
						</li>';
        } elseif (is_array($var['menu'])) {
            $html2 .= '<li class="has_sub">';
            $html2 .= '		<a title="' . $var['item'] . '" data-filter-tags="' . $var['item'] . ' ' . $var['acao'] . ' ' . $var['modulo'] . '" href="javascript:void(0);" class="waves-effect">';

            $icone = empty($var['icone']) ? '<i class="ni ni-tag"></i>' : '<i class = "' . $var['icone'] . '"></i>';
            $html2 .= $icone;
            $html2 .= '			<span class="nav-link-text" data-i18n="nav.' . str_replace('.', '', str_replace(' ', '_', str_replace('-', '_', strtolower($var['item'])))) . '"> ' . $var['item'] . ' </span>';
            $html2 .= '		</a>';
            $html2 .= '		<ul>';
            foreach ($var['menu'] as $vari) {
                $html3 .= self::returnHtmlMenu($vari);
            }
            if ($html3) {
                $html .= $html2 . $html3;
                $html .= '	</ul>';
                $html .= '</li>';
            }
        }
        return $html;
    }

    public static function loadFromActionByModule($action, $modulo) {
        $query = "SELECT * FROM sistema_action WHERE modulo = '{$modulo}' AND action='{$action}'";
        return parent::fetchArray($query);
    }

    /**
     * RECUPERAÇÃO DE SENHA
     */
    public static function validateRecuperarSenha($post) {
        $arrMsg = [];
        $vld = new ValidationFields();

        $vld->add_link_field('E-mail', $post['email'], 'email', 'y', 255);

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        if (count($arrMsg) <= 0) {
            $loadUsuario = self::loadFromTabelaByCampo('sistema_usuario', 'email', $post['email']);
            if (empty($loadUsuario)) {
                $arrMsg[] = 'O e-mail informado não foi encontrado';
            }
        }

        return $arrMsg;
    }

    public static function createRecuperarSenha($post) {
        $table = 'sistema_usuario_recuperar_senha';
        $loadRecuperar = self::loadFromTabelaByCampo($table, 'email', $post['email']);
        $post['token'] = sha1(date('YmdHis'));
        if (empty($loadRecuperar)) {
            $id = parent::create($post, $table);
        } else {
            $post['id'] = $loadRecuperar['id'];
            parent::update($post, $table);
            $id = $loadRecuperar['id'];
        }
        self::sendMailRecuperarSenhaUsuarioSistema($id);
        return $id;
    }

    public static function sendMailRecuperarSenhaUsuarioSistema($id) {
        $mail = new Template('controllers/sistema/html/tpl-email-recuperar-senha.html');
        $loadRecuperar = self::loadFromTabelaByCampo('sistema_usuario_recuperar_senha', 'id', $id);
        if (!empty($loadRecuperar)) {
            $mail->set('server', Uteis::getNameServer());
            $mail->Set('token', $loadRecuperar['token']);

            $msg = $mail->show('header', 1);
            $mail = (new Mail($loadRecuperar['email'], 'Recuperar Senha', $msg, REPLY_MAIL, REPLY_NAME))->sendMail();
        }
    }

    public static function validateNovaSenha($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Senha', $post['senha'], 'text', 'y', 80);
        $vld->add_text_field('Confirma senha', $post['confirma_senha'], 'text', 'y', 80);
        $vld->add_text_field('Token', $post['token'], 'text', 'y', 80);

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        if (!empty($post['senha']) && !empty($post['confirma_senha'])) {
            $forcaDaSenha = Uteis::passwordVerify($post['senha']);
            if (!$forcaDaSenha) {
                $arrMsg[] = 'A <strong>senha:</strong>
				<ul class="list-unstyled">
				<li><i class="fal fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> entre 6 e 16 caracteres</li>
				<li><i class="fal fa-angle-right"></i>&nbsp;<strong>Deve conter</strong> letras e números</li>
				<li><i class="fal fa-angle-right"></i>&nbsp;Caracteres especiais (ex: !@#$%¨&*()_+) <strong>são opcionais</strong></li>
				</ul>';
            }

            if ($post['senha'] != $post['confirma_senha']) {
                $arrMsg[] = 'As <strong>Senhas</strong> informadas devem ser idênticas';
            }
        }

        if (!empty($post['token'])) {
            $loadRecuperar = self::loadFromTabelaByCampo('sistema_usuario_recuperar_senha', 'token', $post['token']);
            if (empty($loadRecuperar)) {
                $arrMsg[] = 'Solicitação de recuperação não encontrada.';
            }
        }

        return $arrMsg;
    }

    public static function removeRecuperarSenha($id) {
        parent::remove($id, 'sistema_usuario_recuperar_senha');
    }

}
