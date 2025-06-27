<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * PaymentService
 * Handles multiple payment gateway integrations
 */
class PaymentService
{
    private $db;
    
    public function __construct()
    {
        global $host, $user, $pass, $dbname;
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods($amount = null)
    {
        try {
            $whereConditions = ['is_active = 1'];
            $params = [];
            
            if ($amount !== null) {
                $whereConditions[] = '(min_amount IS NULL OR min_amount <= ?)';
                $whereConditions[] = '(max_amount IS NULL OR max_amount >= ?)';
                $params[] = $amount;
                $params[] = $amount;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    payment_method_id,
                    name,
                    code,
                    description,
                    processing_fee_percent,
                    min_amount,
                    max_amount,
                    sort_order
                FROM payment_methods 
                WHERE $whereClause
                ORDER BY sort_order ASC, name ASC
            ");
            
            $stmt->execute($params);
            $methods = $stmt->fetchAll();
            
            return ['success' => true, 'payment_methods' => $methods];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch payment methods: ' . $e->getMessage()];
        }
    }
    
    /**
     * Process payment for an order
     */
    public function processPayment($orderId, $paymentMethodId, $amount, $paymentData = [])
    {
        try {
            $this->db->beginTransaction();
            
            // Get payment method details
            $stmt = $this->db->prepare("
                SELECT * FROM payment_methods 
                WHERE payment_method_id = ? AND is_active = 1
            ");
            $stmt->execute([$paymentMethodId]);
            $paymentMethod = $stmt->fetch();
            
            if (!$paymentMethod) {
                throw new Exception('Payment method not found or inactive');
            }
            
            // Calculate processing fees
            $processingFee = ($amount * $paymentMethod['processing_fee_percent']) / 100;
            $totalAmount = $amount + $processingFee;
            
            // Create payment record
            $referenceNumber = $this->generateReferenceNumber();
            
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    order_id, payment_method_id, amount, currency, 
                    reference_number, status, processed_at
                ) VALUES (?, ?, ?, 'MYR', ?, 'processing', NOW())
            ");
            
            $stmt->execute([
                $orderId, $paymentMethodId, $totalAmount, $referenceNumber
            ]);
            
            $paymentId = $this->db->lastInsertId();
            
            // Process payment based on gateway
            $result = $this->processPaymentByGateway($paymentMethod, $totalAmount, $referenceNumber, $paymentData);
            
            if ($result['success']) {
                // Update payment status
                $stmt = $this->db->prepare("
                    UPDATE payments 
                    SET status = 'completed', transaction_id = ?, gateway_response = ?
                    WHERE payment_id = ?
                ");
                $stmt->execute([
                    $result['transaction_id'] ?? null,
                    json_encode($result['response'] ?? []),
                    $paymentId
                ]);
                
                // Update order payment status
                $stmt = $this->db->prepare("
                    UPDATE orders 
                    SET payment_status = 'Paid', payment_method = ?
                    WHERE order_id = ?
                ");
                $stmt->execute([$paymentMethod['name'], $orderId]);
                
                $this->db->commit();
                
                return [
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'payment_id' => $paymentId,
                    'reference_number' => $referenceNumber,
                    'transaction_id' => $result['transaction_id'] ?? null
                ];
                
            } else {
                // Payment failed
                $stmt = $this->db->prepare("
                    UPDATE payments 
                    SET status = 'failed', failure_reason = ?, gateway_response = ?
                    WHERE payment_id = ?
                ");
                $stmt->execute([
                    $result['error'] ?? 'Payment processing failed',
                    json_encode($result['response'] ?? []),
                    $paymentId
                ]);
                
                $this->db->commit();
                
                return [
                    'success' => false,
                    'message' => $result['error'] ?? 'Payment processing failed',
                    'payment_id' => $paymentId,
                    'reference_number' => $referenceNumber
                ];
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Payment processing error: ' . $e->getMessage()];
        }
    }
    
    // Payment Gateway Processors
    private function processPaymentByGateway($paymentMethod, $amount, $referenceNumber, $paymentData)
    {
        switch (strtolower($paymentMethod['code'])) {
            case 'credit_card':
            case 'debit_card':
                return $this->processCreditCardPayment($amount, $referenceNumber, $paymentData);
                
            case 'fpx':
            case 'bank_transfer':
                return $this->processFPXPayment($amount, $referenceNumber, $paymentData);
                
            case 'grabpay':
                return $this->processGrabPayPayment($amount, $referenceNumber, $paymentData);
                
            case 'boost':
                return $this->processBoostPayment($amount, $referenceNumber, $paymentData);
                
            case 'tng':
            case 'touch_n_go':
                return $this->processTouchNGoPayment($amount, $referenceNumber, $paymentData);
                
            default:
                return $this->processGenericPayment($amount, $referenceNumber, $paymentData);
        }
    }
    
    private function processCreditCardPayment($amount, $referenceNumber, $paymentData)
    {
        // Mock credit card processing - replace with actual gateway integration
        if (empty($paymentData['card_number']) || empty($paymentData['cvv'])) {
            return ['success' => false, 'error' => 'Missing card details'];
        }
        
        $transactionId = 'CC_' . uniqid();
        
        // Mock successful payment (90% success rate)
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'response' => [
                    'status' => 'success',
                    'card_last4' => substr($paymentData['card_number'], -4),
                    'processed_at' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Card declined',
                'response' => ['decline_code' => 'insufficient_funds']
            ];
        }
    }
    
    private function processFPXPayment($amount, $referenceNumber, $paymentData)
    {
        // Mock FPX processing
        $transactionId = 'FPX_' . uniqid();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'response' => [
                'bank_code' => $paymentData['bank_code'] ?? 'MAYBANK',
                'fpx_id' => $transactionId,
                'status' => 'success'
            ]
        ];
    }
    
    private function processGrabPayPayment($amount, $referenceNumber, $paymentData)
    {
        $transactionId = 'GRAB_' . uniqid();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'response' => [
                'grab_txn_id' => $transactionId,
                'status' => 'success'
            ]
        ];
    }
    
    private function processBoostPayment($amount, $referenceNumber, $paymentData)
    {
        $transactionId = 'BOOST_' . uniqid();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'response' => [
                'boost_txn_id' => $transactionId,
                'status' => 'success'
            ]
        ];
    }
    
    private function processTouchNGoPayment($amount, $referenceNumber, $paymentData)
    {
        $transactionId = 'TNG_' . uniqid();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'response' => [
                'tng_txn_id' => $transactionId,
                'status' => 'success'
            ]
        ];
    }
    
    private function processGenericPayment($amount, $referenceNumber, $paymentData)
    {
        $transactionId = 'PAY_' . uniqid();
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'response' => [
                'txn_id' => $transactionId,
                'status' => 'success'
            ]
        ];
    }
    
    private function generateReferenceNumber()
    {
        return 'REF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
    
    /**
     * Get payment history for an order
     */
    public function getOrderPaymentHistory($orderId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    pm.name as payment_method_name,
                    pm.code as payment_method_code
                FROM payments p
                LEFT JOIN payment_methods pm ON p.payment_method_id = pm.payment_method_id
                WHERE p.order_id = ?
                ORDER BY p.created_at DESC
            ");
            
            $stmt->execute([$orderId]);
            $payments = $stmt->fetchAll();
            
            return ['success' => true, 'payments' => $payments];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch payment history: ' . $e->getMessage()];
        }
    }
}

?> 