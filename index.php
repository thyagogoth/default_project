<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.inc.php';

use System\MasterAction;

class Index extends MasterAction
{

    private $_LOCAL = array();
    static $pgn;

    public function __construct($action = '')
    {
        if ($action == "" || empty($action)) {
            $action = 'home';
        }
        $this->_LOCAL['ACTION'] = $action;
        $_GET['action'] = $action;
        $action = (empty($action) ? 'home' : str_replace('-', '', $action));

        if (!is_callable(array($this, $action))) {
            $action = 'home';
        }
        $this->_LOCAL['SERVER'] = SERVER;
        $this->_LOCAL['SESSION_ID'] = SESSION_ID;
        parent::__construct(null, str_replace('-', '', strtolower($action)));
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
        $tpl = $this->getTemplate('views/index.html');
        $tpl->set('header', $header);
        $header = $tpl->Show('header', 1);
        echo $header;
    }

    public function showBody($body)
    {
        $tpl = $this->getTemplate('views/index.html');
        $tpl->Set('page', $body);
        $body = $tpl->Show('body', 1);
        echo $body;
    }

    public function home()
    {
        $tpl = $this->getTemplate('views/home.html');
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return true;
    }

    public function contato()
    {
        $tpl = $this->getTemplate('views/home.html');
        $header = $tpl->Show('header', 1);
        $body = $tpl->Show('body', 1);
        $this->showHeader($header);
        $this->showBody($body);
        return true;
    }
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'url.php';
