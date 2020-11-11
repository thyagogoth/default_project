<?php
use System\Uteis;
use System\MasterAction;
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');

// class Contato extends MasterAction implements iStructure
class Contato extends MasterAction 
{
    private $_LOCAL = array();
    private $_SOURCE;
    private $n_paginas;

    public function __construct($module, $action, &$source)
    {
        $this->n_paginas = 10;
        $class = strtolower(get_class($this));
        $this->_SOURCE = &$source;
        $this->_LOCAL['SERVER'] = SERVER;
        $this->_LOCAL['SESSION_ID'] = SESSION_ID;
        $this->_LOCAL['HTTP_MODULE_PATH'] = SERVER . '/controllers/' . $class . '/';
        $this->_LOCAL['SERVER_MODULE_PATH'] = __CONTEXTPATH__ . $class . '/';
        $this->_LOCAL['MODULE'] = $module;
        $this->_LOCAL['ACTION'] = $action;
        parent::__construct($module, str_replace('-', '', $action));
    }

    public function showHeader($val)
    {
        $this->_SOURCE->showHeader($val);
    }

    public function showBody($val)
    {
        $this->_SOURCE->showBody($val);
    }

    public function getTemplate($path, $initVars = [])
    {
        return parent::getTemplate($path, $this->_LOCAL);
    }

    /**
     * Gerenciamento de assuntos
     */
    public function assuntos()
    {
        $header = NULL;
        $body = NULL;
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/assuntos/gerenciar.html');
        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "true";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Departamentos', 'action' => 'assuntos', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['alert-box'])) {
            $tpl->set('type', $_SESSION['alert-box']['type']);
            $tpl->set('title', $_SESSION['alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['alert-box']['mensagem']));

            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);
        $arr = [];
        $list = ContatoBusiness::findAllAssunto($pagination, $arr, 'a.ordem', 'ASC', $pagina);
        if (count($list) > 0) {
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $body .= $tpl->Show('loop', 1);
            }
        }
        $body .= $tpl->Show('end-loop', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /**
     * Cadastro de assuntos
     */
    public function cadastrarassunto()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/assuntos/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Contato', 'action' => 'assuntos', 'active' => NULL],
                ['label' => 'Cadastro de departamento', 'action' => 'cadastrar', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        $arrErros = array();
        $body = NULL;
        if (count($_POST) > 0) {
            $post = $_POST;

            $arrErros = ContatoBusiness::validateAssunto($post);
            if (count($arrErros) <= 0) {
                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }

                if (empty($post['id'])) {
                    $post['slug'] = $this->geraSlug($post['assunto'], NULL, 'contato_assunto');
                    $cod = ContatoBusiness::createAssunto($post);
                    ContatoBusiness::ordenaAssunto(NULL, 'up', 0);
                } else {
                    $lSlug = ContatoBusiness::loadFromTabelaByCampo('contato_assunto', 'slug', $post['slug']);
                    if (empty($post['slug']) || (!empty($lSlug['slug']) && $post['id'] != $lSlug['id'])) {
                        $post['slug'] = $this->geraSlug($post['assunto'], NULL, 'contato_assunto', $post['id']);
                    }
                    $cod = ContatoBusiness::updateAssunto($post, $_SESSION['sistema']['login']);
                }
                Uteis::redirect('contato/assuntos/');
            }
        } else {
            // Uteis::ajaxLimpaTemp();
            $tpl->set('ativo_checked', 'checked="checked"');
        }
        $id = empty($_GET['id']) ? $post['id'] : $_GET['id'];

        $tpl->Set('id', $id);
        if (!empty($id)) {
            $arr = ContatoBusiness::loadFromTabelaByCampo('contato_assunto', 'id', $id);
            $tpl->SetArr($arr);
            $tpl->Set('cod-item', $arr['id']);
            $tpl->Set('ativo_checked', $arr['ativo'] == 'Y' ? 'checked' : '');
        }

        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['alert-box'])) {
            $tpl->set('type', $_SESSION['alert-box']['type']);
            $tpl->set('title', $_SESSION['alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['alert-box']['mensagem']));

            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['alert-box']);
            if (count($_POST)) {
                $tpl->SetArr($_POST);
            }
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /**
     * Exclui o assunto $id
     */
    public function excluirassunto()
    {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = ContatoBusiness::loadFromTabelaByCampo('contato_assunto', 'id', $id);
            if (!empty($load)) {
                $_SESSION['alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                ContatoBusiness::removeAssunto($id);
            } else {
                $_SESSION['alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/assuntos');
    }

    /**
     * Gerenciamento de assuntos
     */
    public function mensagens()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/mensagens/gerenciar.html');
        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "true";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Mensagens', 'action' => 'assuntos', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['alert-box'])) {
            $tpl->set('type', $_SESSION['alert-box']['type']);
            $tpl->set('title', $_SESSION['alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['alert-box']['mensagem']));

            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);
        $arr = ['origem' => 0];
        $list = ContatoBusiness::findAllMensagem($pagination, $arr, 'a.id', 'DESC', $pagina);
        if (count($list) > 0) {
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->Set('lida', $var['lida'] == 'Y' ? '-alt' : '');
                $tpl->Set('b', NULL);
                $tpl->Set('/b', NULL);
                if ($var['lida'] == 'N') {
                    $tpl->Set('b', '<b>');
                    $tpl->Set('/b', '</b>');
                }
                $tpl->Set('fal', 'fal');
                if ($var['important'] == 'Y') {
                    $tpl->Set('fal', 'fas color-warning-500');
                }
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $body .= $tpl->Show('loop', 1);
            }
        }
        $body .= $tpl->Show('end-loop', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /**
     * Exclui a mensagem $id
     */
    public function excluirmensagem()
    {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = ContatoBusiness::loadFromTabelaByCampo('contato_mensagem', 'id', $id);
            if (!empty($load)) {
                $_SESSION['alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                ContatoBusiness::removeMensagem($id);
            } else {
                $_SESSION['alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/mensagens');
    }

    /**
     * Visualiza uma mensagem recebida
     */

    public function visualizar()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/mensagens/visualizar.html');

        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $tpl->set('id-main', $id);
            $load = ContatoBusiness::loadFromTabelaByCampo('contato_mensagem', 'id', $id);
            ContatoBusiness::updateMensagem(['id' => $load['id'], 'lida' => 'Y']);
            $load['horario'] = strtoupper(date('g:i a', strtotime($load['hora_cadastro_formatada'])));
            $load['time_ago'] = Uteis::time_ago($load['created_at'] . ' ' . $load['hora_cadastro']);
            $fal = 'fal';
            $color_important = '';
            if ($load['important'] == 'Y') {
                $fal = 'fas';
                $color_important = 'color-warning-500';
            }
            $load['fal'] = $fal;
            $load['color_important'] = $color_important;
            $tpl->setarr($load);
            $respostas = ContatoBusiness::findAllMensagem('true', ['origem' => $load['id']], 'a.id', 'ASC');
        } else {
            Index::error();
        }
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (count($respostas) > 0) {
            $body .= $tpl->Show('respostas', 1);
            foreach ($respostas as $resposta) {
                $resposta['horario'] = strtoupper(date('g:i a', strtotime($resposta['hora_cadastro_formatada'])));
                $resposta['time_ago'] = Uteis::time_ago($resposta['created_at'] . ' ' . $resposta['hora_cadastro']);
                $tpl->setarr($resposta);
                $body .= $tpl->Show('loop', 1);
            }
            $body .= $tpl->Show('end-loop', 1);
        }
        $body .= $tpl->Show('end-respostas', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    public static function ajaxsendresponse()
    {
        $origem = ContatoBusiness::loadFromTabelaByCampo('contato_mensagem', 'id', $_POST['origin']);
        $post = [
            'email' => $origem['email_assunto'],
            'nome' => $_SESSION['usuario']['nome'],
            'assunto' => $origem['assunto'],
            'origem' => $_POST['origin'],
            'mensagem' => $_POST['content'],
            'lida' => 'Y',
        ];
        $id = ContatoBusiness::createResposta($post);
        if ($id) {
            echo "ok";
        } else {
            echo "error";
        }
    }

    public static function ajaxsetunread()
    {
        ContatoBusiness::updateMensagem($_POST);
    }

    public static function ajaxtoggleimportant()
    {
        $id = $_POST['id'];
        $load = ContatoBusiness::loadFromTabelaByCampo('contato_mensagem', 'id', $id);
        if (!empty($load)) {
            $new_marquee = $load['important'] == 'Y' ? 'N' : 'Y';
            $upd = [
                'id' => $id,
                'important' => $new_marquee
            ];
            ContatoBusiness::updateMensagem($upd);
            echo $new_marquee;
        }
    }

    public static function geraSlug($string, $rand = NULL, $tabela = 'contato_assunto', $id = null)
    {
        $auxSlug = $string;
        if (!empty($rand)) {
            $auxSlug = "$string-$rand";
        }
        $slug = Uteis::slugify($auxSlug);
        $loadSlug = ContatoBusiness::loadFromTabelaByCampo($tabela, 'slug', $slug);
        if (!empty($loadSlug) && $loadSlug['id'] != $id) {
            return self::geraSlug($slug, Uteis::getIdAleatorio(1, 4, FALSE));
        } else {
            return $slug;
        }
    }

}
