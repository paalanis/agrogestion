<?php
/**
 * Status Bar para bot-dashboard.php
 * Mostrar estado de Railway, API Anthropic y detectar errores de billing
 * 
 * USAR DENTRO DE bot-dashboard.php (ya tiene $conexion disponible)
 */

// ============================================================
// 1. ESTADO RAILWAY (Ping)
// ============================================================
$railway_status = 'OFFLINE';
$railway_color = '#E74C3C';

$ch = curl_init('https://wapp-webhook-production.up.railway.app/health');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($http_code === 200) {
  $railway_status = 'ONLINE';
  $railway_color = '#27AE60';
}

// ============================================================
// 2. ESTADO API ANTHROPIC
// ============================================================
$api_status = 'OK';
$api_color = '#27AE60';
$api_message = '';

// Verificar últimos errores críticos en última 1 hora
$query = "SELECT COUNT(*) as error_count, ocr_error 
          FROM business_records 
          WHERE ocr_error IS NOT NULL 
          AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
          AND (ocr_error LIKE '%credit balance%' 
               OR ocr_error LIKE '%billing_error%' 
               OR ocr_error LIKE '%rate_limit%')
          GROUP BY ocr_error
          ORDER BY error_count DESC LIMIT 1";

$result = mysqli_query($conexion, $query);
if ($result) {
  $error_row = mysqli_fetch_assoc($result);
  
  if ($error_row && $error_row['error_count'] > 0) {
    $error_type = $error_row['ocr_error'];
    
    if (strpos($error_type, 'credit balance') !== false) {
      $api_status = 'SIN CRÉDITOS';
      $api_color = '#E74C3C';
      $api_message = 'Cargar saldo';
    } elseif (strpos($error_type, 'billing_error') !== false) {
      $api_status = 'ERROR BILLING';
      $api_color = '#E74C3C';
      $api_message = 'Verificar facturación';
    } elseif (strpos($error_type, 'rate_limit') !== false) {
      $api_status = 'RATE LIMIT';
      $api_color = '#F39C12';
      $api_message = 'Uso muy alto';
    }
    
    // Notificar admin (una sola vez por sesión)
    $notification_key = 'notified_api_' . md5($error_type);
    if (!isset($_SESSION[$notification_key]) && !empty($_SESSION['admin_whatsapp'])) {
      $_SESSION[$notification_key] = time();
      
      // Enviar alerta por WhatsApp
      $messages = [
        'credit balance' => "🚨 *ALERTA CRÍTICA*: Sin créditos Anthropic. Detectados " . $error_row['error_count'] . " errores. Cargar: https://console.anthropic.com/settings/billing",
        'billing_error' => "🚨 *ALERTA*: Error billing Anthropic (" . $error_row['error_count'] . " fallos). Verificar: https://console.anthropic.com/settings/billing",
        'rate_limit' => "⚠️ *ALERTA*: Rate limit (" . $error_row['error_count'] . " fallos). Bot usando muchos recursos."
      ];
      
      foreach ($messages as $key => $msg) {
        if (strpos($error_type, $key) !== false) {
          $phone = $_SESSION['admin_whatsapp'];
          $token = getenv('WHATSAPP_TOKEN');
          $phone_id = getenv('PHONE_NUMBER_ID');
          
          if ($token && $phone_id) {
            $ch = curl_init("https://graph.facebook.com/v22.0/$phone_id/messages");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
              "Authorization: Bearer $token",
              "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
              'messaging_product' => 'whatsapp',
              'to' => $phone,
              'type' => 'text',
              'text' => ['body' => $msg]
            ]));
            curl_exec($ch);
            curl_close($ch);
          }
          break;
        }
      }
    }
  }
}

// Última llamada exitosa
$last_success_query = "SELECT MAX(created_at) as last_success FROM business_records WHERE ocr_status = 'DONE'";
$last_result = mysqli_query($conexion, $last_success_query);
$last_row = mysqli_fetch_assoc($last_result);
$last_success = $last_row['last_success'] ? date('H:i', strtotime($last_row['last_success'])) : 'N/A';

?>

<!-- STATUS BAR HTML -->
<div style="background: linear-gradient(90deg, #2C3E50 0%, #34495E 100%); padding: 12px 20px; margin-bottom: 20px; border-radius: 4px; color: white; font-size: 13px; display: flex; gap: 30px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
  
  <!-- Railway Status -->
  <div style="display: flex; align-items: center; gap: 8px;">
    <span style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo $railway_color; ?>;"></span>
    <strong>Railway:</strong>
    <span><?php echo $railway_status; ?></span>
  </div>
  
  <!-- API Status -->
  <div style="display: flex; align-items: center; gap: 8px;">
    <span style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo $api_color; ?>;"></span>
    <strong>API:</strong>
    <span><?php echo $api_status; ?></span>
    <?php if ($api_message): ?>
      <span style="margin-left: 5px; opacity: 0.85; font-size: 11px;">(<?php echo $api_message; ?>)</span>
    <?php endif; ?>
  </div>
  
  <!-- Last Success -->
  <div style="display: flex; align-items: center; gap: 8px; margin-left: auto; opacity: 0.9;">
    <i class="fa fa-clock-o"></i>
    <span>Último: <?php echo $last_success; ?></span>
  </div>
  
</div>

