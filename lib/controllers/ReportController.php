<?php
require_once __DIR__ . '/../models/Report.php';

class ReportController {
    private $report;

    public function __construct($db) {
        $this->report = new Report($db);
    }

    public function generateReport($type, $startDate, $endDate, $status = null) {
        switch ($type) {
            case 'bookings':
                return $this->report->generateBookingReport($startDate, $endDate, $status);
            case 'officer-performance':
                return $this->report->generateOfficerPerformanceReport($startDate, $endDate);
            default:
                throw new Exception("Invalid report type");
        }
    }

    public function exportToPDF($data, $title) {
        // Implementation for PDF generation
        // Requires a PDF library like TCPDF or Dompdf
    }

    public function exportToExcel($data, $filename) {
        if (!is_array($data) || empty($data)) {
            throw new Exception("No data available to export.");
        }
    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        
        $html = '<table border="1">';
        
        // Add headers
        $html .= '<tr>';
        foreach (array_keys($data[0]) as $header) {
            $html .= '<th>'.ucwords(str_replace('_', ' ', $header)).'</th>';
        }
        $html .= '</tr>';
        
        // Add data
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>'.$cell.'</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        echo $html;
        exit;
    }
    
}
?>