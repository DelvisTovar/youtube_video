<?php

if (!defined('_PS_VERSION_'))
    exit();

class Youtube_Video extends Module
{
    public function __construct()
    {
        $this->name = 'youtube_video';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Delvis Tovar';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('YouTube Video', 'youtube_video');
        $this->description = $this->l('This module is developed to display an YouTube video.', 'youtube_video');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', 'youtube_video');
    }

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        return parent::install() &&
            $this->registerHook('displayHome') && Configuration::updateValue('youtube_video_url', 'wlsdMpnDBn8') && Configuration::updateValue('youtube_video_text', 'Delvis Tovar');
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('youtube_video_url') || !Configuration::deleteByName('youtube_video_text'))
            return false;
        return true;
    }

    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign(
            array(
                'youtube_url'  => Configuration::get('youtube_video_url'),
                'youtube_text' => Configuration::get('youtube_video_text')
            )
        );
        return $this->display(__FILE__,'views/templates/hook/youtube_video.tpl');
    }

    public function displayForm()
    {
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('YouTube Module'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('URL of the YouTube video'),
                    'name' => 'youtube_video_url',
                    'size' => 20,
                    'required' => true
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_info'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Text block'),
                    'name' => 'youtube_video_text',
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        $helper->fields_value['youtube_video_url']  = Configuration::get('youtube_video_url');
        $helper->fields_value['youtube_video_text'] = Configuration::get('youtube_video_text');
        return $helper->generateForm($fields_form);
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $youtube_url  = strval(Tools::getValue('youtube_video_url'));
            $youtube_text = Tools::getValue('youtube_video_text');

            if (!isset($youtube_url) && !isset($youtube_text))
                $output .= $this->displayError($this->l('Please insert something in this field.'));
            else
            {
                Configuration::updateValue('youtube_video_url', $youtube_url);
                Configuration::updateValue('youtube_video_text', $youtube_text);
                $output .= $this->displayConfirmation($this->l('Video URL updated!'));
            }
        }
        return $output.$this->displayForm();
    }
}

?>