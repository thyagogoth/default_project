<?php

use System\Uteis;
use System\MasterAction;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');
// class Regiao extends MasterAction implements iStructure
class Regiao extends MasterAction 
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

    public function regioes()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/gerenciar.html');
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
                ['label' => 'Regiaos', 'action' => 'regioes', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));

            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['sess-alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);
        $arr = [];
        $list = RegiaoBusiness::findAllRegiao($pagination, $arr, 'a.id', 'DESC', $pagina);
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

    public function excluir()
    {
        $id = $_GET['id'];
        if (!empty($id)) {
            $verifica = FALSE;
            // $verifica = RegiaoBusiness::verifyRegiaoEmUso($id);
            if ($verifica) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído', 'Há clientes associados à este regiao']]
                ];
            } else {
                $load = RegiaoBusiness::loadFromTabelaByCampo('regioes', 'id', $id);
                if (!empty($load)) {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                    ];
                    RegiaoBusiness::removeRegiao($id);
                } else {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'danger',
                        'title' => 'Atenção',
                        'mensagem' => ['error' => ['O <strong>item selecionado</strong> não foi encontrado']]
                    ];
                }
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/regioes');
    }

    public function cadastrar()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Regiões', 'action' => 'regioes', 'active' => NULL],
                ['label' => 'Cadastro de regiões', 'action' => 'cadastrar', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        /** CONTINUAR */
        $arrErros = array();
        $body = NULL;
        if (count($_POST) > 0) {
            $post = $_POST;

            $arrErros = RegiaoBusiness::validateRegiao($post);
            if (count($arrErros) <= 0) {

                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }

                if (empty($post['id'])) {
                    $post['slug'] = $this->geraSlug($post['regiao'], NULL, 'regioes');
                    $cod = RegiaoBusiness::createRegiao($post);
                    RegiaoBusiness::ordenaRegiao(NULL, 'up', 0);
                } else {
                    $lSlug = RegiaoBusiness::loadFromTabelaByCampo('regioes', 'slug', $post['slug']);
                    if (empty($post['slug']) || (!empty($lSlug['slug']) && $post['id'] != $lSlug['id'])) {
                        $post['slug'] = $this->geraSlug($post['regiao'], NULL, 'regioes', $post['id']);
                    }
                    $cod = RegiaoBusiness::updateRegiao($post, $_SESSION['sistema']['login']);
                }
                Uteis::redirect('regiao/regioes');
            } else {
                Uteis::ajaxLimpaTemp();
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => [
                        'error' => $arrErros
                    ]
                ];
            }
        } else {
            Uteis::ajaxLimpaTemp();
            $tpl->set('ativo_checked', 'checked="checked"');
        }
        $id = empty($_GET['id']) ? $post['id'] : $_GET['id'];
        $tpl->Set('id', $id);
        if (!empty($id)) {
            $arr = RegiaoBusiness::loadFromTabelaByCampo('regioes', 'id', $id);
            $tpl->SetArr($arr);
            $_POST['estado'] = $arr['estado'];
            $tpl->Set('cod-regiao', $arr['id']);
            $tpl->Set('ativo_checked', $arr['ativo'] == 'Y' ? 'checked' : '');
        }

        /** CONTINUAR */
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));

            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['sess-alert-box']);
            if (count($_POST)) {
                $tpl->SetArr($_POST);
            }
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arrEstados = Uteis::findAllEstados(1);
        $body .= $tpl->setarrselect($arrEstados, $_POST, 'estado');

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    public static function geraSlug($string, $rand = NULL, $tabela = 'regioes', $id = null)
    {
        $auxSlug = $string;
        if (!empty($rand)) {
            $auxSlug = "$string-$rand";
        }
        $slug = Uteis::slugify($auxSlug);
        $loadSlug = RegiaoBusiness::loadFromTabelaByCampo($tabela, 'slug', $slug);
        if (!empty($loadSlug) && $loadSlug['id'] != $id) {
            return self::geraSlug($slug, Uteis::getIdAleatorio(1, 4, FALSE));
        } else {
            return $slug;
        }
    }

}
