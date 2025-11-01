<?php
namespace Opencart\Admin\Controller\Extension\Module;
class AutoAgentPage extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/module/auto_agent_page');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/product');
        $this->load->model('setting/setting');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['action_generate'] = $this->url->link('extension/module/auto_agent_page.generate', 'user_token=' . $this->session->data['user_token']);
        $data['products'] = $this->model_catalog_product->getProducts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/auto_agent_page', $data));
    }

    public function generate(): void {
        $this->load->model('catalog/product');
        $this->load->model('extension/module/auto_agent');

        $product_id = (int)$this->request->post['product_id'];
        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            $this->model_extension_module_auto_agent->generateDescription($product_info);
            $this->response->setOutput(json_encode(['success' => 'Description generated successfully!']));
        } else {
            $this->response->setOutput(json_encode(['error' => 'Product not found.']));
        }
    }
}
