<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/ModelLoader.php';

/**
 * PageVisit Service
 * Handles page visit tracking and analytics
 */
class PageVisitService
{
    private $db;
    private $PageVisit;

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
        
        $this->PageVisit = ModelLoader::load('PageVisit');
    }

    /**
     * Record a page visit
     */
    public function recordPageVisit($pageUrl, $pageTitle = null, $userId = null, $sessionId = null)
    {
        try {
            // Get session ID if not provided
            if (!$sessionId) {
                $sessionId = session_id() ?: $this->generateSessionId();
            }

            // Get user agent and IP
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $ipAddress = $this->getClientIP();
            $referrerUrl = $_SERVER['HTTP_REFERER'] ?? null;

            // Detect device type and browser
            $deviceType = $this->detectDeviceType($userAgent);
            $browser = $this->detectBrowser($userAgent);

            // Get country (you can implement IP geolocation here)
            $country = $this->getCountryFromIP($ipAddress);

            $visitData = [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'page_url' => $pageUrl,
                'page_title' => $pageTitle,
                'referrer_url' => $referrerUrl,
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress,
                'country' => $country,
                'device_type' => $deviceType,
                'browser' => $browser,
                'visit_duration' => 0 // Will be updated when user leaves page
            ];

            // Insert the visit record
            $stmt = $this->db->prepare("
                INSERT INTO page_visits (
                    user_id, session_id, page_url, page_title, referrer_url,
                    user_agent, ip_address, country, device_type, browser, visit_duration
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $visitData['user_id'],
                $visitData['session_id'],
                $visitData['page_url'],
                $visitData['page_title'],
                $visitData['referrer_url'],
                $visitData['user_agent'],
                $visitData['ip_address'],
                $visitData['country'],
                $visitData['device_type'],
                $visitData['browser'],
                $visitData['visit_duration']
            ]);

            $visitId = $this->db->lastInsertId();

            // Store visit ID in session for duration tracking
            if (!isset($_SESSION['current_visit_id'])) {
                $_SESSION['current_visit_id'] = $visitId;
                $_SESSION['visit_start_time'] = time();
            }

            return $visitId;

        } catch (Exception $e) {
            error_log("Error recording page visit: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update visit duration when user leaves page
     */
    public function updateVisitDuration($visitId = null)
    {
        try {
            if (!$visitId) {
                $visitId = $_SESSION['current_visit_id'] ?? null;
            }

            if (!$visitId) {
                return false;
            }

            $startTime = $_SESSION['visit_start_time'] ?? time();
            $duration = time() - $startTime;

            // Update the visit record with duration
            $stmt = $this->db->prepare("
                UPDATE page_visits 
                SET visit_duration = ? 
                WHERE visit_id = ?
            ");

            $stmt->execute([$duration, $visitId]);

            // Clear session data
            unset($_SESSION['current_visit_id']);
            unset($_SESSION['visit_start_time']);

            return true;

        } catch (Exception $e) {
            error_log("Error updating visit duration: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get client IP address
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType($userAgent)
    {
        if (!$userAgent) return 'Unknown';

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false || strpos($userAgent, 'iphone') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'Tablet';
        } elseif (strpos($userAgent, 'bot') !== false || strpos($userAgent, 'crawler') !== false) {
            return 'Bot';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser($userAgent)
    {
        if (!$userAgent) return 'Unknown';

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'opera') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'ie') !== false) {
            return 'Internet Explorer';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get country from IP (basic implementation)
     */
    private function getCountryFromIP($ip)
    {
        // This is a basic implementation
        // For production, consider using a service like MaxMind GeoIP2 or IP-API
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            // You can implement actual geolocation here
            return 'Unknown';
        }
        return 'Local';
    }

    /**
     * Generate a unique session ID
     */
    private function generateSessionId()
    {
        return uniqid('session_', true);
    }

    /**
     * Get visit statistics for a specific page
     */
    public function getPageVisitStats($pageUrl, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'visit_date');
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_visits,
                    COUNT(DISTINCT user_id) as unique_visitors,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    AVG(visit_duration) as avg_duration,
                    COUNT(CASE WHEN user_id IS NOT NULL THEN 1 END) as logged_in_visits,
                    COUNT(CASE WHEN user_id IS NULL THEN 1 END) as anonymous_visits
                FROM page_visits 
                WHERE page_url = ? AND $timeCondition
            ");
            
            $stmt->execute([$pageUrl]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Error getting page visit stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get time condition for queries
     */
    private function getTimeCondition($timeframe, $dateColumn)
    {
        switch ($timeframe) {
            case '7 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case '90 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            case '1 year':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            default:
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
    }
} 