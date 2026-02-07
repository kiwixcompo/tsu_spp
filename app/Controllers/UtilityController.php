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
}