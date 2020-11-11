<?php
use System\MasterAction;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'business.php');
// class Config extends MasterAction implements iStructure
class Config extends MasterAction
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

    public function getTemplate($path)
    {
        return parent::getTemplate($path, $this->_LOCAL);
    }

    public function cadastrarlogotipo()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-cadastrar-logotipo.html');
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = array_map(trim, $_POST['form']);
            $topo = $_FILES['logotipo_topo'];
            $rodape = $_FILES['logotipo_rodape'];
            $arrErros = ConfigBusiness::validateLogotipo($topo, $rodape);
            if (count($arrErros) <= 0) {
                if (empty($post['id'])) {
                    ConfigBusiness::createLogotipo($post, $topo, $rodape);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'create');
                } else {
                    ConfigBusiness::updateLogotipo($post, $topo, $rodape, $_SESSION['sistema']['login']);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'update');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        }
        $header = $tpl->Show('header', 1);
        if (empty($post['id'])) {
            $arr = ConfigBusiness::lastConfig();
            if (!empty($arr)) {
                $id = $arr['id'];
                $tpl->SetArr($arr);
            }
        }
        $tpl->Set('id', $arr['id']);
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
        }
        $body = $tpl->Show('body', 1);
        if ($id) {
            if ($arr['logotipo_topo']) {
                $body .= $tpl->Show('logotipo_topo', 1);
            }
        }
        $body .= $tpl->Show('end-logotipo_topo', 1);
        if ($id) {
            if ($arr['logotipo_rodape']) {
                $body .= $tpl->Show('logotipo_rodape', 1);
            }
        }
        $body .= $tpl->Show('end-logotipo_rodape', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function informacoesdecontato()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-informacoes-de-contato.html');
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = array_map(trim, $_POST['form']);
            $arrErros = ConfigBusiness::validateInformacoesContato($post);
            if (count($arrErros) <= 0) {
                if (empty($post['id'])) {
                    ConfigBusiness::createInformacoesContato($post);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'create');
                } else {
                    ConfigBusiness::updateInformacoesContato($post, $_SESSION['sistema']['login']);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'update');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        }
        $header = $tpl->Show('header', 1);
        if (empty($post['id'])) {
            $arr = ConfigBusiness::lastConfig();
            if (!empty($arr)) {
                $id = $arr['id'];
                $post['uf'] = $arr['uf'];
                $tpl->SetArr($arr);
            }
        }
        $tpl->Set('id', $arr['id']);
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
        }
        $body = $tpl->Show('body', 1);
        $arrEstados = Uteis::findAllEstados(1);
        $body .= $tpl->setArrSelect($arrEstados, $post, 'uf');
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function cadastrarpagseguro()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-cadastrar-pagseguro.html');
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = array_map(trim, $_POST['form']);
            $arrErros = ConfigBusiness::validateConfiguracoesPagseguro($post);
            if (count($arrErros) <= 0) {
                if (empty($post['id'])) {
                    ConfigBusiness::createConfiguracoesPagseguro($post);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'create');
                } else {
                    ConfigBusiness::updateConfiguracoesPagseguro($post, $_SESSION['sistema']['login']);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'update');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        }
        $header = $tpl->Show('header', 1);
        if (empty($post['id'])) {
            $arr = ConfigBusiness::lastConfig();
            if (!empty($arr)) {
                $id = $arr['id'];
                $tpl->SetArr($arr);
            }
        }
        $tpl->Set('id', $arr['id']);
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
        }
        $body = $tpl->Show('body', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function configuracoessmtp()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-configuracoes-smtp.html');
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = array_map(trim, $_POST['form']);
            $arrErros = ConfigBusiness::validateConfiguracoesSMTP($post);
            if (count($arrErros) <= 0) {
                if (empty($post['id'])) {
                    ConfigBusiness::createConfiguracoesSMTP($post);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'create');
                } else {
                    ConfigBusiness::updateConfiguracoesSMTP($post, $_SESSION['sistema']['login']);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'update');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        }
        $header = $tpl->Show('header', 1);
        if (empty($post['id'])) {
            $arr = ConfigBusiness::lastConfig();
            if (!empty($arr)) {
                $id = $arr['id'];
                $tpl->SetArr($arr);
            }
        }
        $tpl->Set('id', $arr['id']);
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
        }
        $body = $tpl->Show('body', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function webservice()
    {
        $p = Uteis::parametroUrl('web-service');
        $param = Uteis::parametroUrl($p);
        $rtn = new stdClass();
        switch ($p) {
            case 'get-cep':
                if ($param) {
                    $rtn = uteis::busca_endereco($param);
                    if (!empty($rtn)) {
                        $lc = ConfigBusiness::loadFromTabela('cidade', $rtn->localidade, 'cidades');
                        $rtn->id_localidade = $lc['id'];
                        $rtn->status = 'ok';
                    }
                }
                break;
        }
        echo json_encode($rtn);
    }

    // ÍCONES
    public function gerenciaricone()
    {
        if (is_numeric(Uteis::parametroUrl('gerenciar-icone'))) {
            if (Uteis::parametroUrl('gerenciar-icone')) {
                ConfigBusiness::removeconfig(Uteis::parametroUrl('gerenciar'));
                IndexCentral::redirect('gerenciar', $this->_LOCAL['MODULE'], 'remove');
            }
            //        } else {
            //            if (Uteis::parametroUrl('gerenciar-icone')) {
            //                $cod = Uteis::parametroUrl('gerenciar-icone');
            //                ConfigBusiness::atualizaAtiva($cod, 'config_icones');
            //            }
        }
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-gerenciar-icone.html');
        $header = $tpl->Show('header', 1);
        $ord = (empty($_GET['ord']) ? 'desc' : ($_GET['ord'] == 'asc' ? 'desc' : 'asc'));
        $campo = (empty($_GET['campo']) ? 'a.id' : $_GET['campo']);
        $tpl->Set('ord', $ord);
        $tpl->Set('listaCompleta', $_GET['listaCompleta'] != 'true' ? 'false' : 'true');
        $arr = array();
        if (!empty($_GET['busca'])) {
            $arr['busca'] = $_GET['busca'];
            $tpl->Set('busca', $_GET['busca']);
        }
        $arr['itens'] = 20;
        $list = ConfigBusiness::findAllIcone($_GET['listaCompleta'], $arr, $campo, $ord, $_GET['pagina']);
        $tpl->SetArr(ConfigBusiness::getPaginacaoCentral());
        $exibindo = count($list);
        $tpl->Set('exibindo', $exibindo);
        $total = ConfigBusiness::getTotalPaginacao();
        $tpl->Set('total', $total);
        $tpl->Set('s', $total > 1 ? 's' : '');
        $body = $tpl->Show('body', 1);
        if (count($list) > 0) {
            $cont = 0;
            foreach ($list as $var) {
                $tpl->SetArr($var);
                $tpl->Set('titulo_url', Uteis::slugify($var['icone']));
                $tpl->Set('ativo', $var['ativo'] == 'Y' ? 'Sim' : 'N&atilde;o');
                $body .= $tpl->Show('loop', 1);
                $cont++;
            }
        } else {
            $body .= $tpl->Show('no-loop', 1);
        }
        $body .= $tpl->Show('end-loop', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function cadastraricone()
    {
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = array_map(trim, $_POST['form']);
            $arrErros = ConfigBusiness::validateIcone($post);
            if (count($arrErros) <= 0) {
                if ($post['ativo']) {
                    $post['ativo'] = 'Y';
                } else {
                    $post['ativo'] = 'N';
                }
                if (empty($post['id'])) {
                    ConfigBusiness::createIcone($post);
                    IndexCentral::redirect('gerenciar-icone', $this->_LOCAL['MODULE'], 'create');
                } else {
                    ConfigBusiness::updateIcone($post, $_SESSION['sistema']['login']);
                    IndexCentral::redirect('gerenciar-icone', $this->_LOCAL['MODULE'], 'update');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        }
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-cadastrar-icone.html');
        $header = $tpl->Show('header', 1);
        $_GET['id'] = Uteis::parametroUrl('cadastrar-icone');
        $id = empty($post['id']) ? $_GET['id'] : $post['id'];
        $tpl->Set('id', $id);
        if (!empty($id)) {
            $arr = ConfigBusiness::loadIcone($id);
            $tpl->SetArr($arr);
            $tpl->Set('ativo_selected', $arr['ativo'] == 'Y' ? 'checked' : '');
        }
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
            $tpl->Set('ativo_selected', $post['ativo'] ? 'checked' : '');
        }
        $body = $tpl->Show('body', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    // Redes Sociais
    public function redessociais()
    {
        $tpl = $this->getTemplate($this->_LOCAL['SERVER_MODULE_PATH'] . 'html/tpl-redes-sociais.html');
        $arrErros = array();
        if ($_POST['system'] == 'true') {
            $post = $_POST['red'];
            if (count($arrErros) <= 0) {
                if (empty($post['id'])) {
                    ConfigBusiness::truncateAndCreate($post);
                    IndexCentral::redirect($this->_LOCAL['ACTION'], $this->_LOCAL['MODULE'], 'create');
                }
            } else {
                $arrMsg['error'] = $arrErros;
            }
        } else {
            $tpl->Set('ativo_selected', 'checked');
        }
        $header = $tpl->Show('header', 1);
        $tpl->Set('msg-sistema', $tpl->setArrMsgSistema($arrMsg, 'html'));
        $tpl->set('erro', $arrMsg ? '' : 'hide');
        if (is_array($arrMsg['error'])) {
            $tpl->SetArr($post);
        }
        $body = $tpl->Show('body', 1);
        if (empty($post['id'])) {
            $arrRedes = ConfigBusiness::findAllRedeSocial('true', array(), 'a.id', 'ASC');
            $arrIcones = ConfigBusiness::findAllIcone(true, array(), 'a.categoria', 'asc');
            if (count($arrRedes) > 0) {
                $i = 1;
                foreach ($arrRedes as $rede) {
                    $tpl->setarr($rede);
                    $tpl->set('i', $i);
                    $tpl->set('id-rede', $rede['id']);
                    $body .= $tpl->Show('loop-item', 1);
                    $i++;
                    if (count($arrIcones) > 0) {
                        foreach ($arrIcones as $icon) {
                            $tpl->setArr($icon);
                            $tpl->set('selected', '');
                            if ($icon['icone'] == $rede['icone']) {
                                $tpl->set('selected', 'selected="selected"');
                            }
                            $tpl->set('icone-escolhido', $rede['icone']);
                            $body .= $tpl->show('loop-icone', 1);
                        }
                    }
                    $body .= $tpl->show('end-loop-icone', 1);
                    if (!empty($rede['id'])) {
                        $body .= $tpl->show('show-btn', 1);
                    }
                    $body .= $tpl->show('end-show-btn', 1);
                }
            } else {
                $tpl->set('i', '1');
                $body .= $tpl->Show('loop-item', 1);
                if (count($arrIcones) > 0) {
                    foreach ($arrIcones as $icon) {
                        $tpl->setArr($icon);
                        $tpl->set('selected', '');
                        if ($icon['icone'] == $rede['icone']) {
                            $tpl->set('selected', 'selected="selected"');
                        }
                        $body .= $tpl->show('loop-icone', 1);
                    }
                }
                $body .= $tpl->show('end-loop-icone', 1);
                $body .= $tpl->show('end-show-btn', 1);
            }
        }
        $body .= $tpl->Show('end-itens', 1);
        $arrRedes = ConfigBusiness::findAllRedeSocial('true', array(), 'a.id', 'ASC');
        if (is_array($arrRedes)) {
            $j = 1;
            foreach ($arrRedes as $arr) {
                $tpl->SetArr($arr);
                $tpl->set('j', $j++);
                $body .= $tpl->Show('loop-rede', 1);
            }
        }
        $body .= $tpl->Show('end-loop-rede', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return NONE;
    }

    public function jsfindallicones()
    {
        $arrIcones = ConfigBusiness::findAllIcone(true, array(), 'a.categoria', 'asc');
        $_html = '';
        if (count($arrIcones) > 0) {
            $_html = '<option value="">-- Selecione</option>';
            foreach ($arrIcones as $icone) {
                $_html .= '<option value="' . $icone['icone'] . '">' . $icone['nome'] . '</option>';
            }
        }
        echo $_html;
    }
}
