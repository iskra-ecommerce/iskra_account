<?php
namespace Opencart\Catalog\Controller\Extension\Iskra;

class Account extends \Opencart\System\Engine\Controller {
    public function index(): void {
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token'], true));
        }

        $this->load->language('extension/iskra/account');
        $this->load->language('account/register');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', 'language=' . $this->config->get('config_language'))
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_register'),
            'href' => $this->url->link('extension/iskra/account', 'language=' . $this->config->get('config_language'))
        ];

        $data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', 'language=' . $this->config->get('config_language')));

        $this->session->data['register_token'] = oc_token(26);
        $data['register'] = $this->url->link('extension/iskra/account.register', 'language=' . $this->config->get('config_language') . '&register_token=' . $this->session->data['register_token']);

        // Customer Groups
        $data['customer_groups'] = [];
        if (is_array($this->config->get('config_customer_group_display'))) {
            $this->load->model('account/customer_group');
            $customer_groups = $this->model_account_customer_group->getCustomerGroups();
            foreach ($customer_groups as $customer_group) {
                if (in_array($customer_group['customer_group_id'], (array)$this->config->get('config_customer_group_display'))) {
                    $data['customer_groups'][] = $customer_group;
                }
            }
        }
        $data['customer_group_id'] = (int)$this->config->get('config_customer_group_id');

        // Custom Fields
        $data['custom_fields'] = [];
        $this->load->model('account/custom_field');
        $custom_fields = $this->model_account_custom_field->getCustomFields();
        foreach ($custom_fields as $custom_field) {
            if ($custom_field['location'] == 'account') {
                $data['custom_fields'][] = $custom_field;
            }
        }

        // Captcha
        $this->load->model('setting/extension');
        $extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));
        if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
            $data['captcha'] = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code']);
        } else {
            $data['captcha'] = '';
        }

        // Information agree
        $this->load->model('catalog/information');
        $information_info = $this->model_catalog_information->getInformation((int)$this->config->get('config_account_id'));
        if ($information_info) {
            $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information.info', 'language=' . $this->config->get('config_language') . '&information_id=' . $this->config->get('config_account_id')), $information_info['title']);
        } else {
            $data['text_agree'] = '';
        }

        $data['language'] = $this->config->get('config_language');

        // Iskra-specific
        $data['iskra_languages'] = $this->getLanguages();
        $cookie_lang = $this->request->cookie['iskra_language'] ?? '';
        $default_lang = $this->config->get('iskra_account_default_language') ?: 'ru-ru';
        $data['iskra_selected_language'] = $cookie_lang ?: $default_lang;
        $data['iskra_password_strength'] = (bool)$this->config->get('iskra_account_password_strength');
        $data['iskra_phone_mask'] = (bool)$this->config->get('iskra_account_phone_mask');
        $data['iskra_language_select'] = (bool)$this->config->get('iskra_account_language_select');
        $data['iskra_password_min_length'] = (int)($this->config->get('iskra_account_password_min_length') ?: 8);
        $data['iskra_check_email_url'] = $this->url->link('extension/iskra/account.checkEmail', 'language=' . $this->config->get('config_language'));

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/iskra/account_register', $data));
    }

    public function register(): void {
        $this->load->language('extension/iskra/account');
        $this->load->language('account/register');

        $json = [];

        $required = [
            'customer_group_id' => 0,
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'telephone' => '',
            'custom_field' => [],
            'password' => '',
            'agree' => 0,
            'language_preference' => ''
        ];

        $post_info = $this->request->post + $required;

        if (!isset($this->request->get['register_token']) || !isset($this->session->data['register_token']) || ($this->session->data['register_token'] != $this->request->get['register_token'])) {
            $json['redirect'] = $this->url->link('extension/iskra/account', 'language=' . $this->config->get('config_language'), true);
        }

        // Captcha
        $this->load->model('setting/extension');
        $extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));
        if ($extension_info && $this->config->get('captcha_' . $this->config->get('captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
            $captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '.validate');
            if ($captcha) {
                $json['error']['captcha'] = $captcha;
            }
        }

        if (!$json) {
            if ($post_info['customer_group_id']) {
                $customer_group_id = (int)$post_info['customer_group_id'];
            } else {
                $customer_group_id = (int)$this->config->get('config_customer_group_id');
            }

            $this->load->model('account/customer_group');
            $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

            if (!$customer_group_info || !in_array($customer_group_id, (array)$this->config->get('config_customer_group_display'))) {
                $json['error']['warning'] = $this->language->get('error_customer_group');
            }

            if (!oc_validate_length($post_info['firstname'], 1, 32)) {
                $json['error']['firstname'] = $this->language->get('error_firstname');
            }

            if (!oc_validate_length($post_info['lastname'], 1, 32)) {
                $json['error']['lastname'] = $this->language->get('error_lastname');
            }

            if (!oc_validate_email($post_info['email'])) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            $this->load->model('account/customer');
            if ($this->model_account_customer->getTotalCustomersByEmail($post_info['email'])) {
                $json['error']['warning'] = $this->language->get('error_exists');
            }

            if ($this->config->get('config_telephone_required') && !oc_validate_length($post_info['telephone'], 3, 32)) {
                $json['error']['telephone'] = $this->language->get('error_telephone');
            }

            // Custom field validation
            $this->load->model('account/custom_field');
            $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
            foreach ($custom_fields as $custom_field) {
                if ($custom_field['location'] == 'account') {
                    if ($custom_field['required'] && empty($post_info['custom_field'][$custom_field['custom_field_id']])) {
                        $json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                    } elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !oc_validate_regex($post_info['custom_field'][$custom_field['custom_field_id']], $custom_field['validation'])) {
                        $json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
                    }
                }
            }

            $password = html_entity_decode($post_info['password'], ENT_QUOTES, 'UTF-8');
            $min_len = (int)($this->config->get('iskra_account_password_min_length') ?: 8);

            if (!oc_validate_length($password, $min_len, 40)) {
                $json['error']['password'] = sprintf($this->language->get('error_password_length'), $min_len);
            }

            $required = [];
            if ($this->config->get('config_password_uppercase') && !preg_match('/[A-Z]/', $password)) {
                $required[] = $this->language->get('error_password_uppercase');
            }
            if ($this->config->get('config_password_lowercase') && !preg_match('/[a-z]/', $password)) {
                $required[] = $this->language->get('error_password_lowercase');
            }
            if ($this->config->get('config_password_number') && !preg_match('/[0-9]/', $password)) {
                $required[] = $this->language->get('error_password_number');
            }
            if ($this->config->get('config_password_symbol') && !preg_match('/[^a-zA-Z0-9]/', $password)) {
                $required[] = $this->language->get('error_password_symbol');
            }
            if ($required) {
                $json['error']['password'] = sprintf($this->language->get('error_password'), implode(', ', $required), $min_len);
            }

            // Agree to terms
            $this->load->model('catalog/information');
            $information_info = $this->model_catalog_information->getInformation((int)$this->config->get('config_account_id'));
            if ($information_info && !$post_info['agree']) {
                $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
            }
        }

        if (!$json) {
            $customer_id = $this->model_account_customer->addCustomer($post_info);

            // Save language preference
            if ($post_info['language_preference']) {
                $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `language_preference` = '" . $this->db->escape($post_info['language_preference']) . "' WHERE `customer_id` = '" . (int)$customer_id . "'");

                // Set cookie
                $expire = time() + (90 * 86400);
                setcookie('iskra_language', $post_info['language_preference'], $expire, '/', '', false, true);
            }

            if (!$customer_group_info['approval']) {
                $this->customer->login($post_info['email'], html_entity_decode($post_info['password'], ENT_QUOTES, 'UTF-8'));

                $this->session->data['customer'] = [
                    'customer_id' => $customer_id,
                    'customer_group_id' => $customer_group_id,
                    'firstname' => $post_info['firstname'],
                    'lastname' => $post_info['lastname'],
                    'email' => $post_info['email'],
                    'telephone' => $post_info['telephone'],
                    'custom_field' => $post_info['custom_field']
                ];

                $this->model_account_customer->addLogin($this->customer->getId(), oc_get_ip());
                $this->session->data['customer_token'] = oc_token(26);
            }

            unset($this->session->data['register_token']);
            $this->model_account_customer->deleteLoginAttempts($post_info['email']);

            unset($this->session->data['guest']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);

            $json['redirect'] = $this->url->link('account/success', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''), true);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function checkEmail(): void {
        $json = ['exists' => false];

        if (isset($this->request->get['email'])) {
            $this->load->model('account/customer');
            $total = $this->model_account_customer->getTotalCustomersByEmail($this->request->get['email']);
            if ($total) {
                $json['exists'] = true;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function setLanguage(): void {
        $json = [];

        if (isset($this->request->post['code'])) {
            $this->load->model('localisation/language');
            $language_info = $this->model_localisation_language->getLanguageByCode($this->request->post['code']);

            if ($language_info) {
                $lm = new \Opencart\System\Library\Iskra\LanguageManager($this->registry);
                $lm->set($this->request->post['code']);

                $json['success'] = true;
                $json['code'] = $this->request->post['code'];
            } else {
                $json['error'] = 'Invalid language code';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function getLanguages(): array {
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        return array_filter($languages, fn($l) => $l['status']);
    }
}
