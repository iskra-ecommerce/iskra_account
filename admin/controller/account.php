<?php
namespace Opencart\Admin\Controller\Extension\IskraAccount;

class Account extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/iskra/account');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/iskra/account', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['save'] = $this->url->link('extension/iskra/account.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

        // Load settings
        $data['iskra_account_status'] = $this->config->get('iskra_account_status');
        $data['iskra_account_default_language'] = $this->config->get('iskra_account_default_language') ?: 'ru-ru';
        $data['iskra_account_cookie_lifetime'] = $this->config->get('iskra_account_cookie_lifetime') ?: 90;
        $data['iskra_account_password_strength'] = $this->config->get('iskra_account_password_strength');
        $data['iskra_account_phone_mask'] = $this->config->get('iskra_account_phone_mask');
        $data['iskra_account_language_select'] = $this->config->get('iskra_account_language_select');
        $data['iskra_account_password_min_length'] = $this->config->get('iskra_account_password_min_length') ?: 8;

        // Languages
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/iskra_account/account_settings', $data));
    }

    public function save(): void {
        $this->load->language('extension/iskra/account');

        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/iskra/account')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('iskra_account', $this->request->post);

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void {
        $this->load->controller('extension/iskra_account/install');
    }

    public function uninstall(): void {
        $this->load->controller('extension/iskra_account/uninstall');
    }
}
