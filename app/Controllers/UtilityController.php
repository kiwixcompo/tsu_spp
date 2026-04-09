<?php

namespace App\Controllers;

use App\Core\Controller;

class UtilityController extends Controller
{
    public function getDepartments(string $faculty): void
    {
        // TSU Faculty and Department mapping
        $facultyDepartments = $this->getFacultyDepartmentMapping();
        
        $departments = $facultyDepartments[$faculty] ?? [];

        $this->json([
            'status' => 'success',
            'data' => $departments
        ]);
    }

    private function getFacultyDepartmentMapping(): array
    {
        // Fetch from database
        if ($this->db) {
            try {
                $data = $this->db->fetchAll("
                    SELECT faculty, department 
                    FROM faculties_departments 
                    ORDER BY faculty, department
                ");
                
                $mapping = [];
                foreach ($data as $row) {
                    $faculty = $row['faculty'];
                    if (!isset($mapping[$faculty])) {
                        $mapping[$faculty] = [];
                    }
                    $mapping[$faculty][] = $row['department'];
                }
                
                return $mapping;
            } catch (\Exception $e) {
                // Fall back to empty array if database error
                return [];
            }
        }
        
        return [];
    }

    public function healthCheck(): void
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'database' => 'disconnected'
        ];

        // Check database connection
        if ($this->db) {
            try {
                $this->db->fetch("SELECT 1");
                $status['database'] = 'connected';
            } catch (\Exception $e) {
                $status['database'] = 'error: ' . $e->getMessage();
            }
        }

        $this->json($status);
    }

    public function getFaculties(): void
    {
        $faculties = array_keys($this->getFacultyDepartmentMapping());

        $this->json([
            'status' => 'success',
            'data' => $faculties
        ]);
    }

    public function getAllFacultiesAndDepartments(): void
    {
        $this->json([
            'status' => 'success',
            'data' => $this->getFacultyDepartmentMapping()
        ]);
    }

    public function getDirectorates(): void
    {
        if (!$this->db) {
            $this->json(['status' => 'error', 'data' => []], 500);
            return;
        }
        try {
            $rows = $this->db->fetchAll(
                "SELECT id, name FROM directorates WHERE is_active = 1 ORDER BY display_order, name"
            );
            $this->json(['status' => 'success', 'data' => $rows]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'data' => []], 500);
        }
    }

    public function getDirectorateUnits(string $directorateId): void
    {
        if (!$this->db) {
            $this->json(['status' => 'error', 'data' => []], 500);
            return;
        }
        try {
            $units = $this->db->fetchAll(
                "SELECT unit_name FROM directorate_units 
                 WHERE directorate_id = ? AND is_active = 1 
                 ORDER BY display_order, unit_name",
                [(int)$directorateId]
            );
            $this->json(['status' => 'success', 'data' => array_column($units, 'unit_name')]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'data' => []], 500);
        }
    }

    public function getAllDirectoratesAndUnits(): void
    {
        if (!$this->db) {
            $this->json(['status' => 'error', 'data' => []], 500);
            return;
        }
        try {
            $rows = $this->db->fetchAll(
                "SELECT d.id, d.name AS directorate, du.unit_name
                 FROM directorates d
                 LEFT JOIN directorate_units du ON du.directorate_id = d.id AND du.is_active = 1
                 WHERE d.is_active = 1
                 ORDER BY d.display_order, d.name, du.display_order, du.unit_name"
            );
            $data = [];
            foreach ($rows as $row) {
                $dir = $row['directorate'];
                if (!isset($data[$dir])) {
                    $data[$dir] = ['id' => $row['id'], 'units' => []];
                }
                if ($row['unit_name']) {
                    $data[$dir]['units'][] = $row['unit_name'];
                }
            }
            $this->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'data' => []], 500);
        }
    }
}