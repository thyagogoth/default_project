<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.inc.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'sistema' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'business.php';

use System\MasterAction;
use System\Template;
use System\Uteis;

// class Index extends MasterAction implements iStructure {
class Index extends MasterAction
{

    private $_LOCAL = array();
    static $pgn;

    public function __construct($module, $action)
    {
        if (!is_dir('controllers' . DIRECTORY_SEPARATOR . $module)) {
            $module = null;
        }
        $this->_LOCAL['SERVER'] = SERVER;
        $this->_LOCAL['SITE_URL'] = SITE_URL;
        $this->_LOCAL['SESSION_ID'] = SESSION_ID;
        $this->_LOCAL['YEAR'] = date('Y');
        $this->_LOCAL['MODULE'] = $module;
        $this->_LOCAL['ACTION'] = $action;

        foreach ($_SESSION['system_configs'] as $id => $itemConfig) {
            $this->_LOCAL['config_' . $id] = $_SESSION['system_configs'][$id];
            if ($id == 'telefone' || $id == 'celular' || $id == 'whatsapp') {
                $this->_LOCAL['config_' . $id . '_formatado'] = Uteis::removeCaracter($_SESSION['system_configs'][$id]);
            }
        }
        if (!empty($module)) {
            $path = 'controllers' . DIRECTORY_SEPARATOR . strtolower($module);
            require_once $path . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'index.php';

            if (!isset($_SESSION['usuario'])) {
                $r_module = Uteis::parametroUrl(ROOT);
                $r_action = Uteis::parametroUrl($r_module);
                $args = !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '';
                $url_redir = '';
                if ($r_action != 'login') {
                    $url_redir = "?module={$r_module}&action={$r_action}{$args}";
                }
                Uteis::redirect("login{$url_redir}");
                exit;
            }

            // DEVELOPMENT_MODE | TRUE: VERIFICA PERMISSAO - FALSE: LIBERA TUDO
           
            if (RESTRICT_MODE == FALSE) {
                new $module($module, $action, $this);
            } else {
                if (isset($_SESSION['usuario'])) {
                    $loadMod = SistemaBusiness::loadFromTabelaByCampo('sistema_modulo', 'modulo', $module);
                    $loadAct = SistemaBusiness::verifyAction($action, $loadMod['id']);
                    $aux = false;
                    if (is_array($_SESSION['usuario']['permissoes']) && count($_SESSION['usuario']['permissoes']) > 0) {
                        $aux = in_array($action, $_SESSION['usuario']['permissoes'][$module]);
                    }
                    // if ( ($_SESSION['usuario']['permissao'] == 1) || (!empty($loadAct) && $aux) || $loadAct['restrito'] == 'N') {
                    // if (((!empty($loadAct) && $aux) || $loadAct['restrito'] == 'N' || empty($loadAct)) || $_SESSION['usuario']['permissao'] == 1) {
                    if ((!empty($loadAct) 
                    && $aux) || 
                    (isset($loadAct['restrito']) && $loadAct['restrito'] == 'N') || 
                    empty($loadAct)) {
                        new $module($module, $action, $this);
                    } else {
                        $this->error();
                    }
                } else {
                    if (!is_callable(array($this, str_replace('-', '', $action)))) {
                        $action = 'login';
                    }
                    $action = str_replace('-', '', $action);
                    $this->$action();
                }
            }
        } else {
            if (!is_callable(array($this, str_replace('-', '', $action)))) {
                $action = 'login';
            }
            $action = str_replace('-', '', $action);
            $this->$action();
        }
    }

    public function getTemplate($path, $local = array())
    {
        $page = parent::getTemplate($path, $this->_LOCAL);
        return $page;
    }

    public function showPage($header, $body)
    {
        $this->showHeader($header);
        $this->showBody($body);
    }

    public function showHeader($header)
    {
        $tpl = $this->getTemplate('assets/html/index.html');
        $tpl->set('header', $header);
        $header = $tpl->Show('header', 1);
        echo $header;
    }

    public function showBody($body)
    {
        $tpl = $this->getTemplate('assets/html/index.html');
        $tpl->set('top-bar', $this->topbar());
        $tpl->Set('page', $body);
        $tpl->set('sidebar-menu', $this->menu());

        $usuario = $_SESSION['usuario'];
        unset($usuario['permissoes']);
        $tpl->setarr($usuario);
        $tpl->set('SITE_URL', SITE_URL);

        $body = $tpl->Show('body', 1);
        echo $body;
    }

    /**
     * Login no painel de controle
     */
    public function login()
    {
        $tpl = $this->getTemplate('assets/html/' . LOGIN_TEMPLATE . '.html');

        if (!empty($_SESSION['usuario']['id'])) {
            Uteis::redirect('home');
            exit();
        }

        if (count($_POST) > 0 && $_POST['post'] == SESSION_ID) {
            $login = SistemaBusiness::login($_POST['login'], $_POST['senha'], null, true);
            if (empty($login)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Oops!',
                    'mensagem' => ['Senha ou usuário <strong>inválido</strong>'],
                ];
            } else {
                if ($login['ativo'] == 'Y') {
                    $aux = SistemaBusiness::findAllPermissaoByPermissao($login['permissao']);
                    if (is_array($aux)) {
                        foreach ($aux as $value) {
                            $perm[$value['modulo']][] = $value['action'];
                        }
                    }

                    $_SESSION = array();
                    $_SESSION['usuario'] = $login;
                    $_SESSION['usuario']['permissoes'] = $perm;
                    $_SESSION['usuario']['menu'] = SistemaBusiness::geraMenu($login['permissao']);
                    if (!empty($_POST['site'])) {
                        $_SESSION['usuario']['site'] = $_POST['site'];
                    }
                    if (!empty($login['avatar']) && file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios' . DIRECTORY_SEPARATOR . $login['avatar'])) {
                        $_SESSION['usuario']['avatar'] = $login['avatar'];
                    } else {
                        $_SESSION['usuario']['avatar'] = 'avatar.png';
                    }
                    if (isset($_POST['conectado']) && $_POST['conectado'] == 'Sim') {
                        $dadosManter = $login;
                        setcookie('ManterConectado', serialize($dadosManter), (time() + (30 * 24 * 3600)));
                    }

                    $upd = [
                        'id' => $_SESSION['usuario']['id'],
                        'ultimo_login' => date('Y-m-d H:i:s'),
                    ];
                    SistemaBusiness::updateUsuario($upd);

                    $var_redir = '';
                    $escape = 0;
                    if (isset($_GET)) {
                        foreach ($_GET as $id => $value) {
                            if ($id == 'module' || $id == 'action') {
                                $var_redir .= '/' . (!empty($value) ? $value : $id);
                            } else {
                                $escape++;
                                $character = $escape == 1 ? '?' : '&';
                                $var_redir .= $character . (($value) ? ($id . '=' . $value) : $id);
                            }
                        }
                        header('Location: ' . $this->_LOCAL['SERVER'] . $var_redir);
                        exit;
                    }
                    Uteis::redirect('home');
                    exit;
                } else {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'warning',
                        'title' => 'Desculpe!',
                        'mensagem' => ['Não é possível acessar o sistema. <strong>Aguarde a aprovação</strong> de outro administador'],
                    ];
                }
            }
        } else {
            if (isset($_COOKIE['ManterConectado'])) {
                $dados = unserialize($_COOKIE['ManterConectado']);
                $login = SistemaBusiness::login($dados['email'], $dados['senha'], null, false);
                if ($login['ativo'] == 'Y') {
                    $aux = SistemaBusiness::findAllPermissaoByPermissao($login['permissao']);
                    if (is_array($aux)) {
                        foreach ($aux as $value) {
                            $perm[$value['modulo']][] = $value['action'];
                        }
                    }

                    $_SESSION = array();
                    $_SESSION['usuario'] = $login;
                    $_SESSION['usuario']['permissoes'] = $perm;
                    $_SESSION['usuario']['menu'] = SistemaBusiness::geraMenu($login['permissao']);
                    if (!empty($_POST['site'])) {
                        $_SESSION['usuario']['site'] = $_POST['site'];
                    }
                    if (!empty($login['avatar']) && file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . 'usuarios' . DIRECTORY_SEPARATOR . $login['avatar'])) {
                        $_SESSION['usuario']['avatar'] = $login['avatar'];
                    } else {
                        $_SESSION['usuario']['avatar'] = 'avatar.png';
                    }

                    if ($_POST['conectado'] == 'Sim') {
                        $dadosManter = $login;
                        setcookie('ManterConectado', serialize($dadosManter), (time() + (30 * 24 * 3600)));
                    }

                    $upd = [
                        'id' => $_SESSION['usuario']['id'],
                        'ultimo_login' => date('Y-m-d H:i:s'),
                    ];
                    SistemaBusiness::updateUsuario($upd);

                    $var_redir = '';
                    $escape = 0;
                    if (isset($_GET)) {
                        foreach ($_GET as $id => $value) {
                            if ($id == 'module' || $id == 'action') {
                                $var_redir .= '/' . (!empty($value) ? $value : $id);
                            } else {
                                $escape++;
                                $character = $escape == 1 ? '?' : '&';
                                $var_redir .= $character . (($value) ? ($id . '=' . $value) : $id);
                            }
                        }
                        header('Location: ' . $this->_LOCAL['SERVER'] . $var_redir);
                        exit;
                    }
                    Uteis::redirect('home');
                    exit;
                } else {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'warning',
                        'title' => 'Desculpe!',
                        'mensagem' => ['Não é possível acessar o sistema. <strong>Aguarde a aprovação</strong> de outro administador'],
                    ];
                }
            }
        }
        $body = '';
        $link_redir = '';
        if (isset($_GET)) {
            $link_redir = '?';
            foreach ($_GET as $id => $value) {
                if (!empty($value)) {
                    $link_redir .= $id . '=' . $value . '&';
                } else {
                    $link_redir .= $id;
                }
            }
            $tpl->set('redir-link', $link_redir);
        }
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box']['type'])) {
            foreach ($_SESSION['sess-alert-box'] as $it => $value) {
                if ($it !== 'mensagem') {
                    $tpl->set($it, $_SESSION['sess-alert-box'][$it]);
                }
            }
            $mensagens = $_SESSION['sess-alert-box']['mensagem'];
            $arrMsg['error'] = $mensagens;
            $tpl->set('msg-sistema', $tpl->setarrmsgsistema($arrMsg));
            unset($_SESSION['sess-alert-box']);
            $tpl->setarr($_POST);
            $body .= $tpl->show('alert-box', 1);
        }
        $body .= $tpl->show('end-alert-box', 1);

        if (isset($_SESSION['logout-msg-box']['type'])) {
            $tpl->set('type', $_SESSION['logout-msg-box']['type']);
            $tpl->set('title', $_SESSION['logout-msg-box']['title']);
            $tpl->set('mensagem', $_SESSION['logout-msg-box']['mensagem']);
            unset($_SESSION['logout-msg-box']);
            $body .= $tpl->show('logout-box', 1);
        }
        $body .= $tpl->show('end-logout-box', 1);

        echo $body;
    }

    public function desconectar()
    {
        if (isset($_SESSION['usuario'])) {
            unset($_SESSION['usuario']);
            if ($_COOKIE['ManterConectado']) {
                setcookie('ManterConectado', null, (time() - (3600 * 24 * 30)));
            }
            $data_logout = Uteis::formata_data_extenso(date('Y-m-d'), 4) . ' às ' . date('H:i:s');
            $_SESSION['logout-msg-box'] = [
                'type' => 'primary',
                'title' => 'Mensagem do sistema!',
                'mensagem' => 'Você desconectou do servidor em ' . $data_logout,
            ];
        } else {
            $data_logout = Uteis::formata_data_extenso(date('Y-m-d'), 4) . ' às ' . date('H:i:s');
            $_SESSION['logout-msg-box'] = [
                'type' => 'info',
                'title' => 'Mensagem do sistema!',
                'mensagem' => 'Você ainda não está conectado no servidor',
            ];
        }
        header('Location: login');
    }

    public function topbar()
    {
        $tpl = new Template('assets/html/topbar.html');
        $tpl->Set('SERVER', SERVER);
        $tpl->Set('SITE_URL', SITE_URL);
        $usuario = $_SESSION['usuario'];
        unset($usuario['permissoes']);
        $tpl->SetArr($usuario);
        $body = $tpl->show('body', 1);
        return $body;
    }

    public function menu()
    {
        $tpl = new Template('assets/html/menu.html');
        $tpl->Set('SERVER', SERVER);
        if (isset($_SESSION['usuario']['nome_usuario'])) {
            $tpl->Set('nome_usuario', $_SESSION['usuario']['nome_usuario']);
        }
        $_SESSION['usuario']['avatar'] = empty($_SESSION['usuario']['avatar']) ? 'no-avatar.png' : $_SESSION['usuario']['avatar'];
        $usuario = $_SESSION['usuario'];
        unset($usuario['permissoes']);
        $tpl->SetArr($usuario);
        $body = $tpl->show('body', 1);
        return $body;
    }

    public function home()
    {
        $this->sessionVerify();

        $tpl = $this->getTemplate('assets/html/home.html');
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

        $this->showHeader($header);
        $this->showBody($body);
        return true;
    }

    public static function breadcrumb($dados)
    {
        $tpl = parent::getTemplate('assets/html/breadcrumb.html');
        $tpl->set('module', $dados['module']);
        $tpl->set('Module', ucfirst($dados['module']));
        $html = $tpl->show('html-2', 1);
        $tpl->set('active', '');
        foreach ($dados['actions'] as $id => $item) {
            $tpl->set('label', $item['label']);
            $tpl->set('action', $item['action']);
            $tpl->set('acao', 'javascript:void(0);');
            if (!empty($item['action'])) {
                $tpl->set('acao', SERVER . '/' . $dados['module'] . '/' . $item['action']);
            }
            $tpl->set('active', $item['active']);
            $tpl->set('arrow', 'angle-right');
            if ($id == count($dados['actions']) - 1) {
                $tpl->set('arrow', 'arrow-down');
            }
            $html .= $tpl->show('item-breadcrumb-2', 1);
        }
        $html .= $tpl->show('end-item-breadcrumb-2', 1);
        return $html;
    }

    public function atualizapermissao()
    {
        $this->sessionVerify();

        $path = 'controllers/sistema';
        require_once $path . '/php/index.php';
        $aux = SistemaBusiness::findAllPermissaoByPermissao($_SESSION['usuario']['permissao']);
        if (is_array($aux)) {
            foreach ($aux as $value) {
                $perm[$value['modulo']][] = $value['action'];
            }
        }
        $_SESSION['usuario']['permissoes'] = $perm;
        $_SESSION['usuario']['menu'] = SistemaBusiness::geraMenu($_SESSION['usuario']['permissao']);

        $_SESSION['sess-alert-box'] = [
            'type' => 'info',
            'title' => 'Feito',
            'mensagem' => ['success' => ['Suas <strong>permissões</strong> foram atualizadas com sucesso']],
        ];
        Uteis::redirect('');
    }

    public function recuperarasenha()
    {
        $tpl = $this->getTemplate('assets/html/recuperar-a-senha.html');

        if (!empty($_SESSION['usuario']['id'])) {
            Uteis::redirect('home');
            exit();
        }

        if (count($_POST)) {
            $arrValidate = SistemaBusiness::validateRecuperarSenha($_POST);
            if (count($arrValidate) <= 0) {
                SistemaBusiness::createRecuperarSenha($_POST);
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => [
                        'success' => ['>Verifique em seu e-mail as instruções para redefinir sua senha'],
                    ],
                ];
                Uteis::redirect('recuperar-a-senha');
                exit();
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => [
                        'error' => $arrValidate,
                    ],
                ];
            }
        }
        $body = '';
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));

            $tpl->SetArr($_POST);
            $body .= $tpl->Show('alert-box', 1);
            unset($_SESSION['sess-alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);

        echo $body;
    }

    public function novasenha()
    {
        $tpl = $this->getTemplate('assets/html/nova-senha.html');
        if (!empty($_SESSION['usuario']['id'])) {
            Uteis::redirect('home');
            exit();
        }
        $body = '';

        $token = Uteis::parametroUrl($this->_LOCAL['ACTION']);
        $loadByToken = SistemaBusiness::loadFromTabelaByCampo('sistema_usuario_recuperar_senha', 'token', $token);
        if (!empty($loadByToken)) {
            $tpl->set('nome', SistemaBusiness::loadFromTabelaByCampo('sistema_usuario', 'email', $loadByToken['email'])['nome']);
        }

        if (count($_POST) > 0) {
            $arrMsg = SistemaBusiness::validateNovaSenha($_POST);
            if (count($arrMsg) <= 0) {
                $loadRecuperarSenha = SistemaBusiness::loadFromTabelaByCampo('sistema_usuario_recuperar_senha', 'token', $_POST['token']);
                if (!empty($loadRecuperarSenha)) {
                    $loadCliente = SistemaBusiness::loadFromTabelaByCampo('sistema_usuario', 'email', $loadRecuperarSenha['email']);
                    if (!empty($loadCliente)) {
                        $arr['id'] = $loadCliente['id'];
                        $arr['senha'] = sha1($_POST['senha']);
                        SistemaBusiness::updateUsuario($arr);
                        SistemaBusiness::removeRecuperarSenha($loadRecuperarSenha['id']);
                        $_SESSION['sess-alert-box'] = [
                            'type' => 'success',
                            'title' => 'Feito',
                            'mensagem' => ['success' => ['Sua senha foi alterada com sucesso! Efetue o <a href="' . $this->_LOCAL['SERVER'] . '/login">login</a>']],
                        ];
                    }
                }
                Uteis::redirect('nova-senha/' . $loadRecuperarSenha['token']);
                exit();
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => [
                        'error' => $arrMsg,
                    ],
                ];
            }
        } else {
            //Verifica o token na URL
            $token = Uteis::parametroUrl($this->_LOCAL['ACTION']);
            if (!empty($token)) {
                $tpl->Set('token', $token);
            } else {
                Uteis::redirect('recuperar-senha');
            }
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

        echo $body;
    }

    public function settings()
    {
        $this->sessionVerify();

        $tpl = $this->getTemplate('assets/html/settings.html');
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

        $this->showHeader($header);
        $this->showBody($body);
        return true;
    }

    public static function sessionVerify()
    {
        if (isset($_SESSION['usuario']['permissoes'])) {
            return true;
        }
        Uteis::redirect('');
    }

    /**
     * Página genérica para tratamento de erros
     */
    public static function error()
    {
        $tpl = parent::getTemplate('assets/html/page_error.html');
        $tpl->set("SERVER", SERVER);
        $body = $tpl->show('html', 1);
        echo $body;
        exit;
    }

    public static function teste()
    {
        $mail = (new Mail('thyagogoth@gmail.com', 'teste Classe Mail', 'SDJ ASDLÇKJ ASDÇKJ ASDÇLJASD', 'anoneffect@gmail.com', 'Thiago F. da Rosa'))->sendMail();
    }

}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'url.php';
