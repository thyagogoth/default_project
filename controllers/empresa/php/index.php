<?php

use System\Uteis;
use System\MasterAction;
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');
// class Empresa extends MasterAction implements iStructure
class Empresa extends MasterAction 
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

    /*
    * Cadastro/Edição de conteudo
    */
    public function cadastrar()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                // ['label' => 'Institucional', 'action' => NULL, 'active' => NULL],
                ['label' => 'Cadastro de conteúdo', 'action' => 'cadastrar', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));


        $arrErros = array();
        if (count($_POST)) {
            $post = $_POST;

            $files = $_POST['imagem_up'];
            /* ORDENAÇÃO DAS IMAGENS */
            if ($files) {
                $i = 0;
                $arrImg = array();
                foreach ($files as $file) {
                    $arrImg[$i]['ordem'] = $i;
                    $arrImg[$i]['imagem'] = $file;
                    $arrImg[$i]['created_at'] = date('Y-m-d H:i:s');
                    if ($post['id']) {
                        $arrImg[$i]['empresa'] = $post['id'];
                    }
                    $i++;
                }
            }
            /* END ORDENAÇÃO DAS IMAGENS */

            $arrErros = EmpresaBusiness::validateEmpresa($post);
            if (count($arrErros) <= 0) {
                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }
                if ($post['galeria']) {
                    $post['galeria'] = 'Y';
                } else {
                    $post['galeria'] = 'N';
                }

                unset($post['imagem_up']);
                if (empty($post['id'])) {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['Conteúdo gerenciado com sucesso']]
                    ];
                    $id = EmpresaBusiness::createEmpresa($post, $arrImg);
                    if (count($files) > 0) {
                        $this->cria_pasta($id['empresa']);
                    }
                    Uteis::redirect('empresa/editar-legenda/?id=/' . $id['empresa']);
                } else {
                    $_SESSION['sess-alert-box'] = [
                        'type' => 'success',
                        'title' => 'Feito',
                        'mensagem' => ['success' => ['Conteúdo gerenciado com sucesso']]
                    ];
                    $id = EmpresaBusiness::updateEmpresa($post, $arrImg);
                    if (count($files) > 0) {
                        $this->mover_arquivos($post['id']);
                    }
                    Uteis::redirect('empresa/editar-legenda/?id=' . $post['id']);
                }
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => $arrErros]
                ];
                $this->ajaxlimpatmp();
            }
        } else {
            $this->ajaxlimpatmp();
            unset($files['imagem']);
            $tpl->set('galeria_checked', 'checked');
            $tpl->set('ativo_checked', 'checked');
        }

        $id = empty($_GET['id']) ? $post['id'] : $_GET['id'];
        $tpl->Set('id', $id);
        $arr = EmpresaBusiness::lastEmpresa();
        if (!empty($arr)) {
            $arr['id-action'] = $arr['id'];
            $tpl->SetArr($arr);
            $tpl->set('empresa', $arr['id']);
            $tpl->Set('galeria_checked', $arr['galeria'] == 'Y' ? 'checked' : '');
            $tpl->Set('ativo_checked', $arr['ativo'] == 'Y' ? 'checked' : '');
        }

        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));
            $body .= $tpl->Show('alert-box', 1);
            $tpl->setarr($post);
            $tpl->Set('galeria_checked', $post['galeria'] == 'Y' ? 'checked' : '');
            $tpl->Set('ativo_checked', $post['ativo'] == 'Y' ? 'checked' : '');
            unset($_SESSION['sess-alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $imgs = EmpresaBusiness::findAllImagemByEmpresa('true', array('empresa' => $id), 'a.ordem', 'asc');
        if (count($imgs) > 0) {
            $body .= $tpl->show('btn-editar-legenda', 1);
        }
        $body .= $tpl->show('end-btn-editar-legenda', 1);

        //Setando e mostrando tudo
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function cria_pasta($id)
    {
        $load = EmpresaBusiness::loadEmpresa($id);
        if (!empty($load['id'])) {
            $pasta = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'empresa_' . $load['id'];

            if (!is_dir($pasta)) {
                @mkdir($pasta, 0777);
            } else {
                if (!is_writable($pasta)) {
                    @chmod($pasta, 0777);
                }
            }
            self::mover_arquivos($id);
        }
    }

    public function mover_arquivos($id)
    {
        $arquivos = EmpresaBusiness::findAllImagemByEmpresa('true', array('empresa' => $id), 'a.id', 'asc');
        $pasta = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'empresa_' . $id;
        if (count($arquivos) > 0) {
            if (!is_dir($pasta)) {
                $this->cria_pasta($id);
            }
            foreach ($arquivos as $arq) {
                if (file_exists(UPLOAD_DIR . DIRECTORY_SEPARATOR . $arq['imagem'])) {
                    rename(UPLOAD_DIR . DIRECTORY_SEPARATOR . $arq['imagem'], $pasta . DIRECTORY_SEPARATOR . $arq['imagem']);
                }
            }
        }
    }

    public function getimagens()
    {
        if ($_GET['id']) {
            $list = EmpresaBusiness::findAllImagemByEmpresa('true', array('empresa' => $_GET['id']), 'ordem');
            if (count($list) > 0) {
                foreach ($list as $item) {
                    $img .= $item['imagem'] . "|";
                }
                $aux = substr($img, 0, -1);
            } else {
                $aux = 'false';
            }
        } else {
            $aux = 'false';
        }
        echo $aux;
    }

    public function ajaxremoveimagem()
    {
        if ($_GET['nome']) {
            $img = EmpresaBusiness::loadImagemByName($_GET['nome']);
            EmpresaBusiness::removeFile($img['imagem'], 'empresa_' . $img['empresa']);
            EmpresaBusiness::removeImagemByName($_GET['nome']);
        } else {
            $aux = 'false';
        }
        echo $aux;
    }

    public function editarlegenda()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/editar-legenda.html');

        if (count($_POST) > 0) {
            $post = $_POST['form'];
            if (count($post['legenda']) > 0) {
                foreach ($post['legenda'] as $id => $var) {
                    $dado['id'] = $id;
                    $dado['legenda'] = $var;
                    EmpresaBusiness::updateTituloImagem($dado);
                }
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Conteúdo gerenciado com sucesso']]
                ];
            }
            Uteis::redirect('empresa/cadastrar');
        } else {
            if (!empty($_GET['id'])) {
                $tpl->Set('id', $_GET['id']);
            }
        }

        if (empty($_GET['id'])) {
            Uteis::redirect('empresa/cadastrar');
        }

        $arr['empresa'] = !empty($post['empresa']) ? $post['empresa'] : $_GET['id'];
        // $arr['id'] = Uteis::parametroUrl('id');

        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);

        if (isset($_SESSION['sess-alert-box'])) {
            $tpl->set('type', $_SESSION['sess-alert-box']['type']);
            $tpl->set('title', $_SESSION['sess-alert-box']['title']);
            $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($_SESSION['sess-alert-box']['mensagem']));
            $body .= $tpl->Show('alert-box', 1);
            $tpl->setarr($post);
            $tpl->Set('galeria_checked', $post['galeria'] == 'Y' ? 'checked' : '');
            $tpl->Set('ativo_checked', $post['ativo'] == 'Y' ? 'checked' : '');
            unset($_SESSION['sess-alert-box']);
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $arrFiles = EmpresaBusiness::findAllImagemByEmpresa('true', $arr, 'a.id', 'DESC');
        if (count($arrFiles) > 0) {
            $body .= $tpl->Show('galeria', 1);

            foreach ($arrFiles as $arr) {
                if (!empty($arr['imagem'])) {
                    $tpl->setArr($arr);
                    $body .= $tpl->Show('loop-imagem', 1);
                }
            }
            $body .= $tpl->Show('end-loop-imagem', 1);
        }
        $body .= $tpl->Show('end-galeria', 1);

        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function ajaxlimpatmp()
    {
        if (isset($_SESSION['imagens_tmp'])) {
            foreach ($_SESSION['imagens_tmp'] as $img) {
                EmpresaBusiness::removeFile($img);
            }
            unset($_SESSION['imagens_tmp']);
        }
    }
}
