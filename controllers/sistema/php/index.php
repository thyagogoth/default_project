<?php

use System\Uteis;
use System\MasterAction;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');

// class Sistema extends MasterAction implements iStructure 
class Sistema extends MasterAction
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

    /*
     * Listagem dos Usuários cadastrados
     */

    public function usuarios() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/usuarios/gerenciar.html');

        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "false";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Usuários', 'action' => 'usuarios', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $body .= $tpl->Show('alert-box', 1);
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arr = [];

        if ($_SESSION['usuario']['permissao'] != '1') {
            $arr['no_permissao'] = 1;
        }

        $list = SistemaBusiness::findAllUsuario($pagination, $arr, 'a.id', 'DESC', $pagina);
        if (count($list) > 0) {
            $body .= $tpl->Show('list', 1);
            // $tpl->SetArr(SistemaBusiness::getPaginacaoCentral());
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $body .= $tpl->Show('loop', 1);
            }
            $body .= $tpl->Show('end-loop', 1);
        }
        $body .= $tpl->Show('end', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /*
     * Cadastro/Edição de um usuário no sistema
     */

    public function cadastrarusuario() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/usuarios/cadastrar.html');

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Usuários', 'action' => 'usuarios', 'active' => NULL],
                ['label' => 'Cadastro de usuário', 'action' => 'cadastrar-usuario', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        if (count($_POST) > 0) {
            $avatar = $_FILES['avatar'];
            $arrErros = SistemaBusiness::validateUsuario($_POST, $avatar);
            if (count($arrErros) <= 0) {
                if ($_POST['ativo']) {
                    $_POST['ativo'] = 'Y';
                } else {
                    $_POST['ativo'] = 'N';
                }
                unset($_POST['repita_senha']);
                if (!empty($_POST['senha'])) {
                    $_POST['senha'] = sha1($_POST['senha']);
                } else {
                    unset($_POST['senha']);
                }

                $_POST['slug'] = Uteis::slugify($_POST['nome']);
                if (empty($_POST['id'])) {
                    SistemaBusiness::createUsuario($_POST, $avatar);
                } else {
                    SistemaBusiness::updateUsuario($_POST, $avatar, $_SESSION['sistema']['login']);
                }
                Uteis::redirect($this->_LOCAL['MODULE'] . '/usuarios');
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => [
                        'error' => $arrErros
                    ]
                ];
            }
        }
        $header = $tpl->Show('header', 1);

        if (!empty($_GET['id'])) {
            $load = SistemaBusiness::loadFromTabelaByCampo('sistema_usuario', 'id', $_GET['id']);
            $tpl->set('picture', SERVER . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . UPLOAD_DIRNAME . DIRECTORY_SEPARATOR . 'usuarios'. DIRECTORY_SEPARATOR. $load['avatar']);
            $tpl->set('cod-usuario', $load['id']);
            $_POST['permissao'] = $load['permissao'];
            $tpl->Set('ativo_checked', $load['ativo'] == 'Y' ? 'checked' : '');
            $tpl->setarr($load);
        }

        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));

            $tpl->SetArr($_POST);
            $tpl->Set('ativo_selected', $_POST['ativo'] ? 'checked' : '');
            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['sess-alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $filter = [
            'ativo' => 'Y',
        ];
        $permissoes = SistemaBusiness::findAllPermissao('false', $filter, 'a.nome', 'ASC');
        $body .= $tpl->setarrselect($permissoes, $_POST, 'permissao');

        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /**
     * Excluir um usuário do sistema
     */
    public function excluirusuario() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = SistemaBusiness::loadFromTabelaByCampo('sistema_usuario', 'id', $id);
            if (!empty($load)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                SistemaBusiness::removeUsuario($id);
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/usuarios');
    }

    /**
     * Lilstagem dos módulos disponíveis no sistema
     */
    public function modulos() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/modulos/gerenciar.html');

        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "false";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Módulos', 'action' => NULL, 'active' => 'active'],
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
            if (count($_POST)) {
                $tpl->SetArr($_POST);
            }
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arr = [];
        $list = SistemaBusiness::findAllModulo($pagination, $arr, 'a.id', 'DESC', $pagina);
        if (count($list) > 0) {
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('slug', Uteis::slugify($var['label']));
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $body .= $tpl->Show('loop', 1);
            }
        }
        $body .= $tpl->Show('end-loop', 1);

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Cadastra um módulo disponibilizando-o
     * para a geração do Menu
     */
    public function cadastrarmodulo() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/modulos/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Módulos', 'action' => 'modulos', 'active' => NULL],
                ['label' => 'Cadastro', 'action' => NULL, 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        if (count($_POST)) {
            $arrValidate = SistemaBusiness::validateModulo($_POST);
            if (count($arrValidate) <= 0) {
                if ($_POST['ativo'] == 'on') {
                    $_POST['ativo'] = 'Y';
                } else {
                    $_POST['ativo'] = 'N';
                }
                if (empty($_POST['id'])) {
                    $POST['ordem'] = 1;
                    SistemaBusiness::createModulo($_POST);
                    SistemaBusiness::ordenaModulo(NULL, 'up', 0);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Cadastro</strong> efetuado com sucesso']]
                    ];
                } else {
                    SistemaBusiness::updateModulo($_POST);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Alteração</strong> efetuada com sucesso']]
                    ];
                }
                Uteis::redirect($this->_LOCAL['MODULE'] . '/modulos');
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'warning',
                    'title' => 'Atenção',
                    'mensagem' => [$arrValidate]
                ];
            }
        } else {
            $tpl->set('ativo_checked', 'checked');
        }

        $id = $_GET['id'];
        $load = SistemaBusiness::loadFromTabelaByCampo('sistema_modulo', 'id', $id);
        if (!empty($load)) {
            $tpl->setarr($load);
            $tpl->set('ativo_checked', $load['ativo'] == "Y" ? 'checked="checked"' : NULL);
        }
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

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Excluir um módulo
     */
    public function excluirmodulo() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = SistemaBusiness::loadFromTabelaByCampo('sistema_modulo', 'id', $id);
            if (!empty($load)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                SistemaBusiness::removeModulo($id);
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/modulos');
    }

    /**
     * Listagem das ações disponíveis no sistema
     */
    public function acoes() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/actions/gerenciar.html');

        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "false";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Ações', 'action' => NULL, 'active' => 'active'],
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
            if (count($_POST)) {
                $tpl->SetArr($_POST);
            }
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arr = [];
        $list = SistemaBusiness::findAllAcao($pagination, $arr, 'a.ordem', 'ASC', $pagina);
        if (count($list) > 0) {
            $body .= $tpl->Show('list', 1);
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->set('slug', Uteis::slugify($var['label']));
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));
                $tpl->Set('restrito', $var['restrito'] == 'Y' ? 'Sim' : 'Não');
                $tpl->set('cor-restrito', Uteis::get_cor_restrito($var['restrito']));
                $body .= $tpl->Show('loop', 1);
            }
        }
        $body .= $tpl->Show('end-loop', 1);

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Cadastra uma ação disponibilizando-a
     * para a geração do Menu
     */
    public function cadastraracao() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/actions/cadastrar.html');

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Ações', 'action' => 'acoes', 'active' => NULL],
                ['label' => 'Cadastro', 'action' => NULL, 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        if (count($_POST)) {
            $arrValidate = SistemaBusiness::validateAcao($_POST);
            if (count($arrValidate) <= 0) {
                if ($_POST['ativo'] == 'on') {
                    $_POST['ativo'] = 'Y';
                } else {
                    $_POST['ativo'] = 'N';
                }
                if ($_POST['restrito'] == 'on') {
                    $_POST['restrito'] = 'Y';
                } else {
                    $_POST['restrito'] = 'N';
                }
                
                if( $_POST['oculto'] == 'Y' ) {
                    $_POST['ordem'] = 999999;
                }
                
                if (empty($_POST['id'])) {
                    $id = SistemaBusiness::createAcao($_POST);
                    SistemaBusiness::ordenaAcao($id, 'up', 1);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Cadastro</strong> efetuado com sucesso']]
                    ];
                } else {
                    SistemaBusiness::updateAcao($_POST);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Alteração</strong> efetuada com sucesso']]
                    ];
                }
                Uteis::redirect($this->_LOCAL['MODULE'] . '/acoes');
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'warning',
                    'title' => 'Atenção',
                    'mensagem' => [$arrValidate]
                ];
            }
        } else {
            $tpl->set('ativo_checked', 'checked');
            $tpl->set('restrito_checked', 'checked');
        }

        $id = $_GET['id'];
        $load = SistemaBusiness::loadFromTabelaByCampo('sistema_action', 'id', $id);
        if (!empty($load)) {
            $load['id-action'] = $load['id'];
            $load['label-action'] = $load['label'];
            $tpl->setarr($load);
            $_POST['modulo'] = $load['modulo'];
            $tpl->set('restrito_checked', $load['restrito'] == "Y" ? 'checked="checked"' : NULL);
            $tpl->set('ativo_checked', $load['ativo'] == "Y" ? 'checked="checked"' : NULL);
        }
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
            $tpl->Set('restrito_checked', $_POST['restrito'] ? 'checked' : '');
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arr = [];
        $modulos = SistemaBusiness::findAllModulo('false', $arr, 'a.label', 'ASC');
        $body .= $tpl->setArrSelect($modulos, $_POST, 'modulo');

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Excluir uma ação
     */
    public function excluiracao() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = SistemaBusiness::loadFromTabelaByCampo('sistema_action', 'id', $id);
            if (!empty($load)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                SistemaBusiness::removeAcao($id);
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/acoes');
    }

    /**
     * Gerenciar Permissões
     */
    public function permissoes() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/permissoes/gerenciar.html');

        if (isset($_GET['pagination'])) {
            $pagination = $_GET['pagination'] != 'true' ? 'false' : 'true';
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        } else {
            $pagination = "false";
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 0;
        }
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Permissões', 'action' => NULL, 'active' => 'active'],
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
            if (count($_POST)) {
                $tpl->SetArr($_POST);
            }
            $tpl->Set('ativo_checked', $_POST['ativo'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arr = [];
        $list = SistemaBusiness::findAllPermissao($pagination, $arr, 'a.id', 'DESC', $pagina);
        if (count($list) > 0) {
            $body .= $tpl->Show('list', 1);
            // $tpl->SetArr(SistemaBusiness::getPaginacaoCentral());
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Ativo' : 'Inativo');
                $tpl->set('cor-ativo', Uteis::get_cor_ativo($var['ativo']));

                $body .= $tpl->Show('loop', 1);
            }
            $body .= $tpl->Show('end-loop', 1);
        }
        $body .= $tpl->Show('end', 1);

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Cadastro/Edição de Permissão e itens permitidos na permissão
     */
    public function cadastrarpermissao() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/sistema/permissoes/cadastrar.html');

        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Permissões', 'action' => 'acoes', 'active' => NULL],
                ['label' => 'Cadastro', 'action' => NULL, 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        if (count($_POST)) {
            $arrValidate = SistemaBusiness::validatePermissao($_POST);

            if (count($arrValidate) <= 0) {
                $arrAcoesPermissao = $_POST['arrAct'];

                unset($_POST['arrAct']);
                if (count($arrAcoesPermissao) > 0) {
                    foreach ($arrAcoesPermissao as $id => $p) {
                        $arrAcoesFinal[] = $p;
                    }
                } else {
                    $arrAcoesFinal = [];
                }

                if ($_POST['ativo'] == 'on') {
                    $_POST['ativo'] = 'Y';
                } else {
                    $_POST['ativo'] = 'N';
                }
                if (empty($_POST['id'])) {
                    $post['slug'] = $this->geraSlug($_POST['nome'], NULL, 'sistema_permissoes');
                    SistemaBusiness::createPermissao($_POST, $arrAcoesFinal);
                    SistemaBusiness::ordenaPermissao(NULL, 'up', 0);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Cadastro</strong> efetuado com sucesso']]
                    ];
                } else {
                    $lSlug = SistemaBusiness::loadFromTabelaByCampo('sistema_permissoes', 'slug', $_POST['slug']);
                    if (empty($_POST['slug']) || (!empty($lSlug['slug']) && $_POST['id'] != $lSlug['id'])) {
                        $_POST['slug'] = $this->geraSlug($_POST['nome'], NULL, 'sistema_permissoes', $_POST['id']);
                    }
                    SistemaBusiness::updatePermissao($_POST, $arrAcoesFinal);
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['<strong>Alteração</strong> efetuada com sucesso']]
                    ];
                }
                Uteis::redirect($this->_LOCAL['MODULE'] . '/permissoes');
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'warning',
                    'title' => 'Atenção',
                    'mensagem' => [$arrValidate]
                ];
            }
        } else {
            $arrAcoesFinal = NULL;
            $tpl->set('ativo_checked', 'checked');
            $cod_permissao = ($_GET['id'] ? $_GET['id'] : $_POST['id']);
            if (!empty($cod_permissao)) {
                $arrAcoesPermissao = SistemaBusiness::findAllActionPermissao("false", ['permissao' => $cod_permissao], 'a.id', 'DESC');
                if (count($arrAcoesPermissao) > 0) {
                    foreach ($arrAcoesPermissao as $id => $p) {
                        $arrAcoesFinal[] = $p['action'];
                    }
                } else {
                    $arrAcoesFinal = [];
                }
            }
        }
        $arrCheckboxMenu = SistemaBusiness::getMenuWithPerm('0');
        if (count($arrCheckboxMenu) > 0) {
            $htmlCheckboxes = SistemaBusiness::doHTMLCheckboxes($arrCheckboxMenu, $arrAcoesFinal);
        }
        $tpl->set('html-gerado', $htmlCheckboxes);


        $id = $_GET['id'];
        $load = SistemaBusiness::loadFromTabelaByCampo('sistema_permissoes', 'id', $id);
        if (!empty($load)) {
            $load['id-action'] = $load['id'];
            $tpl->setarr($load);
            $tpl->set('ativo_checked', $load['ativo'] == "Y" ? 'checked="checked"' : NULL);
        }
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

        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Exclui o cadastro de uma permissao
     */
    public function excluirpermissao() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = SistemaBusiness::loadFromTabelaByCampo('sistema_permissoes', 'id', $id);
            if (!empty($load)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                SistemaBusiness::removePermissao($id);
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['danger' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/permissoes');
    }

    /**
     * Gerenciamento de Menus (Exibião Nestable)
     */
    public function menu() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/gerenciamento-menu.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Menus', 'action' => NULL, 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));
        $post = [];
        if (count($_POST)) {
            $post = $_POST;
            $post['action'] = !empty($_POST['acao']) ? $_POST['acao'] : 0;
            unset($post['acao']);
            if ($post['oculto'] == 'on' || $post['oculto'] == 'Y') {
                $post['oculto'] = 'Y';
            } else {
                $post['oculto'] = 'N';
            }
            if (!empty($post['id'])) {
                SistemaBusiness::updateMenu($post);
                $_SESSION['sess-alert-box'] = [
                    'type' => 'info',
                    'title' => 'Feito!!!',
                    'mensagem' => ['success' => ['Item do menu alterado com sucesso']]
                ];
            } else {
                SistemaBusiness::createMenu($post);
                $_SESSION['sess-alert-box'] = [
                    'type' => 'info',
                    'title' => 'Feito!!!',
                    'mensagem' => ['success' => ['Item do menu criado com sucesso']]
                ];
            }
            if (isset($_GET['hide'])) {
                $get_redir = '/?hide=false';
            }
            Uteis::redirect('sistema/menu' . $get_redir);
        }
        $header = $tpl->Show('header', 1);

        $ocultar_itens = true;
        if (isset($_GET['hide']) && $_GET['hide'] == 'false') {
            $ocultar_itens = false;
        }
        $list = SistemaBusiness::getMenuEdit($ocultar_itens);
        $tpl->set('list', $list);

        $label = 'Exibir itens';
        $var_get = '/?hide=false';
        $continue = NULL;
        if (isset($_GET['hide']) && $_GET['hide'] == 'false') {
            $var_get = NULL;
            $label = 'Ocultar itens';
            $continue = '/?hide=false';
        }
        $tpl->set('label-btn-exibir', $label);
        $tpl->set('get-btn-exibir', $var_get);
        $tpl->set('get-continue', $continue);

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

        if ((int) $_SESSION['usuario']['permissao'] == 1) {
            $body .= $tpl->Show('show-botao-ocultar-itens', 1);
        }
        $body .= $tpl->Show('end-show-botao-ocultar-itens', 1);

        $filter = [
            'ativo' => 'Y'
        ];
        $acoes = SistemaBusiness::findAllAcao('false', $filter, 'b.label', 'ASC');
        $body .= $tpl->setArrSelect($acoes, $post, 'acao-cadastro');
        $body .= $tpl->setArrSelect($acoes, $post, 'acao-edicao');

        $this->showHeader($header);
        $this->showBody($body);
    }

    /*
     * Função útil a edição do menu Nestable
     */

    public function ajaxgetmenu() {
        $id = $_GET['id'];
        $load = SistemaBusiness::loadFromTabelaByCampo('sistema_menu', 'id', $id);
        echo json_encode($load);
    }

    /**
     * Exclui um item do menu
     */
    public function excluiritemmenu() {
        $id = $_GET['excluir'];
        $load = SistemaBusiness::loadFromTabelaByCampo('sistema_menu', 'id', $id);
        if (!empty($load)) {
            $id = SistemaBusiness::removeMenu($load['id']);
            $_SESSION['sess-alert-box'] = [
                'type' => 'success',
                'title' => 'Feito!!!',
                'mensagem' => ['success' => ['Item do menu criado com sucesso']]
            ];
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Desculpe!!!',
                'mensagem' => ['warning' => ['Não foi possível excluir o item solicitado. Ele existe?']]
            ];
        }
        Uteis::redirect('sistema/menu');
    }

    /**
     * Ordena os itens do menu vindos do Evento Drag&Drop do Menu Nestable
     */
//    public function jsupdatemenuorder() {
//        $arr = [];
//        $arr['id'] = $_GET['id'];
//        $arr['menu'] = $_GET['parent'];
//        
//        $orders = json_decode($_POST['data']);
//        
//        $hasSub = SistemaBusiness::findAllSubItens($arr['id']);
//        if (count($hasSub) > 0) {
//            $arr['action'] = '0';
//        }
//        SistemaBusiness::updateMenu($arr);
//
//        $itemHasSub = SistemaBusiness::findAllSubItens($arr['menu']);
//        if (count($itemHasSub) > 0) {
//            $arr2['id'] = $arr['menu'];
//            $arr2['action'] = '0';
//            SistemaBusiness::updateMenu($arr2);
//        }
//    }

    public function jsupdatemenuorder() {
        $data = json_decode($_POST['data']);
        
        $i = 0;
        foreach($data as $item) {
            $i++;
            $upd = [
                'id' => $item->id,
                'ordem' => $i,
                'menu' => 0,
            ];
            SistemaBusiness::updateMenu($upd);
            if (array_key_exists('children', $item)) {
                $o = 500;
                foreach($item->children as $child) {
                    $o++;
                    $upd2 = [
                        'menu' => $item->id,
                        'id' => $child->id,
                        'ordem' => $o
                    ];
                    SistemaBusiness::updateMenu($upd2);
                }
            }
        }

//        $i = 0;
//        foreach ($orders as $item) {
//            $i++;
//            $arr = [
//                'id' => $item->id,
//                'ordem' => $i
//            ];
//            SistemaBusiness::updateMenu($arr);
//        }
    }

    /**
     * Gera uma SLUG de identificação do item
     */
    public static function geraSlug($string, $rand = NULL, $tabela = NULL, $id = null) {
        if (!empty($tabela)) {
            $auxSlug = $string;
            if (!empty($rand)) {
                $auxSlug = "$string-$rand";
            }
            $slug = Uteis::slugify($auxSlug);
            $loadSlug = SistemaBusiness::loadFromTabelaByCampo($tabela, 'slug', $slug);
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
