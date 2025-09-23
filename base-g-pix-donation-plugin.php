<?php
/**
 * Plugin Name: Base G PIX Donation
 * Plugin URI: https://github.com/ggortan/BaseGPix-Wordpress-Plugin
 * Description: Um plugin simples para adicionar doações via PIX no seu site.
 * Version: 1.0.1
 * Author: Base G
 * Text Domain: base-g-pix
 */

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Adicionar menu de administração
function base_g_pix_admin_menu() {
    add_options_page(
        'Base G PIX Doações',
        'Base G PIX',
        'manage_options',
        'base-g-pix-settings',
        'base_g_pix_admin_page'
    );
}
add_action('admin_menu', 'base_g_pix_admin_menu');

// Página de administração
function base_g_pix_admin_page() {
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Salvar configurações
    if (isset($_POST['save_base_g_pix'])) {
        check_admin_referer('base_g_pix_save_settings');
        
        $options = array(
            'pix_key' => sanitize_text_field($_POST['pix_key'] ?? ''),
            'beneficiary' => sanitize_text_field($_POST['beneficiary'] ?? ''),
            'city' => sanitize_text_field($_POST['city'] ?? 'São Paulo'),
            'identifier' => sanitize_text_field($_POST['identifier'] ?? 'BASEG'),
            'suggested_values' => sanitize_text_field($_POST['suggested_values'] ?? '5,10,20,50'),
            'button_text' => sanitize_text_field($_POST['button_text'] ?? 'Faça uma doação via PIX'),
            'load_bootstrap' => isset($_POST['load_bootstrap']) ? 1 : 0
        );
        
        update_option('base_g_pix_options', $options);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configurações salvas com sucesso!', 'base-g-pix') . '</p></div>';
    }
    
    // Obter configurações
    $options = get_option('base_g_pix_options', array(
        'pix_key' => '',
        'beneficiary' => '',
        'city' => 'São Paulo',
        'identifier' => 'BASEG',
        'suggested_values' => '5,10,20,50',
        'button_text' => 'Faça uma doação via PIX',
        'load_bootstrap' => 1
    ));
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Base G PIX Doações', 'base-g-pix'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('base_g_pix_save_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="pix_key"><?php echo esc_html__('Chave PIX', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="pix_key" type="text" id="pix_key" value="<?php echo esc_attr($options['pix_key']); ?>" class="regular-text">
                        <p class="description"><?php echo esc_html__('Sua chave PIX para receber as doações.', 'base-g-pix'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="beneficiary"><?php echo esc_html__('Nome do Beneficiário', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="beneficiary" type="text" id="beneficiary" value="<?php echo esc_attr($options['beneficiary']); ?>" class="regular-text">
                        <p class="description"><?php echo esc_html__('Nome que aparecerá como beneficiário do PIX.', 'base-g-pix'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="city"><?php echo esc_html__('Cidade', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="city" type="text" id="city" value="<?php echo esc_attr($options['city']); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="identifier"><?php echo esc_html__('Identificador', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="identifier" type="text" id="identifier" value="<?php echo esc_attr($options['identifier']); ?>" class="regular-text">
                        <p class="description"><?php echo esc_html__('Identificador para o código PIX (será concatenado com a data/hora).', 'base-g-pix'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="suggested_values"><?php echo esc_html__('Valores Sugeridos', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="suggested_values" type="text" id="suggested_values" value="<?php echo esc_attr($options['suggested_values']); ?>" class="regular-text">
                        <p class="description"><?php echo esc_html__('Valores sugeridos para doação, separados por vírgula (ex: 5,10,20,50).', 'base-g-pix'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_text"><?php echo esc_html__('Texto do Botão', 'base-g-pix'); ?></label></th>
                    <td>
                        <input name="button_text" type="text" id="button_text" value="<?php echo esc_attr($options['button_text']); ?>" class="regular-text">
                        <p class="description"><?php echo esc_html__('Texto padrão que será exibido no botão de doação.', 'base-g-pix'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Carregar Bootstrap', 'base-g-pix'); ?></th>
                    <td>
                        <label for="load_bootstrap">
                            <input name="load_bootstrap" type="checkbox" id="load_bootstrap" value="1" <?php checked(1, $options['load_bootstrap']); ?>>
                            <?php echo esc_html__('Carregar Bootstrap (desmarque se seu tema já utiliza Bootstrap ou se houver conflitos)', 'base-g-pix'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="save_base_g_pix" class="button button-primary" value="<?php echo esc_attr__('Salvar Configurações', 'base-g-pix'); ?>">
            </p>
        </form>
        
        <div class="card" style="max-width: 600px; margin-top: 20px; padding: 15px;">
            <h2><?php _e('Como usar o plugin', 'base-g-pix'); ?></h2>
            <p><?php _e('Para adicionar um botão de doação no seu site, use o seguinte shortcode:', 'base-g-pix'); ?></p>
            <code>[base_g_pix]</code>
            
            <p><?php _e('Para personalizar o texto do botão:', 'base-g-pix'); ?></p>
            <code>[base_g_pix text="Apoie nosso projeto"]</code>
            
            <p><?php _e('Para personalizar a classe CSS do botão:', 'base-g-pix'); ?></p>
            <code>[base_g_pix class="minha-classe"]</code>
        </div>
    </div>
    <?php
}

// Carregar scripts e estilos
function base_g_pix_enqueue_scripts() {
    // Obter configurações
    $options = get_option('base_g_pix_options', array(
        'load_bootstrap' => 1
    ));
    
    // Verificar se deve carregar o Bootstrap
    if ($options['load_bootstrap']) {
        // Carregar Bootstrap apenas para o modal, com um namespace específico
        wp_enqueue_script('base-g-bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
        
        // Carregar CSS necessário com escopo limitado
        wp_enqueue_style('base-g-bootstrap-css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap-scope.css', array(), '5.3.0');
    }
    
    // Adicionar biblioteca de ícones Bootstrap
    wp_enqueue_style('bootstrap-icons', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css', array(), '1.8.1');
    
    // Adicionar biblioteca QRCode.js
    wp_enqueue_script('qrcode-js', 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js', array(), '1.0.0', true);
    
    // Adicionar estilos e scripts do plugin
    wp_enqueue_style('base-g-pix-css', plugin_dir_url(__FILE__) . 'assets/css/base-g-pix.css', array(), '1.0.1');
    wp_enqueue_script('base-g-pix-js', plugin_dir_url(__FILE__) . 'assets/js/base-g-pix.js', array('jquery'), '1.0.1', true);
    
    // Passar dados para o JavaScript
    wp_localize_script('base-g-pix-js', 'baseGPix', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('base_g_pix_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'base_g_pix_enqueue_scripts');

// Configurar o shortcode
function base_g_pix_shortcode($atts) {
    $options = get_option('base_g_pix_options', array(
        'button_text' => 'Faça uma doação via PIX'
    ));
    
    $atts = shortcode_atts(array(
        'text' => $options['button_text'],
        'class' => 'btn btn-primary'
    ), $atts, 'base_g_pix');
    
    return '<button type="button" class="' . esc_attr($atts['class']) . ' base-g-pix-button" data-bs-toggle="modal" data-bs-target="#baseGPixModal">
        <i class="bi bi-heart-fill"></i> ' . esc_html($atts['text']) . '
    </button>';
}
add_shortcode('base_g_pix', 'base_g_pix_shortcode');

// Renderizar o modal no rodapé
function base_g_pix_render_modal() {
    $options = get_option('base_g_pix_options', array(
        'beneficiary' => '',
        'suggested_values' => '5,10,20,50'
    ));
    
    // Obter os valores sugeridos
    $suggested_values = explode(',', $options['suggested_values']);
    $suggested_values = array_map('trim', $suggested_values);
    $suggested_values = array_filter($suggested_values, 'is_numeric');
    
    if (empty($suggested_values)) {
        $suggested_values = array(5, 10, 20, 50);
    }
    
    ?>
    <!-- Modal de Doação PIX com namespace base-g -->
    <div class="base-g-pix-modal modal fade" id="baseGPixModal" tabindex="-1" aria-labelledby="baseGPixModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="baseGPixModalLabel">Faça uma doação via PIX</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <div class="text-center mb-4">
              <p>Sua contribuição é muito importante para o projeto.</p>
              <p>A Base G agradeçe a doação!</p>
            </div>
            
            <div class="pix-values mb-4">
              <p class="text-center mb-2">Escolha um valor:</p>
              <div class="d-flex justify-content-center gap-2 mb-3">
                <?php foreach ($suggested_values as $valor): ?>
                  <button type="button" class="btn btn-outline-primary valor-pix" data-valor="<?php echo esc_attr($valor); ?>">
                    R$ <?php echo esc_html($valor); ?>
                  </button>
                <?php endforeach; ?>
              </div>
              <div class="input-group mt-2">
                <span class="input-group-text">R$</span>
                <input type="number" class="form-control" id="valorPersonalizado" placeholder="Outro valor" min="1" max="1000" step="1">
                <button class="btn btn-primary" type="button" id="gerarPixBtn">Gerar PIX</button>
              </div>
            </div>
            
            <div id="pix-result" class="text-center d-none">
              <div class="qrcode-container mb-3">
                <div id="pixQrCodeContainer" class="mx-auto d-flex justify-content-center"></div>
              </div>
              
              <p class="mb-1">Valor: <strong id="pixValor"></strong></p>
              <p class="mb-3">Beneficiário: <strong id="pixBeneficiario"><?php echo esc_html($options['beneficiary']); ?></strong></p>
              
              <div class="input-group mb-3">
                <input type="text" class="form-control" id="pixCopyCola" readonly>
                <button class="btn btn-outline-primary" type="button" id="copiarPixBtn">
                  <i class="bi bi-clipboard"></i> Copiar
                </button>
              </div>
              
              <div class="alert alert-info">
                <small>
                  <i class="bi bi-info-circle"></i> Abra o aplicativo do seu banco, escolha a opção PIX &gt; QR Code ou Copia e Cola.
                </small>
              </div>
              
              <!-- Área de informações de erro -->
              <div id="error-container" class="alert alert-danger d-none mt-3">
                <h6><i class="bi bi-exclamation-triangle-fill"></i> Erro ao gerar PIX</h6>
                <div id="error-details"></div>
              </div>
                <div class="accordion mb-3" id="accordionSaberMais">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="sabermais-headingOne">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sabermais-collapseOne" aria-expanded="false" aria-controls="sabermais-collapseOne">
                        Quem é <strong class="ms-2">Gabriel Gortan</strong>
                      </button>
                    </h2>
                    <div id="sabermais-collapseOne" class="accordion-collapse collapse" aria-labelledby="sabermais-headingOne" data-bs-parent="#accordionSaberMais">
                      <div class="accordion-body">
                        Gabriel Gortan é o criador e mantenedor da Base G. <br>Saiba mais em <a href="https://baseg.com.br/sobre-base-g/" target="_blank">Sobre a Base G</a>
                      </div>
                    </div>
                  </div>
                </div>


            </div>
          </div>
          
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-success" id="voltarValoresBtn" style="display: none;">
              <i class="bi bi-arrow-left"></i> Voltar
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php
}
add_action('wp_footer', 'base_g_pix_render_modal');

/**
 * Método para criar um valor formatado para o payload
 */
function base_g_pix_get_value($id, $value) {
    $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
    return $id . $size . $value;
}

/**
 * Gerar o payload do Pix usando a chave do recebedor
 */
function base_g_pix_gerar_payload($valor, $chave, $nome, $cidade, $identificador = '***') {
    // Definição dos IDs dos campos do payload do PIX
    $ID_PAYLOAD_FORMAT = '00';
    $ID_MERCHANT_ACCOUNT = '26';
    $ID_MERCHANT_CATEGORY = '52';
    $ID_TRANSACTION_CURRENCY = '53';
    $ID_TRANSACTION_AMOUNT = '54';
    $ID_COUNTRY_CODE = '58';
    $ID_MERCHANT_NAME = '59';
    $ID_MERCHANT_CITY = '60';
    $ID_ADDITIONAL_FIELD = '62';
    $ID_CRC16 = '63';
    $ID_ADDITIONAL_FIELD_TXID = '05';
    
    // Formata o valor com 2 casas decimais e ponto como separador
    $valorFormatado = number_format($valor, 2, '.', '');
    
    // Informações da conta
    $merchantAccount = base_g_pix_get_value('00', 'br.gov.bcb.pix');
    $merchantAccount .= base_g_pix_get_value('01', $chave);
    $merchantAccountInfo = base_g_pix_get_value($ID_MERCHANT_ACCOUNT, $merchantAccount);
    
    // Informações adicionais
    $additionalField = base_g_pix_get_value($ID_ADDITIONAL_FIELD_TXID, $identificador);
    $additionalFieldTemplate = base_g_pix_get_value($ID_ADDITIONAL_FIELD, $additionalField);
    
    // Monta o payload completo
    $payload = base_g_pix_get_value($ID_PAYLOAD_FORMAT, '01') . 
               $merchantAccountInfo .
               base_g_pix_get_value($ID_MERCHANT_CATEGORY, '0000') .
               base_g_pix_get_value($ID_TRANSACTION_CURRENCY, '986') .
               base_g_pix_get_value($ID_TRANSACTION_AMOUNT, $valorFormatado) .
               base_g_pix_get_value($ID_COUNTRY_CODE, 'BR') .
               base_g_pix_get_value($ID_MERCHANT_NAME, $nome) .
               base_g_pix_get_value($ID_MERCHANT_CITY, $cidade) .
               $additionalFieldTemplate;
    
    // Adiciona o CRC16 ao final
    return $payload . base_g_pix_get_crc16($payload);
}

/**
 * Método para calcular o CRC16 (CCITT-FALSE) conforme especificação do Bacen
 */
function base_g_pix_get_crc16($payload) {
    // Definição do ID do campo CRC16
    $ID_CRC16 = '63';
    
    // Adiciona o campo do CRC16 (tamanho fixo 04)
    $payload .= $ID_CRC16 . '04';
    
    // Parâmetros para CRC16-CCITT (0xFFFF)
    $polinomio = 0x1021;
    $resultado = 0xFFFF;
    
    // Cálculo do CRC16
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) {
                    $resultado ^= $polinomio;
                }
                $resultado &= 0xFFFF;
            }
        }
    }
    
    // Formata o resultado como campo EMV
    return $ID_CRC16 . '04' . strtoupper(dechex($resultado));
}

/**
 * Processar solicitação AJAX para gerar o código PIX
 */
function base_g_pix_ajax_handler() {
    // Verificar nonce
    if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'base_g_pix_nonce')) {
        wp_send_json_error(array('message' => 'Erro de segurança. Por favor, recarregue a página e tente novamente.'), 403);
        return;
    }
    
    // Obter valor
    $valor = isset($_REQUEST['valor']) ? floatval($_REQUEST['valor']) : 0;
    
    // Validar valor
    if ($valor < 1 || $valor > 1000) {
        wp_send_json_error(array('message' => 'Valor inválido. Deve estar entre R$ 1,00 e R$ 1.000,00.'), 400);
        return;
    }
    
    // Obter configurações
    $options = get_option('base_g_pix_options', array());
    
    // Verificar configurações
    if (empty($options['pix_key']) || empty($options['beneficiary'])) {
        wp_send_json_error(array('message' => 'Configuração incompleta. Por favor, configure a chave PIX e o beneficiário.'), 500);
        return;
    }
    
    // Dados do PIX
    $chave_pix = $options['pix_key'];
    $beneficiario = $options['beneficiary'];
    $cidade = $options['city'] ?? 'São Paulo';
    $identificador = ($options['identifier'] ?? 'BASEG') . date('YmdHis');
    
    // Registrar log para debug
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Gerando PIX: valor=$valor, chave=$chave_pix, beneficiario=$beneficiario, cidade=$cidade, id=$identificador");
    }
    
    try {
        // Gerar o código PIX
        $codigo_pix = base_g_pix_gerar_payload($valor, $chave_pix, $beneficiario, $cidade, $identificador);
        
        // Registrar log para debug
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Código PIX gerado: $codigo_pix");
        }
        
        // Retornar resposta
        wp_send_json_success(array(
            'codigo' => $codigo_pix,
            'valor' => $valor,
            'valorFormatado' => 'R$ ' . number_format($valor, 2, ',', '.'),
            'beneficiario' => $beneficiario
        ));
    } catch (Exception $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Erro ao gerar PIX: " . $e->getMessage());
        }
        wp_send_json_error(array('message' => 'Erro ao gerar código PIX: ' . $e->getMessage()), 500);
    }
}
add_action('wp_ajax_base_g_pix_generate', 'base_g_pix_ajax_handler');
add_action('wp_ajax_nopriv_base_g_pix_generate', 'base_g_pix_ajax_handler');