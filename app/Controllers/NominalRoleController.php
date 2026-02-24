<?php

namespace App\Controllers;

use App\Core\Controller;

class NominalRoleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Nominal Role Dashboard
     */
    public function dashboard(): void
    {
        $this->requireAuth();
        $this->requireNominalRole();

        // Get statistics
        $stats = $this->getStats();
        
        // Get faculties for filter
        $faculties = $this->getFaculties();
        
        // Get units for filter
        $units = $this->getUnits();

        $this->view('nominal-role/dashboard', [
            'stats' => $stats,
            'faculties' => $faculties,
            'units' => $units,
        ]);
    }

    /**
     * Filter and view staff list
     */
    public function filterStaff(): void
    {
        $this->requireAuth();
        $this->requireNominalRole();

        $filters = [
            'gender' => $_GET['gender'] ?? '',
            'staff_type' => $_GET['staff_type'] ?? '',
            'faculty' => $_GET['faculty'] ?? '',
            'department' => $_GET['department'] ?? '',
            'unit' => $_GET['unit'] ?? '',
            'account_status' => $_GET['account_status'] ?? '',
        ];

        $staff = $this->getFilteredStaff($filters);
        
        $faculties = $this->getFaculties();
        $units = $this->getUnits();

        $this->view('nominal-role/staff-list', [
            'staff' => $staff,
            'filters' => $filters,
            'faculties' => $faculties,
            'units' => $units,
        ]);
    }

    /**
     * Export staff list
     */
    public function exportStaff(): void
    {
        $this->requireAuth();
        $this->requireNominalRole();

        $format = $_GET['format'] ?? 'csv';
        
        $filters = [
            'gender' => $_GET['gender'] ?? '',
            'staff_type' => $_GET['staff_type'] ?? '',
            'faculty' => $_GET['faculty'] ?? '',
            'department' => $_GET['department'] ?? '',
            'unit' => $_GET['unit'] ?? '',
            'account_status' => $_GET['account_status'] ?? '',
        ];

        $staff = $this->getFilteredStaff($filters);

        if ($format === 'excel') {
            $this->exportToExcel($staff, $filters);
        } else {
            $this->exportToCSV($staff, $filters);
        }
    }

    /**
     * Get filtered staff
     */
    private function getFilteredStaff(array $filters): array
    {
        $query = "SELECT u.id, u.email, u.account_status, u.created_at,
                         p.first_name, p.last_name, p.middle_name, p.title, p.designation,
                         p.staff_number, p.gender, p.staff_type, p.faculty, p.department, p.unit,
                         p.office_phone, p.office_location
                  FROM users u
                  INNER JOIN profiles p ON u.id = p.user_id
                  WHERE 1=1";
        
        $params = [];

        if (!empty($filters['gender'])) {
            $query .= " AND p.gender = ?";
            $params[] = $filters['gender'];
        }

        if (!empty($filters['staff_type'])) {
            $query .= " AND p.staff_type = ?";
            $params[] = $filters['staff_type'];
        }

        if (!empty($filters['faculty'])) {
            $query .= " AND p.faculty = ?";
            $params[] = $filters['faculty'];
        }

        if (!empty($filters['department'])) {
            $query .= " AND p.department = ?";
            $params[] = $filters['department'];
        }

        if (!empty($filters['unit'])) {
            $query .= " AND p.unit = ?";
            $params[] = $filters['unit'];
        }

        if (!empty($filters['account_status'])) {
            $query .= " AND u.account_status = ?";
            $params[] = $filters['account_status'];
        }

        $query .= " ORDER BY p.last_name, p.first_name";

        return $this->db->fetchAll($query, $params);
    }

    /**
     * Export to CSV
     */
    private function exportToCSV(array $staff, array $filters): void
    {
        $filename = 'staff_list_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, [
            'Staff Number',
            'Title',
            'First Name',
            'Middle Name',
            'Last Name',
            'Gender',
            'Designation',
            'Staff Type',
            'Faculty',
            'Department',
            'Unit',
            'Email',
            'Office Phone',
            'Office Location',
            'Account Status',
            'Registration Date'
        ]);
        
        // Data
        foreach ($staff as $s) {
            fputcsv($output, [
                $s['staff_number'] ?? '',
                $s['title'] ?? '',
                $s['first_name'] ?? '',
                $s['middle_name'] ?? '',
                $s['last_name'] ?? '',
                $s['gender'] ?? '',
                $s['designation'] ?? '',
                ucfirst($s['staff_type'] ?? ''),
                $s['faculty'] ?? '',
                $s['department'] ?? '',
                $s['unit'] ?? '',
                $s['email'] ?? '',
                $s['office_phone'] ?? '',
                $s['office_location'] ?? '',
                ucfirst($s['account_status'] ?? ''),
                date('Y-m-d', strtotime($s['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export to Excel (using simple HTML table method)
     */
    private function exportToExcel(array $staff, array $filters): void
    {
        $filename = 'staff_list_' . date('Y-m-d_His') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="UTF-8"></head>';
        echo '<body>';
        echo '<table border="1">';
        
        // Headers
        echo '<tr style="background-color: #1e40af; color: white; font-weight: bold;">';
        echo '<th>Staff Number</th>';
        echo '<th>Title</th>';
        echo '<th>First Name</th>';
        echo '<th>Middle Name</th>';
        echo '<th>Last Name</th>';
        echo '<th>Gender</th>';
        echo '<th>Designation</th>';
        echo '<th>Staff Type</th>';
        echo '<th>Faculty</th>';
        echo '<th>Department</th>';
        echo '<th>Unit</th>';
        echo '<th>Email</th>';
        echo '<th>Office Phone</th>';
        echo '<th>Office Location</th>';
        echo '<th>Account Status</th>';
        echo '<th>Registration Date</th>';
        echo '</tr>';
        
        // Data
        foreach ($staff as $s) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($s['staff_number'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['title'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['first_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['middle_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['last_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['gender'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['designation'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars(ucfirst($s['staff_type'] ?? '')) . '</td>';
            echo '<td>' . htmlspecialchars($s['faculty'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['department'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['unit'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['email'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['office_phone'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($s['office_location'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars(ucfirst($s['account_status'] ?? '')) . '</td>';
            echo '<td>' . date('Y-m-d', strtotime($s['created_at'])) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    /**
     * Get statistics
     */
    private function getStats(): array
    {
        $stats = [];
        
        $stats['total_staff'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles")['count'];
        $stats['male_staff'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE gender = 'Male'")['count'];
        $stats['female_staff'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE gender = 'Female'")['count'];
        $stats['teaching_staff'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE staff_type = 'teaching'")['count'];
        $stats['non_teaching_staff'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE staff_type = 'non-teaching'")['count'];
        
        return $stats;
    }

    /**
     * Get faculties
     */
    private function getFaculties(): array
    {
        return $this->db->fetchAll("SELECT DISTINCT faculty FROM faculties_departments WHERE faculty IS NOT NULL ORDER BY faculty");
    }

    /**
     * Get units
     */
    private function getUnits(): array
    {
        return $this->db->fetchAll("SELECT DISTINCT name FROM units_offices WHERE name IS NOT NULL ORDER BY name");
    }

    /**
     * Require nominal role
     */
    private function requireNominalRole(): void
    {
        $allowedRoles = ['nominal_role', 'admin'];
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
            $this->redirect('dashboard');
            exit;
        }
    }
}
