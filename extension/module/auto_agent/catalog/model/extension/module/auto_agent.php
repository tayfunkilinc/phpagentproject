<?php
namespace Opencart\Catalog\Model\Extension\Module;
class AutoAgent extends \Opencart\System\Engine\Model {

    public function generateDescription($product_info) {
        $api_url = $this->config->get('module_auto_agent_api_url');
        $api_key = $this->config->get('module_auto_agent_api_key');

        if (!$api_url || !$api_key) {
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
        $description = $data['text'] ?? 'Description unavailable';

        $this->db->query("UPDATE " . DB_PREFIX . "product_description
            SET description = '" . $this->db->escape($description) . "'
            WHERE product_id = '" . (int)$product_info['product_id'] . "'");
    }
}