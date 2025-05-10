<?php
class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function generateBookingReport($startDate, $endDate, $status = null) {
        $query = "SELECT 
                    b.id AS booking_id,
                    b.event_name,
                    b.event_date,
                    b.event_time,
                    b.location,
                    b.status,
                    b.created_at,
                    c.full_name AS customer_name,
                    o.full_name AS officer_name
                  FROM bookings b
                  JOIN users c ON b.user_id = c.id
                  LEFT JOIN users o ON b.officer_id = o.id
                  WHERE b.event_date BETWEEN ? AND ?";
        
        $params = [$startDate, $endDate];
        
        if ($status) {
            $query .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY b.event_date ASC";
        
        return $this->db->query($query, $params);
    }

    public function generateOfficerPerformanceReport($startDate, $endDate) {
        return $this->db->query(
            "SELECT 
                u.id AS officer_id,
                u.full_name AS officer_name,
                COUNT(b.id) AS total_assignments,
                SUM(CASE WHEN b.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN b.status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
             FROM users u
             LEFT JOIN bookings b ON u.id = b.officer_id
             WHERE u.role = 'officer'
             AND (b.event_date BETWEEN ? AND ? OR b.event_date IS NULL)
             GROUP BY u.id
             ORDER BY total_assignments DESC",
            [$startDate, $endDate]
        );
    }
}
?>