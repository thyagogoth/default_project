<?php
use System\MasterBusiness;
use System\UploadArquivo;
use System\Uteis;
use System\ValidationFields;

class ConfigBusiness extends MasterBusiness {

    public static function getPaginacao() {
        return preg_replace('/&(?!amp;|nbsp;)/', '&amp;', parent::$pgn->paginas());
    }

    public static function getTotalPaginacao() {
        return parent::$pgn->getTotal();
    }

    public static function getPaginacaoCentral() {
        $aux = parent::$pgn->getInfoPgn();
        if ($aux['paginacao']) {
            $aux['paginacao'] = preg_replace('/&(?!amp;|nbsp;)/', '&amp;', $aux['paginacao']);
        }
        return $aux;
    }

    public static function createLogotipo($post, $topo = '', $rodape = '') {
        $post['created_at'] = date('Y-m-d H:i:s');
        if (!empty($topo['name'])) {
            $arquivo = UploadArquivo::upload(array(0 => $topo), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo', '');
            $post['logotipo_topo'] = $arquivo[0]['nomeFormatado'];
        }
        if (!empty($rodape['name'])) {
            $arquivo = UploadArquivo::upload(array(0 => $rodape), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo', '');
            $post['logotipo_rodape'] = $arquivo[0]['nomeFormatado'];
        }
        $id = parent::create($post, 'system_config');
        return $id;
    }

    public static function updateLogotipo($post, $topo = '', $rodape = '', $usuario = '') {
        $tabela = 'system_config';

        if (!empty($topo['name'])) {
            self::removeImagemLogotipo($post['id'], 'topo');
            $arquivo = UploadArquivo::upload(array(0 => $topo), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo', '');
            $post['logotipo_topo'] = $arquivo[0]['nomeFormatado'];
        }
        if (!empty($rodape['name'])) {
            self::removeImagemLogotipo($post['id'], 'rodape');
            $arquivo = UploadArquivo::upload(array(0 => $rodape), UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo', '');
            $post['logotipo_rodape'] = $arquivo[0]['nomeFormatado'];
        }
        $alt = parent::update($post, $tabela);
        return $alt;
    }

    public static function removeImagemLogotipo($id, $area = 'topo') {
        $img = self::lastConfig($id);

        $dimensoes[] = '330x130_fill_';
        $dimensoes[] = '330x130_crop_';
        switch ($area) {
            case '':
            case 'topo':
            default:
                foreach ($dimensoes as $dimensao) {
                    if (file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $dimensao . $img['logotipo_topo'])) {
                        @unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $dimensao . $img['logotipo_topo']);
                    }
                }
                @unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $img['logotipo_topo']);
                break;
            case 'rodape':
                foreach ($dimensoes as $dimensao) {
                    if (file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $dimensao . $img['logotipo_rodape'])) {
                        @unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $dimensao . $img['logotipo_rodape']);
                    }
                }
                @unlink(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo/' . $img['logotipo_rodape']);
                break;
        }
    }

    public static function lastConfig($id = '') {
        $query = NULL;
        if ($id) {
            $query .= "WHERE a.id = '" . $id . "'";
        }
        $query = "SELECT a.* FROM system_config a " . $query . " ORDER BY a.id DESC LIMIT 1";
        $cs = parent::fetchArray($query);
        return $cs;
    }

    public static function validateLogotipo($topo = '', $rodape = '') {
        $arrMsg = array();
        $arrMsg1 = array();

        if (!$post['id']) {
            if (!is_writable(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo')) {
                @mkdir(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo');
                chmod(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'logotipo', 777);
            }
            $vl = UploadArquivo::validateArquivo(array(0 => $topo), '/(jpg|jpeg|gif|png)$/', '524288', 0); //500 KB
            $vl2 = UploadArquivo::validateArquivo(array(0 => $rodape), '/(jpg|jpeg|gif|png)$/', '524288', 0); //500 KB
        } else {
            $vl = UploadArquivo::validateArquivo(array(0 => $topo), '/(jpg|jpeg|gif|png)$/', '524288', 0); // 500 KB
            $vl2 = UploadArquivo::validateArquivo(array(0 => $rodape), '/(jpg|jpeg|gif|png)$/', '524288', 0); //500 KB
        }
        if (count($vl) > 0) {
            foreach ($vl as $var) {
                $arrMsg[] = $var;
            }
        }
        if (count($vl2) > 0) {
            foreach ($vl2 as $var2) {
                $arrMsg1[] = $var2;
            }
        }
        if (count($arrMsg1) > 0) {
            $rtn = array_merge($arrMsg, $arrMsg1);
        } else {
            $rtn = $arrMsg;
        }
        return $rtn;
    }

    public static function validateInformacoesContato($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Rua', $post['rua'], 'text', 'n', 255);
        $vld->add_text_field('Número', $post['numero'], 'text', 'n', 255);
        $vld->add_text_field('Complemento', $post['complemento'], 'text', 'n', 255);
        $vld->add_text_field('Bairro', $post['bairro'], 'text', 'n', 255);
        $vld->add_text_field('Cidade', $post['cidade'], 'text', 'n', 255);
        $vld->add_text_field('Estado', $post['uf'], 'text', 'n', 255);
        $vld->add_link_field('E-mail', $post['email'], 'email', 'n', 255);
        $vld->add_link_field('telefone', $post['telefone'], 'text', 'n', 255);
        $vld->add_link_field('WhatsApp', $post['whatsapp'], 'text', 'n', 255);

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    public static function createInformacoesContato($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'system_config');
        return $id;
    }

    public static function updateInformacoesContato($post, $usuario = '') {
        $tabela = 'system_config';
        $alt = parent::update($post, 'system_config');
        return $alt;
    }

    public static function validateConfiguracoesSMTP($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        if (!empty($post['php_mailer_username'])) {
            $vld->add_link_field('E-mail', $post['php_mailer_username'], 'email', 'y', 255);
        }

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    public static function createConfiguracoesSMTP($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'system_config');
        return $id;
    }

    public static function updateConfiguracoesSMTP($post, $usuario = '') {
        $tabela = 'system_config';

        $alt = parent::update($post, 'system_config');
        return $alt;
    }

    public static function validateConfiguracoesPagseguro($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        if (!empty($post['email_pagseguro'])) {
            $vld->add_link_field('E-mail', $post['pagseguro_email'], 'email', 'y', 255);
        }
        $vld->add_text_field('Token', $post['pagseguro_token'], 'text', 'y', 255);

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }

        return $arrMsg;
    }

    public static function createConfiguracoesPagseguro($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'system_config');
        return $id;
    }

    public static function updateConfiguracoesPagseguro($post, $usuario = '') {
        $tabela = 'system_config';

        $alt = parent::update($post, $tabela);
        return $alt;
    }

    /**
     * ICONES
     */
    public static function createIcone($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'system_config_icones');
        return $id;
    }

    public static function updateIcone($post, $usuario = '') {
        $tabela = 'system_config_icones';

        $alt = parent::update($post, $tabela);
        return $alt;
    }

    public static function removeIcones($id) {
        $alt = parent::remove($id, 'system_config_icones');
        return $alt;
    }

    public static function loadIcone() {
        $query = "SELECT a.* FROM system_config_icones a WHERE a.id = '" . $id . "'";
        $cs = parent::fetchArray($query);
        return $cs;
    }

    public static function findAllIcone($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        
        if ($arr['busca']) {
            $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
            $i = 1;
            foreach ($termos as $item) {
                $termo = Uteis::space2like($item);
                if ($i < 2) {
                    $query = "AND (a.icone LIKE '" . $termo . "') ";
                } else {
                    $query .= "OR (a.icone LIKE '" . $termo . "') ";
                }
                $i++;
            }
        }
        if ($arr['ativo']) {
            $query .= "AND a.ativo = '" . $arr['ativo'] . "' ";
        }
        if ($arr['itens']) {
            $n_de_resultados = $arr['itens'];
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.*, DATE_FORMAT('a.created_at', '%d/%m/%Y') AS created_at_formatada,
			   DATE_FORMAT('a.created_at', '%H:%i') AS hora_cadastro_formatada,
			   DATE_FORMAT('a.updated_at', '%d/%m/%Y') AS updated_at_formatada,
			   DATE_FORMAT('a.updated_at', '%H:%i') AS hora_atualizacao_formatada FROM system_config_icones a " . $query . " ORDER BY " . $campo . ' ' . $ord;
        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function validateIcone($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        $vld->add_text_field('Nome', $post['nome'], 'text', 'y', 255);
        $vld->add_text_field('&Iacute;cone', $post['icone'], 'text', 'y', 255);

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        return $arrMsg;
    }

    public static function atualizaAtiva($id, $tabela = 'config_icones') {
        switch ($tabela) {
            case 'config_icones':
                $coluna = self::loadIcone($id);
                break;
        }
        if ($coluna['ativo'] == 'Y') {
            $n = 'N';
        } else if ($coluna['ativo'] == 'N') {
            $n = 'Y';
        }
        $con = self::getConnection();
        $con->query("UPDATE $tabela SET ativo = '" . $n . "' WHERE id = '" . $id . "'");
        $con->close();
    }

    public static function findAllRedeSocial($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        if ($arr['busca']) {
            $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
            $i = 1;
            foreach ($termos as $item) {
                $termo = Uteis::space2like($item);
                if ($i < 2) {
                    $query = "AND (a.titulo LIKE '" . $termo . "') ";
                } else {
                    $query .= "OR (a.titulo LIKE '" . $termo . "') ";
                }
                $i++;
            }
        }
        if ($arr['ativo']) {
            $query .= "AND a.ativo = '" . $arr['ativo'] . "' ";
        }
        if ($arr['itens']) {
            $n_de_resultados = $arr['itens'];
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.*, DATE_FORMAT('a.created_at', '%d/%m/%Y') AS created_at_formatada,
			   DATE_FORMAT('a.created_at', '%H:%i') AS hora_cadastro_formatada,
			   DATE_FORMAT('a.updated_at', '%d/%m/%Y') AS updated_at_formatada,
			   DATE_FORMAT('a.updated_at', '%H:%i') AS hora_atualizacao_formatada FROM system_config_redes_sociais a " . $query . " ORDER BY " . $campo . ' ' . $ord;
        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function truncateAndCreate($post = '') {
        $con = self::getConnection();
        $con->query("TRUNCATE TABLE system_config_redes_sociais");
        $con->close();
        if (count($post) > 0) {
            foreach ($post as $item) {
                $item['created_at'] = date('Y-m-d H:i:s');
                parent::create($item, 'system_config_redes_sociais');
            }
        }
    }

    public static function loadFromTabela($campo, $value, $tabela = 'system_config') {
        $query = "SELECT a.* FROM $tabela a WHERE a.$campo = '" . $value . "'";
        $cs = parent::fetchArray($query);
        return $cs;
    }

    public static function removeRede($id, $tabela = 'system_config') {
        parent::remove($id, $tabela);
    }

    public static function findAllBanco($modo = 'false', $arr = '', $campo = 'a.id', $ord = 'desc', $pagina = 0) {
        $query = '';
        $n_de_resultados = PER_PAGE_LIMIT;
        if ($arr['busca']) {
            $termos = explode(' ', str_replace('  ', ' ', $arr['busca']));
            $i = 1;
            foreach ($termos as $item) {
                $termo = Uteis::space2like($item);
                if ($i < 2) {
                    $query = "AND (a.banco LIKE '" . $termo . "') ";
                } else {
                    $query .= "OR (a.banco LIKE '" . $termo . "') ";
                }
                $i++;
            }
        }
        if ($arr['ativo']) {
            $query .= "AND a.ativo = '" . $arr['ativo'] . "' ";
        }
        if ($arr['itens']) {
            $n_de_resultados = $arr['itens'];
        }
        if ($query) {
            $query = 'WHERE' . strstr($query, ' ');
        }

        $query = "SELECT a.*, DATE_FORMAT('a.created_at', '%d/%m/%Y') AS created_at_formatada,
			   DATE_FORMAT('a.created_at', '%H:%i') AS hora_cadastro_formatada,
			   DATE_FORMAT('a.updated_at', '%d/%m/%Y') AS updated_at_formatada,
			   DATE_FORMAT('a.updated_at', '%H:%i') AS hora_atualizacao_formatada FROM system_config_bancos a " . $query . " ORDER BY " . $campo . ' ' . $ord;
        if ($modo == 'true') {
            $cs = parent::transformFetchToArray($query);
        } else {
            $cs = parent::transformFetchToArrayPgn($query, $pagina, $n_de_resultados);
        }
        return $cs;
    }

    public static function validateIntegracoes($post) {
        $arrMsg = array();
        $vld = new ValidationFields();

        switch ($post['pagar_me_modo']) {
            case '':
            default:
                break;
            case 'sandbox':
                $vld->add_text_field('API KEY SANDBOX', $post['pagar_me_api_key_sandbox'], 'text', 'y', 255);
                $vld->add_text_field('SECRET KEY PRODUÇÃO', $post['pagar_me_api_key_criptografia_sandbox'], 'text', 'y', 255);
                $vld->add_link_field('Postback URL', $post['pagar_me_postback_url'], 'text', 'y', 255);
                break;
            case 'production':
                $vld->add_text_field('API KEY PRODUÇÃO', $post['pagar_me_api_key_producao'], 'text', 'y', 255);
                $vld->add_text_field('SECRET KEY PRODUÇÃO', $post['pagar_me_api_key_criptografia_producao'], 'text', 'y', 255);
                $vld->add_link_field('Postback URL', $post['pagar_me_postback_url'], 'text', 'y', 255);
                break;
        }

        switch ($post['eadim_api_modo']) {
            case '':
            default:
                break;
            case 'sandbox':
                $vld->add_link_field('URL SANDBOX', $post['eadim_api_url_sandbox'], 'text', 'y', 255);
                $vld->add_text_field('TOKEN SANDBOX', $post['eadim_api_token_sandbox'], 'text', 'y', 255);
                break;
            case 'production':
                $vld->add_link_field('URL PRODUÇÃO', $post['eadim_api_url_producao'], 'text', 'y', 255);
                $vld->add_text_field('TOKEN PRODUÇÃO', $post['eadim_api_token_producao'], 'text', 'y', 255);
                break;
        }

        if (!$vld->validation()) {
            $arrMsg = $vld->create_msg();
        }
        return $arrMsg;
    }

    public static function createIntegracoes($post) {
        $post['created_at'] = date('Y-m-d H:i:s');
        $id = parent::create($post, 'system_config');
        return $id;
    }

    public static function updateIntegracoes($post, $usuario = '') {
        $tabela = 'system_config';
        $alt = parent::update($post, 'system_config');
        return $alt;
    }

}
