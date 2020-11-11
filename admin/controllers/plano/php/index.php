<?php

use System\Uteis;
use System\MasterAction;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');
// class Plano extends MasterAction implements iStructure
class Plano extends MasterAction
{
    private $_LOCAL = array();
    private $_SOURCE;
    private $n_paginas;

    public function __construct($module, $action, &$source) {
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

    public function showHeader($val) {
        $this->_SOURCE->showHeader($val);
    }

    public function showBody($val) {
        $this->_SOURCE->showBody($val);
    }

    public function getTemplate($path, $initVars = []) {
        return parent::getTemplate($path, $this->_LOCAL);
    }

    public function planos() {
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
                ['label' => 'Planos', 'action' => 'planos', 'active' => 'active'],
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
        $list = PlanoBusiness::findAllPlano($pagination, $arr, 'a.ordem', 'ASC', $pagina);
        if (count($list) > 0) {
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('star-best', $var['best'] == 'Y' ? 'fas fa-star color-warning-500' : 'fal fa-star');
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $tpl->set('valor-formatado', number_format($var['valor'], 2, ',', '.'));
                $body .= $tpl->Show('loop', 1);
            }
        }
        $body .= $tpl->Show('end-loop', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    public function excluir() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $verifica = FALSE;
            // $verifica = PlanoBusiness::verifyPlanoEmUso($id);

            if ($verifica) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído', 'Há clientes associados à este plano']]
                ];
            } else {
                $load = PlanoBusiness::loadFromTabelaByCampo('planos', 'id', $id);
                if (!empty($load)) {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                    ];
                    PlanoBusiness::removePlano($id);
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
        Uteis::redirect($this->_LOCAL['MODULE'] . '/planos');
    }

    public function cadastrar()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Planos', 'action' => 'planos', 'active' => NULL],
                ['label' => 'Cadastro de plano', 'action' => 'cadastrar', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        /** CONTINUAR */
        $arrErros = array();
        $body = NULL;
        if (count($_POST) > 0) {
            $post = $_POST;

            $arrErros = PlanoBusiness::validatePlano($post);
            if (count($arrErros) <= 0) {

                if ($post['dashboard']) {
                    $post['dashboard'] = 'Y';
                } else {
                    $post['dashboard'] = 'N';
                }
                if ($post['leads']) {
                    $post['leads'] = 'Y';
                } else {
                    $post['leads'] = 'N';
                }

                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }

                if ($post['programado']) {
                    $post['programado'] = 'Y';
                } else {
                    $post['programado'] = 'N';
                    $post['data_inicio'] = '';
                    $post['data_termino'] = '';
                }
                $data_inicio = trim($post['data_inicio']);
                if (!empty($data_inicio) && $data_inicio !== '0000-00-00') {
                    $post['data_inicio'] = Uteis::formataData($post['data_inicio'], '/', '-');
                } else {
                    $post['data_inicio'] = '';
                }
                $data_termino = trim($post['data_termino']);
                if (!empty($data_termino) && $data_termino !== '0000-00-00') {
                    $post['data_termino'] = Uteis::formataData($post['data_termino'], '/', '-');
                } else {
                    $post['data_termino'] = '';
                }

                $post['valor'] = Uteis::formataPreco($post['valor']);
                if (empty($post['id'])) {
                    $post['slug'] = $this->geraSlug($post['titulo'], NULL, 'planos');
                    $cod = PlanoBusiness::createPlano($post);
                    PlanoBusiness::ordenaPlano(NULL, 'up', 0);
                } else {
                    $lSlug = PlanoBusiness::loadFromTabelaByCampo('planos', 'slug', $post['slug']);
                    if (empty($post['slug']) || (!empty($lSlug['slug']) && $post['id'] != $lSlug['id'])) {
                        $post['slug'] = $this->geraSlug($post['titulo'], NULL, 'planos', $post['id']);
                    }
                    $cod = PlanoBusiness::updatePlano($post, $_SESSION['sistema']['login']);
                }
                Uteis::redirect('plano/planos');
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
            $arr = PlanoBusiness::loadFromTabelaByCampo('planos', 'id', $id);

            if ($arr['programado'] == 'Y') {
                if ($arr['data_inicio'] !== '0000-00-00' && !empty($arr['data_inicio'])) {
                    $arr['data_inicio'] = Uteis::formataData($arr['data_inicio'], '-', '/');
                }
                if ($arr['data_termino'] !== '0000-00-00' && !empty($arr['data_termino'])) {
                    $arr['data_termino'] = Uteis::formataData($arr['data_termino'], '-', '/');
                }
            }
            $arr['valor'] = Uteis::formataPreco($arr['valor'], 1);
            $tpl->SetArr($arr);
            $tpl->Set('cod-plano', $arr['id']);
            $tpl->Set($arr['prioridade'] . '_selected', 'selected="selected"');
            $tpl->Set('programado_checked', $arr['programado'] == 'Y' ? 'checked' : '');
            $tpl->Set('dashboard_checked', $arr['dashboard'] == 'Y' ? 'checked' : '');
            $tpl->Set('leads_checked', $arr['leads'] == 'Y' ? 'checked' : '');
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
            $tpl->Set('programado_checked', $_POST['programado'] ? 'checked' : '');
            $tpl->Set($_POST['prioridade'] . '_selected', 'selected="selected"');
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    public static function ajaxtogglebest()
    {
        $id = $_GET['id'];
        PlanoBusiness::alternateBestPlan($id);
    }
    
    public static function geraSlug($string, $rand = NULL, $tabela = 'planos', $id = null) {
        if (!empty($tabela)) {
            $auxSlug = $string;
            if (!empty($rand)) {
                $auxSlug = "$string-$rand";
            }
            $slug = Uteis::slugify($auxSlug);
            $loadSlug = PlanoBusiness::loadFromTabelaByCampo($tabela, 'slug', $slug);
            if (!empty($loadSlug) && $loadSlug['id'] != $id) {
                return self::geraSlug($slug, Uteis::getIdAleatorio(1, 4, FALSE));
            } else {
                return $slug;
            }
        } else {
            return Uteis::slugify($string);
        }
    }

}
