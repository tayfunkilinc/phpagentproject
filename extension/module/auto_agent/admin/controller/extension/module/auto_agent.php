<?php
namespace Opencart\Admin\Controller\Extension\Module;
class AutoAgent extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/module/auto_agent');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_auto_agent', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_api_key'] = $this->language->get('entry_api_key');
        $data['entry_api_url'] = $this->language->get('entry_api_url');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['action'] = $this->url->link('extension/module/auto_agent', 'user_token=' . $this->session->data['user_token']);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']);

        $data['module_auto_agent_api_url'] = $this->config->get('module_auto_agent_api_url') ?? '';
        $data['module_auto_agent_api_key'] = $this->config->get('module_auto_agent_api_key') ?? '';
        $data['module_auto_agent_status'] = $this->config->get('module_auto_agent_status') ?? '';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/auto_agent', $data));
    }

    protected function validate(): bool {
        if (!$this->user->hasPermission('modify', 'extension/module/auto_agent')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
