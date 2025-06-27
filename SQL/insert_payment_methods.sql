-- Insert sample payment methods for checkout functionality
-- This script ensures the payment_methods table has data for testing

-- Create payment_methods table if it doesn't exist
CREATE TABLE IF NOT EXISTS `payment_methods` (
    `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `code` varchar(20) NOT NULL,
    `description` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `processing_fee_percent` decimal(5,2) DEFAULT 0.00,
    `min_amount` decimal(10,2) DEFAULT 0.00,
    `max_amount` decimal(10,2) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`payment_method_id`),
    UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample payment methods
INSERT IGNORE INTO `payment_methods` (`name`, `code`, `description`, `is_active`, `processing_fee_percent`, `min_amount`, `max_amount`, `sort_order`) VALUES
('Credit Card', 'credit_card', 'Pay with Visa, MasterCard, or other major credit cards', 1, 2.90, 1.00, NULL, 1),
('Debit Card', 'debit_card', 'Pay with your debit card', 1, 1.50, 1.00, NULL, 2),
('FPX Online Banking', 'fpx', 'Pay directly from your bank account via FPX', 1, 0.50, 1.00, 30000.00, 3),
('Bank Transfer', 'bank_transfer', 'Direct bank transfer', 1, 0.00, 1.00, NULL, 4),
('GrabPay', 'grabpay', 'Pay with your GrabPay wallet', 1, 1.00, 1.00, 1500.00, 5),
('Boost', 'boost', 'Pay with Boost e-wallet', 1, 1.00, 1.00, 1500.00, 6),
('Touch \'n Go eWallet', 'tng', 'Pay with Touch \'n Go eWallet', 1, 1.00, 1.00, 1500.00, 7),
('Cash on Delivery', 'cod', 'Pay cash when your order is delivered', 1, 0.00, 1.00, 500.00, 8);

-- Update existing records to ensure they have proper data
UPDATE `payment_methods` SET 
    `description` = CASE 
        WHEN `code` = 'credit_card' THEN 'Pay with Visa, MasterCard, or other major credit cards'
        WHEN `code` = 'debit_card' THEN 'Pay with your debit card'
        WHEN `code` = 'fpx' THEN 'Pay directly from your bank account via FPX'
        WHEN `code` = 'bank_transfer' THEN 'Direct bank transfer'
        WHEN `code` = 'grabpay' THEN 'Pay with your GrabPay wallet'
        WHEN `code` = 'boost' THEN 'Pay with Boost e-wallet'
        WHEN `code` = 'tng' THEN 'Pay with Touch \'n Go eWallet'
        WHEN `code` = 'cod' THEN 'Pay cash when your order is delivered'
        ELSE `description`
    END,
    `is_active` = 1
WHERE `code` IN ('credit_card', 'debit_card', 'fpx', 'bank_transfer', 'grabpay', 'boost', 'tng', 'cod'); 