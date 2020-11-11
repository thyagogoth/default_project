<?php

use System\Uteis;
use System\MasterAction;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');

class Noticia extends MasterAction
{

    private $_LOCAL = array();
    private $_SOURCE;

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

    public function noticias() {
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
                ['label' => 'Notícias', 'action' => 'noticias', 'active' => 'active'],
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
        $list =  NoticiaBusiness::findAllNoticia('false', $arr, 'a.ordem', 'ASC', $pagina);
        if (count($list) > 0) {
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('galeria', $var['galeria'] == 'Y' ? 'Sim' : 'Não');
                $tpl->Set('destaque', $var['destaque'] == 'Y' ? 'Sim' : 'Não');
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

    public function excluir() {
        $id = $_GET['id'];
        if (!empty($id)) {
            $load = NoticiaBusiness::loadFromTabelaByCampo('noticia', 'id', $id);
            if (!empty($load)) {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Item <strong>excluído</strong> com sucesso']]
                ];
                NoticiaBusiness::removeNoticia($id);
            } else {
                $_SESSION['sess-alert-box'] = [
                    'type' => 'danger',
                    'title' => 'Atenção',
                    'mensagem' => ['error' => ['O <strong>item selecionado</strong> não foi encontrado']]
                ];
            }
        } else {
            $_SESSION['sess-alert-box'] = [
                'type' => 'warning',
                'title' => 'Atenção',
                'mensagem' => ['error' => ['O <strong>item selecionado</strong> não pode ser excluído']]
            ];
        }
        Uteis::redirect($this->_LOCAL['MODULE'] . '/noticias');
    }

    public function cadastrar() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/cadastrar.html');
        $data = [
            'module' => $this->_LOCAL['MODULE'],
            'actions' => [
                ['label' => 'Notícias', 'action' => 'noticias', 'active' => NULL],
                ['label' => 'Cadastro de notícia', 'action' => 'cadastrar', 'active' => 'active'],
            ]
        ];
        $tpl->set('breadcrumb', Index::breadcrumb($data));

        /** CONTINUAR */
        $arrErros = array();
        $body = NULL;
        if (count($_POST) > 0) {
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
                        $arrImg[$i]['noticia'] = $post['id'];
                    }
                    $i++;
                }
                $_SESSION['imagens_tmp'] = $files;
            }
            /* END ORDENAÇÃO DAS IMAGENS */

            $arrErros = NoticiaBusiness::validateNoticia($post, $files);
            if (count($arrErros) <= 0) {
                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }
                if ($post['share']) {
                    $post['share'] = 'Y';
                } else {
                    $post['share'] = 'N';
                }
                if ($post['galeria']) {
                    $post['galeria'] = 'Y';
                } else {
                    $post['galeria'] = 'N';
                }

                if ($post['destaque']) {
                    $post['destaque'] = 'Y';
                } else {
                    $post['destaque'] = 'N';
                }

                if ($post['programado']) {
                    $post['programado'] = 'Y';
                } else {
                    $post['programado'] = 'N';
                    $post['data_inicio'] = '';
                    $post['data_termino'] = '';
                }

                $data_inicio = trim($post['data_inicio']);
                if (!empty($data_inicio)) {
                    $post['data_inicio'] = Uteis::formataData($post['data_inicio'], '/', '-');
                    $hora_termino = trim($_POST['hora_inicio']);
                    if (!empty($hora_termino)) {
                        $post['data_inicio'] = $post['data_inicio'] . ' ' . $_POST['hora_inicio'];
                    }
                }

                $data_termino = trim($post['data_termino']);
                if (!empty($data_termino)) {
                    $post['data_termino'] = Uteis::formataData($post['data_termino'], '/', '-');
                    $hora_termino = trim($_POST['hora_termino']);
                    if (!empty($hora_termino)) {
                        $post['data_termino'] = $post['data_termino'] . ' ' . $_POST['hora_termino'];
                    }
                }

                unset($post['imagem_up']);
                if (empty($post['id'])) {
                    $post['slug'] = $this->geraSlug($post['titulo'], NULL, 'noticia');
                    $cod = NoticiaBusiness::createNoticia($post, $arrImg);
                    NoticiaBusiness::ordenaNoticia(NULL, 'up', 0);
                    $this->cria_pasta($cod);
                    unset($_SESSION['imagens_tmp']);
                    Uteis::redirect('noticia/editar-legenda/?id=' . $cod);
                } else {
                    $lSlug = NoticiaBusiness::loadFromTabelaByCampo('noticia', 'slug', $post['slug']);
                    if (empty($post['slug']) || (!empty($lSlug['slug']) && $post['id'] != $lSlug['id'])) {
                        $post['slug'] = $this->geraSlug($post['titulo'], NULL, 'noticia', $post['id']);
                    }
                    $cod = NoticiaBusiness::updateNoticia($post, $arrImg, $_SESSION['sistema']['login']);
                    $this->mover_arquivos($post['id']);
                    unset($_SESSION['imagens_tmp']);
                    Uteis::redirect('noticia/editar-legenda/?id=' . $cod);
                }
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
            $tpl->set('galeria_checked', 'checked="checked"');
            $tpl->set('destaque_checked', '');
            $tpl->set('share_selected', 'checked="checked"');
        }
        $id = empty($_GET['id']) ? $post['id'] : $_GET['id'];
        $tpl->Set('id', $id);
        if (!empty($id)) {
            $arr = NoticiaBusiness::loadFromTabelaByCampo('noticia', 'id', $id);
            if ($arr['programado'] == 'Y') {
                if ($arr['data_inicio'] !== '0000-00-00' && !empty($arr['data_inicio'])) {
                    $arr['data_inicio'] = Uteis::formataData($arr['data_inicio'], '-', '/');
                }
                if ($arr['data_termino'] !== '0000-00-00' && !empty($arr['data_termino'])) {
                    $arr['data_termino'] = Uteis::formataData($arr['data_termino'], '-', '/');
                }
            }
            $tpl->SetArr($arr);
            $tpl->Set('cod-noticia', $arr['id']);
            $tpl->Set('galeria_checked', $arr['galeria'] == 'Y' ? 'checked' : '');
            $tpl->Set('destaque_checked', $arr['destaque'] == 'Y' ? 'checked' : '');
            $tpl->Set('ativo_checked', $arr['ativo'] == 'Y' ? 'checked' : '');
            $tpl->Set('share_selected', $arr['share'] == 'Y' ? 'checked' : '');
            $tpl->Set('programado_checked', $arr['programado'] == 'Y' ? 'checked' : '');
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
            $tpl->Set('galeria_checked', $_POST['galeria'] ? 'checked' : '');
            $tpl->Set('destaque_checked', $_POST['destaque'] ? 'checked' : '');
        }
        $body .= $tpl->Show('end-alert-box', 1);

        $imagens = NoticiaBusiness::findAllImagemByNoticia('true', ['noticia' => $arr['id']], 'a.ordem', 'asc');
        if (!empty($arr['id']) && count($imagens) > 0) {
            $body .= $tpl->Show('btn-editar-legendas', 1);
        }
        $body .= $tpl->Show('end-btn-editar-legendas', 1);


        $this->showHeader($header);
        $this->showBody($body);
        return TRUE;
    }

    /**
     * Insere/Edita legenda das imagens cadastrada da notícia $id
     */
    public function editarlegenda() {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/editar-legenda.html');

        if (count($_POST) > 0) {
            $post = $_POST['form'];
            if (count($post['legenda']) > 0) {
                foreach ($post['legenda'] as $id => $var) {
                    $dado['id'] = $id;
                    $dado['legenda'] = $var;
                    NoticiaBusiness::updateTituloImagem($dado);
                }
                $_SESSION['sess-alert-box'] = [
                    'type' => 'success',
                    'title' => 'Feito',
                    'mensagem' => ['success' => ['Conteúdo gerenciado com sucesso']]
                ];
            }
            Uteis::redirect('noticia/noticias');
        } else {
            if (!empty($_GET['id'])) {
                $tpl->Set('id', $_GET['id']);
            }
        }

        if (empty($_GET['id'])) {
            Uteis::redirect('noticia/cadastrar');
        }

        $arr['noticia'] = !empty($post['noticia']) ? $post['noticia'] : $_GET['id'];

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

        $arrFiles = NoticiaBusiness::findAllImagemByNoticia('true', $arr, 'a.id', 'DESC');
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

    public function cria_pasta($id) {
        $load = NoticiaBusiness::loadFromTabelaByCampo('noticia', 'id', $id);
        if (!empty($load['id'])) {
            $pasta = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'noticia_' . $load['id'];
            if (!is_dir($pasta)) {
                @mkdir($pasta, 0777);
            } else {
                if (!is_writable($pasta)) {
                    @chmod($pasta, 0777);
                }
            }
            self::mover_arquivos($id);
        }
        return true;
    }

    public function mover_arquivos($id) {
        $arquivos = NoticiaBusiness::findAllImagemByNoticia('true', array('noticia' => $id), 'a.id', 'asc');
        $pasta = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'noticia_' . $id;
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
        return true;
    }

    public function getimagens() {
        $img = NULL;
        if ($_GET['id']) {
            $list = NoticiaBusiness::findAllImagemByNoticia('true', array('noticia' => $_GET['id']), 'ordem');
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

    public function ajaxremoveimagem() {
        if ($_GET['nome']) {
            $img = NoticiaBusiness::loadImagemByName($_GET['nome']);
            NoticiaBusiness::removeFile($img['imagem'], 'noticia_' . $img['noticia']);
            NoticiaBusiness::removeImagemByName($_GET['nome']);
        } else {
            $aux = 'false';
        }
        echo $aux;
    }

    public static function geraSlug($string, $rand = NULL, $tabela = 'noticia', $id = null) {
        $auxSlug = $string;
        if (!empty($rand)) {
            $auxSlug = "$string-$rand";
        }
        $slug = Uteis::slugify($auxSlug);
        $loadSlug = NoticiaBusiness::loadFromTabelaByCampo($tabela, 'slug', $slug);
        if (!empty($loadSlug) && $loadSlug['id'] != $id) {
            return self::geraSlug($slug, Uteis::getIAleatorio(1, 4, FALSE));
        } else {
            return $slug;
        }
    }

}
