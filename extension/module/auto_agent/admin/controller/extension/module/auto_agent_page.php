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

    // 👇 Veritabanına yazmaz, sadece sonucu döner
    public function generate(): void {
        $this->load->model('catalog/product');

        $product_id = (int)$this->request->post['product_id'];
        $product_info = $this->model_catalog_product->getProduct($product_id);

        if (!$product_info) {
            $this->response->setOutput(json_encode(['error' => 'Product not found.']));
            return;
        }

        $api_url = $this->config->get('module_auto_agent_api_url');
        $api_key = $this->config->get('module_auto_agent_api_key');

        if (!$api_url || !$api_key) {
            $this->response->setOutput(json_encode(['error' => 'API settings are missing.']));
            return;
        }

        $prompt = "Write an SEO-friendly product description for: " . $product_info['name'];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['prompt' => $prompt]));
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $description = $data['text'] ?? 'No description returned.';

        // ✅ Veritabanına yazmıyoruz, sadece önizleme olarak döndür
        $this->response->setOutput(json_encode(['success' => true, 'description' => $description]));
    }
}